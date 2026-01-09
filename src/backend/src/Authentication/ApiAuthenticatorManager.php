<?php

declare(strict_types=1);

namespace App\Authentication;

use App\Enums\Security\AuthenticationTypeEnum;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\AuthenticationEvents;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Core\Exception\AccountStatusException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Debug\TraceableAuthenticator;
use Symfony\Component\Security\Http\Authenticator\InteractiveAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\BadgeInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Event\AuthenticationTokenCreatedEvent;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticatorManager;

class ApiAuthenticatorManager extends AuthenticatorManager
{
    private iterable $authenticators;

    private TokenStorageInterface $tokenStorage;

    private EventDispatcherInterface $eventDispatcher;

    private bool $eraseCredentials;

    private ?LoggerInterface $logger;

    private string $firewallName;

    private bool $hideUserNotFoundExceptions;

    private array $requiredBadges;

    /**
     * @param iterable<mixed, AuthenticatorInterface> $authenticators
     */
    public function __construct(
        iterable $authenticators,
        TokenStorageInterface $tokenStorage,
        EventDispatcherInterface $eventDispatcher,
        string $firewallName,
        LoggerInterface $logger = null,
        bool $eraseCredentials = true,
        bool $hideUserNotFoundExceptions = true,
        array $requiredBadges = []
    ) {
        $this->authenticators = $authenticators;
        $this->tokenStorage = $tokenStorage;
        $this->eventDispatcher = $eventDispatcher;
        $this->firewallName = $firewallName;
        $this->logger = $logger;
        $this->eraseCredentials = $eraseCredentials;
        $this->hideUserNotFoundExceptions = $hideUserNotFoundExceptions;
        $this->requiredBadges = $requiredBadges;

        parent::__construct(
            $authenticators,
            $tokenStorage,
            $eventDispatcher,
            $firewallName,
            $logger,
            $eraseCredentials,
            $hideUserNotFoundExceptions,
            $requiredBadges
        );
    }

    public function authenticateRequest(Request $request): ?Response
    {
        $authenticators = $request->attributes->get('_security_authenticators');
        $request->attributes->remove('_security_authenticators');
        $request->attributes->remove('_security_skipped_authenticators');

        if (!$authenticators) {
            return null;
        }

        return $this->executeAuthenticators($authenticators, $request);
    }

    /**
     * @param AuthenticatorInterface[] $authenticators
     */
    private function executeAuthenticators(array $authenticators, Request $request): ?Response
    {
        foreach ($authenticators as $authenticator) {
            // recheck if the authenticator still supports the listener. supports() is called
            // eagerly (before token storage is initialized), whereas authenticate() is called
            // lazily (after initialization).
            if (false === $authenticator->supports($request)) {
                $this->logger?->debug(
                    'Skipping the "{authenticator}" authenticator as it did not support the request.',
                    [
                        'authenticator' => \get_class(
                            $authenticator instanceof TraceableAuthenticator
                                ? $authenticator->getAuthenticator() : $authenticator
                        )
                    ]
                );
                continue;
            }

            if ($this->isTokenAuth($request)) {
                $response = $this->executeTokenAuthenticator($authenticator, $request);
            } else {
                $response = $this->executeUserAuthenticator($authenticator, $request);
            }

            if (null !== $response) {
                $this->logger?->debug(
                    'The "{authenticator}" authenticator set the response. Any later authenticator will not be called',
                    [
                        'authenticator' => \get_class(
                            $authenticator instanceof TraceableAuthenticator
                                ? $authenticator->getAuthenticator() : $authenticator
                        )
                    ]
                );

                return $response;
            }
        }
        return null;
    }

    /**
     * Executing token authenticating
     *
     * @param AuthenticatorInterface $authenticator
     * @param Request $request
     *
     * @return Response|null
     */
    private function executeTokenAuthenticator(AuthenticatorInterface $authenticator, Request $request): ?Response
    {
        $passport = null;
        $previousToken = $this->tokenStorage->getToken();

        try {
            // get the passport from the Authenticator
            $passport = $authenticator->authenticate($request);

            // create the authentication token
            $authenticatedToken = $authenticator->createApiToken($passport, $this->firewallName);

            if (true === $this->eraseCredentials) {
                $authenticatedToken->eraseCredentials();
            }
        } catch (AuthenticationException $e) {
            // oh no! Authentication failed!
            $response = $this->handleAuthenticationFailure($e, $request, $authenticator, $passport);
            if ($response instanceof Response) {
                return $response;
            }

            return null;
        }

        $response = $this->handleAuthenticationSuccess(
            $authenticatedToken,
            $passport,
            $request,
            $authenticator,
            $previousToken
        );

        if ($response instanceof Response) {
            return $response;
        }

        $this->logger?->debug('Authenticator set no success response: request continues.', ['authenticator' => \get_class($authenticator instanceof TraceableAuthenticator ? $authenticator->getAuthenticator() : $authenticator)]);

        return null;
    }

