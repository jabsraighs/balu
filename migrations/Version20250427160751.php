<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250427160751 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice_line DROP CONSTRAINT fk_d3d1d6934584665a
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_d3d1d6934584665a
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice_line ADD product_name VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice_line ADD product_description VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice_line DROP product_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quote_line DROP CONSTRAINT fk_43f3eb7c4584665a
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_43f3eb7c4584665a
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quote_line ADD product_name VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quote_line ADD product_description VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quote_line DROP product_id
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice_line ADD product_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice_line DROP product_name
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice_line DROP product_description
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice_line ADD CONSTRAINT fk_d3d1d6934584665a FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_d3d1d6934584665a ON invoice_line (product_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quote_line ADD product_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quote_line DROP product_name
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quote_line DROP product_description
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quote_line ADD CONSTRAINT fk_43f3eb7c4584665a FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_43f3eb7c4584665a ON quote_line (product_id)
        SQL);
    }
}
