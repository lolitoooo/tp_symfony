<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\Payment;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class PaymentController extends AbstractController
{
    #[Route('/payment/create-session/{id}', name: 'payment_create_session')]
    public function createSession(Activity $activity, Request $request): Response
    {
        // Initialisez Stripe avec votre clé secrète
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        $checkout_session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $activity->getPrice() * 100, // Le montant doit être en centimes
                    'product_data' => [
                        'name' => $activity->getName(),
                        'description' => $activity->getDescription(),
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $request->getSchemeAndHttpHost() . $this->generateUrl('payment_success', [
                'id' => $activity->getId(),
                'session_id' => '{CHECKOUT_SESSION_ID}'
            ]),
            'cancel_url' => $request->getSchemeAndHttpHost() . $this->generateUrl('payment_cancel', ['id' => $activity->getId()]),
        ]);

        return $this->redirect($checkout_session->url);
    }

    #[Route('/payment/success/{id}', name: 'payment_success')]
    public function success(Activity $activity, Request $request, EntityManagerInterface $entityManager): Response
    {
        $session_id = $request->query->get('session_id');
        
        if (!$session_id) {
            throw $this->createNotFoundException('Session ID not found.');
        }

        // Vérifier le statut du paiement avec Stripe
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
        $session = Session::retrieve($session_id);

        if ($session->payment_status === 'paid') {
            // Créer un nouvel enregistrement de paiement
            $payment = new Payment();
            $payment->setUser($this->getUser());
            $payment->setActivity($activity);
            $payment->setAmount($activity->getPrice());
            $payment->setStatus('completed');
            $payment->setStripePaymentId($session_id);

            $entityManager->persist($payment);
            $entityManager->flush();

            $this->addFlash('success', 'Paiement effectué avec succès !');
        } else {
            $this->addFlash('error', 'Le paiement n\'a pas pu être validé.');
        }

        return $this->redirectToRoute('app_activity_show', ['id' => $activity->getId()]);
    }

    #[Route('/payment/cancel/{id}', name: 'payment_cancel')]
    public function cancel(Activity $activity): Response
    {
        $this->addFlash('error', 'Le paiement a été annulé.');
        return $this->redirectToRoute('app_activity_show', ['id' => $activity->getId()]);
    }
}
