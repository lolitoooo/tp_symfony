<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Event;
use App\Entity\Activity;
use App\Entity\Place;
use App\Entity\Category;
use App\Entity\Payment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/users', name: 'admin_users')]
    public function users(EntityManagerInterface $entityManager): Response
    {
        $users = $entityManager->getRepository(User::class)->findAll();
        
        return $this->render('admin/users.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/users/{id}/edit', name: 'admin_users_edit', methods: ['POST'])]
    public function editUser(
        Request $request,
        User $user,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        if ($this->isCsrfTokenValid('edit'.$user->getId(), $request->request->get('_token'))) {
            $user->setName($request->request->get('name'));
            $user->setLastname($request->request->get('lastname'));
            $user->setEmail($request->request->get('email'));
            
            if ($password = $request->request->get('password')) {
                $hashedPassword = $passwordHasher->hashPassword($user, $password);
                $user->setPassword($hashedPassword);
            }
            
            // Gestion des rôles
            $roles = $request->request->all()['roles'] ?? ['ROLE_USER'];
            if (in_array('ROLE_ADMIN', $roles)) {
                $roles[] = 'ROLE_USER';
            }
            $user->setRoles(array_unique($roles));
            
            $entityManager->flush();
            
            $this->addFlash('success', 'Utilisateur modifié avec succès.');
        }
        
        return $this->redirectToRoute('admin_users');
    }

    #[Route('/users/{id}/delete', name: 'admin_users_delete', methods: ['POST'])]
    public function deleteUser(
        Request $request,
        User $user,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            if ($user === $this->getUser()) {
                $this->addFlash('error', 'Vous ne pouvez pas supprimer votre propre compte.');
                return $this->redirectToRoute('admin_users');
            }
            
            $entityManager->remove($user);
            $entityManager->flush();
            
            $this->addFlash('success', 'Utilisateur supprimé avec succès.');
        }
        
        return $this->redirectToRoute('admin_users');
    }

    #[Route('/users/new', name: 'admin_users_new', methods: ['POST'])]
    public function newUser(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        if ($this->isCsrfTokenValid('add_user', $request->request->get('_token'))) {
            $user = new User();
            $user->setName($request->request->get('name'));
            $user->setLastname($request->request->get('lastname'));
            $user->setEmail($request->request->get('email'));
            
            $hashedPassword = $passwordHasher->hashPassword($user, $request->request->get('password'));
            $user->setPassword($hashedPassword);
            
            // Gestion des rôles
            $roles = $request->request->all()['roles'] ?? ['ROLE_USER'];
            if (in_array('ROLE_ADMIN', $roles)) {
                $roles[] = 'ROLE_USER';
            }
            $user->setRoles(array_unique($roles));
            
            $entityManager->persist($user);
            $entityManager->flush();
            
            $this->addFlash('success', 'Utilisateur créé avec succès.');
        }
        
        return $this->redirectToRoute('admin_users');
    }

    #[Route('/events', name: 'admin_events')]
    public function events(EntityManagerInterface $entityManager): Response
    {
        $events = $entityManager->getRepository(Event::class)->findAll();
        
        return $this->render('admin/events.html.twig', [
            'events' => $events
        ]);
    }

    #[Route('/events/{id}/edit', name: 'admin_events_edit', methods: ['POST'])]
    public function editEvent(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('edit'.$event->getId(), $request->request->get('_token'))) {
            $event->setTitle($request->request->get('title'));
            $event->setDescription($request->request->get('description'));
            $event->setStartDate(new \DateTime($request->request->get('startDate')));
            $event->setEndDate(new \DateTime($request->request->get('endDate')));
            $event->setLocation($request->request->get('location'));
            $event->setUpdatedAt(new \DateTimeImmutable());
            
            $entityManager->flush();
            $this->addFlash('success', 'Événement modifié avec succès.');
        }
        
        return $this->redirectToRoute('admin_events');
    }

    #[Route('/events/{id}/delete', name: 'admin_events_delete', methods: ['POST'])]
    public function deleteEvent(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
            $entityManager->remove($event);
            $entityManager->flush();
            $this->addFlash('success', 'Événement supprimé avec succès.');
        }
        
        return $this->redirectToRoute('admin_events');
    }

    #[Route('/events/new', name: 'admin_events_new', methods: ['POST'])]
    public function newEvent(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('add_event', $request->request->get('_token'))) {
            $event = new Event();
            $event->setTitle($request->request->get('title'));
            $event->setDescription($request->request->get('description'));
            $event->setStartDate(new \DateTime($request->request->get('startDate')));
            $event->setEndDate(new \DateTime($request->request->get('endDate')));
            $event->setLocation($request->request->get('location'));
            $event->setOrganizer($this->getUser());
            
            $entityManager->persist($event);
            $entityManager->flush();
            $this->addFlash('success', 'Événement créé avec succès.');
        }
        
        return $this->redirectToRoute('admin_events');
    }

    #[Route('/places', name: 'admin_places')]
    public function places(EntityManagerInterface $entityManager): Response
    {
        $places = $entityManager->getRepository(Place::class)->findAll();
        return $this->render('admin/places.html.twig', [
            'places' => $places
        ]);
    }

    #[Route('/places/{id}/edit', name: 'admin_places_edit', methods: ['POST'])]
    public function editPlace(Request $request, Place $place, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('edit'.$place->getId(), $request->request->get('_token'))) {
            $place->setName($request->request->get('name'));
            $place->setDescription($request->request->get('description'));
            $place->setAddress($request->request->get('address'));
            $place->setCity($request->request->get('city'));
            $place->setZipCode($request->request->get('zipCode'));
            $place->setCountry($request->request->get('country'));
            $place->setUpdatedAt(new \DateTimeImmutable());
            
            $entityManager->flush();
            $this->addFlash('success', 'Lieu modifié avec succès.');
        }
        
        return $this->redirectToRoute('admin_places');
    }

    #[Route('/places/{id}/delete', name: 'admin_places_delete', methods: ['POST'])]
    public function deletePlace(Request $request, Place $place, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$place->getId(), $request->request->get('_token'))) {
            $entityManager->remove($place);
            $entityManager->flush();
            $this->addFlash('success', 'Lieu supprimé avec succès.');
        }
        
        return $this->redirectToRoute('admin_places');
    }

    #[Route('/places/new', name: 'admin_places_new', methods: ['POST'])]
    public function newPlace(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('add_place', $request->request->get('_token'))) {
            $place = new Place();
            $place->setName($request->request->get('name'));
            $place->setDescription($request->request->get('description'));
            $place->setAddress($request->request->get('address'));
            $place->setCity($request->request->get('city'));
            $place->setZipCode($request->request->get('zipCode'));
            $place->setCountry($request->request->get('country'));
            $place->setOwner($this->getUser());
            
            $entityManager->persist($place);
            $entityManager->flush();
            $this->addFlash('success', 'Lieu créé avec succès.');
        }
        
        return $this->redirectToRoute('admin_places');
    }

    #[Route('/activities', name: 'admin_activities')]
    public function activities(EntityManagerInterface $entityManager): Response
    {
        $activities = $entityManager->getRepository(Activity::class)->findAll();
        $categories = $entityManager->getRepository(Category::class)->findAll();
        
        return $this->render('admin/activities.html.twig', [
            'activities' => $activities,
            'categories' => $categories
        ]);
    }

    #[Route('/activities/{id}/edit', name: 'admin_activities_edit', methods: ['POST'])]
    public function editActivity(Request $request, Activity $activity, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('edit'.$activity->getId(), $request->request->get('_token'))) {
            $activity->setName($request->request->get('name'));
            $activity->setDescription($request->request->get('description'));
            $activity->setDuration((int)$request->request->get('duration'));
            $activity->setMaxParticipants((int)$request->request->get('maxParticipants'));
            $activity->setPrice((float)$request->request->get('price'));
            
            $category = $entityManager->getRepository(Category::class)->find($request->request->get('category_id'));
            if ($category) {
                $activity->setCategory($category);
            }
            
            $activity->setUpdatedAt(new \DateTimeImmutable());
            
            $entityManager->flush();
            $this->addFlash('success', 'Activité modifiée avec succès.');
        }
        
        return $this->redirectToRoute('admin_activities');
    }

    #[Route('/activities/{id}/delete', name: 'admin_activities_delete', methods: ['POST'])]
    public function deleteActivity(Request $request, Activity $activity, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$activity->getId(), $request->request->get('_token'))) {
            $entityManager->remove($activity);
            $entityManager->flush();
            $this->addFlash('success', 'Activité supprimée avec succès.');
        }
        
        return $this->redirectToRoute('admin_activities');
    }

    #[Route('/activities/new', name: 'admin_activities_new', methods: ['POST'])]
    public function newActivity(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('add_activity', $request->request->get('_token'))) {
            $activity = new Activity();
            $activity->setName($request->request->get('name'));
            $activity->setDescription($request->request->get('description'));
            $activity->setDuration((int)$request->request->get('duration'));
            $activity->setMaxParticipants((int)$request->request->get('maxParticipants'));
            $activity->setPrice((float)$request->request->get('price'));
            $activity->setOrganizer($this->getUser());
            
            $category = $entityManager->getRepository(Category::class)->find($request->request->get('category_id'));
            if ($category) {
                $activity->setCategory($category);
            }
            
            $entityManager->persist($activity);
            $entityManager->flush();
            $this->addFlash('success', 'Activité créée avec succès.');
        }
        
        return $this->redirectToRoute('admin_activities');
    }

    #[Route('/categories', name: 'admin_categories')]
    public function categories(EntityManagerInterface $entityManager): Response
    {
        $categories = $entityManager->getRepository(Category::class)->findAll();
        
        return $this->render('admin/categories.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/categories/{id}/edit', name: 'admin_categories_edit', methods: ['POST'])]
    public function editCategory(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('edit'.$category->getId(), $request->request->get('_token'))) {
            $category->setName($request->request->get('name'));
            $category->setDescription($request->request->get('description'));
            
            $entityManager->flush();
            $this->addFlash('success', 'Catégorie modifiée avec succès.');
        }
        
        return $this->redirectToRoute('admin_categories');
    }

    #[Route('/categories/{id}/delete', name: 'admin_categories_delete', methods: ['POST'])]
    public function deleteCategory(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $entityManager->remove($category);
            $entityManager->flush();
            $this->addFlash('success', 'Catégorie supprimée avec succès.');
        }
        
        return $this->redirectToRoute('admin_categories');
    }

    #[Route('/categories/new', name: 'admin_categories_new', methods: ['POST'])]
    public function newCategory(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('add_category', $request->request->get('_token'))) {
            $category = new Category();
            $category->setName($request->request->get('name'));
            $category->setDescription($request->request->get('description'));
            
            $entityManager->persist($category);
            $entityManager->flush();
            $this->addFlash('success', 'Catégorie créée avec succès.');
        }
        
        return $this->redirectToRoute('admin_categories');
    }

    #[Route('/payments', name: 'admin_payments')]
    public function payments(EntityManagerInterface $entityManager): Response
    {
        $payments = $entityManager->getRepository(Payment::class)->findAll();
        
        return $this->render('admin/payments.html.twig', [
            'payments' => $payments
        ]);
    }
}
