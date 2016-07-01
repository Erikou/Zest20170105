<?php

namespace SE\InputBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * TransferRepository
 */
class TransferRepository extends EntityRepository
{

	public function getAll()
	{
		$qb = $this
		->createQueryBuilder('a')
		->select("a")
		->orderBy('a.date_start')
		->getQuery()
		->getResult()
		;
		return $qb;
	}
	

	public function getValidToday()
	{
		$now = new \DateTime("now");
		$qb = $this
		->createQueryBuilder('a')
		->select("a")
		->where("a.validated = 1")
		->andWhere("a.date_start >= '".$now->format('Y-m-d 00:00:00')."'")
		->andWhere("a.date_start <= '".$now->format('Y-m-d 23:59:59')."'")
		->getQuery()
		->getResult()
		;
		return $qb;
	}
}
