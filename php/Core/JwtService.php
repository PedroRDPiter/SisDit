<?php
declare(strict_types=1);

require_once __DIR__ . '/ApiException.php';

final class JwtService
{
    private static function encode(array $data): string
    {
        return rtrim(strtr(base64_encode(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)), '+/', '-_'), '=');
    }

    private static function decode(string $data): array
    {
        $decoded = base64_decode(strtr($data, '-_', '+/') . str_repeat('=', (4 - strlen($data) % 4) % 4), true);
        $value = $decoded === false ? null : json_decode($decoded, true);
        if (!is_array($value)) throw new ApiException('Token JWT inválido', 401);
        return $value;
    }

    private static function secret(): string
    {
        $environment = getenv('SISDIT_JWT_SECRET');
        if (is_string($environment) && strlen($environment) >= 32) return $environment;

        $directory = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . '.private';
        $path = $directory . DIRECTORY_SEPARATOR . 'jwt.key';
        if (!is_dir($directory)) @mkdir($directory, 0750, true);
        if (!is_file($path)) {
            if (@file_put_contents($path, bin2hex(random_bytes(32)), LOCK_EX) === false) {
                throw new RuntimeException('No fue posible configurar la clave JWT');
            }
        }
        $secret = trim((string)@file_get_contents($path));
        if (strlen($secret) < 32) throw new RuntimeException('La clave JWT configurada no es segura');
        return $secret;
    }

    public static function issue(int $userId, string $role, int $ttl = 900): string
    {
        $now = time();
        $header = self::encode(['alg' => 'HS256', 'typ' => 'JWT']);
        $payload = self::encode([
            'iss' => 'sisdit', 'sub' => $userId, 'role' => $role,
            'iat' => $now, 'nbf' => $now - 5, 'exp' => $now + $ttl,
            'jti' => bin2hex(random_bytes(12)),
        ]);
        $signature = rtrim(strtr(base64_encode(hash_hmac('sha256', "$header.$payload", self::secret(), true)), '+/', '-_'), '=');
        return "$header.$payload.$signature";
    }

    public static function authenticate(array $roles = []): array
    {
        $authorization = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (!preg_match('/^Bearer\s+(.+)$/i', trim($authorization), $match)) {
            throw new ApiException('Se requiere un token Bearer', 401);
        }
        $parts = explode('.', $match[1]);
        if (count($parts) !== 3) throw new ApiException('Token JWT inválido', 401);
        [$header, $payload, $signature] = $parts;
        $expected = rtrim(strtr(base64_encode(hash_hmac('sha256', "$header.$payload", self::secret(), true)), '+/', '-_'), '=');
        if (!hash_equals($expected, $signature)) throw new ApiException('Firma JWT inválida', 401);
        $claims = self::decode($payload);
        $now = time();
        if (($claims['iss'] ?? '') !== 'sisdit' || (int)($claims['exp'] ?? 0) < $now || (int)($claims['nbf'] ?? 0) > $now) {
            throw new ApiException('Token JWT vencido o inválido', 401);
        }
        if ($roles && !in_array((string)($claims['role'] ?? ''), $roles, true)) {
            throw new ApiException('Sin permisos para este recurso', 403);
        }
        return $claims;
    }
}
