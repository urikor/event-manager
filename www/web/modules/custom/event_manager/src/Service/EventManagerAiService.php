<?php

namespace Drupal\event_manager\Service;

use Drupal\event_manager\AI\EmOpenAiClient;

/**
 * Provides AI functionality for Event Manager.
 */
class EventManagerAiService {

  /**
   * Constructs an EventManagerAiService object.
   *
   * @param \Drupal\event_manager\AI\EmOpenAiClient $openAiClient
   *   OpenAI client.
   */
  public function __construct(
    private EmOpenAiClient $openAiClient,
  ) {}

  /**
   * Generates text using OpenAI Responses API.
   *
   * @param string $model
   *   Model name.
   * @param string $input
   *   User input.
   *
   * @return array
   *   Generated text and usage information.
   */
  public function generate(
    string $model,
    string $input,
  ): array {
    return $this->openAiClient->request(
      EmOpenAiClient::RESPONSES,
      $model,
      $input,
    );
  }

}
