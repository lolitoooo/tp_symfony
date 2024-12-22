<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241220165613 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create Category entity and update Activity to use Category relation';
    }

    public function up(Schema $schema): void
    {
        // Create category table
        $this->addSql('CREATE TABLE category (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN category.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN category.updated_at IS \'(DC2Type:datetime_immutable)\'');

        // Create default category
        $this->addSql('INSERT INTO category (name, description, created_at, updated_at) VALUES (\'Autre\', \'Catégorie par défaut\', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)');
        
        // Add category_id column as nullable first
        $this->addSql('ALTER TABLE activity ADD category_id INT NULL');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095A12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_AC74095A12469DE2 ON activity (category_id)');

        // Update existing activities to use the default category
        $this->addSql('UPDATE activity SET category_id = (SELECT id FROM category WHERE name = \'Autre\' LIMIT 1)');

        // Now make category_id not nullable
        $this->addSql('ALTER TABLE activity ALTER COLUMN category_id SET NOT NULL');
        
        // Finally, drop the old category column
        $this->addSql('ALTER TABLE activity DROP category');
    }

    public function down(Schema $schema): void
    {
        // Add back the category column
        $this->addSql('ALTER TABLE activity ADD category VARCHAR(255) NULL');
        
        // Copy category names back
        $this->addSql('UPDATE activity a SET category = (SELECT name FROM category c WHERE c.id = a.category_id)');
        
        // Make category not nullable
        $this->addSql('ALTER TABLE activity ALTER COLUMN category SET NOT NULL');
        
        // Remove the foreign key and index
        $this->addSql('ALTER TABLE activity DROP CONSTRAINT FK_AC74095A12469DE2');
        $this->addSql('DROP INDEX IDX_AC74095A12469DE2');
        
        // Remove category_id column
        $this->addSql('ALTER TABLE activity DROP category_id');
        
        // Drop category table
        $this->addSql('DROP TABLE category');
    }
}
