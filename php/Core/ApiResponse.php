<?php
declare(strict_types=1);

require_once __DIR__ . '/ApiException.php';
require_once __DIR__ . '/AppLogger.php';

final class ApiResponse
{
    public static function json(array $payload, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        exit;
    }

    public static function run(callable $handler): never
    {
        try {
            $result = $handler();
            self::json(is_array($result) ? $result : ['success' => true]);
        } catch (ApiException $exception) {
            AppLogger::event('warning', 'api.request_rejected', ['message' => $exception->getMessage(), 'status' => $exception->status()]);
            self::json(['success' => false, 'message' => $exception->getMessage()], $exception->status());
        } catch (Throwable $exception) {
            AppLogger::event('error', 'api.unhandled_exception', ['type' => get_class($exception), 'message' => $exception->getMessage()]);
            self::json(['success' => false, 'message' => 'Error interno del servidor'], 500);
        }
    }
}
