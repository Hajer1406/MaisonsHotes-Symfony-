<?php

namespace App\Controller;

use App\Entity\Maison;
use App\Entity\MaisonSearch;
use App\Form\MaisonSearchType;
use App\Form\MaisonType;
use App\Repository\MaisonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/maison')]
final class MaisonController extends AbstractController
{
    #[Route(name: 'app_maison_index', methods: ['GET', 'POST'])]
    public function index(Request $request, MaisonRepository $maisonRepository): Response
    {
        $search = new MaisonSearch();
        $cityChoices = $maisonRepository->findDistinctCities();
        $cityChoices = array_combine($cityChoices, $cityChoices);

        $form = $this->createForm(MaisonSearchType::class, $search, [
            'city_choices' => $cityChoices,
        ]);
        $form->handleRequest($request);

        $maisons = $maisonRepository->findBySearchCriteria(
            $search->getCity(),
            $search->getMinPrice(),
            $search->getMaxPrice()
        );

        return $this->render('maison/index.html.twig', [
            'maisons' => $maisons,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/new', name: 'app_maison_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $maison = new Maison();
        $form = $this->createForm(MaisonType::class, $maison);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($maison);
            $entityManager->flush();

            return $this->redirectToRoute('app_maison_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('maison/new.html.twig', [
            'maison' => $maison,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_maison_show', methods: ['GET'])]
    public function show(Maison $maison): Response
    {
        return $this->render('maison/show.html.twig', [
            'maison' => $maison,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_maison_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Maison $maison, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MaisonType::class, $maison);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_maison_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('maison/edit.html.twig', [
            'maison' => $maison,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_maison_delete', methods: ['POST'])]
    public function delete(Request $request, Maison $maison, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$maison->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($maison);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_maison_index', [], Response::HTTP_SEE_OTHER);
    }
}
