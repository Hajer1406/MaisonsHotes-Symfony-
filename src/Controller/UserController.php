<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserController extends AbstractController
{
    #[Route('/mon-compte', name: 'app_user_account')]
    #[IsGranted('ROLE_USER')]
    public function account(): Response
    {
        $user = $this->getUser();

        return $this->render('user/account.html.twig', [
            'user' => $user,
        ]);
    }
}