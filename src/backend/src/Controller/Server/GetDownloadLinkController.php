<?php

declare(strict_types=1);

namespace App\Controller\Server;

use App\Entity\AccessBackupToken;
use App\Enums\ServerStatusEnum;
use App\Repository\AccessBackupTokenRepository;
use App\Repository\Database\DatabaseRepository;
use App\Repository\ServerRepository;
use App\Security\TokenProcessor;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class GetDownloadLinkController extends AbstractController
{
    public const TTL = 3600;

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

    /**
     * @throws \Exception
     */
    #[Route('/api/get_download_link/{dbUid}/{dumpUid}/', name: 'app_server_get_download_link', methods: ["GET"])]
    public function getServerToken(string $dbUid, string $dumpUid): JsonResponse
    {
        $database = $this->databaseRepository->findOneBy(
            [
                'uid' => $dbUid
            ]
        );
        $server = $this->serverRepository->find($database->getServer());

        if (!$server || $server->getStatus() !== ServerStatusEnum::ENABLED->value) {
            throw new \Exception('Server not found or not active.');
        }

        $user = $this->security->getUser();

        if (!$token = $this->getAccessToken($user, $dumpUid)) {
            $token = $this->createAccessToken($user, $dumpUid);
        }

        $downloadLink = rtrim($server->getUrl(), '/') . '/download/' . $token . '/';

        return new JsonResponse(
            [
                'link' => $downloadLink
            ]
        );
    }

    /**
     * Create Access token
     *
     * @param UserInterface $user
     * @param string $dumpUid
     *
     * @return string
     */
    private function createAccessToken(UserInterface $user, string $dumpUid): string
    {
        $exp   = time() + self::TTL;
        $token = sha1($dumpUid . '_' . $user->getId() . '_' . $user->getUserIdentifier());

        $accessBackupToken = new AccessBackupToken();

        $accessBackupToken->setUserIdentifier(
            $user->getUserIdentifier()
        )->setDumpUid(
            $dumpUid
        )->setToken(
            $token
        )->setExpirationDate(
            \DateTime::createFromInterface(new \DateTimeImmutable("@{$exp}"))
        );

        $this->accessBackupTokenRepository->save($accessBackupToken, true);

        return $accessBackupToken->getToken();
    }

    /**
     * Get access token
     *
     * @param UserInterface $user
     * @param string $dumpUid
     *
     * @return string|null
     * @throws \Exception
     */
    private function getAccessToken(UserInterface $user, string $dumpUid): ?string
    {
        $token = sha1($dumpUid . '_' . $user->getId() . '_' . $user->getUserIdentifier());
        if (!$accessBackupToken = $this->accessBackupTokenRepository->getByToken($token)) {
            return null;
        }

        if (!$accessBackupToken->isValid()) {
            $this->accessBackupTokenRepository->remove($accessBackupToken);
            return null;
        }
        return $accessBackupToken->getToken();
    }
}
