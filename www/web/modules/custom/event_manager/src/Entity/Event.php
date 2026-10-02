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
    'label' => 'field_title',
    'langcode' => 'langcode',
    'published' => 'status',
  ],
  handlers: [
    'view_builder' => EventViewBuilder::class,
    'list_builder' => 'Drupal\event_manager\EventListBuilder',
    'form' => [
      'default' => 'Drupal\event_manager\Form\EventForm',
      'add' => 'Drupal\event_manager\Form\EventForm',
      'edit' => 'Drupal\event_manager\Form\EventForm',
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
  admin_permission: 'administer event',
  base_table: 'event',
  data_table: 'event_field_data',
  revision_table: 'event_revision',
  revision_data_table: 'event_field_revision',
  translatable: TRUE,
  show_revision_ui: TRUE,
  field_ui_base_route: 'entity.event.settings',
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

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(new TranslatableMarkup('Created'));
    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(new TranslatableMarkup('Changed'));

    return $fields;
  }

}
