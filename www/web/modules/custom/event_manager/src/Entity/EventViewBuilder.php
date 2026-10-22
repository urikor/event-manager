<?php

namespace Drupal\event_manager\Entity;

use Drupal\Core\Entity\EntityViewBuilder;
use Drupal\Core\Url;

/**
 * View builder for Event entities.
 */
class EventViewBuilder extends EntityViewBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildComponents(array &$build, array $entities, array $displays, $view_mode): void {
    parent::buildComponents($build, $entities, $displays, $view_mode);

    if ($view_mode !== 'full') {
      return;
    }

    foreach ($entities as $id => $entity) {
      $build[$id]['actions'] = [
        '#type' => 'container',
        '#attributes' => [
          'class' => ['event-actions'],
        ],
        '#weight' => -100,
        'events_list' => [
          '#type' => 'link',
          '#title' => $this->t('Events list'),
          '#url' => Url::fromRoute('entity.event.collection'),
          '#attributes' => [
            'class' => ['button', 'button--primary'],
          ],
        ],
        'edit' => [
          '#type' => 'link',
          '#title' => $this->t('Edit'),
          '#url' => $entity->toUrl('edit-form'),
          '#attributes' => [
            'class' => ['button'],
          ],
          '#access' => $entity->access('update'),
        ],
        'delete' => [
          '#type' => 'link',
          '#title' => $this->t('Delete'),
          '#url' => $entity->toUrl('delete-form'),
          '#attributes' => [
            'class' => ['button', 'button--danger'],
          ],
          '#access' => $entity->access('delete'),
        ],
      ];
    }
  }

}
