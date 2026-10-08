<?php

namespace Drupal\event_manager\AI;

use Drupal\Core\Site\Settings;
use Psr\Log\InvalidArgumentException;

/**
 * Main client for OpenAI API.
 *
 * Acts as a facade for supported OpenAI API types.
 */
class EmOpenAiClient {

  /**
   * Responses API type.
   */
  public const RESPONSES = 'responses';

  /**
   * Chat Completions API type.
   */
  public const CHAT_COMPLETIONS = 'chat_completions';

  /**
   * OpenAI API key.
   *
   * @var string
   */
  private string $apiKey;

  /**
   * Constructs an EmOpenAiClient object.
   *
   * @param \Drupal\event_manager\AI\EmResponsesClient $responsesClient
   *   Responses API client.
   * @param \Drupal\event_manager\AI\EmChatCompletionsClient $chatCompletionsClient
   *   Chat Completions API client.
   */
  public function __construct(
    private EmResponsesClient $responsesClient,
    private EmChatCompletionsClient $chatCompletionsClient,
  ) {
    $this->apiKey = Settings::get('openai_api_key') ?: '';
    if ($this->apiKey === '') {
      throw new \RuntimeException('OPENAI_API_KEY is not configured.');
    }
  }

  /**
   * Sends a request to the selected OpenAI API.
   *
   * @param string $type
   *   API type. One of RESPONSES or CHAT_COMPLETIONS.
   * @param string $model
   *   Model name.
   * @param string $input
   *   User input.
   *
   * @return array
   *   Normalized response containing text and usage data.
   *
   * @throws \InvalidArgumentException
   *   If the API type is not supported.
   */
  public function request(
    string $type,
    string $model,
    string $input,
  ): array {

    return match ($type) {
      self::RESPONSES => $this->responsesClient->request(
        $this->apiKey,
        $model,
        $input,
      ),

      self::CHAT_COMPLETIONS => $this->chatCompletionsClient->request(
        $this->apiKey,
        $model,
        $input,
      ),

      default => throw new InvalidArgumentException(
        sprintf('Unsupported OpenAI API type: %s', $type)
      ),
    };
  }

}
