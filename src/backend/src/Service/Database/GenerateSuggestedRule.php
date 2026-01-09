<?php

declare(strict_types=1);

namespace App\Service\Database;

use App\Entity\Database\Database;
use App\Entity\Database\DatabaseRuleSuggestion;
use App\Enums\Database\Rule\SuggestionStatusEnum;
use App\Repository\Database\DatabaseRuleSuggestionRepository;
use App\Service\Database\RuleGenerator\RuleGenerator;
use App\Service\Logger;

final class GenerateSuggestedRule
{
    public function __construct(
        protected Logger $logger,
        protected readonly RuleGenerator $ruleGenerator,
        protected readonly DatabaseRuleSuggestionRepository $databaseRuleSuggestionRepository
    ) {
    }

    /**
     * @param Database $db
     *
     * @return void
     * @throws \Exception
     */
    public function execute(Database $db): void
    {
        $rule = $this->ruleGenerator->generate(
            $db->getDbSchema() ? json_decode($db->getDbSchema(), true) : []
        );

        if (count($rule)) {
            $this->saveRule($db, $rule);
        }
    }

    /**
     * Saves a rule suggestion into the database.
     *
     * @param Database $db The database connection.
     * @param array $rule The rule suggestion data.
     *
     * @return void
     */
    protected function saveRule(Database $db, array $rule): void
    {
        $ruleSuggestion = $this->getSuggestionRule($db);

        $prevRule = $ruleSuggestion->getRule();

        $ruleSuggestion->setDb($db)
            ->setRule(array_values($rule))
            ->setStatus(SuggestionStatusEnum::ACTIVE->value);

        if (count(array_diff_key($rule, $prevRule))) {
            $ruleSuggestion->setStatus(SuggestionStatusEnum::UPDATED->value);
        }
        $this->databaseRuleSuggestionRepository->save($ruleSuggestion, true);
    }

    /**
     * Retrieves a suggestion rule from the given database.
     *
     * @param Database $db The database from which to retrieve the suggestion rule.
     *
     * @return DatabaseRuleSuggestion
     */
    private function getSuggestionRule(Database $db): DatabaseRuleSuggestion
    {
        if (!$ruleSuggestion = $db->getDatabaseRuleSuggestions()->first()) {
            $ruleSuggestion = new DatabaseRuleSuggestion();
        }
        return $ruleSuggestion;
    }
}
