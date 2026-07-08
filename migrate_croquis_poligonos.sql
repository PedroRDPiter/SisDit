CREATE TABLE IF NOT EXISTS `croquis_poligonos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tramite_id` int(11) NOT NULL,
  `origen` enum('catastro','dibujado','guardado','seleccionado') NOT NULL DEFAULT 'seleccionado',
  `cuenta_catastral_origen` varchar(50) DEFAULT NULL,
  `texto_poligono` text DEFAULT NULL,
  `geojson` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`geojson`)),
  `utm_vertices_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (`utm_vertices_json` IS NULL OR json_valid(`utm_vertices_json`)),
  `utm_centro_x` decimal(12,2) DEFAULT NULL,
  `utm_centro_y` decimal(12,2) DEFAULT NULL,
  `georeferencia_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (`georeferencia_json` IS NULL OR json_valid(`georeferencia_json`)),
  `croquis_archivo` varchar(255) DEFAULT NULL,
  `creado_por` int(11) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_croquis_poligonos_tramite_activo` (`tramite_id`, `activo`),
  KEY `idx_croquis_poligonos_cuenta` (`cuenta_catastral_origen`),
  KEY `idx_croquis_poligonos_creado_por` (`creado_por`),
  CONSTRAINT `fk_croquis_poligonos_tramite` FOREIGN KEY (`tramite_id`) REFERENCES `tramites` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_croquis_poligonos_usuario` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `croquis_poligonos`
  MODIFY `origen` varchar(40) NOT NULL DEFAULT 'seleccionado';

CREATE TABLE IF NOT EXISTS `croquis_poligono_detalles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `croquis_poligono_id` int(11) NOT NULL,
  `tramite_id` int(11) NOT NULL,
  `feature_uid` varchar(80) DEFAULT NULL,
  `numero_poligono` varchar(80) DEFAULT NULL,
  `origen` varchar(40) NOT NULL DEFAULT 'seleccionado',
  `cuenta_catastral_origen` varchar(50) DEFAULT NULL,
  `texto_poligono` text DEFAULT NULL,
  `geojson` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`geojson`)),
  `utm_vertices_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (`utm_vertices_json` IS NULL OR json_valid(`utm_vertices_json`)),
  `utm_centro_x` decimal(12,2) DEFAULT NULL,
  `utm_centro_y` decimal(12,2) DEFAULT NULL,
  `label_lng` decimal(11,8) DEFAULT NULL,
  `label_lat` decimal(10,8) DEFAULT NULL,
  `seleccionado` tinyint(1) NOT NULL DEFAULT 0,
  `croquis_archivo` varchar(255) DEFAULT NULL,
  `creado_por` int(11) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_croquis_detalles_tramite_activo` (`tramite_id`, `activo`),
  KEY `idx_croquis_detalles_croquis` (`croquis_poligono_id`),
  KEY `idx_croquis_detalles_feature_uid` (`feature_uid`),
  KEY `idx_croquis_detalles_numero` (`numero_poligono`),
  KEY `idx_croquis_detalles_cuenta` (`cuenta_catastral_origen`),
  KEY `idx_croquis_detalles_creado_por` (`creado_por`),
  CONSTRAINT `fk_croquis_detalles_croquis` FOREIGN KEY (`croquis_poligono_id`) REFERENCES `croquis_poligonos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_croquis_detalles_tramite` FOREIGN KEY (`tramite_id`) REFERENCES `tramites` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_croquis_detalles_usuario` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `croquis_poligono_detalles`
  ADD COLUMN IF NOT EXISTS `numero_poligono` varchar(80) DEFAULT NULL AFTER `feature_uid`,
  ADD KEY IF NOT EXISTS `idx_croquis_detalles_numero` (`numero_poligono`);
