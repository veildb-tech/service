<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230608050109 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE database_engine (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `database` ADD engine_id INT NOT NULL, DROP engine');
        $this->addSql('ALTER TABLE `database` ADD CONSTRAINT FK_C953062E522C4A1C FOREIGN KEY (engine_id) REFERENCES database_engine (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C953062E522C4A1C ON `database` (engine_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `database` DROP FOREIGN KEY FK_C953062E522C4A1C');
        $this->addSql('DROP TABLE database_engine');
        $this->addSql('DROP INDEX UNIQ_C953062E522C4A1C ON `database`');
        $this->addSql('ALTER TABLE `database` ADD engine INT DEFAULT NULL, DROP engine_id_id');
    }
}
