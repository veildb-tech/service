<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230719125112 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64982D40A1F');
        $this->addSql('DROP INDEX IDX_8D93D64982D40A1F ON user');
        $this->addSql('ALTER TABLE user DROP workspace_id');
        $this->addSql('CREATE TABLE workspace_user (workspace_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_C971A58B82D40A1F (workspace_id), INDEX IDX_C971A58BA76ED395 (user_id), PRIMARY KEY(workspace_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE workspace_user ADD CONSTRAINT FK_C971A58B82D40A1F FOREIGN KEY (workspace_id) REFERENCES workspace (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE workspace_user ADD CONSTRAINT FK_C971A58BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE workspace_user DROP FOREIGN KEY FK_C971A58B82D40A1F');
        $this->addSql('ALTER TABLE workspace_user DROP FOREIGN KEY FK_C971A58BA76ED395');
        $this->addSql('DROP TABLE workspace_user');
    }
}
