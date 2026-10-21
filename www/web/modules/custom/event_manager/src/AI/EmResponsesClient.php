<?php

namespace Drupal\event_manager\AI;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;

/**
 * Client for OpenAI Responses API.
 */
class EmResponsesClient {

  /**
   * OpenAI Responses API endpoint.
   */
  private const ENDPOINT = 'https://api.openai.com/v1/responses';

  /**
   * Constructs an EmResponsesClient object.
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
   * Sends a request to OpenAI Responses API.
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
          'input' => $input,
          'instructions' => 'You are a helpful assistant.',
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
        'OpenAI Responses API request failed: @message',
        [
          '@message' => $e->getMessage(),
          'exception' => $e,
        ]
      );

      return [];
    }

    return [
      'text' => $this->extractOutputText($data),
      'usage' => $this->extractUsage($data),
    ];
  }

  /**
   * Extracts generated text from a Responses API response.
   *
   * @param array $response
   *   Decoded API response.
   *
   * @return string
   *   Generated text.
   */
  private function extractOutputText(array $response): string {
    foreach ($response['output'] ?? [] as $output) {
      if (($output['type'] ?? NULL) !== 'message') {
        continue;
      }

      foreach ($output['content'] ?? [] as $content) {
        if (($content['type'] ?? NULL) === 'output_text') {
          return $content['text'] ?? '';
        }
      }
    }

    return '';
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
      'input_tokens' => $usage['input_tokens'] ?? 0,
      'output_tokens' => $usage['output_tokens'] ?? 0,
      'reasoning_tokens' => $usage['output_tokens_details']['reasoning_tokens'] ?? 0,
      'total_tokens' => $usage['total_tokens'] ?? 0,
    ];
  }

}
