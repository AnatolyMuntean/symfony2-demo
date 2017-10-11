<?php
/**
 * @file
 */
namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class CategoryRepository extends EntityRepository
{
    public function getWithJobs()
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.jobs', 'j')
            ->where('j.expiresAt > :date')
            ->setParameter('date', date('Y-m-d H:i:s', time()))
            ->andWhere('j.isActivated = :activated')
            ->setParameter(':activated', 1)
            ->getQuery();

        return $qb->getResult();
    }
}
