<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230711122305 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `database` ADD created_at DATETIME NOT NULL DEFAULT \'2022-01-01 00:00:00\' COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME NOT NULL DEFAULT \'2022-01-01 00:00:00\' COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE database_dump ADD created_at DATETIME NOT NULL DEFAULT \'2022-01-01 00:00:00\' COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME NOT NULL DEFAULT \'2022-01-01 00:00:00\' COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE database_dump_logs ADD created_at DATETIME NOT NULL DEFAULT \'2022-01-01 00:00:00\' COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE database_dump DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE database_dump_logs DROP created_at');
        $this->addSql('ALTER TABLE `database` DROP created_at, DROP updated_at');
    }
}
