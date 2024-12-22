<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[Route('/reset-password')]
class ResetPasswordController extends AbstractController
{
    #[Route('/request', name: 'app_forgot_password_request')]
    public function request(Request $request, UserRepository $userRepository, TokenGeneratorInterface $tokenGenerator, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $user = $userRepository->findOneBy(['email' => $email]);

            if ($user) {
                $token = $tokenGenerator->generateToken();
                $user->setResetToken($token);
                $user->setResetTokenExpiresAt(new \DateTime('+1 hour'));
                $entityManager->flush();

                $resetUrl = $this->generateUrl('app_reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

                $email = (new Email())
                    ->from('noreply@eventmanager.com')
                    ->to($user->getEmail())
                    ->subject('Réinitialisation de votre mot de passe')
                    ->html(
                        $this->renderView('reset_password/email.html.twig', [
                            'resetUrl' => $resetUrl,
                            'user' => $user,
                        ])
                    );

                $mailer->send($email);

                $this->addFlash('success', 'Un email de réinitialisation vous a été envoyé.');
                return $this->redirectToRoute('app_login');
            }

            $this->addFlash('error', 'Cette adresse email n\'existe pas.');
        }

        return $this->render('reset_password/request.html.twig');
    }

    #[Route('/reset/{token}', name: 'app_reset_password')]
    public function reset(string $token, Request $request, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = $userRepository->findOneBy(['resetToken' => $token]);

        if (!$user || !$user->isResetTokenValid()) {
            $this->addFlash('error', 'Le lien de réinitialisation est invalide ou a expiré.');
            return $this->redirectToRoute('app_forgot_password_request');
        }

        if ($request->isMethod('POST')) {
            $password = $request->request->get('password');
            $confirmPassword = $request->request->get('confirm_password');

            if ($password === $confirmPassword) {
                $user->setPassword($passwordHasher->hashPassword($user, $password));
                $user->setResetToken(null);
                $user->setResetTokenExpiresAt(null);
                $user->setUpdateAt(new \DateTime());
                $entityManager->flush();

                $this->addFlash('success', 'Votre mot de passe a été réinitialisé avec succès.');
                return $this->redirectToRoute('app_login');
            }

            $this->addFlash('error', 'Les mots de passe ne correspondent pas.');
        }

        return $this->render('reset_password/reset.html.twig', [
            'token' => $token
        ]);
    }
}
