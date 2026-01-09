<?php

declare(strict_types=1);

namespace App\Controller\Webhook;

use App\Entity\Webhook;
use App\Service\Webhook\Execute;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ExecuteWebhook extends AbstractController
{
    public function __construct(
        private readonly Execute $webhookExecutor
    ) {
    }

    #[Route('/webhook/execute/{uuid}', name: 'webhook_execute', methods: ["POST"])]
    #[IsGranted('execute_webhook', 'webhook')]
    public function execute(Webhook $webhook): JsonResponse
    {
        $result = false;
        $message = 'Success';

        if ($webhook->getId()) {
            try {
                $result = $this->webhookExecutor->execute($webhook);
            } catch (\Exception $exception) {
                $message = $exception->getMessage();
            }
        } else {
            throw $this->createNotFoundException();
        }

        return new JsonResponse(
            [
                'result' => $result,
                'message' => $message
            ]
        );
    }
}
