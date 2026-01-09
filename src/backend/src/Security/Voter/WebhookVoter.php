<?php
declare(strict_types=1);

namespace App\Security\Voter;

use App\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\HttpFoundation\RequestStack;

class WebhookVoter extends Voter
{
    /**
     * @param RequestStack $requestStack
     */
    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === 'execute_webhook';
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     * @return bool
     * @throws AccessDeniedException
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $host = $this->requestStack->getCurrentRequest()->getHost();
        $allowedDomains = explode(',', $subject->getDomains());
        $allowedDomains = array_map(function ($domain) {
            $parsed = parse_url(trim($domain));
            return !empty($parsed['host']) ? $parsed['host'] : trim($domain);
        }, $allowedDomains);

        if (!in_array($host, $allowedDomains)) {
            throw new AccessDeniedException();
        }

        return true;
    }
}
