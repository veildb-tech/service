<?php

declare(strict_types=1);

namespace App\Security\Http;

use App\Entity\Server;
use App\Entity\Workspace\User;
use App\Enums\Workspace\UserRoleEnum;
use App\Enums\Security\AuthenticationTypeEnum;
use App\Repository\ServerRepository;
use App\Repository\Workspace\WorkspaceRepository;
use App\Security\TokenProcessor;
use App\Service\Url;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authenticator\JWTAuthenticator;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authenticator\Token\JWTPostAuthenticationToken;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\TokenExtractorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class TokenAuthenticator extends JWTAuthenticator
{
    /**
     * @var Server|null
     */
    private ?Server $server;

    public function __construct(
        protected readonly JWTTokenManagerInterface $jwtManager,
        protected readonly EventDispatcherInterface $eventDispatcher,
        protected readonly TokenExtractorInterface $tokenExtractor,
        protected readonly UserProviderInterface $userProvider,
        protected readonly TokenProcessor $tokenProcessor,
        protected readonly ServerRepository $serverRepository,
        protected readonly WorkspaceRepository $workspaceRepository,
        protected readonly Url $url,
        protected null|TranslatorInterface $translator = null
    ) {
        parent::__construct(
            $jwtManager,
            $eventDispatcher,
            $tokenExtractor,
            $userProvider,
            $translator
        );
    }

    /**
     * Check is authentication method is supported
     *
     * @param Request $request
     *
     * @return bool|null
     */
    public function supports(Request $request): ?bool
    {
        if ($this->isTokenAuth($request)) {
            return false !== $this->getTokenExtractor()->extract($request);
        }
        return parent::supports($request);
    }

    /**
     * @throws \Exception
     */
    public function authenticate(Request $request): Passport
    {
        if (!$this->isTokenAuth($request)) {
            return parent::authenticate($request);
        }

        $token = $this->getTokenExtractor()->extract($request);
        $token = $this->tokenProcessor->parse($token);
        if ($token->isExpired(new \DateTimeImmutable())) {
            throw new \Exception('JWT Key is expired.');
        }

        $uuid         = $token->claims()->get('uid');
        $secretKey    = $token->headers()->get('secret_key');
        $this->server = $this->serverRepository->findOneBy(['uuid' => $uuid, 'secret_key' => $secretKey]);
        if (!$this->server) {
            throw new \Exception('JWT Key is incorrect.');
        }

        return new SelfValidatingPassport(new UserBadge('server.api.request'));
    }

    public function onAuthenticationSuccess(Request $request, ?TokenInterface $token, string $firewallName): ?Response
    {
        if (!$this->isTokenAuth($request)) {
            return parent::onAuthenticationSuccess($request, $token, $firewallName);
        }
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        if (!$this->isTokenAuth($request)) {
            return parent::onAuthenticationFailure($request, $exception);
        }

        return new JsonResponse(
            [
                'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
            ],
            Response::HTTP_UNAUTHORIZED
        );
    }

    public function createApiToken(Passport $passport, string $firewallName): TokenInterface
    {
        return new JWTPostAuthenticationToken(
            $this->getTempApiUser(),
            $firewallName,
            [
                UserRoleEnum::DBM_OWNER->name,
                UserRoleEnum::DBM_ADMIN->name
            ],
            $this->server->getSecretKey()
        );
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

    /**
     * Get API temporary user
     *
     * @return User
     */
    private function getTempApiUser(): User
    {
        $workspace = $this->server->getWorkspace();

        $user = new User();
        $user->setFirstname(
            $this->server->getName()
        )->setLastname(
            $this->server->getName()
        )->setEmail(
            $this->server->getUuid() . '@' . $this->url->getDomain()
        )->setApiWorkspaceCode($workspace->getCode())/*->setRoles(
            [
                UserRoleEnum::DBM_OWNER->name,
                UserRoleEnum::DBM_ADMIN->name
            ]
        )*/;

        return $user;
    }
}
