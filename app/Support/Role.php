<?php

namespace App\Support;

/**
 * Role helper: canonical role constants and helpers to check a user's role.
 *
 * Accepts $user as array or object (the project stores user info from API in session
 * as either associative array or object-like). Use Role::isAdmin($user) etc.
 */
class Role
{
    public const ADMIN = 'quan_tri';
    public const OWNER = 'chu_tro';
    public const TENANT = 'khach_thue';

    /**
     * Return the raw role string from $user (array|object|null)
     */
    public static function name($user): string
    {
        if (!$user) return '';
        if (is_array($user)) return $user['vai_tro'] ?? '';
        if (is_object($user)) return $user->vai_tro ?? '';
        return '';
    }

    public static function isAdmin($user): bool
    {
        return self::name($user) === self::ADMIN;
    }

    public static function isOwner($user): bool
    {
        return self::name($user) === self::OWNER;
    }

    public static function isTenant($user): bool
    {
        return self::name($user) === self::TENANT;
    }
}
