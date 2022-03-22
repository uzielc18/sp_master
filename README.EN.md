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


#### consulta 

select p.id,p.detalle,p.nombrecorto,p.nombre,d.tescolores_id,d.lote, sum(d.cantidad),s.femision ,'salida' as tipo
from tesproductos as p
INNER JOIN tesdetallesalidas as d ON d.tesproductos_id=p.id
INNER JOIN tessalidas as s ON s.id=d.tessalidas_id 
AND (s.estadosalida="Pendiente" or s.estadosalida="TERMINADO" or s.estadosalida="PAGADO" or s.estadosalida="Pagado" or s.estadosalida="Enviado") 
AND s.tesdocumentos_id=15 AND s.aclempresas_id=1
WHERE ISNULL(d.prorollos_id)
group by p.id,d.tescolores_id,d.lote,s.femision
union
select p.id,p.detalle,p.nombrecorto,p.nombre,d.tescolores_id,d.lote, sum(d.cantidad),i.femision ,'ingreso' as tipo
from tesproductos as p
INNER JOIN tesdetalleingresos as d ON d.tesproductos_id=p.id
INNER JOIN tesingresos as i ON i.id = d.tesingresos_id 
AND (i.estadoingreso="PAGADO" OR i.estadoingreso="Pendiente" OR i.estadoingreso="INGRESO-CH") AND i.aclempresas_id=1 AND (i.tesdocumentos_id=15 OR i.tesdocumentos_id=27)
WHERE !ISNULL(d.cantidad) AND d.cantidad >0 AND (ISNULL(d.prorollos_id) OR d.prorollos_id=0)
group by p.id,d.tescolores_id,d.lote,i.femision
order by id,tescolores_id,femision,lote
;