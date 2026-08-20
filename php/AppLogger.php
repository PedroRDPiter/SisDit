<?php

final class AppLogger
{
    public static function evento(?mysqli $conn, string $accion, ?string $tabla = null, ?int $registroId = null, ?string $detalles = null, ?int $usuarioId = null): void
    {
        $contexto = ['accion' => $accion, 'tabla' => $tabla, 'registro_id' => $registroId, 'usuario_id' => $usuarioId, 'detalles' => $detalles, 'ip' => $_SERVER['REMOTE_ADDR'] ?? 'cli'];
        self::archivo('INFO', $contexto);
        if (!$conn) return;
        try {
            $stmt = $conn->prepare('INSERT INTO logs_actividad (usuario_id, accion, tabla_afectada, registro_id, detalles, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?)');
            if (!$stmt) throw new RuntimeException($conn->error);
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'desconocida';
            $agente = $_SERVER['HTTP_USER_AGENT'] ?? 'desconocido';
            $stmt->bind_param('ississs', $usuarioId, $accion, $tabla, $registroId, $detalles, $ip, $agente);
            if (!$stmt->execute()) throw new RuntimeException($stmt->error);
            $stmt->close();
        } catch (Throwable $error) {
            self::error($error, ['al_registrar_evento' => $accion]);
        }
    }

    public static function error(Throwable $error, array $contexto = []): void
    {
        $contexto += ['tipo' => get_class($error), 'mensaje' => $error->getMessage(), 'archivo' => $error->getFile(), 'linea' => $error->getLine()];
        self::archivo('ERROR', $contexto);
    }

    private static function archivo(string $nivel, array $contexto): void
    {
        $directorio = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'logs';
        if (!is_dir($directorio) && !@mkdir($directorio, 0750, true) && !is_dir($directorio)) {
            error_log('[SisDiT] No fue posible crear el directorio de logs.');
            return;
        }
        $linea = json_encode(['fecha' => date(DATE_ATOM), 'nivel' => $nivel, 'contexto' => $contexto], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($linea === false || @file_put_contents($directorio . DIRECTORY_SEPARATOR . 'app.log', $linea . PHP_EOL, FILE_APPEND | LOCK_EX) === false) {
            error_log('[SisDiT] No fue posible escribir app.log.');
        }
    }
}
