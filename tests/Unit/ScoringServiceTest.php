<?php

namespace App\Tests\Unit;

use App\Entity\Client;
use App\Service\ScoringService;
use PHPUnit\Framework\TestCase;

class ScoringServiceTest extends TestCase
{
    private ScoringService $scoringService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->scoringService = new ScoringService();
    }

    public function testCalculateScoreDetails_ReturnsCorrectStructureAndTotal(): void
    {
        // 1. Arrange
        $client = new Client();
        $client->setPhoneNumber('+79211234567'); // Мегафон = 10
        $client->setEmail('test@gmail.com');     // Gmail = 10
        $client->setEducation('Высшее образование'); // Высшее = 15
        $client->setConsentProcessingPersonalData(true); // Согласие = 4
        // Ожидаемый общий скоринг: 10 + 10 + 15 + 4 = 39

        $expectedTotalScore = 39;
        $expectedDetails = [
            'operator' => 10,
            'email' => 10,
            'education' => 15,
            'consent' => 4,
        ];

        // 2. Act
        $result = $this->scoringService->calculateScoreDetails($client);

        // 3. Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('total', $result);
        $this->assertArrayHasKey('details', $result);
        $this->assertEquals($expectedTotalScore, $result['total']);
        $this->assertEquals($expectedDetails, $result['details']);
    }

    public function testCalculateScore_OtherOperatorAndEmail(): void
    {
        // 1. Arrange
        $client = new Client();
        $client->setPhoneNumber('+71234567890'); // Иной оператор = 1
        $client->setEmail('test@example.com');  // Иной домен = 3
        $client->setEducation('Среднее образование'); // Среднее = 5
        $client->setConsentProcessingPersonalData(true); // Согласие = 4
        // Ожидаемый общий скоринг: 1 + 3 + 5 + 4 = 13

        $expectedTotalScore = 13;
        $expectedDetails = [
            'operator' => 1,
            'email' => 3,
            'education' => 5,
            'consent' => 4,
        ];

        // 2. Act
        $result = $this->scoringService->calculateScoreDetails($client);

        // 3. Assert
        $this->assertEquals($expectedTotalScore, $result['total']);
        $this->assertEquals($expectedDetails, $result['details']);
    }
} 