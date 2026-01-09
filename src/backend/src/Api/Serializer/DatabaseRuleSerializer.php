<?php

declare(strict_types=1);

namespace App\Api\Serializer;

use App\Enums\Database\Rule\OperatorEnum;
use App\Service\Database\Rule\FakerOptionsFactory;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use App\Entity\Database\DatabaseRule;

class DatabaseRuleSerializer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;
    private const ALREADY_CALLED = 'RULE_ATTRIBUTE_NORMALIZER_ALREADY_CALLED';

    /**
     * @param $object
     * @param $format
     * @param array $context
     * @return array|\ArrayObject|bool|float|int|string|null
     * @throws ExceptionInterface
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $context[self::ALREADY_CALLED] = true;
        $object->setRule($this->normalizeRule($object->getRule()));
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
        // Make sure we're not called twice and avoid this response for graphql
        if (isset($context[self::ALREADY_CALLED]) || $format === 'graphql') {
            return false;
        }
        return $data instanceof DatabaseRule;
    }

    private function normalizeRule(array $rules): array
    {
        $fakerOptionFactory = new FakerOptionsFactory();
        $transformedRules = [];
        foreach ($rules as $rule) {
            $transformedRules[$rule['table']] = [
                'columns' => [],
                'method' => $rule['method']
            ];

            if (isset($rule['conditions']) && count($rule['conditions'])) {
                $transformedRules[$rule['table']]['where'] = $this->prepareCondition(
                    $rule['conditions'],
                    $rule['conditionOperator'] ?? ''
                );
            }

            if (isset($rule['columns']) && count($rule['columns']) && $rule['method'] !== 'truncate') {
                $transformedRules[$rule['table']]['columns'] = $this->processColumns(
                    $rule['columns'],
                    $rule['method'],
                    $fakerOptionFactory
                );
            }
        }

        return $transformedRules;
    }

    /**
     * Process rule columns
     *
     * @param array $columns
     * @param string $method
     * @param FakerOptionsFactory $fakerOptionFactory
     *
     * @return array
     */
    private function processColumns(array $columns, string $method, FakerOptionsFactory $fakerOptionFactory): array
    {
        $processedColumns = [];
        foreach ($columns as $column) {
            $optionFactory = $fakerOptionFactory->create($column['value']);
            $options = $optionFactory->process($column['options'] ?? []);

            $processedColumns[$column['name']] = [
                'name' => $column['name'],
                'value' => $column['value'],
                'options' => $options,
                'method' => $column['method'] ?? $method
            ];

            if (isset($column['conditions']) && count($column['conditions'])) {
                $processedColumns[$column['name']]['where'] = $this->prepareCondition(
                    $column['conditions'],
                    $column['conditionOperator'] ?? ''
                );
            }
        }
        return $processedColumns;
    }

    /**
     * Retrieve SQL query for conditions
     *
     * @param array $conditions
     * @param string $conditionOperator
     *
     * @return string
     */
    private function prepareCondition(array $conditions, string $conditionOperator = 'and'): string
    {
        $where = [];
        foreach ($conditions as $condition) {
            $enum = OperatorEnum::from($condition['operator']);
            $where[] = $enum->getSqlCondition($condition['column'], $condition['value']);
        }

        if ($conditionOperator == 'or') {
            return implode(' OR ', $where);
        }
        return implode(' AND ', $where);
    }
}
