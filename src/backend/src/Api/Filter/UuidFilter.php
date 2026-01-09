<?php

declare(strict_types=1);

namespace App\Api\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Exception\InvalidArgumentException;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Uid\Uuid;

final class UuidFilter extends AbstractFilter
{
    /**
     * @inheritDoc
     */
    public function getDescription(string $resourceClass): array
    {
        if (!$this->properties) {
            return [];
        }

        $description = [];
        foreach ($this->properties as $property => $strategy) {
            $description["$property"] = [
                'property' => $property,
                'type' => 'string',
                'required' => false,
                'description' => 'Filter using a uuid.'
            ];
        }

        return $description;
    }

    /**
     * @inheritDoc
     */
    protected function filterProperty(
        string $property,
        $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        Operation $operation = null,
        array $context = []
    ): void {
        if (
            null === $value
            || !$this->isPropertyEnabled($property, $resourceClass)
            || !$this->isPropertyMapped($property, $resourceClass, true)
        ) {
            return;
        }

        $values = $this->normalizeValues((array) $value, $property);
        if (null === $values || 'db.uid' !== $property) {
            return;
        }

        $field = $property;
        $alias = $queryBuilder->getRootAliases()[0];

        if ($this->isPropertyNested($property, $resourceClass)) {
            [$alias, $field] = $this->addJoinsForNestedProperty(
                $property,
                $alias,
                $queryBuilder,
                $queryNameGenerator,
                $resourceClass,
                Join::INNER_JOIN
            );
        }

        $values = array_map($this->getUidFromValue(...), $values);

        $this->addWhereByStrategy($queryBuilder, $queryNameGenerator, $alias, $field, $values);
    }

    protected function addWhereByStrategy(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $alias,
        string $field,
        mixed $values,
        bool $caseSensitive = true
    ): void {
        if (!\is_array($values)) {
            $values = [$values];
        }

        $wrapCase = $this->createWrapCase($caseSensitive);
        $valueParameter = ':'.$queryNameGenerator->generateParameterName($field);
        $aliasedField = sprintf('%s.%s', $alias, $field);

        if (1 === \count($values)) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->eq($wrapCase($aliasedField), $wrapCase($valueParameter)))
                ->setParameter($valueParameter, $values[0]);

            return;
        }

        $queryBuilder
            ->andWhere($queryBuilder->expr()->in($wrapCase($aliasedField), $valueParameter))
            ->setParameter($valueParameter, $caseSensitive ? $values : array_map('strtolower', $values));
    }

    /**
     * Creates a function that will wrap a Doctrine expression according to the
     * specified case sensitivity.
     *
     * For example, "o.name" will get wrapped into "LOWER(o.name)" when $caseSensitive
     * is false.
     */
    protected function createWrapCase(bool $caseSensitive): \Closure
    {
        return static function (string $expr) use ($caseSensitive): string {
            if ($caseSensitive) {
                return $expr;
            }

            return sprintf('LOWER(%s)', $expr);
        };
    }

    protected function normalizeValues(array $values, string $property): ?array
    {
        foreach ($values as $key => $value) {
            if (!\is_int($key) || !(\is_string($value) || \is_int($value))) {
                unset($values[$key]);
            }
        }

        if (empty($values)) {
            $this->getLogger()->notice('Invalid filter ignored', [
                'exception' => new InvalidArgumentException(
                    sprintf(
                        'At least one value is required, multiple values should be in "%1$s[]=firstvalue&%1$s[]=secondvalue" format',
                        $property
                    )
                ),
            ]);

            return null;
        }

        return array_values($values);
    }

    /**
     * Convert UUID to binary string
     *
     * @param string $value
     *
     * @return string
     */
    protected function getUidFromValue(string $value): string
    {
        return (new Uuid($value))->toBinary();
    }
}
