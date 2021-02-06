<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210205181906 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE vacation CHANGE organiser organiser_id INT NOT NULL');
        $this->addSql('ALTER TABLE vacation ADD CONSTRAINT FK_E3DADF75A0631C12 FOREIGN KEY (organiser_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_E3DADF75A0631C12 ON vacation (organiser_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE vacation DROP FOREIGN KEY FK_E3DADF75A0631C12');
        $this->addSql('DROP INDEX IDX_E3DADF75A0631C12 ON vacation');
        $this->addSql('ALTER TABLE vacation CHANGE organiser_id organiser INT NOT NULL');
    }
}
