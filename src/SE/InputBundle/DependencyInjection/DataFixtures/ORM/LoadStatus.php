<?php

namespace SE\InputBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SE\InputBundle\Entity\Status;

class LoadStatus implements FixtureInterface
{
  public function load(ObjectManager $manager)
  {
    $names = array(
      'Working',
      'Not Working'
    );

    foreach ($names as $name) {
      $status = new Status();
      $status
        ->setName($name)
        ->setPermanent(true)
      ;

      $manager->persist($status);
    }

    $manager->flush();
  }
}