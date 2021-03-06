<?php

namespace SE\InputBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * SAPRFRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SAPRFRepository extends EntityRepository
{

	public function getTo($date, $user){

		$qb = $this
			->createQueryBuilder('a')
			->select("a")
			->where("a.dateImport = :date")
            ->andWhere("a.recorded is NULL OR a.recorded = 0")
            ->andWhere("a.user = :user");

  		$qb->setParameter('date', $date->format('Y-m-d H:i:s'));
  		$qb->setParameter('user', $user);

  		return $qb->getQuery()->getResult();
	}

	public function getRecordedTo($date, $user){

		$qb = $this
			->createQueryBuilder('a')
			->select("a")
			->where("a.dateImport = :date")
            ->andWhere("a.recorded = 1")
            ->andWhere("a.user = :user");

  		$qb->setParameter('date', $date->format('Y-m-d H:i:s'));
  		$qb->setParameter('user', $user);

  		return $qb->getQuery()->getResult();
	}

	public function getManualTo($date, $team){

		$qb = $this
			->createQueryBuilder('a')
			->select("a")
			->where("a.dateImport = :date")
            ->andWhere("a.recorded is NULL OR a.recorded = 0")
            ->andWhere("a.storageLocation = :sLoc");

 			if($team == 1){	
		        $qb->andWhere("a.user = :userA OR a.user = :userB OR a.user = :userC")
            	   ->andWhere("a.sourceStorageType <> :sTypeA OR a.sourceStorageType <> :sTypeB");
				$qb->setParameter('userA', 'SESI000005');
				$qb->setParameter('userB', 'SESI000006');
				$qb->setParameter('userC', 'SESI000008');
				$qb->setParameter('sTypeA', '901');
				$qb->setParameter('sTypeB', '902');
				$qb->setParameter('sLoc', '4000');
            }elseif ($team == 2) {
            	$qb->andWhere("a.user = :userA OR a.user = :userB OR a.user = :userC OR a.user = :userD OR a.user = :userE")
            	   ->andWhere("a.sourceStorageType = :sTypeA OR a.sourceStorageType = :sTypeB");
				$qb->setParameter('userA', 'SESI000012');
				$qb->setParameter('userB', 'SESI000013');
				$qb->setParameter('userC', 'SESI000014');
				$qb->setParameter('userD', 'SESI000015');
				$qb->setParameter('userE', 'SESI000030');
				$qb->setParameter('sTypeA', '902');
				$qb->setParameter('sTypeB', 'X04');
				$qb->setParameter('sLoc', '4000');
            }elseif ($team == 3) {
            	$qb->andWhere("a.user = :userA OR a.user = :userB")
            	   ->andWhere("a.sourceStorageType <> :sTypeA OR a.sourceStorageType <> :sTypeB");
				$qb->setParameter('userA', 'SESI000016');
				$qb->setParameter('userB', 'SESI000017');
				$qb->setParameter('sTypeA', '901');
				$qb->setParameter('sTypeB', '902');
				$qb->setParameter('sLoc', '4000');
            }elseif ($team == 4) {
            	$qb->andWhere("a.user = :userA")
            	   ->andWhere("a.sourceStorageType <> :sTypeA OR a.sourceStorageType <> :sTypeB");
				$qb->setParameter('userA', 'SESI000019');
				$qb->setParameter('sTypeA', '901');
				$qb->setParameter('sTypeB', '902');
				$qb->setParameter('sLoc', '4001');
            }elseif ($team == 5) {
            	$qb->andWhere("a.user = :userA OR a.user = :userB OR a.user = :userC")
            	   ->andWhere("a.sourceStorageType = :sTypeA OR a.sourceStorageType = :sTypeB");
				$qb->setParameter('userA', 'SESI000021');
				$qb->setParameter('userB', 'SESI000022');
				$qb->setParameter('userC', 'SESI000023');
				$qb->setParameter('sTypeA', '902');
				$qb->setParameter('sTypeB', 'X04');
				$qb->setParameter('sLoc', '4001');
            }elseif ($team == 8) {
            	$qb->andWhere("a.user = :userA")
				   ->andWhere("a.sourceStorageType <> :sType");
				$qb->setParameter('userA', 'SESI000018');
				$qb->setParameter('sType', 'O14');
				$qb->setParameter('sLoc', '4000');
            }else{
            	//send nothing
            	$qb->setParameter('sLoc', '0');
            }

  		$qb->setParameter('date', $date->format('Y-m-d H:i:s'));
  		
  		return $qb->getQuery()->getResult();
	}

