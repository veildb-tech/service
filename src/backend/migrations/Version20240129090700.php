<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240129090700 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE database_rule_suggestion (id INT AUTO_INCREMENT NOT NULL, db_id INT NOT NULL, status TINYINT(1) DEFAULT 0 NOT NULL, rule LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', INDEX IDX_4FB5911AA2BF053A (db_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE database_rule_suggestion ADD CONSTRAINT FK_4FB5911AA2BF053A FOREIGN KEY (db_id) REFERENCES `database` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE database_rule_suggestion DROP FOREIGN KEY FK_4FB5911AA2BF053A');
        $this->addSql('DROP TABLE database_rule_suggestion');
    }
}
