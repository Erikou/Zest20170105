<?php

namespace SE\InputBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends EntityRepository
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