<?php

declare(strict_types=1);

namespace App\Api\Serializer\Workspace;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use App\Entity\Workspace\UserInvitation;
use App\Service\Url;

class UserInvitationSerializer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;
    private const ALREADY_CALLED = 'USER_INVITATION_ATTRIBUTE_NORMALIZER_ALREADY_CALLED';

    /**
     * @param Url $urlService
     */
    public function __construct(private readonly Url $urlService)
    {
    }

    /**
     * @param $object
     * @param $format
     * @param array $context
     * @return array|\ArrayObject|bool|float|int|string|null
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function normalize($object, $format = null, array $context = [])
    {
        /** @var $object UserInvitation */
        $context[self::ALREADY_CALLED] = true;

        $expirationDate = new \DateTimeImmutable();
        $expirationDate = $expirationDate->setTimestamp(
            (new \DateTimeImmutable())->getTimestamp() + UserInvitation::EXPIRATION_PERIOD
        );

        $object->setUrl($this->urlService->getUrl('auth/invitation', ['invitation' => $object->getUuid()]));
        $object->setExpirationDate($expirationDate);
        return $this->normalizer->normalize($object, $format, $context);
    }

    /**
     * Only for database rules. Doesn't support of graphql. Avoid double call
     *
     * @param $data
     * @param $format
     * @param array $context
     * @return bool
     */
    public function supportsNormalization($data, $format = null, array $context = []): bool
    {
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }
        return $data instanceof UserInvitation;
    }
}
