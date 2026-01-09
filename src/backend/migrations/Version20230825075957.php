<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230825075957 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE server DROP FOREIGN KEY FK_5A6DD5F6A6ADFB8');
        $this->addSql('DROP INDEX IDX_5A6DD5F6A6ADFB8 ON server');
        $this->addSql('ALTER TABLE server CHANGE workspace_id_id workspace_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE server ADD CONSTRAINT FK_5A6DD5F682D40A1F FOREIGN KEY (workspace_id) REFERENCES workspace (id)');
        $this->addSql('CREATE INDEX IDX_5A6DD5F682D40A1F ON server (workspace_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE server DROP FOREIGN KEY FK_5A6DD5F682D40A1F');
        $this->addSql('DROP INDEX IDX_5A6DD5F682D40A1F ON server');
        $this->addSql('ALTER TABLE server CHANGE workspace_id workspace_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE server ADD CONSTRAINT FK_5A6DD5F6A6ADFB8 FOREIGN KEY (workspace_id_id) REFERENCES workspace (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_5A6DD5F6A6ADFB8 ON server (workspace_id_id)');
    }
}
