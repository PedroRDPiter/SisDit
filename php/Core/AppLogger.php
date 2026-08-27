<?php
declare(strict_types=1);

final class AppLogger
{
    public static function event(string $level, string $event, array $context = []): void
    {
        $directory = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'logs';
        if (!is_dir($directory) && !@mkdir($directory, 0750, true) && !is_dir($directory)) {
            error_log("[$level] $event " . json_encode($context, JSON_UNESCAPED_UNICODE));
            return;
        }

        foreach (['password', 'token', 'authorization', 'csrf_token'] as $secret) {
            unset($context[$secret]);
        }
        $record = [
            'timestamp' => date(DATE_ATOM),
            'level' => strtoupper($level),
            'event' => $event,
            'context' => $context,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
        ];
        @file_put_contents(
            $directory . DIRECTORY_SEPARATOR . 'app-' . date('Y-m-d') . '.log',
            json_encode($record, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL,
            FILE_APPEND | LOCK_EX
        );
    }
}
