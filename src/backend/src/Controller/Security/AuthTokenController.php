<?php

declare(strict_types=1);

namespace App\Controller\Security;

use App\Enums\ServerStatusEnum;
use App\Repository\ServerRepository;
use App\Security\TokenProcessor;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

class AuthTokenController extends AbstractController
{
    /**
     * @param TokenProcessor $tokenProcessor
     * @param ServerRepository $serverRepository
     */
    public function __construct(
        protected readonly TokenProcessor $tokenProcessor,
        protected readonly ServerRepository $serverRepository
    ) {
    }

    #[Route('/api/token_check', name: 'app_server_token_check', methods: ["POST"])]
    public function getServerToken(
        Request $request
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $server = $this->serverRepository->findOneBy(
            [
                'uuid' => Uuid::fromString($data['uuid']),
                'secret_key' => $data['secret_key']
            ]
        );

        if (!$server || $server->getStatus() !== ServerStatusEnum::ENABLED->value) {
            throw new \Exception('Server not found or not active.');
        }

        $token = $this->tokenProcessor->generate(
            $server->getUuid()->toRfc4122(),
            $server->getSecretKey(),
            $server->getIpAddress() ?? '127.0.0.1'
        );

        return new JsonResponse(
            [
                'token' => $token->toString()
            ]
        );
    }
}
