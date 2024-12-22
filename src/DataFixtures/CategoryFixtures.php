<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categories = [
            [
                'name' => 'Sport',
                'description' => 'Activités sportives et de bien-être pour tous les niveaux',
            ],
            [
                'name' => 'Culture',
                'description' => 'Activités culturelles, artistiques et créatives',
            ],
            [
                'name' => 'Loisirs',
                'description' => 'Activités récréatives et de divertissement',
            ],
            [
                'name' => 'Bien-être',
                'description' => 'Activités de relaxation et de développement personnel',
            ],
            [
                'name' => 'Formation',
                'description' => 'Ateliers et cours pour développer de nouvelles compétences',
            ],
            [
                'name' => 'Autre',
                'description' => 'Autres types d\'activités',
            ],
        ];

        foreach ($categories as $categoryData) {
            $category = new Category();
            $category->setName($categoryData['name']);
            $category->setDescription($categoryData['description']);

            $manager->persist($category);
        }

        $manager->flush();
    }
}
