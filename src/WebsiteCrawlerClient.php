/*
* Author: Pramod.Choudhary
*/

<?php
namespace WebsiteCrawler;

use GuzzleHttp\Client;

class WebsiteCrawlerClient {
    private $client;
    private $token;

    public function __construct(string $apiKey) {
        $this->client = new Client(['base_uri' => 'https://www.websitecrawler.org/api/']);
        $this->authenticate($apiKey);
    }

    private function authenticate(string $apiKey) {
        $response = $this->client->post('crawl/authenticate', [
            'json' => ['apiKey' => $apiKey]
        ]);
        $data = json_decode($response->getBody(), true);
        $this->token = $data['token'] ?? null;
    }

    private function request(string $endpoint, array $payload = []) {
        $response = $this->client->post($endpoint, [
            'headers' => ['Authorization' => "Bearer {$this->token}"],
            'json' => $payload
        ]);
        return json_decode($response->getBody(), true);
    }

    public function startCrawl(string $url, int $limit = 100) {
        return $this->request('crawl/start', ['url' => $url, 'limit' => $limit]);
    }

    public function getCrawlData(string $url) {
        return $this->request('crawl/cwdata', ['url' => $url]);
    }

    public function getCurrentUrl(string $url) {
        return $this->request('crawl/currentURL', ['url' => $url]);
    }

     public function getCurrentStatus(string $url) {
        return $this->request('crawl/status', ['url' => $url]);
    }

    public function clearJob(string $url) {
        return $this->request('crawl/clear', ['url' => $url]);
    }
}
