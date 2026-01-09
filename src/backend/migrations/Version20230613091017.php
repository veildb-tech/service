<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230613091017 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE database_dump_logs (id INT AUTO_INCREMENT NOT NULL, dump_id_id INT NOT NULL, status VARCHAR(255) NOT NULL, message VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_64C3FFC5628AFCCF (dump_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE database_dump_logs ADD CONSTRAINT FK_64C3FFC5628AFCCF FOREIGN KEY (dump_id_id) REFERENCES database_dump (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE database_dump_logs DROP FOREIGN KEY FK_64C3FFC5628AFCCF');
        $this->addSql('DROP TABLE database_dump_logs');
    }
}
