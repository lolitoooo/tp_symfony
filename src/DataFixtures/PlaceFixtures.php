<?php

namespace App\DataFixtures;

use App\Entity\Place;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PlaceFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $places = [
            [
                'name' => 'Palais des Congrès',
                'description' => 'Un lieu prestigieux pour vos événements professionnels et culturels. Capacité d\'accueil importante et équipements modernes.',
                'address' => '2 Place de la Porte Maillot',
                'city' => 'Paris',
                'zipCode' => '75017',
                'country' => 'France',
            ],
            [
                'name' => 'Le Petit Théâtre',
                'description' => 'Une salle intimiste parfaite pour les représentations théâtrales et les petits concerts. Charme authentique.',
                'address' => '15 Rue des Arts',
                'city' => 'Lyon',
                'zipCode' => '69001',
                'country' => 'France',
            ],
            [
                'name' => 'Espace Culturel du Port',
                'description' => 'Centre culturel moderne avec plusieurs salles modulables. Vue imprenable sur le port.',
                'address' => '45 Quai des Belges',
                'city' => 'Marseille',
                'zipCode' => '13001',
                'country' => 'France',
            ],
            [
                'name' => 'La Grande Halle',
                'description' => 'Ancien bâtiment industriel reconverti en espace événementiel. Parfait pour les expositions et grands événements.',
                'address' => '8 Avenue de l\'Innovation',
                'city' => 'Bordeaux',
                'zipCode' => '33000',
                'country' => 'France',
            ],
            [
                'name' => 'Le Jardin des Arts',
                'description' => 'Espace extérieur aménagé pour les événements en plein air. Jardins paysagers et scène couverte.',
                'address' => '23 Route des Fleurs',
                'city' => 'Nantes',
                'zipCode' => '44000',
                'country' => 'France',
            ],
        ];

        $users = $manager->getRepository(User::class)->findAll();

        foreach ($places as $placeData) {
            $place = new Place();
            $place->setName($placeData['name']);
            $place->setDescription($placeData['description']);
            $place->setAddress($placeData['address']);
            $place->setCity($placeData['city']);
            $place->setZipCode($placeData['zipCode']);
            $place->setCountry($placeData['country']);
            
            // Assigner un utilisateur aléatoire comme propriétaire
            $randomUser = $users[array_rand($users)];
            $place->setOwner($randomUser);

            $manager->persist($place);
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
