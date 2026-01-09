<?php

namespace App\Factory\Database;

use App\Entity\Database\Database;
use App\Repository\Database\DatabaseRepository;
use App\Enums\Database\DatabaseStatusEnum;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Database>
 *
 * @method        Database|Proxy create(array|callable $attributes = [])
 * @method static Database|Proxy createOne(array $attributes = [])
 * @method static Database|Proxy find(object|array|mixed $criteria)
 * @method static Database|Proxy findOrCreate(array $attributes)
 * @method static Database|Proxy first(string $sortedField = 'id')
 * @method static Database|Proxy last(string $sortedField = 'id')
 * @method static Database|Proxy random(array $attributes = [])
 * @method static Database|Proxy randomOrCreate(array $attributes = [])
 * @method static DatabaseRepository|RepositoryProxy repository()
 * @method static Database[]|Proxy[] all()
 * @method static Database[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Database[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Database[]|Proxy[] findBy(array $attributes)
 * @method static Database[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Database[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class DatabaseFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        return [
            'name' => self::faker()->company(),
            'status' => DatabaseStatusEnum::ENABLED->value,
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Database $database): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Database::class;
    }
}
