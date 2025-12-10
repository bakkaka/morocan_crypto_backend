<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251210214702 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ad (id SERIAL NOT NULL, currency_id INT DEFAULT NULL, user_id INT DEFAULT NULL, type VARCHAR(10) NOT NULL, amount DOUBLE PRECISION NOT NULL, price DOUBLE PRECISION NOT NULL, payment_method VARCHAR(255) NOT NULL, status VARCHAR(20) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, min_amount_per_transaction DOUBLE PRECISION DEFAULT NULL, max_amount_per_transaction DOUBLE PRECISION DEFAULT NULL, time_limit_minutes INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_77E0ED5838248176 ON ad (currency_id)');
        $this->addSql('CREATE INDEX IDX_77E0ED58A76ED395 ON ad (user_id)');
        $this->addSql('COMMENT ON COLUMN ad.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN ad.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE ad_payment_method (ad_id INT NOT NULL, payment_method_id INT NOT NULL, PRIMARY KEY(ad_id, payment_method_id))');
        $this->addSql('CREATE INDEX IDX_A017536E4F34D596 ON ad_payment_method (ad_id)');
        $this->addSql('CREATE INDEX IDX_A017536E5AA1164F ON ad_payment_method (payment_method_id)');
        $this->addSql('CREATE TABLE ad_accepted_bank_details (ad_id INT NOT NULL, user_bank_detail_id INT NOT NULL, PRIMARY KEY(ad_id, user_bank_detail_id))');
        $this->addSql('CREATE INDEX IDX_77BD00CF4F34D596 ON ad_accepted_bank_details (ad_id)');
        $this->addSql('CREATE INDEX IDX_77BD00CFC0B88A7B ON ad_accepted_bank_details (user_bank_detail_id)');
        $this->addSql('CREATE TABLE chat_message (id SERIAL NOT NULL, transaction_id INT DEFAULT NULL, sender_id INT DEFAULT NULL, message TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FAB3FC162FC0CB0F ON chat_message (transaction_id)');
        $this->addSql('CREATE INDEX IDX_FAB3FC16F624B39D ON chat_message (sender_id)');
        $this->addSql('COMMENT ON COLUMN chat_message.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE currency (id SERIAL NOT NULL, code VARCHAR(10) NOT NULL, name VARCHAR(50) NOT NULL, type VARCHAR(10) NOT NULL, decimals INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6956883F77153098 ON currency (code)');
        $this->addSql('CREATE TABLE dispute (id SERIAL NOT NULL, transaction_id INT DEFAULT NULL, opened_by_id INT DEFAULT NULL, reason TEXT NOT NULL, status VARCHAR(20) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3C9250072FC0CB0F ON dispute (transaction_id)');
        $this->addSql('CREATE INDEX IDX_3C925007AB159F5 ON dispute (opened_by_id)');
        $this->addSql('COMMENT ON COLUMN dispute.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE payment_method (id SERIAL NOT NULL, user_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, details VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7B61A1F6A76ED395 ON payment_method (user_id)');
        $this->addSql('CREATE TABLE transaction (id SERIAL NOT NULL, ad_id INT DEFAULT NULL, buyer_id INT DEFAULT NULL, seller_id INT DEFAULT NULL, usdt_amount DOUBLE PRECISION NOT NULL, fiat_amount DOUBLE PRECISION NOT NULL, status VARCHAR(20) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, paid_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, released_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, payment_reference VARCHAR(100) DEFAULT NULL, payment_proof_image VARCHAR(255) DEFAULT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_723705D14F34D596 ON transaction (ad_id)');
        $this->addSql('CREATE INDEX IDX_723705D16C755722 ON transaction (buyer_id)');
        $this->addSql('CREATE INDEX IDX_723705D18DE820D9 ON transaction (seller_id)');
        $this->addSql('COMMENT ON COLUMN transaction.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN transaction.paid_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN transaction.released_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN transaction.expires_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE "user" (id SERIAL NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, full_name VARCHAR(255) NOT NULL, phone VARCHAR(20) NOT NULL, is_verified BOOLEAN NOT NULL, reputation DOUBLE PRECISION NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, roles JSON NOT NULL, wallet_address VARCHAR(255) DEFAULT NULL, login_attempts INT NOT NULL, locked_until TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_active BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649444F97DD ON "user" (phone)');
        $this->addSql('COMMENT ON COLUMN "user".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "user".updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE user_bank_detail (id SERIAL NOT NULL, user_id INT NOT NULL, bank_name VARCHAR(50) NOT NULL, account_holder VARCHAR(100) NOT NULL, account_number VARCHAR(50) NOT NULL, swift_code VARCHAR(100) DEFAULT NULL, branch_name VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_active BOOLEAN NOT NULL, account_type VARCHAR(20) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A778997BA76ED395 ON user_bank_detail (user_id)');
        $this->addSql('COMMENT ON COLUMN user_bank_detail.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN user_bank_detail.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE wallet_movement (id SERIAL NOT NULL, user_id INT DEFAULT NULL, transaction_id INT DEFAULT NULL, amount DOUBLE PRECISION NOT NULL, type VARCHAR(10) NOT NULL, description VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_782183A76ED395 ON wallet_movement (user_id)');
        $this->addSql('CREATE INDEX IDX_7821832FC0CB0F ON wallet_movement (transaction_id)');
        $this->addSql('COMMENT ON COLUMN wallet_movement.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE ad ADD CONSTRAINT FK_77E0ED5838248176 FOREIGN KEY (currency_id) REFERENCES currency (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ad ADD CONSTRAINT FK_77E0ED58A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ad_payment_method ADD CONSTRAINT FK_A017536E4F34D596 FOREIGN KEY (ad_id) REFERENCES ad (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ad_payment_method ADD CONSTRAINT FK_A017536E5AA1164F FOREIGN KEY (payment_method_id) REFERENCES payment_method (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ad_accepted_bank_details ADD CONSTRAINT FK_77BD00CF4F34D596 FOREIGN KEY (ad_id) REFERENCES ad (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ad_accepted_bank_details ADD CONSTRAINT FK_77BD00CFC0B88A7B FOREIGN KEY (user_bank_detail_id) REFERENCES user_bank_detail (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE chat_message ADD CONSTRAINT FK_FAB3FC162FC0CB0F FOREIGN KEY (transaction_id) REFERENCES transaction (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE chat_message ADD CONSTRAINT FK_FAB3FC16F624B39D FOREIGN KEY (sender_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE dispute ADD CONSTRAINT FK_3C9250072FC0CB0F FOREIGN KEY (transaction_id) REFERENCES transaction (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE dispute ADD CONSTRAINT FK_3C925007AB159F5 FOREIGN KEY (opened_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payment_method ADD CONSTRAINT FK_7B61A1F6A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D14F34D596 FOREIGN KEY (ad_id) REFERENCES ad (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D16C755722 FOREIGN KEY (buyer_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D18DE820D9 FOREIGN KEY (seller_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_bank_detail ADD CONSTRAINT FK_A778997BA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE wallet_movement ADD CONSTRAINT FK_782183A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE wallet_movement ADD CONSTRAINT FK_7821832FC0CB0F FOREIGN KEY (transaction_id) REFERENCES transaction (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE ad DROP CONSTRAINT FK_77E0ED5838248176');
        $this->addSql('ALTER TABLE ad DROP CONSTRAINT FK_77E0ED58A76ED395');
        $this->addSql('ALTER TABLE ad_payment_method DROP CONSTRAINT FK_A017536E4F34D596');
        $this->addSql('ALTER TABLE ad_payment_method DROP CONSTRAINT FK_A017536E5AA1164F');
        $this->addSql('ALTER TABLE ad_accepted_bank_details DROP CONSTRAINT FK_77BD00CF4F34D596');
        $this->addSql('ALTER TABLE ad_accepted_bank_details DROP CONSTRAINT FK_77BD00CFC0B88A7B');
        $this->addSql('ALTER TABLE chat_message DROP CONSTRAINT FK_FAB3FC162FC0CB0F');
        $this->addSql('ALTER TABLE chat_message DROP CONSTRAINT FK_FAB3FC16F624B39D');
        $this->addSql('ALTER TABLE dispute DROP CONSTRAINT FK_3C9250072FC0CB0F');
        $this->addSql('ALTER TABLE dispute DROP CONSTRAINT FK_3C925007AB159F5');
        $this->addSql('ALTER TABLE payment_method DROP CONSTRAINT FK_7B61A1F6A76ED395');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT FK_723705D14F34D596');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT FK_723705D16C755722');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT FK_723705D18DE820D9');
        $this->addSql('ALTER TABLE user_bank_detail DROP CONSTRAINT FK_A778997BA76ED395');
        $this->addSql('ALTER TABLE wallet_movement DROP CONSTRAINT FK_782183A76ED395');
        $this->addSql('ALTER TABLE wallet_movement DROP CONSTRAINT FK_7821832FC0CB0F');
        $this->addSql('DROP TABLE ad');
        $this->addSql('DROP TABLE ad_payment_method');
        $this->addSql('DROP TABLE ad_accepted_bank_details');
        $this->addSql('DROP TABLE chat_message');
        $this->addSql('DROP TABLE currency');
        $this->addSql('DROP TABLE dispute');
        $this->addSql('DROP TABLE payment_method');
        $this->addSql('DROP TABLE transaction');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE user_bank_detail');
        $this->addSql('DROP TABLE wallet_movement');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
