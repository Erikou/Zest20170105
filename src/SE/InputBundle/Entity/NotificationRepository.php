<?php

namespace SE\InputBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * NotificationRepository
 */
class NotificationRepository extends EntityRepository
{
	public function getAll()
	{
		$qb = $this
		->createQueryBuilder('a')
		->select("a")
		->orderBy('a.name', 'DESC')
		->getQuery()
		->getResult()
		;
		return $qb;
	}
}
