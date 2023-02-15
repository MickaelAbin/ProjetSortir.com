<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\SortieRepository;
use Symfony\Component\Form\FormInterface;
use function PHPUnit\Framework\isEmpty;
use function PHPUnit\Framework\isNull;
use function PHPUnit\Framework\throwException;

class SortieService
{
    private SortieRepository $sortieRepository;


    public function __construct(SortieRepository $sortieRepository) {
        $this->sortieRepository = $sortieRepository;
    }

    public function findSortieWithFiltre(
        FormInterface $filtreForm,
        string $user
    ) {
        $filtres = $filtreForm->getData();
        $filtres = self::verifForm($filtres);
        return $this->sortieRepository->findSortieWithFiltre(
            $filtres,
            $user
        );
    }

    private function verifForm($filtres){
        if (!isNull($filtres['dateDepart']) && !isNull($filtres['dateFin']) && ($filtres['dateDebut'] > $filtres['dateFin'])){
            throw new \Exception('La date de début doit être antérieure à la date de fin');
        }
        if (isNull($filtres['recherche'])){
            $filtres['recherche'] = '';
        }
        if (isNull($filtres['dateDepart'])){
            $filtres['dateDepart'] = '';
        }
        if (isNull($filtres['dateFin'])){
            $filtres['dateFin'] = '';
        }
        return $filtres;
    }
}