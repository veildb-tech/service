<?php
declare(strict_types=1);


namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GraphQl\Query;
use App\Enums\Database\DatabaseEngineEnum;
use App\Enums\Database\DatabasePlatformEnum;
use App\Enums\Database\DatabaseStatusEnum;
use App\Enums\Database\DumpLogsStatusEnum;
use App\Enums\Database\DumpStatusEnum;
use App\Enums\Database\Rule\CleanUpEnum;
use App\Enums\Database\Rule\FakersEnum;
use App\Enums\Database\Rule\OperatorEnum;
use App\Enums\Database\Rule\ScheduleTypeEnum;
use App\Enums\ServerStatusEnum;
use App\Enums\Webhook\WebhookOperationEnum;
use App\Enums\Webhook\WebhookStatusEnum;
use App\Enums\Workspace\UserGroupPermissionEnum;
use App\Resolver\ConfigurationResolver;

#[ApiResource(
    operations: [
        new Get()
    ],
    graphQlOperations: [
        new Query(
            resolver: ConfigurationResolver::class,
            args: [],
            name: ''
        )
    ]
)]
class Configuration
{
    /**
     * @return array
     */
    public function getWorkspaceGroupRoles(): array
    {
        return UserGroupPermissionEnum::getOptions();
    }

    /**
     * @return array
     */
    public function getDatabaseStatuses(): array
    {
        return DatabaseStatusEnum::getOptions();
    }

    /**
     * @return array
     */
    public function getPlatforms(): array
    {
        return DatabasePlatformEnum::getOptions();
    }

    /**
     * @return array
     */
    public function getDumpStatuses(): array
    {
        return DumpStatusEnum::getOptions();
    }

    /**
     * @return array
     */
    public function getEngines(): array
    {
        return DatabaseEngineEnum::getOptions();
    }

    /**
     * @return array
     */
    public function getDumpLogsStatuses(): array
    {
        return DumpLogsStatusEnum::getOptions();
    }

    /**
     * @return array
     */
    public function getServerStatuses(): array
    {
        return ServerStatusEnum::getOptions();
    }

    /**
     * @return array
     */
    public function getRuleOperators(): array
    {
        return OperatorEnum::getOptions();
    }

    /**
     * @return arrayFakersEnum::getOptions();
     */
    public function getRuleFakers(): array
    {
        return FakersEnum::getOptionsWithType();
    }

    public function getCleanUpRules(): array
    {
        return CleanUpEnum::getOptions();
    }

    /**
     * @return array
     */
    public function getWebhookOperations(): array
    {
        return WebhookOperationEnum::getOptions();
    }

    /**
     * @return array
     */
    public function getWebhookStatuses(): array
    {
        return WebhookStatusEnum::getOptions();
    }

    /**
     * @return array
     */
    public function getScheduleTypes(): array
    {
        return ScheduleTypeEnum::getOptions();
    }
}
