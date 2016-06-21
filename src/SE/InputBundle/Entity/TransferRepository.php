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
}
