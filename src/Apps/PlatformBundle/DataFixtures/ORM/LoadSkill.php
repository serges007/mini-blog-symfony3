<?php
// src/Apps/PlatformBundle/DataFixtures/ORM/LoadSkill.php

namespace Apps\PlatformBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Apps\PlatformBundle\Entity\Skill;

class LoadSkill implements FixtureInterface
{
  public function load(ObjectManager $manager)
  {
    // Liste des noms de compétences à ajouter
    $names = array('PHP', 'Symfony', 'C++', 'Java', 'Photoshop', 'Blender', 'Bloc-note');

    foreach ($names as $name) {
      // On crée la compétence
      $skill = new Skill();
      $skill->setName($name);

      // On la persiste
      $manager->persist($skill);
    }

    // On déclenche l'enregistrement de toutes les compétences
    $manager->flush();
  }
}
