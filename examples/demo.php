
<?php

/*
* Author Pramod.Choudhary
*/

require __DIR__ . '/../vendor/autoload.php';

use WebsiteCrawler\WebsiteCrawlerClient;
use parallel\Runtime;

$apiKey = "your_api_key"; // get your api_key from websitecrawler.org
$url = "your_website_url"; // replace your_website_url with a non redirecting domain starting with https:// or http or https://www
$limit = "your_limit"; // replace "your_limit" with your preferred limit i.e. number of urls to make webistecrawler crawl


$crawler = new WebsiteCrawlerClient($apiKey);


echo "Starting crawl for $url...\n";
$response = $crawler->startCrawl($url, $limit);
print_r($response);

$runtime = new Runtime();

$future = $runtime->run(function($apiKey, $url) {
    require __DIR__ . '/../vendor/autoload.php';
    $crawler = new WebsiteCrawler\WebsiteCrawlerClient($apiKey);

    while (true) {
        $statusResponse = $crawler->getCurrentStatus($url);

        if (isset($statusResponse['status']) && $statusResponse['status'] === 'completed') {
            echo "Crawl completed in thread!\n";
            return $statusResponse;
        }

        echo "Thread polling: status = " . ($statusResponse['status'] ?? 'unknown') . "\n";
        sleep(5);
    }
}, [$apiKey, $url]);

for ($i = 0; $i < 5; $i++) {
    echo "Main script still active... iteration $i\n";
    sleep(5);
}

$status = $future->value();
print_r($status);

echo "\nFetching crawl data...\n";
$data = $crawler->getCrawlData($url);
print_r($data);


echo "\nClearing crawl job...\n";
$clear = $crawler->clearJob($url);
print_r($clear);
