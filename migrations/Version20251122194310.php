<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251122194310 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE currency CHANGE type type VARCHAR(10) NOT NULL');
        $this->addSql('ALTER TABLE user ADD login_attempts INT NOT NULL, ADD locked_until DATETIME DEFAULT NULL, ADD is_active TINYINT(1) NOT NULL, CHANGE reputation reputation DOUBLE PRECISION NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE currency CHANGE type type VARCHAR(10) DEFAULT \'crypto\'');
        $this->addSql('ALTER TABLE `user` DROP login_attempts, DROP locked_until, DROP is_active, CHANGE reputation reputation DOUBLE PRECISION DEFAULT NULL');
    }
}
