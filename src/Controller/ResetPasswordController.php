<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\ResetPassword;
use App\Entity\User;
use App\Form\ResetPasswordType;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Psr\Log\LoggerInterface;

class ResetPasswordController extends AbstractController
{
    private $entityManager;
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    #[Route('/forgot-password', name: 'reset_password')]
    public function index(Request $request, Mail $mail)
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_maison_index');
        }

        if ($request->request->get('email')) {

            // ✅ Chercher par EMAIL et non par username
            $user = $this->entityManager
                ->getRepository(User::class)
                ->findOneBy(['email' => $request->request->get('email')]);

            if ($user) {
                // Supprimer les anciens tokens de cet utilisateur
                $oldTokens = $this->entityManager
                    ->getRepository(ResetPassword::class)
                    ->findBy(['user' => $user]);
                foreach ($oldTokens as $old) {
                    $this->entityManager->remove($old);
                }
                $this->entityManager->flush();

                // Créer un nouveau token
                $resetPassword = new ResetPassword();
                $resetPassword->setUser($user)
                    ->setToken(uniqid())
                    ->setCreatedAt(new DateTimeImmutable());

                $this->entityManager->persist($resetPassword);
                $this->entityManager->flush();

                // Générer l'URL absolue du lien de reset
                $url = $this->generateUrl('update_password', [
                    'token' => $resetPassword->getToken()
                ], UrlGeneratorInterface::ABSOLUTE_URL);

                // Contenu HTML de l'email
                $content = "Bonjour <strong>" . htmlspecialchars($user->getUsername()) . "</strong>,<br><br>"
                    . "Cliquez sur le bouton ci-dessous pour réinitialiser votre mot de passe :<br><br>"
                    . "<div style='text-align:center; margin:30px 0;'>"
                    . "<a href='" . $url . "' style='background:linear-gradient(135deg,#667eea,#764ba2);"
                    . "color:white;padding:15px 30px;text-decoration:none;"
                    . "border-radius:25px;font-size:16px;font-weight:bold;'>"
                    . "Réinitialiser mon mot de passe</a></div>"
                    . "<p style='color:#999;font-size:14px;'>Ce lien expire dans 30 minutes.</p>";

                // ✅ Envoi via Mailjet avec l'email réel
                $mail->send(
                    $user->getEmail(),
                    $user->getUsername(),
                    'Réinitialisation de votre mot de passe',
                    $content
                );

                $this->addFlash('notice', 'Un email vous a été envoyé !');
            } else {
                $this->addFlash('notice', 'Cette adresse email n\'existe pas.');
            }
        }

        return $this->render('reset_password/index.html.twig');
    }

    #[Route('/update-password/{token}', name: 'update_password')]
    public function reset(string $token, Request $request, UserPasswordHasherInterface $passwordHasher)
    {
        $this->logger->info('=== RESET PASSWORD PAGE ACCESSED ===', ['token' => $token]);

        $resetPassword = $this->entityManager
            ->getRepository(ResetPassword::class)
            ->findOneBy(['token' => $token]);

        if (!$resetPassword) {
            $this->logger->error('Token not found', ['token' => $token]);
            $this->addFlash('error', 'Lien invalide. Veuillez faire une nouvelle demande.');
            return $this->redirectToRoute('reset_password');
        }

        $this->logger->info('Token found for user', ['username' => $resetPassword->getUser()->getUsername()]);

        // Vérifier expiration (30 minutes)
        $now = new \DateTime();
        $expiresAt = \DateTime::createFromImmutable($resetPassword->getCreatedAt());
        $expiresAt->modify('+30 minutes');

        if ($expiresAt < $now) {
            $this->logger->error('Token expired');
            $this->addFlash('error', 'Ce lien a expiré. Veuillez faire une nouvelle demande.');
            // Supprimer le token expiré
            $this->entityManager->remove($resetPassword);
            $this->entityManager->flush();
            return $this->redirectToRoute('reset_password');
        }

        $this->logger->info('Token is valid, showing form');

        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        $this->logger->info('Form handled', ['isSubmitted' => $form->isSubmitted()]);

        if ($form->isSubmitted()) {
            $this->logger->info('Form validation result', ['isValid' => $form->isValid()]);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('new_password')->getData();

            $user = $resetPassword->getUser();
            $username = $user->getUsername();

            $this->logger->info('=== UPDATING PASSWORD ===', [
                'username' => $username,
                'password_length' => strlen($newPassword)
            ]);

            try {
                // ✅ HASHAGE du nouveau mot de passe
                $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($hashedPassword);

                $this->entityManager->persist($user);

                // Supprimer le token après usage
                $this->entityManager->remove($resetPassword);
                $this->entityManager->flush();

                $this->logger->info('Password successfully updated', ['username' => $username]);

                $this->addFlash('notice', 'Votre mot de passe a été mis à jour avec succès ! Connectez-vous avec le nouvel utilisateur : ' . $username);
                return $this->redirectToRoute('login');
            } catch (\Exception $e) {
                $this->logger->error('Failed to update password', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                $this->addFlash('error', 'Une erreur est survenue : ' . $e->getMessage());
            }
        } elseif ($form->isSubmitted()) {
            $this->logger->error('Form is invalid', [
                'errors' => (string) $form->getErrors(true)
            ]);
        }

        return $this->render('reset_password/reset.html.twig', [
            'form' => $form->createView()
        ]);
    }
}