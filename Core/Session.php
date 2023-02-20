<?php

declare(strict_types=1);

namespace Core;

use Core\Contracts\SessionInterface;

class Session implements SessionInterface
{
    protected const FLASH_KEY = 'messages_key';

    public function __construct()
    {
        session_start();
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach ($flashMessages as $key => &$flashMessage) {
            $flashMessage['remove'] = true;
        }
        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }

    public function setFlash(string $key, string $message): void
    {
        $_SESSION[self::FLASH_KEY][$key] = [
            'remove' => false,
            'value' => $message
        ];
    }

    public function getFlash(string $key): string|bool
    {
        return $_SESSION[self::FLASH_KEY][$key]['value'] ?? false;
    }

    public function setSession(string $key, string $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function getSession(string $key): string|bool
    {
        return $_SESSION[$key] ?? false;
    }

    public function removeSession(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public function __destruct()
    {
        $this->removeFlashMessages();
    }

    private function removeFlashMessages(): void
    {
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach ($flashMessages as $key => $flashMessage) {
            if ($flashMessage['remove']) {
                unset($flashMessages[$key]);
            }
        }
        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }
}