<?php

namespace Drupal\event_manager\AI;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;

/**
 * Sends JSON requests to the OpenAI API.
 */
class EmOpenAiHttpClient {

  /**
   * Constructs an EmOpenAiHttpClient object.
   */
  public function __construct(
    private ClientInterface $httpClient,
    private LoggerInterface $logger,
  ) {}

  /**
   * Sends a JSON POST request and decodes the JSON response.
   *
   * @param string $endpoint
   *   The API endpoint.
   * @param array $payload
   *   The request payload.
   * @param string $apiKey
   *   The OpenAI API key.
   *
   * @return array
   *   The decoded response, or an empty array on failure.
   */
  public function post(string $endpoint, array $payload, string $apiKey): array {
    try {
      $response = $this->httpClient->request('POST', $endpoint, [
        'headers' => [
          'Authorization' => 'Bearer ' . $apiKey,
          'Content-Type' => 'application/json',
        ],
        'json' => $payload,
      ]);
    }
    catch (GuzzleException $e) {
      $this->logger->error(
        'OpenAI API request failed: @message',
        [
          '@message' => $e->getMessage(),
          'exception' => $e,
        ]
      );
      return [];
    }

    $statusCode = $response->getStatusCode();
    if ($statusCode < 200 || $statusCode >= 300) {
      $this->logger->error('OpenAI API returned HTTP status @status.', [
        '@status' => $statusCode,
      ]);
      return [];
    }

    try {
      $data = json_decode(
        $response->getBody()->getContents(),
        TRUE,
        512,
        JSON_THROW_ON_ERROR
      );
    }
    catch (\JsonException $e) {
      $this->logger->error('Could not decode the OpenAI API response: @message', [
        '@message' => $e->getMessage(),
        'exception' => $e,
      ]);
      return [];
    }

    if (!is_array($data)) {
      $this->logger->error('OpenAI API response JSON was not an object or array.');
      return [];
    }

    return $data;
  }

}
