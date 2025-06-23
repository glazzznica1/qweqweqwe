<?php

namespace App\Command;

use App\Entity\Document;
use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:check-expired-documents',
    description: 'Проверяет просроченные документы и создаёт уведомления',
)]
class CheckExpiredDocumentsCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $documents = $this->entityManager
            ->getRepository(Document::class)
            ->createQueryBuilder('d')
            ->where('d.expiryDate < :now')
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->getResult();

        foreach ($documents as $document) {
            $notification = new Notification();
            $notification->setDocument($document);
            $notification->setCreatedAt(new \DateTime());
            $notification->setStatus('new');

            $this->entityManager->persist($notification);
            $output->writeln("Создано уведомление для документа: " . $document->getTitle());
        }

        $this->entityManager->flush();
        $output->writeln(sprintf('Создано уведомлений: %d', count($documents)));

        return Command::SUCCESS;
    }
}