<?php

namespace SE\InputBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SE\InputBundle\Entity\Job;

class LoadJob implements FixtureInterface
{
  public function load(ObjectManager $manager)
  {
    $descriptions = array(
      'Senior Supervisor',
      'Supervisor',
      'Team Leader',
      'DVC'
    );

    foreach ($descriptions as $description) {
      $job = new Job();
      $job->setDescription($description);

      $manager->persist($job);
    }

    $manager->flush();
  }
}