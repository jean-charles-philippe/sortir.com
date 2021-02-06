<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210205143102 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE vacation_user (vacation_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_6AFE70EB54DD8D72 (vacation_id), INDEX IDX_6AFE70EBA76ED395 (user_id), PRIMARY KEY(vacation_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE vacation_user ADD CONSTRAINT FK_6AFE70EB54DD8D72 FOREIGN KEY (vacation_id) REFERENCES vacation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE vacation_user ADD CONSTRAINT FK_6AFE70EBA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE vacation DROP FOREIGN KEY FK_E3DADF7567B3B43D');
        $this->addSql('DROP INDEX IDX_E3DADF7567B3B43D ON vacation');
        $this->addSql('ALTER TABLE vacation DROP users_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE vacation_user');
        $this->addSql('ALTER TABLE vacation ADD users_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE vacation ADD CONSTRAINT FK_E3DADF7567B3B43D FOREIGN KEY (users_id) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_E3DADF7567B3B43D ON vacation (users_id)');
    }
}
