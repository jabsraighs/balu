<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250426093112 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice ADD company_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice ADD client_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice ADD customer_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice ADD total_amount NUMERIC(12, 2) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice ADD CONSTRAINT FK_90651744979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice ADD CONSTRAINT FK_9065174419EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice ADD CONSTRAINT FK_906517449395C3F3 FOREIGN KEY (customer_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_90651744979B1AD6 ON invoice (company_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_9065174419EB6921 ON invoice (client_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_906517449395C3F3 ON invoice (customer_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice DROP CONSTRAINT FK_90651744979B1AD6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice DROP CONSTRAINT FK_9065174419EB6921
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice DROP CONSTRAINT FK_906517449395C3F3
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_90651744979B1AD6
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_9065174419EB6921
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_906517449395C3F3
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice DROP company_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice DROP client_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice DROP customer_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice DROP total_amount
        SQL);
    }
}
