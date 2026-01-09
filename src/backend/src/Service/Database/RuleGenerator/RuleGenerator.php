<?php

declare(strict_types=1);

namespace App\Service\Database\RuleGenerator;

use App\Enums\Database\Rule\MethodEnum;
use Symfony\Component\Yaml\Yaml;

class RuleGenerator
{
    const STATUS_SUGGESTED = 'suggested';

    /**
     * @var array
     */
    private array $patterns = [];

    /**
     * @var array
     */
    private array $serviceWordsList = [];

    /**
     * @param string $patternsListFile
     * @param string $serviceWordsFile
     */
    public function __construct(
        protected string $patternsListFile,
        protected string $serviceWordsFile
    ) {
        $this->patterns = Yaml::parseFile($patternsListFile);
        $this->serviceWordsList = $this->readServiceWords($serviceWordsFile);
    }

    /**
     * Generate rule
     *
     * @param array $dbSchema
     *
     * @return array
     */
    public function generate(array $dbSchema): array
    {
        if (!count($dbSchema)) {
            return [];
        }

        $rule = [];
        foreach ($this->getIterableDbSchema($dbSchema) as $table => $columns) {
            $rule[] = $this->processTableData($table, $columns);
        }
        return array_filter(array_map('array_filter', $rule));
    }

    /**
     * Processes table data and updates given rule array with column analysis results.
     *
     * @param string $table The table name.
     * @param array $columns
     *
     * @return array
     **/
    protected function processTableData(string $table, array $columns): array
    {
        $columnsRule = [];
        $columnInternalIndex = 0;
        foreach ($columns as $column => $columnData) {
            $result = $this->analyzeColumn($table, $column, $columnData);
            if (count($result)) {
                // This is temporary (maybe no) workaround to fix issue with internal indexes on the frontend
                // TODO: remove it. better to update index logic on the frontend
                $result['index'] = sprintf('%s_row_%s', $table, $columnInternalIndex);
                $columnInternalIndex++;
                $columnsRule[] = $result;
            }
        }

        if (count($columnsRule)) {
            return [
                'table' => $table,
                'status' => self::STATUS_SUGGESTED,
                'method' => MethodEnum::CUSTOM->value,
                'columns' => $columnsRule
            ];
        }
        return [];
    }

    /**
     * Analyze a row of data and return an array based on the row value.
     *
     * @param string $table The table name to analyze.
     * @param string $column The row value to analyze.
     * @param array $columnData The data of the row.
     *
     * @return array The analyzed row data.
     */
    protected function analyzeColumn(string $table, string $column, array $columnData): array
    {
        foreach ($this->patterns as $config) {
            if ($this->isPatternPassed($column, $table, $config, $columnData['type'])) {
                return array_merge(
                    ['name' => $column],
                    $config['rule']
                );
            }
        }
        return [];
    }

    /**
     * Check if the pattern passed
     *
     * @param string $column
     * @param string $table
     * @param array $pattern
     * @param string $columnType
     *
     * @return bool
     */
    protected function isPatternPassed(string $column, string $table, array $pattern, string $columnType): bool
    {
        if (isset($pattern['table_pattern'])) {
            if (!(int)preg_match_all($pattern['table_pattern'], $table) || $this->containsServiceWord($table)) {
                return false;
            }
        }

        if (isset($pattern['column_pattern'])) {
            if ((int)preg_match_all($pattern['column_pattern'], $table) < $pattern['column_pattern_precision']) {
                return false;
            }
        }

        if (isset($pattern['term'])) {
            return str_contains($column, $pattern['term']) && in_array($columnType, ['string', 'varchar', 'text']);
        }
        return true;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    private function containsServiceWord(string $name): bool
    {
        foreach ($this->serviceWordsList['table_ignore'] as $needle) {
            if (str_contains(strtolower($name), strtolower($needle))) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get iterable db schema instance
     *
     * @param array $dbSchema
     *
     * @return \Iterator
     */
    protected function getIterableDbSchema(array $dbSchema): \Iterator
    {
        return new \ArrayIterator($dbSchema);
    }

    /**
     * Fill array with service workds
     *
     * @param string $fileName
     *
     * @return array
     */
    protected function readServiceWords(string $fileName): array
    {
        if (!is_file($fileName) || !is_readable($fileName)) {
            return [];
        }
        return json_decode(file_get_contents($fileName), true);
    }
}
