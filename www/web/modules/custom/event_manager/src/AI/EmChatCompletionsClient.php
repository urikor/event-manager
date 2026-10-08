<?php

namespace Drupal\event_manager\AI;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;

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
   * @param \GuzzleHttp\ClientInterface $httpClient
   *   HTTP client.
   * @param \Psr\Log\LoggerInterface $logger
   *   Event Manager logger.
   */
  public function __construct(
    private ClientInterface $httpClient,
    private LoggerInterface $logger,
  ) {}

  /**
   * Sends a request to OpenAI Chat Completions API.
   *
   * @param string $apiKey
   *   OpenAI API key.
   * @param string $model
   *   Model name.
   * @param string $input
   *   User input.
   *
   * @return array
   *   Normalized response containing text and usage data.
   */
  public function request(
    string $apiKey,
    string $model,
    string $input,
  ): array {
    try {
      $response = $this->httpClient->request('POST', self::ENDPOINT, [
        'headers' => [
          'Authorization' => 'Bearer ' . $apiKey,
          'Content-Type' => 'application/json',
        ],
        'json' => [
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
        ],
      ]);

      $data = json_decode(
        $response->getBody()->getContents(),
        TRUE,
        512,
        JSON_THROW_ON_ERROR
      );
    }
    catch (GuzzleException | \JsonException $e) {
      $this->logger->error(
        'OpenAI Chat Completions API request failed: @message',
        [
          '@message' => $e->getMessage(),
          'exception' => $e,
        ]
      );

      return [];
    }

    return [
      'text' => $data['choices'][0]['message']['content'] ?? '',
      'usage' => $this->extractUsage($data),
    ];
  }

  /**
   * Extracts usage information.
   *
   * @param array $response
   *   Decoded API response.
   *
   * @return array
   *   Token usage information.
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
