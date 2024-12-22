<?php

namespace App\DataFixtures;

use App\Entity\Event;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class EventFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $events = [
            [
                'name' => 'Concert de Jazz',
                'description' => 'Une soirée exceptionnelle avec les meilleurs artistes de jazz de la région. Au programme : standards de jazz, improvisations et découvertes musicales.',
                'startDate' => new \DateTime('+2 weeks'),
                'endDate' => new \DateTime('+2 weeks +3 hours'),
                'location' => 'Le Petit Journal Montparnasse, Paris',
            ],
            [
                'name' => 'Exposition d\'Art Moderne',
                'description' => 'Découvrez les œuvres contemporaines d\'artistes locaux et internationaux. Une exposition qui mêle peinture, sculpture et installations numériques.',
                'startDate' => new \DateTime('+1 month'),
                'endDate' => new \DateTime('+1 month +1 week'),
                'location' => 'Galerie d\'Art Contemporain, Lyon',
            ],
            [
                'name' => 'Marathon de Paris',
                'description' => 'Le plus grand événement sportif de l\'année. Parcourez 42,195 km à travers les rues de Paris et ses monuments emblématiques.',
                'startDate' => new \DateTime('+3 months'),
                'endDate' => new \DateTime('+3 months +8 hours'),
                'location' => 'Champs-Élysées, Paris',
            ],
            [
                'name' => 'Festival de Gastronomie',
                'description' => 'Un week-end dédié aux saveurs du monde. Dégustations, ateliers de cuisine et rencontres avec des chefs étoilés.',
                'startDate' => new \DateTime('+1 week'),
                'endDate' => new \DateTime('+1 week +2 days'),
                'location' => 'Parc des Expositions, Bordeaux',
            ],
            [
                'name' => 'Conférence Tech Innovation',
                'description' => 'Les dernières tendances en matière d\'intelligence artificielle et de développement durable. Interventions d\'experts et démonstrations.',
                'startDate' => new \DateTime('+2 months'),
                'endDate' => new \DateTime('+2 months +2 days'),
                'location' => 'Centre de Congrès, Marseille',
            ],
            [
                'name' => 'Théâtre : "Le Misanthrope"',
                'description' => 'Une nouvelle mise en scène moderne du classique de Molière. Une production qui mêle tradition et modernité.',
                'startDate' => new \DateTime('+3 weeks'),
                'endDate' => new \DateTime('+3 weeks +3 hours'),
                'location' => 'Théâtre National, Strasbourg',
            ],
            [
                'name' => 'Salon du Livre',
                'description' => 'Le rendez-vous annuel des amoureux de la littérature. Rencontres avec les auteurs, dédicaces et conférences.',
                'startDate' => new \DateTime('+45 days'),
                'endDate' => new \DateTime('+48 days'),
                'location' => 'Grande Halle de la Villette, Paris',
            ],
            [
                'name' => 'Festival de Musique Électronique',
                'description' => 'Trois jours de musique non-stop avec les meilleurs DJs internationaux. Une expérience sonore et visuelle unique.',
                'startDate' => new \DateTime('+4 months'),
                'endDate' => new \DateTime('+4 months +3 days'),
                'location' => 'Parc des Expositions, Montpellier',
            ],
            [
                'name' => 'Atelier de Photographie',
                'description' => 'Apprenez les techniques de la photographie avec des professionnels. Théorie et pratique sur le terrain.',
                'startDate' => new \DateTime('+5 days'),
                'endDate' => new \DateTime('+5 days +6 hours'),
                'location' => 'École des Beaux-Arts, Nantes',
            ],
            [
                'name' => 'Spectacle de Cirque Moderne',
                'description' => 'Un spectacle qui repousse les limites de l\'imagination. Acrobaties, jonglage et performances artistiques époustouflantes.',
                'startDate' => new \DateTime('+2 months +15 days'),
                'endDate' => new \DateTime('+2 months +15 days +2 hours'),
                'location' => 'Zénith, Toulouse',
            ],
        ];

        $users = $manager->getRepository(User::class)->findAll();

        foreach ($events as $eventData) {
            $event = new Event();
            $event->setTitle($eventData['name']);
            $event->setDescription($eventData['description']);
            $event->setStartDate($eventData['startDate']);
            $event->setEndDate($eventData['endDate']);
            $event->setLocation($eventData['location']);
            
            // Assigner un utilisateur aléatoire comme organisateur
            $randomUser = $users[array_rand($users)];
            $event->setOrganizer($randomUser);

            $manager->persist($event);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
