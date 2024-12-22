<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $users = [
            [
                'email' => 'admin@example.com',
                'roles' => ['ROLE_ADMIN', 'ROLE_USER'],
                'password' => 'admin123',
                'name' => 'Admin',
                'lastname' => 'System',
            ],
            [
                'email' => 'john.doe@example.com',
                'roles' => ['ROLE_USER'],
                'password' => 'user123',
                'name' => 'John',
                'lastname' => 'Doe',
            ],
            [
                'email' => 'jane.smith@example.com',
                'roles' => ['ROLE_USER'],
                'password' => 'user123',
                'name' => 'Jane',
                'lastname' => 'Smith',
            ],
            [
                'email' => 'pierre.durand@example.com',
                'roles' => ['ROLE_USER'],
                'password' => 'user123',
                'name' => 'Pierre',
                'lastname' => 'Durand',
            ],
            [
                'email' => 'marie.martin@example.com',
                'roles' => ['ROLE_USER'],
                'password' => 'user123',
                'name' => 'Marie',
                'lastname' => 'Martin',
            ],
            [
                'email' => 'banned.user@example.com',
                'roles' => ['ROLE_BANNED'],
                'password' => 'user123',
                'name' => 'Utilisateur',
                'lastname' => 'Banni',
            ],
        ];

        foreach ($users as $userData) {
            $user = new User();
            $user->setEmail($userData['email']);
            $user->setRoles($userData['roles']);
            $user->setName($userData['name']);
            $user->setLastname($userData['lastname']);

            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $userData['password']
            );
            $user->setPassword($hashedPassword);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
