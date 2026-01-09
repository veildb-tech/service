<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230821131432 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_group MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE user_group DROP FOREIGN KEY FK_8F02BF9DA76ED395');
        $this->addSql('ALTER TABLE user_group DROP FOREIGN KEY FK_8F02BF9DFE54D947');
        $this->addSql('DROP INDEX `primary` ON user_group');
        $this->addSql('ALTER TABLE user_group DROP id, CHANGE user_id user_id INT NOT NULL, CHANGE group_id group_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_group ADD CONSTRAINT FK_8F02BF9DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_group ADD CONSTRAINT FK_8F02BF9DFE54D947 FOREIGN KEY (group_id) REFERENCES `workspace_group` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_group ADD PRIMARY KEY (group_id, user_id)');
        $this->addSql('ALTER TABLE user_group_database MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE user_group_database DROP FOREIGN KEY FK_ABE69508A2BF053A');
        $this->addSql('ALTER TABLE user_group_database DROP FOREIGN KEY FK_ABE69508FE54D947');
        $this->addSql('DROP INDEX `primary` ON user_group_database');
        $this->addSql('ALTER TABLE user_group_database CHANGE db_id database_id INT NULL DEFAULT NULL');
        $this->addSql('ALTER TABLE user_group_database DROP id, CHANGE group_id group_id INT NOT NULL, CHANGE database_id database_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_group_database ADD CONSTRAINT FK_ABE69508F0AA09DB FOREIGN KEY (database_id) REFERENCES `database` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_group_database ADD CONSTRAINT FK_ABE69508FE54D947 FOREIGN KEY (group_id) REFERENCES `workspace_group` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_group_database ADD PRIMARY KEY (group_id, database_id)');
        $this->addSql('ALTER TABLE user_group_database RENAME INDEX idx_abe69508a2bf053a TO IDX_ABE69508F0AA09DB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_group DROP FOREIGN KEY FK_8F02BF9DFE54D947');
        $this->addSql('ALTER TABLE user_group DROP FOREIGN KEY FK_8F02BF9DA76ED395');
        $this->addSql('ALTER TABLE user_group ADD id INT AUTO_INCREMENT NOT NULL, CHANGE group_id group_id INT DEFAULT NULL, CHANGE user_id user_id INT DEFAULT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE user_group ADD CONSTRAINT FK_8F02BF9DFE54D947 FOREIGN KEY (group_id) REFERENCES workspace_group (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_group ADD CONSTRAINT FK_8F02BF9DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_group_database DROP FOREIGN KEY FK_ABE69508F0AA09DB');
        $this->addSql('ALTER TABLE user_group_database DROP FOREIGN KEY FK_ABE69508FE54D947');
        $this->addSql('ALTER TABLE user_group_database ADD id INT AUTO_INCREMENT NOT NULL, CHANGE group_id group_id INT DEFAULT NULL, CHANGE database_id database_id INT DEFAULT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE user_group_database ADD CONSTRAINT FK_ABE69508A2BF053A FOREIGN KEY (database_id) REFERENCES `database` (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_group_database ADD CONSTRAINT FK_ABE69508FE54D947 FOREIGN KEY (group_id) REFERENCES workspace_group (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_group_database RENAME INDEX idx_abe69508f0aa09db TO IDX_ABE69508A2BF053A');
    }
}