	public function resetManualTo($date, $team){

		$qb = $this
			->createQueryBuilder('a')
			->select("a")
			->where("a.dateImport = :date")
            ->andWhere("a.recorded = 1")
            ->andWhere("a.storageLocation = :sLoc");

            if($team == 1){	
		        $qb->andWhere("a.user = :userA OR a.user = :userB OR a.user = :userC")
            	   ->andWhere("a.sourceStorageType <> :sTypeA OR a.sourceStorageType <> :sTypeB");
				$qb->setParameter('userA', 'SESI000005');
				$qb->setParameter('userB', 'SESI000006');
				$qb->setParameter('userC', 'SESI000008');
				$qb->setParameter('sTypeA', '901');
				$qb->setParameter('sTypeB', '902');
				$qb->setParameter('sLoc', '4000');
            }elseif ($team == 2) {
            	$qb->andWhere("a.user = :userA OR a.user = :userB OR a.user = :userC OR a.user = :userD OR a.user = :userE")
            	   ->andWhere("a.sourceStorageType = :sTypeA OR a.sourceStorageType = :sTypeB");
				$qb->setParameter('userA', 'SESI000012');
				$qb->setParameter('userB', 'SESI000013');
				$qb->setParameter('userC', 'SESI000014');
				$qb->setParameter('userD', 'SESI000015');
				$qb->setParameter('userE', 'SESI000030');
				$qb->setParameter('sTypeA', '902');
				$qb->setParameter('sTypeB', 'X04');
				$qb->setParameter('sLoc', '4000');
            }elseif ($team == 3) {
            	$qb->andWhere("a.user = :userA OR a.user = :userB")
            	   ->andWhere("a.sourceStorageType <> :sTypeA OR a.sourceStorageType <> :sTypeB");
				$qb->setParameter('userA', 'SESI000016');
				$qb->setParameter('userB', 'SESI000017');
				$qb->setParameter('sTypeA', '901');
				$qb->setParameter('sTypeB', '902');
				$qb->setParameter('sLoc', '4000');
            }elseif ($team == 4) {
            	$qb->andWhere("a.user = :userA")
            	   ->andWhere("a.sourceStorageType <> :sTypeA OR a.sourceStorageType <> :sTypeB");
				$qb->setParameter('userA', 'SESI000019');
				$qb->setParameter('sTypeA', '901');
				$qb->setParameter('sTypeB', '902');
				$qb->setParameter('sLoc', '4001');
            }elseif ($team == 5) {
            	$qb->andWhere("a.user = :userA OR a.user = :userB OR a.user = :userC")
            	   ->andWhere("a.sourceStorageType = :sTypeA OR a.sourceStorageType = :sTypeB");
				$qb->setParameter('userA', 'SESI000021');
				$qb->setParameter('userB', 'SESI000022');
				$qb->setParameter('userC', 'SESI000023');
				$qb->setParameter('sTypeA', '902');
				$qb->setParameter('sTypeB', 'X04');
				$qb->setParameter('sLoc', '4001');
            }elseif ($team == 8) {
            	$qb->andWhere("a.user = :userA")
				   ->andWhere("a.sourceStorageType <> :sType");
				$qb->setParameter('userA', 'SESI000018');
				$qb->setParameter('sType', 'O14');
				$qb->setParameter('sLoc', '4000');
            }else{
            	//send nothing
            	$qb->setParameter('sLoc', '0');
            }

  		$qb->setParameter('date', $date->format('Y-m-d H:i:s'));
  		
  		return $qb->getQuery()->getResult();
	}

	public function getGeneralManualTo($date, $team){

		$qb = $this
			->createQueryBuilder('a')
			->select("a")
			->where("a.dateImport = :date")
            ->andWhere("a.recorded is NULL OR a.recorded = 0");

 			if($team == 1){	
		        $qb->andWhere("a.user = :userA OR a.user = :userB OR a.user = :userC OR a.user = :userD OR a.user = :userE OR a.user = :userF")
            	   ->andWhere("a.sourceStorageType <> :sTypeA AND a.sourceStorageType <> :sTypeB");
				$qb->setParameter('userA', 'SESI000004');
				$qb->setParameter('userB', 'SESI000005');//outbound4
				$qb->setParameter('userC', 'SESI000006');
				$qb->setParameter('userD', 'SESI000007');
				$qb->setParameter('userE', 'SESI000019');
				$qb->setParameter('userF', 'SESI000020');//outbound3
				$qb->setParameter('sTypeA', '901');
				$qb->setParameter('sTypeB', '902');
            }elseif ($team == 2) {
            	$qb->andWhere("a.user = :userA OR a.user = :userB OR a.user = :userC OR a.user = :userD OR a.user = :userE OR a.user = :userF OR a.user = :userG OR a.user = :userH")
            	   ->andWhere("a.sourceStorageType = :sTypeA OR a.sourceStorageType = :sTypeB");
				$qb->setParameter('userA', 'SESI000012');//inbound4
				$qb->setParameter('userB', 'SESI000013');
				$qb->setParameter('userC', 'SESI000014');
				$qb->setParameter('userD', 'SESI000015');
				$qb->setParameter('userE', 'SESI000030');
				$qb->setParameter('userF', 'SESI000021');//inbound3
				$qb->setParameter('userG', 'SESI000022');
				$qb->setParameter('userH', 'SESI000023');
				$qb->setParameter('sTypeA', '902');
				$qb->setParameter('sTypeB', 'X04');
            }elseif ($team == 3) {
            	$qb->andWhere("a.user = :userA OR a.user = :userB OR a.user = :userC OR a.user = :userD OR a.user = :userE")
            	   ->andWhere("a.sourceStorageType <> :sTypeA OR a.sourceStorageType <> :sTypeB");
            	$qb->setParameter('userA', 'SESI000002');
				$qb->setParameter('userB', 'SESI000008');
				$qb->setParameter('userC', 'SESI000016');
				$qb->setParameter('userD', 'SESI000017');
				$qb->setParameter('userE', 'SESI000028');
				$qb->setParameter('sTypeA', '901');
				$qb->setParameter('sTypeB', '902');
            }elseif ($team == 8) {
            	$qb->andWhere("a.user = :userA")
				   ->andWhere("a.sourceStorageType <> :sType");
				$qb->setParameter('userA', 'SESI000018');
				$qb->setParameter('sType', 'O14');
	        }else{
	        	return null;
	        }
	        
  		$qb->setParameter('date', $date->format('Y-m-d H:i:s'));
  		
  		return $qb->getQuery()->getResult();
	}

