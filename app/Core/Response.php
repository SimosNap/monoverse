<?php
declare(strict_types=1);

namespace Monoverse\Core;

class Response
{
    private int $statusCode = 200;
    private array $headers = [];

    public function status(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }

    public function header(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function send(string $content): void
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $name => $value) {
            header($name . ': ' . $value);
        }

        echo $content;
    }
    
    public function text(
        string $content,
        int $statusCode = 200
    ): void {
        $this->status($statusCode);
        $this->header('Content-Type', 'text/plain; charset=UTF-8');
        $this->send($content);
    }

    public function redirect(string $url, int $statusCode = 302): void
    {
        http_response_code($statusCode);
        header('Location: ' . $url);
        exit;
    }

    public function json(array $data, int $statusCode = 200): void
    {
        $this->status($statusCode);
        $this->header('Content-Type', 'application/json; charset=utf-8');
        $this->send(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}