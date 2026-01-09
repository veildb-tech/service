<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231201185510 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE database_rule ADD uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('UPDATE database_rule SET uuid = FLOOR(RAND() * 100000000000)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_34A5EE80D17F50A6 ON database_rule (uuid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DFDA31F5D17F50A6 ON user_restore (uuid)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_34A5EE80D17F50A6 ON `database_rule`');
        $this->addSql('ALTER TABLE `database_rule` DROP uuid');
        $this->addSql('DROP INDEX UNIQ_DFDA31F5D17F50A6 ON user_restore');
    }
}
