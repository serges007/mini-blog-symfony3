<?php
// src/Apps/PlatformBundle/DoctrineListener/ApplicationCreationListener.php

namespace Apps\PlatformBundle\DoctrineListener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Apps\PlatformBundle\Email\ApplicationMailer;
use Apps\PlatformBundle\Entity\Application;

class ApplicationCreationListener
{
  /**
   * @var ApplicationMailer
   */
  private $applicationMailer;

  public function __construct(ApplicationMailer $applicationMailer)
  {
    $this->applicationMailer = $applicationMailer;
  }

  public function postPersist(LifecycleEventArgs $args)
  {
    $entity = $args->getObject();

    // On ne veut envoyer un email que pour les entités Application
    if (!$entity instanceof Application) {
      return;
    }

    $this->applicationMailer->sendNewNotification($entity);
  }
}
