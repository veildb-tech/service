<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230727084807 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_group (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, group_id_id INT DEFAULT NULL, INDEX IDX_8F02BF9DA76ED395 (user_id), INDEX IDX_8F02BF9D2F68B530 (group_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_group_database (id INT AUTO_INCREMENT NOT NULL, group_id_id INT DEFAULT NULL, db_id INT DEFAULT NULL, INDEX IDX_ABE695082F68B530 (group_id_id), INDEX IDX_ABE69508A2BF053A (db_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_group ADD CONSTRAINT FK_8F02BF9DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_group ADD CONSTRAINT FK_8F02BF9D2F68B530 FOREIGN KEY (group_id_id) REFERENCES `workspace_group` (id)');
        $this->addSql('ALTER TABLE user_group_database ADD CONSTRAINT FK_ABE695082F68B530 FOREIGN KEY (group_id_id) REFERENCES `workspace_group` (id)');
        $this->addSql('ALTER TABLE user_group_database ADD CONSTRAINT FK_ABE69508A2BF053A FOREIGN KEY (db_id) REFERENCES `database` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_group DROP FOREIGN KEY FK_8F02BF9DA76ED395');
        $this->addSql('ALTER TABLE user_group DROP FOREIGN KEY FK_8F02BF9D2F68B530');
        $this->addSql('ALTER TABLE user_group_database DROP FOREIGN KEY FK_ABE695082F68B530');
        $this->addSql('ALTER TABLE user_group_database DROP FOREIGN KEY FK_ABE69508A2BF053A');
        $this->addSql('DROP TABLE user_group');
        $this->addSql('DROP TABLE user_group_database');
    }
}
