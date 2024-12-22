<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241220165703 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create Category entity and migrate existing activity categories';
    }

    public function up(Schema $schema): void
    {
        // Étape 1 : Créer la table category
        $this->addSql('CREATE TABLE category (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN category.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN category.updated_at IS \'(DC2Type:datetime_immutable)\'');

        // Étape 2 : Créer les catégories à partir des valeurs existantes
        $this->addSql('INSERT INTO category (name, description, created_at, updated_at) 
            SELECT DISTINCT category, \'Catégorie migrée depuis les activités\', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP 
            FROM activity');

        // Étape 3 : Ajouter la colonne category_id comme nullable
        $this->addSql('ALTER TABLE activity ADD category_id INT NULL');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095A12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_AC74095A12469DE2 ON activity (category_id)');

        // Étape 4 : Mettre à jour les références
        $this->addSql('UPDATE activity a SET category_id = (SELECT id FROM category c WHERE c.name = a.category)');

        // Étape 5 : Rendre category_id non nullable
        $this->addSql('ALTER TABLE activity ALTER COLUMN category_id SET NOT NULL');

        // Étape 6 : Supprimer l'ancienne colonne category
        $this->addSql('ALTER TABLE activity DROP category');
    }

    public function down(Schema $schema): void
    {
        // Restaurer l'ancienne colonne category
        $this->addSql('ALTER TABLE activity ADD category VARCHAR(255)');
        
        // Restaurer les anciennes valeurs
        $this->addSql('UPDATE activity a SET category = (SELECT name FROM category c WHERE c.id = a.category_id)');
        
        // Rendre la colonne non nullable
        $this->addSql('ALTER TABLE activity ALTER COLUMN category SET NOT NULL');
        
        // Supprimer la nouvelle structure
        $this->addSql('ALTER TABLE activity DROP CONSTRAINT FK_AC74095A12469DE2');
        $this->addSql('DROP INDEX IDX_AC74095A12469DE2');
        $this->addSql('ALTER TABLE activity DROP category_id');
        $this->addSql('DROP TABLE category');
    }
}
