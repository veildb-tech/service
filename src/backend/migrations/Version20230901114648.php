<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230901114648 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `database` DROP FOREIGN KEY FK_C953062E744E0351');
        $this->addSql('DROP INDEX UNIQ_C953062E744E0351 ON `database`');
        $this->addSql('ALTER TABLE `database` DROP rule_id');
        $this->addSql('ALTER TABLE database_rule DROP db_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `database_rule` ADD db_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `database` ADD rule_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `database` ADD CONSTRAINT FK_C953062E744E0351 FOREIGN KEY (rule_id) REFERENCES database_rule (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C953062E744E0351 ON `database` (rule_id)');
    }
}
