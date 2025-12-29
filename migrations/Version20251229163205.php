<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251229163205 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ad ADD approved_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ad ADD approved_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE ad ADD published_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE ad ADD admin_notes TEXT DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN ad.approved_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN ad.published_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE ad ADD CONSTRAINT FK_77E0ED582D234F6A FOREIGN KEY (approved_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_77E0ED582D234F6A ON ad (approved_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE ad DROP CONSTRAINT FK_77E0ED582D234F6A');
        $this->addSql('DROP INDEX IDX_77E0ED582D234F6A');
        $this->addSql('ALTER TABLE ad DROP approved_by_id');
        $this->addSql('ALTER TABLE ad DROP approved_at');
        $this->addSql('ALTER TABLE ad DROP published_at');
        $this->addSql('ALTER TABLE ad DROP admin_notes');
    }
}
