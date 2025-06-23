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
        $today = new \DateTime();
        $output->writeln(sprintf('[%s] Начало проверки просроченных документов', $today->format('Y-m-d H:i:s')));

        $expiredDocuments = $this->entityManager
            ->getRepository(Document::class)
            ->createQueryBuilder('d')
            ->where('d.expiryDate < :now AND d.expiryDate IS NOT NULL')
            ->setParameter('now', $today)
            ->getQuery()
            ->getResult();

        if (empty($expiredDocuments)) {
            $output->writeln('Просроченных документов не найдено');
            return Command::SUCCESS;
        }

        $notificationCount = 0;

        foreach ($expiredDocuments as $document) {
            // Проверяем существующие уведомления за последние 7 дней
            $existingNotification = $this->entityManager
                ->getRepository(Notification::class)
                ->createQueryBuilder('n')
                ->where('n.document = :document')
                ->andWhere('n.createdAt >= :date')
                ->setParameter('document', $document)
                ->setParameter('date', new \DateTime('-7 days'))
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

            if ($existingNotification) {
                $output->writeln(sprintf(
                    'Уведомление для документа "%s" уже существует (создано %s)',
                    $document->getTitle(),
                    $existingNotification->getCreatedAt()->format('Y-m-d')
                ));
                continue;
            }

            $notification = new Notification();
            $notification->setDocument($document);
            $notification->setCreatedAt($today);
            $notification->setStatus('new');

            $this->entityManager->persist($notification);
            $notificationCount++;

            $output->writeln(sprintf(
                'Создано уведомление для документа: "%s" (просрочен %s)',
                $document->getTitle(),
                $document->getExpiryDate()->format('Y-m-d')
            ));
        }

        $this->entityManager->flush();
        
        $output->writeln([
            '=================================',
            sprintf('Всего обработано документов: %d', count($expiredDocuments)),
            sprintf('Создано новых уведомлений: %d', $notificationCount),
            sprintf('Время выполнения: %d сек.', time() - $today->getTimestamp()),
        ]);

        return Command::SUCCESS;
    }
}