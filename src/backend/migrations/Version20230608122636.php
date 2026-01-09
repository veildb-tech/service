<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230608122636 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `database` CHANGE workspace_id workspace_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `database` CHANGE engine_id engine_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `database` ADD CONSTRAINT FK_C953062EE78C9C0A FOREIGN KEY (engine_id) REFERENCES `database_engine` (id) ON DELETE SET NULL ON UPDATE NO ACTION');
        $this->addSql('CREATE INDEX IDX_C953062EE78C9C0A ON `database` (engine_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `database` DROP FOREIGN KEY FK_C953062EE78C9C0A');
        $this->addSql('DROP INDEX IDX_C953062EE78C9C0A ON `database`');
        $this->addSql('ALTER TABLE `database` CHANGE engine_id engine_id INT NOT NULL, CHANGE workspace_id workspace_id INT NOT NULL');
    }
}
