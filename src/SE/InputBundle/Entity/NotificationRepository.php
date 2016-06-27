<?php

namespace SE\InputBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * NotificationRepository
 */
class NotificationRepository extends EntityRepository
{
	public function getByUser($userid)
	{
		$qb = $this
		->createQueryBuilder('a')
		->select("a")
		->where("a.receiver = ".$userid)
		->orderBy('a.dateCreation', 'DESC')
		->getQuery()
		->getResult()
		;
		return $qb;
	}
}