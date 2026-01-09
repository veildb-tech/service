<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230807120123 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE database_dump_delete_rules (id INT AUTO_INCREMENT NOT NULL, db_id INT NOT NULL, name VARCHAR(255) NOT NULL, status TINYINT(1) DEFAULT 0 NOT NULL, rule LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', INDEX IDX_BEF1A7C0A2BF053A (db_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE database_dump_delete_rules ADD CONSTRAINT FK_BEF1A7C0A2BF053A FOREIGN KEY (db_id) REFERENCES `database` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE database_dump_delete_rules DROP FOREIGN KEY FK_BEF1A7C0A2BF053A');
        $this->addSql('DROP TABLE database_dump_delete_rules');
    }
}
