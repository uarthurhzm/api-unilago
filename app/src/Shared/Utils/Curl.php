<?php

namespace App\Shared\Utils;

enum CurlMethodEnum: int
{
    case GET = 0;
    case POST = 1;
}


class Curl
{
    private $ch;

    public function __construct()
    {
        $this->ch = curl_init();
    }

    private function request(string $url, string $method, array $data, array $options = []): array
    {
        curl_setopt_array($this->ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => $method,
            CURLOPT_POSTFIELDS => $data,
            ...$options
        ]);

        $response = curl_exec($this->ch);

        if ($response === false) {
            $error = curl_error($this->ch);
            curl_close($this->ch);
            throw new \Exception('cURL error: ' . $error);
        }

        $httpCode = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
        curl_close($this->ch);

        if ($httpCode >= 400) {
            throw new \Exception('HTTP error: ' . $httpCode);
        }

        $responseData = json_decode($response, true);
        return $responseData;
    }

    public function POST(string $url, array $data, array $options = []): array
    {
        return $this->request($url, CurlMethodEnum::POST->value, $data, $options);
    }

    public function GET(string $url, array $data = [], array $options = []): array
    {
        if (!empty($data)) {
            $url .= '?' . http_build_query($data);
        }
        return $this->request($url, CurlMethodEnum::GET->value, [], $options);
    }
}
