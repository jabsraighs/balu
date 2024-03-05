<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240305211412 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id BLOB NOT NULL --(DC2Type:uuid)
        , name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE client (id BLOB NOT NULL --(DC2Type:uuid)
        , user_client_id BLOB NOT NULL --(DC2Type:uuid)
        , firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_C7440455190BE4C5 FOREIGN KEY (user_client_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_C7440455190BE4C5 ON client (user_client_id)');
        $this->addSql('CREATE TABLE company (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, contact_email VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, address VARCHAR(120) NOT NULL, postal_code VARCHAR(20) NOT NULL, city VARCHAR(100) NOT NULL, siret_number BIGINT NOT NULL, website CLOB DEFAULT NULL, name VARCHAR(255) NOT NULL, tva_number BIGINT NOT NULL)');
        $this->addSql('CREATE TABLE cron_job (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(191) NOT NULL, command VARCHAR(1024) NOT NULL, schedule VARCHAR(191) NOT NULL, description VARCHAR(191) NOT NULL, enabled BOOLEAN NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX un_name ON cron_job (name)');
        $this->addSql('CREATE TABLE cron_report (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, job_id INTEGER DEFAULT NULL, run_at DATETIME NOT NULL, run_time DOUBLE PRECISION NOT NULL, exit_code INTEGER NOT NULL, output CLOB NOT NULL, error CLOB NOT NULL, CONSTRAINT FK_B6C6A7F5BE04EA9 FOREIGN KEY (job_id) REFERENCES cron_job (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_B6C6A7F5BE04EA9 ON cron_report (job_id)');
        $this->addSql('CREATE TABLE invoice (id BLOB NOT NULL --(DC2Type:uuid)
        , quote_id BLOB DEFAULT NULL --(DC2Type:uuid)
        , user_invoice_id BLOB NOT NULL --(DC2Type:uuid)
        , client_id BLOB NOT NULL --(DC2Type:uuid)
        , created_at DATE NOT NULL --(DC2Type:date_immutable)
        , due_date DATE NOT NULL --(DC2Type:date_immutable)
        , payment_status VARCHAR(50) NOT NULL, total_amount DOUBLE PRECISION NOT NULL, name VARCHAR(255) NOT NULL, tva DOUBLE PRECISION NOT NULL, total_tva DOUBLE PRECISION NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_90651744DB805178 FOREIGN KEY (quote_id) REFERENCES quote (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_906517448EEE3711 FOREIGN KEY (user_invoice_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_9065174419EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_90651744DB805178 ON invoice (quote_id)');
        $this->addSql('CREATE INDEX IDX_906517448EEE3711 ON invoice (user_invoice_id)');
        $this->addSql('CREATE INDEX IDX_9065174419EB6921 ON invoice (client_id)');
        $this->addSql('CREATE TABLE payment (id BLOB NOT NULL --(DC2Type:uuid)
        , invoice_id BLOB NOT NULL --(DC2Type:uuid)
        , amount_paid DOUBLE PRECISION NOT NULL, payment_date DATE NOT NULL --(DC2Type:date_immutable)
        , PRIMARY KEY(id), CONSTRAINT FK_6D28840D2989F1FD FOREIGN KEY (invoice_id) REFERENCES invoice (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_6D28840D2989F1FD ON payment (invoice_id)');
        $this->addSql('CREATE TABLE product (id BLOB NOT NULL --(DC2Type:uuid)
        , category_id BLOB NOT NULL --(DC2Type:uuid)
        , user_id BLOB NOT NULL --(DC2Type:uuid)
        , name VARCHAR(255) NOT NULL, description CLOB NOT NULL, price DOUBLE PRECISION NOT NULL, unit VARCHAR(10) NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_D34A04AD12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_D34A04ADA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_D34A04AD12469DE2 ON product (category_id)');
        $this->addSql('CREATE INDEX IDX_D34A04ADA76ED395 ON product (user_id)');
        $this->addSql('CREATE TABLE quote (id BLOB NOT NULL --(DC2Type:uuid)
        , client_id BLOB DEFAULT NULL --(DC2Type:uuid)
        , user_quote_id BLOB NOT NULL --(DC2Type:uuid)
        , created_at DATE DEFAULT NULL --(DC2Type:date_immutable)
        , expiry_at DATE DEFAULT NULL --(DC2Type:date_immutable)
        , status VARCHAR(50) DEFAULT NULL, total_amount DOUBLE PRECISION NOT NULL, tva DOUBLE PRECISION NOT NULL, total_tva DOUBLE PRECISION NOT NULL, description VARCHAR(50) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_6B71CBF419EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_6B71CBF43B0DB578 FOREIGN KEY (user_quote_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_6B71CBF419EB6921 ON quote (client_id)');
        $this->addSql('CREATE INDEX IDX_6B71CBF43B0DB578 ON quote (user_quote_id)');
        $this->addSql('CREATE TABLE quote_line (id BLOB NOT NULL --(DC2Type:uuid)
        , quote_id BLOB DEFAULT NULL --(DC2Type:uuid)
        , invoice_id BLOB DEFAULT NULL --(DC2Type:uuid)
        , quantity INTEGER NOT NULL, unit_price DOUBLE PRECISION NOT NULL, subtotal DOUBLE PRECISION NOT NULL, unit VARCHAR(10) NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_43F3EB7CDB805178 FOREIGN KEY (quote_id) REFERENCES quote (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_43F3EB7C2989F1FD FOREIGN KEY (invoice_id) REFERENCES invoice (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_43F3EB7CDB805178 ON quote_line (quote_id)');
        $this->addSql('CREATE INDEX IDX_43F3EB7C2989F1FD ON quote_line (invoice_id)');
        $this->addSql('CREATE TABLE "user" (id BLOB NOT NULL --(DC2Type:uuid)
        , company_id INTEGER DEFAULT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL, is_verified BOOLEAN NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , PRIMARY KEY(id), CONSTRAINT FK_8D93D649979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('CREATE INDEX IDX_8D93D649979B1AD6 ON "user" (company_id)');
        $this->addSql('CREATE TABLE user_information (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id BLOB NOT NULL --(DC2Type:uuid)
        , firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, CONSTRAINT FK_8062D116A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8062D116A76ED395 ON user_information (user_id)');
        $this->addSql('CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , available_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , delivered_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE company');
        $this->addSql('DROP TABLE cron_job');
        $this->addSql('DROP TABLE cron_report');
        $this->addSql('DROP TABLE invoice');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE quote');
        $this->addSql('DROP TABLE quote_line');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE user_information');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
