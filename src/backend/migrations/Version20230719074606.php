<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230719074606 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE database_rule_template (id INT AUTO_INCREMENT NOT NULL, workspace_id INT DEFAULT NULL, rule JSON DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, type SMALLINT NOT NULL, INDEX IDX_C3D7D48D82D40A1F (workspace_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE database_rule_template ADD CONSTRAINT FK_C3D7D48D82D40A1F FOREIGN KEY (workspace_id) REFERENCES workspace (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE database_rule_template DROP FOREIGN KEY FK_C3D7D48D82D40A1F');
        $this->addSql('DROP TABLE database_rule_template');
    }
}
