<?php

declare(strict_types=1);

namespace AndresD3us\prefixespro\utils;

class TimeParser
{

    public static function parse(?string $input): ?int
    {
        if ($input === null || $input === "") {
            return 0;
        }

        $units = [
            's' => 1,
            'm' => 60,
            'h' => 3600,
            'd' => 86400,
            'w' => 604800,
        ];

        $input = strtolower(trim($input));
        $total = 0;
        $valid = false;

        preg_match_all('/(\d+)([smhdw])/', $input, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $amount = (int) $match[1];
            $unit   = $match[2];
            $total += $amount * ($units[$unit] ?? 0);
            $valid  = true;
        }

        if (!$valid) {
            return null;
        }

        return $total;
    }

    public static function format(int $seconds): string
    {
        if ($seconds <= 0) {
            return "Permanent";
        }

        $parts = [];
        $units = [
            'd' => 86400,
            'h' => 3600,
            'm' => 60,
            's' => 1,
        ];

        foreach ($units as $label => $value) {
            if ($seconds >= $value) {
                $parts[] = floor($seconds / $value) . $label;
                $seconds %= $value;
            }
        }

        return implode(" ", $parts);
    }

    public static function formatExpire(int $timestamp): string
    {
        if ($timestamp === 0) {
            return "Permanent";
        }
        $remaining = $timestamp - time();
        if ($remaining <= 0) {
            return "Expired";
        }
        return self::format($remaining);
    }
}
