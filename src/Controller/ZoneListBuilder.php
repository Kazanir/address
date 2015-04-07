<?php

/**
 * @file
 * Contains \Drupal\address\Controller\ZoneListBuilder.
 */

namespace Drupal\address\Controller;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a listing of zones.
 */
class ZoneListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Machine name');
    $header['name'] = $this->t('Name');
    $header['scope'] = $this->t('Scope');
    $header['priority'] = $this->t('Priority');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['id'] = $entity->getId();
    $row['name'] = $this->getLabel($entity);
    $row['scope'] = $entity->getScope();
    $row['priority'] = $entity->getPriority();
    return $row + parent::buildRow($entity);
  }

}

