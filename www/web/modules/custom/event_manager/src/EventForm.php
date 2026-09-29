<?php

namespace Drupal\event_manager;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for Event add and edit forms.
 */
class EventForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state): int {
    $event = $this->entity;
    $insert = $event->isNew();
    $event->save();

    $this->messenger()->addStatus($insert ? $this->t('Event %label has been created.', ['%label' => $event->label()]) : $this->t('Event %label has been updated.', ['%label' => $event->label()]));
    $form_state->setRedirect('entity.event.collection');
    return $insert ? SAVED_NEW : SAVED_UPDATED;
  }

}
