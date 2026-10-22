<?php

namespace Drupal\event_manager\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Drupal\event_manager\DataTransferObject\EventDto;
use Psr\Log\LoggerInterface;

/**
 * Provides creation flow functionality for Event Manager.
 */
final class EventManagerEventCreator {

  /**
   * Constructs an EventManagerEventCreator object.
   */
  public function __construct(
    protected EntityTypeManagerInterface $entityTypeManager,
    protected LoggerInterface $logger,
    protected ConfigFactoryInterface $configFactory,
  ) {}

  /**
   * Returns newly created Event object.
   *
   * @param \Drupal\event_manager\DataTransferObject\EventDto $eventDto
   *   Event Dto object.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   Event object.
   */
  public function createEvent(EventDto $eventDto): EntityInterface|NULL {
    try {
      $event = $this->entityTypeManager->getStorage('event')->create([
        'field_title' => $eventDto->title,
        'field_description' => $eventDto->description,
        'field_start_date' => $this->toUtc($eventDto->startDate),
        'field_end_date' => $this->toUtc($eventDto->endDate),
        'field_location' => $eventDto->location,
        'status' => $eventDto->status ?? 1,
      ]);

      /** @var \Drupal\Core\Entity\EntityConstraintViolationList $violations */
      $violations = $event->validate();
      if ($violations->count() > 0) {
        $messages = [];
        /** @var \Symfony\Component\Validator\ConstraintViolation $violation */
        foreach ($violations as $violation) {
          $messages[] = sprintf(
            '%s: %s',
            $violation->getPropertyPath(),
            $violation->getMessage(),
          );
        }

        $this->logger->warning(
          'Event validation failed: @violations',
          ['@violations' => implode('; ', $messages)],
        );

        return NULL;
      }

      $event->save();

      return $event;
    }
    catch (\Exception $e) {
      $this->logger->error(
        'Error creating Event object: @message',
        [
          '@message' => $e->getMessage(),
          'exception' => $e,
        ],
      );

      return NULL;
    }
  }

  /**
   * Returns date with timezone.
   *
   * @param string $value
   *   Date.
   *
   * @return string
   *   Date with timezone.
   *
   * @throws \DateInvalidTimeZoneException
   * @throws \DateMalformedStringException
   */
  private function toUtc(string $value): string {
    $siteTimezone = $this->configFactory
      ->get('system.date')
      ->get('timezone.default');

    $date = new \DateTimeImmutable($value, new \DateTimeZone($siteTimezone));

    return $date
      ->setTimezone(new \DateTimeZone(DateTimeItemInterface::STORAGE_TIMEZONE))
      ->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT);
  }

}
