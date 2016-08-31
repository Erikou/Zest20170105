<?php

namespace SE\InputBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * EmployeeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EmployeeRepository extends EntityRepository
{
	public function getAlphaCurrentEmployees()
	{
		$qb = $this
		->createQueryBuilder('a')
		->select("a")
		->leftJoin('a.status', 's')
		->addSelect('s')
		->where("s.id = 1")
		->andWhere("a.statusControl = 1")
        ->orderBy('a.sesa', 'ASC')
		->getQuery()
		->getResult()
		;
		return $qb;
	}

	public function getMaxId()
	{
	    $qb = $this
	    ->createQueryBuilder('s')
	    ->select('MAX(s.masterId)')
    	->setMaxResults(1)
		->getQuery()
		->getResult()
		;
		return $qb;
	}

	public function getCurrentEmployees()
	{
		$qb = $this
		->createQueryBuilder('a')
		->select("a")
		->where("a.statusControl = 1")
	    ->orderBy('a.masterId', 'ASC')
		->getQuery()
		->getResult()
		;
		return $qb;
	}

	public function getHistoricalEmployees($year, $month)
	{
		$start = new \DateTime();
		$end = new \DateTime();
		$start->setDate($year, $month, 1);
		$end = $start->format( 'Y-m-t' );

		$qb = $this
		->createQueryBuilder('a')
		->select("a")
		->where("( a.endDate IS NOT NULL and a.endDate >= '".$start->format( 'Y-m-d' )."' ) or ( a.endDate IS NULL and a.statusControl = 1 ) ")
        ->andWhere("a.startDate <= '".$end."'")
	    ->orderBy('a.masterId', 'ASC')
		->getQuery()
		->getResult()
		;
		return $qb;
	}
	public function getDepartementEmployees($depId)
	{
		$qb = $this
		->createQueryBuilder('e')
		->select("e")
		->leftJoin('e.default_team', 't')
		->addSelect('t')
		->leftJoin('t.departement', 'd')
		->addSelect('d')
		->where("d.id = ".$depId)
        ->orderBy('e.id', 'ASC')
		;
		return $qb;
	}
	

	public function getDepartementEmployeesDate($depId, $date)
	{
		/*$qb = $this
		->createQueryBuilder('e')
		->select("e")
		->leftJoin('e.default_team', 't')
		->addSelect('t')
		->leftJoin('t.departement', 'd')
		->addSelect('d')

		->leftJoin('e.default_team', 't')
		->addSelect('t')
		->leftJoin('t.departement', 'd')
		->addSelect('d')
		->where("d.id = ".$depId)
		
		->orderBy('e.id', 'ASC')
		;*/
		return $this->_em->createQuery('
			SELECT e
			FROM
				SEInputBundle:Employee e
			LEFT JOIN
				SEInputBundle:Team
			LEFT JOIN
				SEInputBundle:Departement d
			WHERE
				d.id = :depId
			UNION
			SELECT e
			FROM
				SEInputBundle:Employee e
			LEFT JOIN
				SEInputBundle:Transfer t
			LEFT JOIN
				SEInputBundle:Departement d
			WHERE
				d.id = :depId AND t.startDate = :tDate
		')
		->setMaxResults($limit)
		->setParameter('depId', $depId)
		->setParameter('tDate', $date);
		return $qb;
	}
}
