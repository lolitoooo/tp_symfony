<?php

namespace App\Controller;

use App\Form\SearchType;
use App\Repository\EventRepository;
use App\Repository\ActivityRepository;
use App\Repository\PlaceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search')]
    public function index(
        Request $request,
        EventRepository $eventRepository,
        ActivityRepository $activityRepository,
        PlaceRepository $placeRepository
    ): Response {
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        $results = [
            'events' => [],
            'activities' => [],
            'places' => []
        ];

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $query = $data['query'];
            $type = $data['type'];

            if ($type === 'all' || $type === 'event') {
                $results['events'] = $eventRepository->search($query);
            }
            
            if ($type === 'all' || $type === 'activity') {
                $results['activities'] = $activityRepository->search($query);
            }
            
            if ($type === 'all' || $type === 'place') {
                $results['places'] = $placeRepository->search($query);
            }
        }

        return $this->render('search/index.html.twig', [
            'form' => $form->createView(),
            'results' => $results,
            'submitted' => $form->isSubmitted() && $form->isValid()
        ]);
    }
}
