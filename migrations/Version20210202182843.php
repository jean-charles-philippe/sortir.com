<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210202182843 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE location CHANGE city_id city_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE vacation CHANGE campus_id campus_id INT DEFAULT NULL, CHANGE state_id state_id INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE location CHANGE city_id city_id INT NOT NULL');
        $this->addSql('ALTER TABLE vacation CHANGE campus_id campus_id INT NOT NULL, CHANGE state_id state_id INT NOT NULL');
    }
}
