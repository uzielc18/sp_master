Nuevo Kumbia y Sistema de Logueo
### Para crear eventos
CREATE EVENT `crear_inventarios` 
ON SCHEDULE EVERY 1 minute STARTS '2022-03-18 18:10:00'
ON COMPLETION NOT PRESERVE ENABLE 
DO INSERT INTO inventarios VALUES (NULL, CURDATE(), 'promedio','sin formula',NOW(),NULL,'1');

#### Table 1
CREATE TABLE `inventariosdetalles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tesunidadesmedidas_id` int(10) unsigned DEFAULT NULL,
  `tesproductos_id` int(10) unsigned DEFAULT NULL,
  `investarios_id` int(10) unsigned NOT NULL,
  `cantidad` varchar(10) DEFAULT NULL,
  `preciounitario` varchar(10) DEFAULT NULL,
  `importe` varchar(20) DEFAULT NULL,
  `lote` varchar(50) DEFAULT NULL,
  `empaque` varchar(10) DEFAULT NULL,
  `bobinas` varchar(10) DEFAULT NULL,
  `pesobruto` varchar(100) DEFAULT NULL,
  `pesoneto` varchar(10) DEFAULT NULL,
  `fecha_at` datetime DEFAULT NULL,
  `fecha_in` datetime DEFAULT NULL,
  `estado` char(1) DEFAULT NULL,
  `userid` int(10) unsigned DEFAULT NULL,
  `aclempresas_id` int(11) DEFAULT NULL,
  `tescolores_id` int(11) DEFAULT NULL,
  `prorollos_id` int(11) DEFAULT '0' COMMENT 'si es diferente de cero es el id del rollo',
  `sc_calidades_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inventariosdetalles_FKIndex1` (`tesproductos_id`),
  KEY `inventariosdetalles_FKIndex2` (`tesunidadesmedidas_id`)
) ENGINE=InnoDB AUTO_INCREMENT=46460 DEFAULT CHARSET=utf8

#### Table 2
CREATE TABLE `inventarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` date DEFAULT NULL,
  `tipo` varchar(255) DEFAULT NULL,
  `formula` varchar(255) DEFAULT NULL,
  `fecha_at` datetime DEFAULT NULL,
  `fecha_in` datetime DEFAULT NULL,
  `estado` char(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8