	public function resetGeneralManualTo($date, $team){

		$qb = $this
			->createQueryBuilder('a')
			->select("a")
			->where("a.dateImport = :date")
            ->andWhere("a.recorded = 1");

            if($team == 1){	
		        $qb->andWhere("a.user = :userA OR a.user = :userB OR a.user = :userC OR a.user = :userD OR a.user = :userE OR a.user = :userF")
            	   ->andWhere("a.sourceStorageType <> :sTypeA AND a.sourceStorageType <> :sTypeB");
				$qb->setParameter('userA', 'SESI000004');
				$qb->setParameter('userB', 'SESI000005');//outbound4
				$qb->setParameter('userC', 'SESI000006');
				$qb->setParameter('userD', 'SESI000007');
				$qb->setParameter('userE', 'SESI000019');
				$qb->setParameter('userF', 'SESI000020');//outbound3
				$qb->setParameter('sTypeA', '901');
				$qb->setParameter('sTypeB', '902');
            }elseif ($team == 2) {
            	$qb->andWhere("a.user = :userA OR a.user = :userB OR a.user = :userC OR a.user = :userD OR a.user = :userE OR a.user = :userF OR a.user = :userG OR a.user = :userH")
            	   ->andWhere("a.sourceStorageType = :sTypeA OR a.sourceStorageType = :sTypeB");
				$qb->setParameter('userA', 'SESI000012');//inbound4
				$qb->setParameter('userB', 'SESI000013');
				$qb->setParameter('userC', 'SESI000014');
				$qb->setParameter('userD', 'SESI000015');
				$qb->setParameter('userE', 'SESI000030');
				$qb->setParameter('userF', 'SESI000021');//inbound3
				$qb->setParameter('userG', 'SESI000022');
				$qb->setParameter('userH', 'SESI000023');
				$qb->setParameter('sTypeA', '902');
				$qb->setParameter('sTypeB', 'X04');
            }elseif ($team == 3) {
            	$qb->andWhere("a.user = :userA OR a.user = :userB OR a.user = :userC OR a.user = :userD OR a.user = :userE")
            	   ->andWhere("a.sourceStorageType <> :sTypeA OR a.sourceStorageType <> :sTypeB");
            	$qb->setParameter('userA', 'SESI000002');
				$qb->setParameter('userB', 'SESI000008');
				$qb->setParameter('userC', 'SESI000016');
				$qb->setParameter('userD', 'SESI000017');
				$qb->setParameter('userE', 'SESI000028');
				$qb->setParameter('sTypeA', '901');
				$qb->setParameter('sTypeB', '902');
            }elseif ($team == 8) {
            	$qb->andWhere("a.user = :userA")
				   ->andWhere("a.sourceStorageType <> :sType");
				$qb->setParameter('userA', 'SESI000018');
				$qb->setParameter('sType', 'O14');
				$qb->setParameter('sLoc', '4000');
            }else{
            	return null;
            }

  		$qb->setParameter('date', $date->format('Y-m-d H:i:s'));
  		
  		return $qb->getQuery()->getResult();
	}


	public function getDayLines($date){
	
		$qb = $this
		->createQueryBuilder('a')
		->select("a")
		->where("a.dateImport = :date")
		->orderBy('a.timeConfirmation', 'ASC');
	
		$qb->setParameter('date', $date->format('Y-m-d H:i:s'));
	
		return $qb->getQuery()->getResult();
	}
	
}