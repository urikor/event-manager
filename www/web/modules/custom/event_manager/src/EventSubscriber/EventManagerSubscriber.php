<?php

namespace Drupal\event_manager\EventSubscriber;

use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Subscribes to kernel events for the Event Manager module.
 */
class EventManagerSubscriber implements EventSubscriberInterface {

  /**
   * Constructs the event manager subscriber.
   */
  public function __construct(
    protected RouteMatchInterface $routeMatch,
  ) {}

  /**
   * Handles an incoming request.
   */
  public function onRequest(RequestEvent $event): void {
    // Request-specific event manager behavior can be added here.
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      KernelEvents::REQUEST => ['onRequest'],
    ];
  }

}
