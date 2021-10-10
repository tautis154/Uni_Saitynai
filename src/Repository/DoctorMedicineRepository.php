<?php

namespace App\Repository;

use App\Entity\DoctorMedicine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DoctorMedicine|null find($id, $lockMode = null, $lockVersion = null)
 * @method DoctorMedicine|null findOneBy(array $criteria, array $orderBy = null)
 * @method DoctorMedicine[]    findAll()
 * @method DoctorMedicine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DoctorMedicineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DoctorMedicine::class);
    }

    // /**
    //  * @return DoctorMedicine[] Returns an array of DoctorMedicine objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DoctorMedicine
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
