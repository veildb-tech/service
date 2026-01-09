<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230808124216 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_group DROP FOREIGN KEY FK_8F02BF9D2F68B530');
        $this->addSql('DROP INDEX IDX_8F02BF9D2F68B530 ON user_group');
        $this->addSql('ALTER TABLE user_group CHANGE group_id_id group_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_group ADD CONSTRAINT FK_8F02BF9DFE54D947 FOREIGN KEY (group_id) REFERENCES `workspace_group` (id)');
        $this->addSql('CREATE INDEX IDX_8F02BF9DFE54D947 ON user_group (group_id)');
        $this->addSql('ALTER TABLE user_group_database DROP FOREIGN KEY FK_ABE695082F68B530');
        $this->addSql('DROP INDEX IDX_ABE695082F68B530 ON user_group_database');
        $this->addSql('ALTER TABLE user_group_database CHANGE group_id_id group_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_group_database ADD CONSTRAINT FK_ABE69508FE54D947 FOREIGN KEY (group_id) REFERENCES `workspace_group` (id)');
        $this->addSql('CREATE INDEX IDX_ABE69508FE54D947 ON user_group_database (group_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_group DROP FOREIGN KEY FK_8F02BF9DFE54D947');
        $this->addSql('DROP INDEX IDX_8F02BF9DFE54D947 ON user_group');
        $this->addSql('ALTER TABLE user_group CHANGE group_id group_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_group ADD CONSTRAINT FK_8F02BF9D2F68B530 FOREIGN KEY (group_id_id) REFERENCES workspace_group (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_8F02BF9D2F68B530 ON user_group (group_id_id)');
        $this->addSql('ALTER TABLE user_group_database DROP FOREIGN KEY FK_ABE69508FE54D947');
        $this->addSql('DROP INDEX IDX_ABE69508FE54D947 ON user_group_database');
        $this->addSql('ALTER TABLE user_group_database CHANGE group_id group_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_group_database ADD CONSTRAINT FK_ABE695082F68B530 FOREIGN KEY (group_id_id) REFERENCES workspace_group (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_ABE695082F68B530 ON user_group_database (group_id_id)');
    }
}
