<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ReservationRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Form\MaisonSearchType;
use App\Entity\MaisonSearch;

final class ReportController extends AbstractController
{
    #[Route('/most-reserved-maisons', name: 'most_reserved_maisons')]
    public function index(ReservationRepository $repository): Response
    {
        $maisons = $repository->findMostReservedMaisons();
        return $this->render('report/index.html.twig', [
            'maisons' => $maisons,
        ]);
    }

    #[Route('/reservation-maison', name: 'reservation_maison')]
    public function reservationMaison(Request $request, ReservationRepository $repository): Response
    {
        $search = new MaisonSearch();

        // Création du formulaire en GET
        $form = $this->createForm(MaisonSearchType::class, $search, [
            'method' => 'GET',
        ]);
        $form->handleRequest($request);

        // Par défaut, on récupère toutes les réservations
        $reservations = $repository->findAll();

        // Si le formulaire est soumis (et qu'il y a des paramètres GET)
        if ($form->isSubmitted() && $form->isValid()) {
            $maison = $search->getMaison();
            if ($maison) {
                $reservations = $repository->findByMaison($maison);
            } else {
                // Si aucune maison sélectionnée, on garde toutes les réservations
                $reservations = $repository->findAll();
            }
        }

        return $this->render('report/reservation_maison.html.twig', [
            'form' => $form->createView(),
            'reservations' => $reservations,
        ]);
    }
}