<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Service\ScoringService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ClientFixtures extends Fixture
{
    private ScoringService $scoringService;

    public function __construct(ScoringService $scoringService)
    {
        $this->scoringService = $scoringService;
    }

    public function load(ObjectManager $manager): void
    {
        $clientsData = [
            [
                'firstName' => 'Иван',
                'lastName' => 'Петров',
                'phoneNumber' => '+79211234567', // Пример МегаФон
                'email' => 'ivan.petrov@gmail.com',
                'education' => 'Высшее образование',
            ],
            [
                'firstName' => 'Мария',
                'lastName' => 'Сидорова',
                'phoneNumber' => '+79052345678', // Пример Билайн
                'email' => 'maria.s@yandex.ru',
                'education' => 'Специальное образование',
            ],
            [
                'firstName' => 'Алексей',
                'lastName' => 'Иванов',
                'phoneNumber' => '+79163456789', // Пример МТС
                'email' => 'alex.ivanov@mail.ru',
                'education' => 'Среднее образование',
            ],
            [
                'firstName' => 'Елена',
                'lastName' => 'Кузнецова',
                'phoneNumber' => '+79994567890', // Пример "Иной" оператор
                'email' => 'elena.k@example.com', // Пример "Иной" домен
                'education' => 'Высшее образование',
            ],
             [
                'firstName' => 'Сергей',
                'lastName' => 'Васильев',
                'phoneNumber' => '89255678901', // Мегафон без +7
                'email' => 'sergey.v@gmail.com',
                'education' => 'Специальное образование',
            ],
        ];

        foreach ($clientsData as $data) {
            $client = new Client();
            $client->setFirstName($data['firstName']);
            $client->setLastName($data['lastName']);
            $client->setPhoneNumber($data['phoneNumber']);
            $client->setEmail($data['email']);
            $client->setEducation($data['education']);
            $client->setConsentProcessingPersonalData(true); // Все тестовые клиенты дали согласие

            // Рассчитываем скоринг
            $score = $this->scoringService->calculateScore($client); // Используем старый метод для простоты
            $client->setScoring($score);

            $manager->persist($client);
        }

        $manager->flush();
    }
}
