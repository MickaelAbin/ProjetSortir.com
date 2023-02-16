<?php

namespace App\Repository;

use App\Entity\Etats;
use App\Entity\Lieu;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\User;
use App\Entity\Ville;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use function PHPUnit\Framework\isEmpty;
use function PHPUnit\Framework\isNull;

/**
 * @extends ServiceEntityRepository<Sortie>
 *
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function save(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findDetailSortie(int $id)
    {
        return $this->createQueryBuilder('s')
            ->innerJoin(User::class, 'u')
            ->addSelect('u')
            ->innerJoin(Lieu::class, 'l', Join::WITH, 's.lieu = l.id')
            ->addSelect('l')
            ->innerJoin(Ville::class, 'v', Join::WITH, 'l.ville = v.id')
            ->addSelect('v')
            ->andWhere('s.id = :val')
            ->setParameter('val', $id)
            ->orderBy('u.nom', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findSortieWithFiltre(
        $filtres,
        string $user
    ) {

        $participants = $this->getEntityManager()->getRepository('App\Entity\User')
            ->createQueryBuilder('u')
            ->innerJoin(Sortie::class, 's')
            ->andWhere('u.email = :mail')
            ->setParameter('mail', $user)
            ->Select('s.id');

        $query = $this->createQueryBuilder('sortie')
            ->addSelect('sortie')
            ->innerJoin(Lieu::class,'lieu', Join::WITH, 'sortie.lieu = lieu.id')
            ->innerJoin(Ville::class,'ville', Join::WITH, 'lieu.ville = ville.id')
            ->innerJoin(Site::class, 'site', Join::WITH, 'sortie.site = site.id')
            ->innerJoin(Etats::class, 'etat', Join::WITH, 'sortie.etat = etat.id')
            ->innerJoin(User::class, 'user', Join::WITH, 'sortie.organisateur = user.id')
        ;
        if (isset($filtres['recherche'])) {
            $query->andWhere("sortie.nom like :recherche")
                ->setParameter('recherche', '%'.$filtres['recherche'].'%');
        }
        if (isset($filtres['dateDepart'])) {
            $query->andWhere('sortie.datedebut >= :dateDebut')
                ->setParameter('dateDebut', $filtres['dateDepart']);
        }
        if (isset($filtres['dateFin'])) {
            $query->andWhere('sortie.datedebut <= :dateFin')
                ->setParameter('dateFin', $filtres['dateFin']);
        }
        if ($filtres['organise']) {
            $query->orWhere('sortie.organisateur = :organisateur')
                ->setParameter('organisateur', $user);
        }
        if ($filtres['inscrit']) {
            $query->orWhere($query->expr()->in('sortie.id',$participants->getDQL()));
        }
        if ($filtres['nonInscrit']) {
            $query->orWhere($query->expr()->notIn('sortie.id',$participants->getDQL()));
        }
        if ($filtres['passe']) {
            $query->orWhere('sortie.datedebut <= :now')
                ->setParameter('now', new \DateTime());
        }
        $query->andWhere('site.id = :id')
            ->setParameter('id', $filtres['site']->getId());
        return $query->getQuery()->getResult();
    }

//    /**
//     * @return Sortie[] Returns an array of Sortie objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;

//    }
//    public function findOneBySomeField($value): ?Sortie
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;

//    }
}
