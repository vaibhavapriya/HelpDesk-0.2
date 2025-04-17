<?php
namespace app\services;

class Auth {
    private static array|null $user = null;

    public static function set(array $userData): void {
        self::$user = $userData;
    }

    public static function user(): ?array {
        return self::$user;
    }

    public static function id(): ?int {
        return self::$user['id'] ?? null;
    }

    public static function email(): ?string {
        return self::$user['email'] ?? null;
    }

    public static function role(): ?string {
        return self::$user['role'] ?? null;
    }
}
