<?php

namespace Drupal\event_manager;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;

/**
 * Builds the administrative Event listing.
 */
class EventListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader(): array {
    $header['title'] = $this->t('Title');
    $header['start_date'] = $this->t('Start date');
    $header['end_date'] = $this->t('End date');
    $header['status'] = $this->t('Published');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity): array {
    $row['title'] = $entity->toLink()->toString();
    $row['start_date'] = $entity->get('start_date')->value;
    $row['end_date'] = $entity->get('end_date')->value;
    $row['status'] = $entity->isPublished() ? $this->t('Published') : $this->t('Unpublished');
    return $row + parent::buildRow($entity);
  }

}
