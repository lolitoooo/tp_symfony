<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241220164742 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE activity (id SERIAL NOT NULL, organizer_id INT NOT NULL, name VARCHAR(255) NOT NULL, description TEXT NOT NULL, duration INT NOT NULL, max_participants INT NOT NULL, price DOUBLE PRECISION NOT NULL, category VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_AC74095A876C4DDA ON activity (organizer_id)');
        $this->addSql('COMMENT ON COLUMN activity.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN activity.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095A876C4DDA FOREIGN KEY (organizer_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE activity DROP CONSTRAINT FK_AC74095A876C4DDA');
        $this->addSql('DROP TABLE activity');
    }
}
