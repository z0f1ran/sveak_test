<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientRegistrationFormType;
use App\Service\ScoringService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        ScoringService $scoringService
    ): Response
    {
        $client = new Client();
        $form = $this->createForm(ClientRegistrationFormType::class, null, [
            'action' => $this->generateUrl('app_register'),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $client->setFirstName($data['firstName']);
            $client->setLastName($data['lastName']);
            $client->setPhoneNumber($data['phoneNumber']);
            $client->setEmail($data['email']);
            $client->setEducation($data['education']);
            $client->setConsentProcessingPersonalData(true);

            $calculatedScore = $scoringService->calculateScore($client);
            $client->setScoring($calculatedScore);

            $entityManager->persist($client);
            $entityManager->flush();

            $this->addFlash('success', 'Вы успешно зарегистрированы! Ваш скоринг: ' . $calculatedScore);

            return $this->redirectToRoute('app_register');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
