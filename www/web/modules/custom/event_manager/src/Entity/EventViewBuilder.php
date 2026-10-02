<?php

namespace Drupal\event_manager\Entity;

use Drupal\Core\Entity\EntityViewBuilder;

/**
 * View builder for Event entities.
 */
class EventViewBuilder extends EntityViewBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildComponents(array &$build, array $entities, array $displays, $view_mode): void {
    parent::buildComponents($build, $entities, $displays, $view_mode);

    foreach ($entities as $id => $entity) {
      if ($view_mode !== 'full') {
        continue;
      }

      $build[$id]['actions'] = [
        '#type' => 'container',
        '#attributes' => [
          'class' => ['event-actions'],
        ],
        '#weight' => -100,
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
