<?php

namespace App\Command;

use App\Entity\Client;
use App\Repository\ClientRepository;
use App\Service\ScoringService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:recalculate-scoring',
    description: 'Recalculates scoring for clients. Can process a single client by ID or all clients.',
)]
class RecalculateScoringCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private ClientRepository $clientRepository;
    private ScoringService $scoringService;

    public function __construct(EntityManagerInterface $entityManager, ClientRepository $clientRepository, ScoringService $scoringService)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->clientRepository = $clientRepository;
        $this->scoringService = $scoringService;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('clientId', InputArgument::OPTIONAL, 'The ID of the client to recalculate scoring for. If not provided, recalculates for all clients.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $clientId = $input->getArgument('clientId');

        if ($clientId) {
            $client = $this->clientRepository->find($clientId);
            if (!$client) {
                $io->error(sprintf('Client with ID %s not found.', $clientId));
                return Command::FAILURE;
            }
            $clients = [$client];
            $io->info(sprintf('Recalculating scoring for client ID: %s', $clientId));
        } else {
            $clients = $this->clientRepository->findAll();
            if (empty($clients)) {
                $io->info('No clients found to recalculate scoring.');
                return Command::SUCCESS;
            }
            $io->info('Recalculating scoring for all clients...');
        }

        $processedCount = 0;
        foreach ($clients as $client) {
            $this->processClient($client, $io);
            $processedCount++;
        }

        $io->success(sprintf('Scoring recalculation finished. Processed %d client(s).', $processedCount));

        return Command::SUCCESS;
    }

    private function processClient(Client $client, SymfonyStyle $io): void
    {
        $io->writeln(sprintf('Processing client ID: %d (%s %s)', $client->getId(), $client->getFirstName(), $client->getLastName()));

        $scoringResult = $this->scoringService->calculateScoreDetails($client);
        $oldScore = $client->getScoring();
        $newScore = $scoringResult['total'];

        $client->setScoring($newScore);
        $this->entityManager->flush(); // Сохраняем изменения для каждого клиента сразу

        $io->writeln(sprintf('  Old score: %s', $oldScore ?? 'N/A'));
        $io->writeln(sprintf('  New score: %s', $newScore));
        $io->writeln('  Score details:');
        $io->listing([
            sprintf('Operator points: %d', $scoringResult['details']['operator']),
            sprintf('Email domain points: %d', $scoringResult['details']['email']),
            sprintf('Education points: %d', $scoringResult['details']['education']),
            sprintf('Consent points: %d', $scoringResult['details']['consent']),
        ]);
        $io->newLine();
    }
}
