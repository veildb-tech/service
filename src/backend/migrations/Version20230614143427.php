<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230614143427 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `database` ADD server_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `database` ADD CONSTRAINT FK_C953062E1844E6B7 FOREIGN KEY (server_id) REFERENCES server (id)');
        $this->addSql('CREATE INDEX IDX_C953062E1844E6B7 ON `database` (server_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `database` DROP FOREIGN KEY FK_C953062E1844E6B7');
        $this->addSql('DROP INDEX IDX_C953062E1844E6B7 ON `database`');
        $this->addSql('ALTER TABLE `database` DROP server_id');
    }
}
