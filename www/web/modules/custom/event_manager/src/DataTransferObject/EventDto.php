<?php

declare(strict_types=1);

namespace Drupal\event_manager\DataTransferObject;

/**
 * Carries event data between the Event Manager's application layers.
 */
final readonly class EventDto {

  /**
   * Constructs an event data transfer object.
   */
  public function __construct(
    public string $title,
    public string $description,
    public string $startDate,
    public string $endDate,
    public string $location,
    public ?string $status,
  ) {}

}
