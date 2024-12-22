<?php

namespace App\DataFixtures;

use App\Entity\Activity;
use App\Entity\Category;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ActivityFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $activities = [
            [
                'name' => 'Yoga en plein air',
                'description' => 'Séance de yoga dans un cadre naturel et apaisant. Tous niveaux bienvenus.',
                'duration' => 60,
                'maxParticipants' => 15,
                'price' => 20.00,
                'category' => 'Bien-être',
            ],
            [
                'name' => 'Cours de peinture',
                'description' => 'Apprenez les techniques de base de la peinture acrylique. Matériel fourni.',
                'duration' => 120,
                'maxParticipants' => 10,
                'price' => 45.00,
                'category' => 'Culture',
            ],
            [
                'name' => 'Football en salle',
                'description' => 'Match de football en salle 5 contre 5. Ambiance conviviale.',
                'duration' => 90,
                'maxParticipants' => 12,
                'price' => 15.00,
                'category' => 'Sport',
            ],
            [
                'name' => 'Atelier cuisine italienne',
                'description' => 'Découvrez les secrets de la cuisine italienne authentique.',
                'duration' => 180,
                'maxParticipants' => 8,
                'price' => 75.00,
                'category' => 'Loisirs',
            ],
            [
                'name' => 'Cours de programmation',
                'description' => 'Initiation au développement web avec HTML, CSS et JavaScript.',
                'duration' => 240,
                'maxParticipants' => 20,
                'price' => 120.00,
                'category' => 'Formation',
            ],
        ];

        $users = $manager->getRepository(User::class)->findAll();
        $categories = $manager->getRepository(Category::class)->findAll();
        $categoriesMap = [];
        foreach ($categories as $category) {
            $categoriesMap[$category->getName()] = $category;
        }

        foreach ($activities as $activityData) {
            $activity = new Activity();
            $activity->setName($activityData['name']);
            $activity->setDescription($activityData['description']);
            $activity->setDuration($activityData['duration']);
            $activity->setMaxParticipants($activityData['maxParticipants']);
            $activity->setPrice($activityData['price']);
            $activity->setCategory($categoriesMap[$activityData['category']]);
            
            // Assigner un utilisateur aléatoire comme organisateur
            $randomUser = $users[array_rand($users)];
            $activity->setOrganizer($randomUser);

            $manager->persist($activity);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            CategoryFixtures::class,
        ];
    }
}
