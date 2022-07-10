<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220710183922 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_in_meeting_meeting DROP FOREIGN KEY FK_1AF1539EC670F87A');
        $this->addSql('ALTER TABLE user_in_meeting_user DROP FOREIGN KEY FK_8248EDFDC670F87A');
        $this->addSql('DROP TABLE user_in_meeting');
        $this->addSql('DROP TABLE user_in_meeting_meeting');
        $this->addSql('DROP TABLE user_in_meeting_user');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_in_meeting (id INT AUTO_INCREMENT NOT NULL, is_going TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE user_in_meeting_meeting (user_in_meeting_id INT NOT NULL, meeting_id INT NOT NULL, INDEX IDX_1AF1539E67433D9C (meeting_id), INDEX IDX_1AF1539EC670F87A (user_in_meeting_id), PRIMARY KEY(user_in_meeting_id, meeting_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE user_in_meeting_user (user_in_meeting_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_8248EDFDA76ED395 (user_id), INDEX IDX_8248EDFDC670F87A (user_in_meeting_id), PRIMARY KEY(user_in_meeting_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE user_in_meeting_meeting ADD CONSTRAINT FK_1AF1539E67433D9C FOREIGN KEY (meeting_id) REFERENCES meeting (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_in_meeting_meeting ADD CONSTRAINT FK_1AF1539EC670F87A FOREIGN KEY (user_in_meeting_id) REFERENCES user_in_meeting (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_in_meeting_user ADD CONSTRAINT FK_8248EDFDA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_in_meeting_user ADD CONSTRAINT FK_8248EDFDC670F87A FOREIGN KEY (user_in_meeting_id) REFERENCES user_in_meeting (id) ON UPDATE NO ACTION ON DELETE CASCADE');
    }
}
