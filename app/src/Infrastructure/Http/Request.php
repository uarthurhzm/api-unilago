<?php

namespace App\Infrastructure\Http;

use App\Shared\Utils\Server;

class Request
{
    private string $method;
    private string $uri;
    private array $params = [];
    protected array $data = [];
    private array $headers = [];

    public function __construct()
    {
        $this->method = Server::REQUEST_METHOD();
        $this->uri = Server::REQUEST_URI();
        $this->headers = getallheaders() ?: [];
        $this->parseData();
    }

    private function parseData(): void
    {
        $input = file_get_contents('php://input');

        if (!empty($input)) {
            $this->data = json_decode($input, true) ?? [];
        }

        // Merge com $_POST para form-data
        $this->data = array_merge($this->data, $_POST);

        // Merge com $_GET para query parameters
        $this->data = array_merge($this->data, $_GET);

        // Merge com $_FILES para arquivos enviados
        foreach ($_FILES as $key => $file) {
            if ($file['error'] === UPLOAD_ERR_OK) {
                $this->data[$key] = $file;
            }
        }
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function get(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getHeader(string $key): ?string
    {
        return $this->headers[$key] ?? null;
    }
}
