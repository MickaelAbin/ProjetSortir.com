<?php

namespace App\Command;

use App\Entity\Etats;
use App\Entity\Sortie;
use App\Repository\EtatsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SortieArchivageCommand extends Command
{

    protected static $defaultName = 'app:sortie-archivage';

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Archive les sorties ouvertes depuis plus d\'un mois.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repository = $this->entityManager->getRepository(Sortie::class);
        $sorties = $repository->createQueryBuilder('s')
            ->join('s.etat', 'e')
            ->where('e.libelle = :etatOuvert')
            ->andWhere('s.datedebut < :date')
            ->setParameters([
                'etatOuvert' => 'ouverte',
                'date' => new \DateTime('-1 month')
            ])
            ->getQuery()
            ->getResult();

        $etatArchive = $this->entityManager->getRepository(Etats::class)->findOneBy(['libelle' => 'archivée']);

        foreach ($sorties as $sortie) {
            $sortie->setEtat($etatArchive);
            $this->entityManager->persist($sortie);
        }

        $this->entityManager->flush();

        $output->writeln('Sorties archivées.');

        return Command::SUCCESS;
    }
}