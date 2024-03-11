<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240311201559 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_information ADD COLUMN phone VARCHAR(20) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__user_information AS SELECT id, user_id, firstname, lastname, email FROM user_information');
        $this->addSql('DROP TABLE user_information');
        $this->addSql('CREATE TABLE user_information (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id BLOB NOT NULL --(DC2Type:uuid)
        , firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, CONSTRAINT FK_8062D116A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO user_information (id, user_id, firstname, lastname, email) SELECT id, user_id, firstname, lastname, email FROM __temp__user_information');
        $this->addSql('DROP TABLE __temp__user_information');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8062D116A76ED395 ON user_information (user_id)');
    }
}
