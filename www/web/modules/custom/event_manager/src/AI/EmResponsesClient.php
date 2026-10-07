<?php

namespace Drupal\event_manager\AI;

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
   * @param \Drupal\event_manager\AI\EmOpenAiHttpClient $httpClient
   *   Shared OpenAI HTTP client.
   */
  public function __construct(
    private EmOpenAiHttpClient $httpClient,
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
   *   Decoded data array.
   */
  public function request(
    string $apiKey,
    string $model,
    string $input,
  ): array {
    $response = $this->httpClient->post(self::ENDPOINT, [
      'model' => $model,
      'input' => $input,
      'instructions' => 'You are a helpful assistant.',
    ], $apiKey);

    return [
      'text' => $this->extractOutputText($response),
      'usage' => $this->extractUsage($response),
    ];
  }

  /**
   * Extracts text from response.
   *
   * @param array $response
   *   Response output array.
   *
   * @return string
   *   Extracted text.
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
      'input_tokens' => $usage['input_tokens'] ?? 0,
      'output_tokens' => $usage['output_tokens'] ?? 0,
      'reasoning_tokens' => $usage['output_tokens_details']['reasoning_tokens'] ?? 0,
      'total_tokens' => $usage['total_tokens'] ?? 0,
    ];
  }

}
