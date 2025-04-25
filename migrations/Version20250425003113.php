<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250425003113 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE category (id SERIAL NOT NULL, company_id INT NOT NULL, name VARCHAR(100) NOT NULL, description TEXT DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_64C19C1979B1AD6 ON category (company_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE client (id SERIAL NOT NULL, company_id INT NOT NULL, name VARCHAR(255) NOT NULL, contact_name VARCHAR(255) DEFAULT NULL, email VARCHAR(180) DEFAULT NULL, phone VARCHAR(50) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_C7440455979B1AD6 ON client (company_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE company (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, siret VARCHAR(14) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE invoice (id SERIAL NOT NULL, invoice_number VARCHAR(50) NOT NULL, status VARCHAR(50) NOT NULL, date_due DATE DEFAULT NULL, date_paid TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE invoice_line (id SERIAL NOT NULL, invoice_id INT NOT NULL, product_id INT NOT NULL, description VARCHAR(255) DEFAULT NULL, quantity INT NOT NULL, unit_price NUMERIC(10, 2) NOT NULL, discount DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D3D1D6932989F1FD ON invoice_line (invoice_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D3D1D6934584665A ON invoice_line (product_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE payment (id SERIAL NOT NULL, invoice_id INT NOT NULL, amount NUMERIC(12, 2) NOT NULL, date_paid TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, method VARCHAR(100) DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_6D28840D2989F1FD ON payment (invoice_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE product (id SERIAL NOT NULL, company_id INT NOT NULL, category_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, unit_price NUMERIC(10, 2) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D34A04AD979B1AD6 ON product (company_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D34A04AD12469DE2 ON product (category_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE quote (id SERIAL NOT NULL, company_id INT NOT NULL, client_id INT NOT NULL, customer_id INT NOT NULL, quote_number VARCHAR(50) NOT NULL, status VARCHAR(50) NOT NULL, date_created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, date_valid_until DATE DEFAULT NULL, total_amount NUMERIC(12, 2) NOT NULL, PRIMARY KEY(id))
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
            CREATE TABLE quote_line (id SERIAL NOT NULL, quote_id INT NOT NULL, product_id INT NOT NULL, description VARCHAR(255) DEFAULT NULL, quantity INT NOT NULL, unit_price NUMERIC(10, 2) NOT NULL, discount DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_43F3EB7CDB805178 ON quote_line (quote_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_43F3EB7C4584665A ON quote_line (product_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE "user" (id SERIAL NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "user" (email)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))
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
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN messenger_messages.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN messenger_messages.available_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN messenger_messages.delivered_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
                BEGIN
                    PERFORM pg_notify('messenger_messages', NEW.queue_name::text);
                    RETURN NEW;
                END;
            $$ LANGUAGE plpgsql;
        SQL);
        $this->addSql(<<<'SQL'
            DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE category ADD CONSTRAINT FK_64C19C1979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE client ADD CONSTRAINT FK_C7440455979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice_line ADD CONSTRAINT FK_D3D1D6932989F1FD FOREIGN KEY (invoice_id) REFERENCES invoice (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice_line ADD CONSTRAINT FK_D3D1D6934584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE payment ADD CONSTRAINT FK_6D28840D2989F1FD FOREIGN KEY (invoice_id) REFERENCES invoice (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product ADD CONSTRAINT FK_D34A04AD979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product ADD CONSTRAINT FK_D34A04AD12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quote ADD CONSTRAINT FK_6B71CBF4979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quote ADD CONSTRAINT FK_6B71CBF419EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quote ADD CONSTRAINT FK_6B71CBF49395C3F3 FOREIGN KEY (customer_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quote_line ADD CONSTRAINT FK_43F3EB7CDB805178 FOREIGN KEY (quote_id) REFERENCES quote (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quote_line ADD CONSTRAINT FK_43F3EB7C4584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE category DROP CONSTRAINT FK_64C19C1979B1AD6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE client DROP CONSTRAINT FK_C7440455979B1AD6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice_line DROP CONSTRAINT FK_D3D1D6932989F1FD
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice_line DROP CONSTRAINT FK_D3D1D6934584665A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE payment DROP CONSTRAINT FK_6D28840D2989F1FD
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product DROP CONSTRAINT FK_D34A04AD979B1AD6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product DROP CONSTRAINT FK_D34A04AD12469DE2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quote DROP CONSTRAINT FK_6B71CBF4979B1AD6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quote DROP CONSTRAINT FK_6B71CBF419EB6921
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quote DROP CONSTRAINT FK_6B71CBF49395C3F3
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quote_line DROP CONSTRAINT FK_43F3EB7CDB805178
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quote_line DROP CONSTRAINT FK_43F3EB7C4584665A
        SQL);
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
