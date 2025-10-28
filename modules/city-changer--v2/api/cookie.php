<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

class Cookie
{
    public function __construct() {}

    public function set($name, $value, $expires_or_options = 0, $path = "/"): string
    {
        $domain = "." . $this->getDomain();

        setcookie(
            $name,
            $value,
            $expires_or_options,
            $path,
            $domain
        );

        exit(json_encode([
            'success' => true,
            'result' => $value,
        ]));
    }

    public function get($name)
    {
        if (!in_array($name, $_COOKIE)) return;

        exit($_COOKIE[$name]);
    }

    protected function getDomain(): string
    {
        $host = $_SERVER['HTTP_HOST'];
        $parts = explode('.', $host);
        if (count($parts) > 1) {
            $domain = $parts[count($parts) - 2] . '.' . $parts[count($parts) - 1];
        } else {
            $domain = $parts[count($parts) - 1];
        }

        return $domain;
    }
}

$data = json_decode(file_get_contents('php://input'), true);

$cookie = new Cookie();

if ($data['action'] === 'set')
    $cookie->set($data['name'], $data['value']);
elseif ($data['action'] === 'get')
    $cookie->get($data['name']);
