<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230526132518 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE database_dump (id INT AUTO_INCREMENT NOT NULL, db_id INT NOT NULL, status VARCHAR(20) DEFAULT NULL, filename VARCHAR(255) DEFAULT NULL, INDEX IDX_351133696914FF8C (db_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE database_dump ADD CONSTRAINT FK_351133696914FF8C FOREIGN KEY (db_id) REFERENCES `database` (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C953062E539B0606 ON `database` (uid)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE database_dump DROP FOREIGN KEY FK_351133696914FF8C');
        $this->addSql('DROP TABLE database_dump');
        $this->addSql('DROP INDEX UNIQ_C953062E539B0606 ON `database`');
    }
}
