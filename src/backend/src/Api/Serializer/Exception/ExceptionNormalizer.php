<?php
namespace App\Api\Serializer\Exception;

use App\Exception\AppExceptionInterface;
use GraphQL\Error\Error;
use GraphQL\Error\FormattedError;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ExceptionNormalizer implements NormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = []): array
    {
        $exception = $object->getPrevious();

        // We could use default error formatter if needed:
        //$error = FormattedError::createFromException($object);

        return [
            'message' => $exception->getMessage()
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof Error && $data->getPrevious() instanceof AppExceptionInterface;
    }
}
