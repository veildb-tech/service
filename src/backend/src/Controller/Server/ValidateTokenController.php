<?php

declare(strict_types=1);

namespace App\Controller\Server;

use App\Repository\AccessBackupTokenRepository;
use App\Repository\Database\DatabaseRepository;
use App\Repository\ServerRepository;
use App\Security\TokenProcessor;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ValidateTokenController extends AbstractController
{
    /**
     * @param Security $security
     * @param TokenProcessor $tokenProcessor
     * @param DatabaseRepository $databaseRepository
     * @param ServerRepository $serverRepository
     * @param AccessBackupTokenRepository $accessBackupTokenRepository
     */
    public function __construct(
        protected readonly Security $security,
        protected readonly TokenProcessor $tokenProcessor,
        protected readonly DatabaseRepository $databaseRepository,
        protected readonly ServerRepository $serverRepository,
        protected readonly AccessBackupTokenRepository $accessBackupTokenRepository
    ) {
    }

    #[Route('/api/validate_token/{token}/', name: 'app_server_validate_token', methods: ["GET"])]
    public function getServerToken(string $token): JsonResponse
    {
        $result = false;
        $accessBackupToken = $this->accessBackupTokenRepository->getByToken($token);
        try {
            if ($accessBackupToken && $accessBackupToken->isValid()) {
                $this->accessBackupTokenRepository->remove($accessBackupToken, true);

                $result = true;
            }
        } catch (\Exception $e) {
            // TODO: Add logging
        }

        return new JsonResponse(
            [
                'result' => $result
            ]
        );
    }
}
