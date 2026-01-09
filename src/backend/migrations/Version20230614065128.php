<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230614065128 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE database_dump_logs DROP INDEX UNIQ_64C3FFC5628AFCCF, ADD INDEX IDX_64C3FFC5628AFCCF (dump_id_id)');
        $this->addSql('ALTER TABLE database_dump_logs CHANGE dump_id_id dump_id_id INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE database_dump_logs DROP INDEX IDX_64C3FFC5628AFCCF, ADD UNIQUE INDEX UNIQ_64C3FFC5628AFCCF (dump_id_id)');
        $this->addSql('ALTER TABLE database_dump_logs CHANGE dump_id_id dump_id_id INT NOT NULL');
    }
}