    private function executeUserAuthenticator(AuthenticatorInterface $authenticator, Request $request): ?Response
    {
        $passport = null;
        $previousToken = $this->tokenStorage->getToken();

        try {
            // get the passport from the Authenticator
            $passport = $authenticator->authenticate($request);

            // check the passport (e.g. password checking)
            $event = new CheckPassportEvent($authenticator, $passport);
            $this->eventDispatcher->dispatch($event);

            // check if all badges are resolved
            $resolvedBadges = [];
            foreach ($passport->getBadges() as $badge) {
                if (!$badge->isResolved()) {
                    throw new BadCredentialsException(
                        sprintf(
                            'Authentication failed: Security badge "%s" is not resolved, did you forget to register the correct listeners?',
                            get_debug_type($badge)
                        )
                    );
                }

                $resolvedBadges[] = $badge::class;
            }

            $missingRequiredBadges = array_diff($this->requiredBadges, $resolvedBadges);
            if ($missingRequiredBadges) {
                throw new BadCredentialsException(sprintf('Authentication failed; Some badges marked as required by the firewall config are not available on the passport: "%s".', implode('", "', $missingRequiredBadges)));
            }

            // create the authentication token
            $authenticatedToken = $authenticator->createToken($passport, $this->firewallName);

            // announce the authentication token
            $authenticatedToken = $this->eventDispatcher->dispatch(new AuthenticationTokenCreatedEvent($authenticatedToken, $passport))->getAuthenticatedToken();

            if (true === $this->eraseCredentials) {
                $authenticatedToken->eraseCredentials();
            }

            $this->eventDispatcher->dispatch(new AuthenticationSuccessEvent($authenticatedToken), AuthenticationEvents::AUTHENTICATION_SUCCESS);

            $this->logger?->info('Authenticator successful!', ['token' => $authenticatedToken, 'authenticator' => \get_class($authenticator instanceof TraceableAuthenticator ? $authenticator->getAuthenticator() : $authenticator)]);
        } catch (AuthenticationException $e) {
            // oh no! Authentication failed!
            $response = $this->handleAuthenticationFailure($e, $request, $authenticator, $passport);
            if ($response instanceof Response) {
                return $response;
            }

            return null;
        }

        // success! (sets the token on the token storage, etc)
        $response = $this->handleAuthenticationSuccess(
            $authenticatedToken,
            $passport,
            $request,
            $authenticator,
            $previousToken
        );

        if ($response instanceof Response) {
            return $response;
        }

        $this->logger?->debug('Authenticator set no success response: request continues.', ['authenticator' => \get_class($authenticator instanceof TraceableAuthenticator ? $authenticator->getAuthenticator() : $authenticator)]);

        return null;
    }

    private function handleAuthenticationSuccess(
        TokenInterface $authenticatedToken,
        Passport $passport,
        Request $request,
        AuthenticatorInterface $authenticator,
        ?TokenInterface $previousToken
    ): ?Response {
        $this->tokenStorage->setToken($authenticatedToken);

        $response = $authenticator->onAuthenticationSuccess($request, $authenticatedToken, $this->firewallName);
        if ($authenticator instanceof InteractiveAuthenticatorInterface && $authenticator->isInteractive()) {
            $loginEvent = new InteractiveLoginEvent($request, $authenticatedToken);
            $this->eventDispatcher->dispatch($loginEvent, SecurityEvents::INTERACTIVE_LOGIN);
        }

        $this->eventDispatcher->dispatch($loginSuccessEvent = new LoginSuccessEvent(
            $authenticator,
            $passport,
            $authenticatedToken,
            $request,
            $response,
            $this->firewallName,
            $previousToken
        ));
        return $loginSuccessEvent->getResponse();
    }

    /**
     * Handles an authentication failure and returns the Response for the authenticator.
     */
    private function handleAuthenticationFailure(
        AuthenticationException $authenticationException,
        Request $request,
        AuthenticatorInterface $authenticator,
        ?Passport $passport
    ): ?Response {
        $this->logger?->info(
            'Authenticator failed.',
            [
                'exception' => $authenticationException,
                'authenticator' => \get_class(
                    $authenticator instanceof TraceableAuthenticator
                        ? $authenticator->getAuthenticator() : $authenticator
                )
            ]
        );

        // Avoid leaking error details in case of invalid user (e.g. user not found or invalid account status)
        // to prevent user enumeration via response content comparison
        if (
            $this->hideUserNotFoundExceptions
            && (
                $authenticationException instanceof UserNotFoundException
                || (
                    $authenticationException instanceof AccountStatusException
                    && !$authenticationException instanceof CustomUserMessageAccountStatusException
                )
            )
        ) {
            $authenticationException = new BadCredentialsException(
                'Bad credentials.',
                0,
                $authenticationException
            );
        }

        $response = $authenticator->onAuthenticationFailure($request, $authenticationException);
        if (null !== $response && null !== $this->logger) {
            $this->logger->debug(
                'The "{authenticator}" authenticator set the failure response.',
                [
                    'authenticator' => \get_class(
                        $authenticator instanceof TraceableAuthenticator
                            ? $authenticator->getAuthenticator() : $authenticator
                    )
                ]
            );
        }

        $this->eventDispatcher->dispatch(
            $loginFailureEvent = new LoginFailureEvent(
                $authenticationException,
                $authenticator,
                $request,
                $response,
                $this->firewallName,
                $passport
            )
        );

        // returning null is ok, it means they want the request to continue
        return $loginFailureEvent->getResponse();
    }

    /**
     * Get Auth Type
     *
     * @param Request $request
     *
     * @return bool
     */
    private function isTokenAuth(Request $request): bool
    {
        $authType = $request->headers->get('Authorization-Type');
        if (!$authType || $authType === AuthenticationTypeEnum::USER->value) {
            return false;
        }
        return true;
    }
}
