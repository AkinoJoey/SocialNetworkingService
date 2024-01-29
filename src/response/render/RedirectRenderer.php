<?php

namespace src\response\render;

use src\response\HTTPRenderer;

class RedirectRenderer implements HTTPRenderer
{
    private string $route;

    public function __construct(string $route)
    {
        $this->route = $route;
    }

    public function getFields(): array
    {
        $protocol = isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        return [
            'Location' => sprintf("%s://%s/%s", $protocol, $host, $this->route),
        ];
    }

    public function getContent(): string
    {
        return '';
    }
}
