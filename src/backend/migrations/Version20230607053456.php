<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230607053456 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE database_dump DROP FOREIGN KEY FK_351133696914FF8C');
        $this->addSql('DROP INDEX idx_351133696914ff8c ON database_dump');
        $this->addSql('CREATE INDEX IDX_35113369A2BF053A ON database_dump (db_id)');
        $this->addSql('ALTER TABLE database_dump ADD CONSTRAINT FK_351133696914FF8C FOREIGN KEY (db_id) REFERENCES `database` (id)');
        $this->addSql('ALTER TABLE database_rule ADD db_id INT NOT NULL, CHANGE rule rule LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE database_rule ADD CONSTRAINT FK_34A5EE80A2BF053A FOREIGN KEY (db_id) REFERENCES `database` (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_34A5EE80A2BF053A ON database_rule (db_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE database_dump DROP FOREIGN KEY FK_35113369A2BF053A');
        $this->addSql('DROP INDEX idx_35113369a2bf053a ON database_dump');
        $this->addSql('CREATE INDEX IDX_351133696914FF8C ON database_dump (db_id)');
        $this->addSql('ALTER TABLE database_dump ADD CONSTRAINT FK_35113369A2BF053A FOREIGN KEY (db_id) REFERENCES `database` (id)');
        $this->addSql('ALTER TABLE database_rule DROP FOREIGN KEY FK_34A5EE80A2BF053A');
        $this->addSql('DROP INDEX UNIQ_34A5EE80A2BF053A ON database_rule');
        $this->addSql('ALTER TABLE database_rule DROP db_id, CHANGE rule rule LONGTEXT DEFAULT NULL COLLATE `utf8mb4_bin`');
    }
}
