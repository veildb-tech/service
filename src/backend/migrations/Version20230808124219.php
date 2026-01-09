<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230808124219 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `user_group` DROP FOREIGN KEY `FK_8F02BF9DA76ED395`');
        $this->addSql('ALTER TABLE `user_group` ADD CONSTRAINT `FK_8F02BF9DA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE CASCADE ON UPDATE CASCADE');
        $this->addSql('ALTER TABLE `user_group` DROP FOREIGN KEY `FK_8F02BF9DFE54D947`');
        $this->addSql('ALTER TABLE `user_group` ADD CONSTRAINT `FK_8F02BF9DFE54D947` FOREIGN KEY (`group_id`) REFERENCES `workspace_group`(`id`) ON DELETE CASCADE ON UPDATE CASCADE');

        $this->addSql('ALTER TABLE `user_group_database` DROP FOREIGN KEY `FK_ABE69508A2BF053A`');
        $this->addSql('ALTER TABLE `user_group_database` ADD CONSTRAINT `FK_ABE69508A2BF053A` FOREIGN KEY (`db_id`) REFERENCES `database`(`id`) ON DELETE CASCADE ON UPDATE CASCADE');
        $this->addSql('ALTER TABLE `user_group_database` DROP FOREIGN KEY `FK_ABE69508FE54D947`');
        $this->addSql('ALTER TABLE `user_group_database` ADD CONSTRAINT `FK_ABE69508FE54D947` FOREIGN KEY (`group_id`) REFERENCES `workspace_group`(`id`) ON DELETE CASCADE ON UPDATE CASCADE');

    }

    public function down(Schema $schema): void
    {
    }
}
