<?php

namespace Drupal\event_manager\Entity;

use Drupal\Core\Entity\Attribute\ContentEntityType;
use Drupal\Core\Entity\EditorialContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Defines the Event content entity.
 */
#[ContentEntityType(
  id: 'event',
  label: new TranslatableMarkup('Event'),
  label_collection: new TranslatableMarkup('Events'),
  label_singular: new TranslatableMarkup('event'),
  label_plural: new TranslatableMarkup('events'),
  entity_keys: [
    'id' => 'id',
    'revision' => 'revision_id',
    'uuid' => 'uuid',
    'label' => 'title',
    'langcode' => 'langcode',
    'published' => 'status',
  ],
  handlers: [
    'view_builder' => EventViewBuilder::class,
    'list_builder' => 'Drupal\event_manager\EventListBuilder',
    'form' => [
      'default' => 'Drupal\event_manager\EventForm',
      'add' => 'Drupal\event_manager\EventForm',
      'edit' => 'Drupal\event_manager\EventForm',
      'delete' => 'Drupal\Core\Entity\ContentEntityDeleteForm',
      'revision-revert' => 'Drupal\Core\Entity\Form\RevisionRevertForm',
      'revision-delete' => 'Drupal\Core\Entity\Form\RevisionDeleteForm',
    ],
    'route_provider' => [
      'html' => 'Drupal\Core\Entity\Routing\AdminHtmlRouteProvider',
      'revision' => 'Drupal\Core\Entity\Routing\RevisionHtmlRouteProvider',
    ],
    'translation' => 'Drupal\content_translation\ContentTranslationHandler',
  ],
  links: [
    'canonical' => '/admin/content/events/{event}',
    'add-form' => '/admin/content/events/add',
    'edit-form' => '/admin/content/events/{event}/edit',
    'delete-form' => '/admin/content/events/{event}/delete',
    'collection' => '/admin/content/events',
    'revision-delete-form' => '/admin/content/events/{event}/revision/{event_revision}/delete',
    'revision-revert-form' => '/admin/content/events/{event}/revision/{event_revision}/revert',
    'version-history' => '/admin/content/events/{event}/revisions',
  ],
  admin_permission: 'administer events',
  base_table: 'event',
  data_table: 'event_field_data',
  revision_table: 'event_revision',
  revision_data_table: 'event_field_revision',
  translatable: TRUE,
  show_revision_ui: TRUE,
  field_ui_base_route: 'entity.event.collection',
  revision_metadata_keys: [
    'revision_user' => 'revision_uid',
    'revision_created' => 'revision_timestamp',
    'revision_log_message' => 'revision_log',
  ],
)]
class Event extends EditorialContentEntityBase {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type): array {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Title'))
      ->setRequired(TRUE)
      ->setSettings(['max_length' => 255])
      ->setTranslatable(TRUE)
      ->setDisplayOptions('form', ['type' => 'string_textfield', 'weight' => -10])
      ->setDisplayOptions('view', ['label' => 'hidden', 'type' => 'string', 'weight' => -10]);

    $fields['description'] = BaseFieldDefinition::create('text_long')
      ->setLabel(new TranslatableMarkup('Description'))
      ->setRequired(TRUE)
      ->setTranslatable(TRUE)
      ->setDisplayOptions('form', ['type' => 'text_textarea', 'weight' => -9])
      ->setDisplayOptions('view', ['label' => 'above', 'type' => 'text_default', 'weight' => 0]);

    foreach (['start_date' => 'Start date', 'end_date' => 'End date'] as $name => $label) {
      $fields[$name] = BaseFieldDefinition::create('datetime')
        ->setLabel(new TranslatableMarkup('@label', ['@label' => $label]))
        ->setRequired(TRUE)
        ->setSettings(['datetime_type' => 'datetime'])
        ->setTranslatable(TRUE)
        ->setDisplayOptions('form', ['type' => 'datetime_default', 'weight' => $name === 'start_date' ? -8 : -7])
        ->setDisplayOptions('view',
          ['label' => 'above', 'type' => 'datetime_default', 'weight' => $name === 'start_date' ? 1 : 2]);
    }

    $fields['location'] = BaseFieldDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Location'))
      ->setRequired(TRUE)
      ->setSettings(['max_length' => 255])
      ->setTranslatable(TRUE)
      ->setDisplayOptions('form', ['type' => 'string_textfield', 'weight' => -6])
      ->setDisplayOptions('view', ['label' => 'above', 'type' => 'string', 'weight' => 3]);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(new TranslatableMarkup('Created'));
    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(new TranslatableMarkup('Changed'));

    return $fields;
  }

}
