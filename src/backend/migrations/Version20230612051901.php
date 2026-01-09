<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230612051901 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `database` ADD rule_id INT DEFAULT NULL, ADD engine VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE `database` ADD CONSTRAINT FK_C953062E744E0351 FOREIGN KEY (rule_id) REFERENCES `database_rule` (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C953062E744E0351 ON `database` (rule_id)');
        $this->addSql('ALTER TABLE database_rule DROP FOREIGN KEY FK_34A5EE80A2BF053A');
        $this->addSql('DROP INDEX UNIQ_34A5EE80A2BF053A ON database_rule');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `database` DROP FOREIGN KEY FK_C953062E744E0351');
        $this->addSql('DROP INDEX UNIQ_C953062E744E0351 ON `database`');
        $this->addSql('ALTER TABLE `database` DROP rule_id, DROP engine');
        $this->addSql('ALTER TABLE `database_rule` ADD CONSTRAINT FK_34A5EE80A2BF053A FOREIGN KEY (db_id) REFERENCES `database` (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_34A5EE80A2BF053A ON `database_rule` (db_id)');
    }
}
