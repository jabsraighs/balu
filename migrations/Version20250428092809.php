<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250428092809 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE category (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, company_id INTEGER NOT NULL, name VARCHAR(100) NOT NULL, description CLOB DEFAULT NULL, CONSTRAINT FK_64C19C1979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_64C19C1979B1AD6 ON category (company_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE client (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, company_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, contact_name VARCHAR(255) DEFAULT NULL, email VARCHAR(180) DEFAULT NULL, phone VARCHAR(50) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , CONSTRAINT FK_C7440455979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_C7440455979B1AD6 ON client (company_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE company (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, siret VARCHAR(14) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE invoice (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, company_id INTEGER NOT NULL, client_id INTEGER NOT NULL, customer_id INTEGER NOT NULL, invoice_number VARCHAR(50) NOT NULL, status VARCHAR(50) NOT NULL, date_due DATE DEFAULT NULL, date_paid DATETIME DEFAULT NULL, total_amount NUMERIC(12, 2) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , CONSTRAINT FK_90651744979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_9065174419EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_906517449395C3F3 FOREIGN KEY (customer_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_906517442DA68207 ON invoice (invoice_number)
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
        $this->addSql(<<<'SQL'
            CREATE TABLE invoice_line (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, invoice_id INTEGER NOT NULL, description VARCHAR(255) DEFAULT NULL, quantity INTEGER NOT NULL, unit_price NUMERIC(10, 2) NOT NULL, discount DOUBLE PRECISION NOT NULL, product_name VARCHAR(255) NOT NULL, product_description VARCHAR(255) NOT NULL, CONSTRAINT FK_D3D1D6932989F1FD FOREIGN KEY (invoice_id) REFERENCES invoice (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D3D1D6932989F1FD ON invoice_line (invoice_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE payment (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, invoice_id INTEGER NOT NULL, amount NUMERIC(12, 2) NOT NULL, date_paid DATETIME NOT NULL, method VARCHAR(100) DEFAULT NULL, CONSTRAINT FK_6D28840D2989F1FD FOREIGN KEY (invoice_id) REFERENCES invoice (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_6D28840D2989F1FD ON payment (invoice_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE product (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, company_id INTEGER NOT NULL, category_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, unit_price NUMERIC(10, 2) NOT NULL, CONSTRAINT FK_D34A04AD979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_D34A04AD12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D34A04AD979B1AD6 ON product (company_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D34A04AD12469DE2 ON product (category_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE quote (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, company_id INTEGER NOT NULL, client_id INTEGER NOT NULL, customer_id INTEGER NOT NULL, quote_number VARCHAR(50) NOT NULL, status VARCHAR(50) NOT NULL, date_created DATETIME NOT NULL, date_valid_until DATE DEFAULT NULL, total_amount NUMERIC(12, 2) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , CONSTRAINT FK_6B71CBF4979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_6B71CBF419EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_6B71CBF49395C3F3 FOREIGN KEY (customer_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_6B71CBF4AC28B117 ON quote (quote_number)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_6B71CBF4979B1AD6 ON quote (company_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_6B71CBF419EB6921 ON quote (client_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_6B71CBF49395C3F3 ON quote (customer_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE quote_line (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, quote_id INTEGER NOT NULL, description VARCHAR(255) DEFAULT NULL, quantity INTEGER NOT NULL, unit_price NUMERIC(10, 2) NOT NULL, discount DOUBLE PRECISION NOT NULL, product_name VARCHAR(255) NOT NULL, product_description VARCHAR(255) NOT NULL, CONSTRAINT FK_43F3EB7CDB805178 FOREIGN KEY (quote_id) REFERENCES quote (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_43F3EB7CDB805178 ON quote_line (quote_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE "user" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, company_id INTEGER DEFAULT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
            , password VARCHAR(255) NOT NULL, is_verified BOOLEAN NOT NULL, fullname VARCHAR(255) NOT NULL, CONSTRAINT FK_8D93D649979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_8D93D649979B1AD6 ON "user" (company_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "user" (email)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , available_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , delivered_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
            )
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE category
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE client
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE company
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE invoice
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE invoice_line
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE payment
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE product
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE quote
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE quote_line
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE "user"
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
    }
}
