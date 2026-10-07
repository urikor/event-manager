<?php

namespace Drupal\event_manager\AI;

/**
 * Client for OpenAI Chat Completions API.
 */
class EmChatCompletionsClient {

  /**
   * OpenAI Chat Completions API endpoint.
   */
  private const ENDPOINT = 'https://api.openai.com/v1/chat/completions';

  /**
   * Constructs an EmChatCompletionsClient object.
   *
   * @param \Drupal\event_manager\AI\EmOpenAiHttpClient $httpClient
   *   Shared OpenAI HTTP client.
   */
  public function __construct(
    private EmOpenAiHttpClient $httpClient,
  ) {}

  /**
   * Sends a request to OpenAI Chat Completions API.
   *
   * @param string $apiKey
   *   OpenAI API key.
   * @param string $model
   *   Model name.
   * @param string $input
   *   Chat messages.
   *
   * @return array
   *   Decoded data array.
   */
  public function request(
    string $apiKey,
    string $model,
    string $input,
  ): array {
    $data = $this->httpClient->post(self::ENDPOINT, [
      'model' => $model,
      'messages' => [
        [
          'role' => 'system',
          'content' => 'You are a helpful assistant.',
        ],
        [
          'role' => 'user',
          'content' => $input,
        ],
      ],
    ], $apiKey);

    return [
      'text' => $data['choices'][0]['message']['content'] ?? '',
      'usage' => $this->extractUsage($data),
    ];
  }

  /**
   * Returns AI request usage data array.
   *
   * @param array $response
   *   AI request response.
   *
   * @return array
   *   AI request usage data array.
   */
  private function extractUsage(array $response): array {
    $usage = $response['usage'] ?? [];

    return [
      'input_tokens' => $usage['prompt_tokens'] ?? 0,
      'output_tokens' => $usage['completion_tokens'] ?? 0,
      'reasoning_tokens' => $usage['completion_tokens_details']['reasoning_tokens'] ?? 0,
      'total_tokens' => $usage['total_tokens'] ?? 0,
    ];
  }

}
