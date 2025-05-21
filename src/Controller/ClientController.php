<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientEditFormType;
use App\Repository\ClientRepository;
use App\Service\ScoringService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ClientController extends AbstractController
{
    #[Route('/clients', name: 'app_client_list')]
    public function index(ClientRepository $clientRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $query = $clientRepository->createQueryBuilder('c')
            ->orderBy('c.id', 'DESC')
            ->getQuery();

        $pagination = $paginator->paginate(
            $query, // Doctrine Query, не результат
            $request->query->getInt('page', 1), // Номер текущей страницы, из GET-параметра 'page', по умолчанию 1
            10 // Количество элементов на странице
        );

        return $this->render('client/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/client/{id}', name: 'app_client_show', requirements: ['id' => '\d+'])]
    public function show(Client $client): Response
    {
        return $this->render('client/show.html.twig', [
            'client' => $client,
        ]);
    }

    #[Route('/client/{id}/edit', name: 'app_client_edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, Client $client, EntityManagerInterface $entityManager, ScoringService $scoringService): Response
    {
        $form = $this->createForm(ClientEditFormType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Пересчитываем скоринг, так как данные могли измениться
            $newScore = $scoringService->calculateScore($client);
            $client->setScoring($newScore);

            $entityManager->flush(); // Doctrine автоматически отслеживает изменения в $client

            $this->addFlash('success', 'Данные клиента обновлены. Новый скоринг: ' . $newScore);

            return $this->redirectToRoute('app_client_show', ['id' => $client->getId()]);
        }

        return $this->render('client/edit.html.twig', [
            'client' => $client,
            'editForm' => $form->createView(),
        ]);
    }
}
