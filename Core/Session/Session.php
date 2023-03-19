<?php

declare(strict_types = 1);

namespace Core\Session;

use Core\Session\SessionInterface;
use Core\Exceptions\SessionFailedException;

class Session implements SessionInterface
{
    private string $name;
    private string $flashName;
    private string $sameSite;
    private bool $secure;
    private bool $httpOnly;

    public function __construct()
    {
        $this->name = "nazdy_session";
        $this->flashName = "nazdy_flash";
        $this->sameSite = 'lax';
        $this->secure = true;
        $this->httpOnly = true;
    }

    public function start(): void
    {
        if ($this->isActive()) {
            throw new SessionFailedException('Session has already been started');
        }

        if (headers_sent($fileName, $line)) {
            throw new SessionFailedException('Headers have already sent by ' . $fileName . ':' . $line);
        }

        session_set_cookie_params(
            [
                'secure'   => $this->secure,
                'httponly' => $this->httpOnly,
                'samesite' => $this->sameSite,
            ]
        );

        if (! empty($this->name)) {
            session_name($this->name);
        }

        if (! session_start()) {
            throw new SessionFailedException('Unable to start the session');
        }
    }

    public function save(): void
    {
        session_write_close();
    }

    public function isActive(): bool
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->has($key) ? $_SESSION[$key] : $default;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $_SESSION);
    }

    public function regenerate(): bool
    {
        return session_regenerate_id();
    }

    public function put(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public function flash(string $key, array $messages): void
    {
        $_SESSION[$this->flashName][$key] = $messages;
    }

    public function getFlash(string $key): array
    {
        $messages = $_SESSION[$this->flashName][$key] ?? [];

        unset($_SESSION[$this->flashName][$key]);

        return $messages;
    }
}