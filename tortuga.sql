-- --------------------------------------------------------
-- Host:                         localhost
-- Server version:               5.5.49-0+deb8u1-log - (Debian)
-- Server OS:                    debian-linux-gnu
-- HeidiSQL Version:             9.3.0.4984
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping database structure for tortuga
CREATE DATABASE IF NOT EXISTS `tortuga` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `tortuga`;


-- Dumping structure for table tortuga.abonos
CREATE TABLE IF NOT EXISTS `abonos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `estampa` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.ac_visitas
CREATE TABLE IF NOT EXISTS `ac_visitas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cedula` int(11) NOT NULL,
  `user` varchar(50) CHARACTER SET utf32 NOT NULL,
  `observa` text CHARACTER SET utf32 NOT NULL,
  `estampa` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `tipo` char(1) CHARACTER SET utf32 NOT NULL,
  `control` varchar(50) CHARACTER SET utf32 NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cedula` (`cedula`),
  KEY `user` (`user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.alma
CREATE TABLE IF NOT EXISTS `alma` (
  `codigo` varchar(4) NOT NULL DEFAULT '',
  `descrip` varchar(200) DEFAULT NULL,
  `uejecuta` varchar(4) DEFAULT NULL,
  `cuenta` varchar(45) DEFAULT NULL,
  `direc` tinytext,
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.anoprox
CREATE TABLE IF NOT EXISTS `anoprox` (
  `numero` varchar(9) NOT NULL DEFAULT '',
  `fecha` date DEFAULT NULL,
  `uejecuta` char(4) DEFAULT NULL,
  `uadministra` char(4) DEFAULT NULL,
  `concepto` tinytext,
  `responsable` varchar(250) DEFAULT NULL,
  `status` char(2) DEFAULT NULL,
  `usuario` varchar(12) DEFAULT NULL COMMENT 'aa',
  PRIMARY KEY (`numero`),
  KEY `uejecuta` (`uejecuta`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Preyeccion proximo ano';

-- Data exporting was unselected.


-- Dumping structure for table tortuga.archivopatentes
CREATE TABLE IF NOT EXISTS `archivopatentes` (
  `archivo` varchar(255) DEFAULT NULL,
  `patente` varchar(255) DEFAULT NULL,
  `razon` varchar(255) DEFAULT NULL,
  `rif` varchar(255) DEFAULT NULL,
  `representante` varchar(255) DEFAULT NULL,
  `ci` varchar(255) DEFAULT NULL,
  `parroquia` varchar(255) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `cancelo` varchar(255) DEFAULT NULL,
  `negocio` varchar(255) DEFAULT NULL,
  `negocio_id` char(5) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.asientos
CREATE TABLE IF NOT EXISTS `asientos` (
  `id_comp` int(11) NOT NULL AUTO_INCREMENT,
  `fecha_comp` date DEFAULT NULL,
  `nro_asto` int(11) DEFAULT NULL,
  `nro_cta` varchar(7) DEFAULT NULL,
  `desc_asto` varchar(100) DEFAULT NULL,
  `ref_asto` char(30) DEFAULT NULL,
  `debe_asto` decimal(19,2) DEFAULT '0.00',
  `haber_asto` decimal(19,2) DEFAULT '0.00',
  `cond_asto` char(1) DEFAULT NULL,
  PRIMARY KEY (`id_comp`),
  KEY `cuenta` (`nro_cta`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.asignomi
CREATE TABLE IF NOT EXISTS `asignomi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero` int(11) DEFAULT NULL,
  `codigoadm` varchar(15) DEFAULT NULL,
  `fondo` varchar(20) DEFAULT NULL,
  `codigopres` varchar(17) DEFAULT NULL,
  `ordinal` char(3) DEFAULT NULL,
  `denominacion` tinytext,
  `monto` decimal(19,2) DEFAULT NULL,
  `opago` int(11) DEFAULT NULL,
  `status` char(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `numero` (`numero`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.asovehi
CREATE TABLE IF NOT EXISTS `asovehi` (
  `codigo` int(4) NOT NULL AUTO_INCREMENT,
  `nombre` char(50) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.audis
CREATE TABLE IF NOT EXISTS `audis` (
  `numero` int(11) NOT NULL AUTO_INCREMENT,
  `status` char(1) DEFAULT NULL,
  `tipo` varchar(12) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `estadmin` char(12) DEFAULT NULL,
  `fondo` varchar(20) DEFAULT NULL,
  `motivo` varchar(200) DEFAULT NULL,
  `total` decimal(19,2) DEFAULT NULL,
  `nrooficio` varchar(10) DEFAULT NULL,
  `faudis` date DEFAULT NULL,
  `resolu` varchar(500) DEFAULT NULL,
  `fresolu` date DEFAULT NULL,
  `uejecutora` varchar(4) DEFAULT NULL,
  `uadministra` varchar(4) DEFAULT NULL,
  PRIMARY KEY (`numero`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.ausu
CREATE TABLE IF NOT EXISTS `ausu` (
  `codigo` char(15) NOT NULL DEFAULT '',
  `nombre` char(30) DEFAULT NULL,
  `fecha` date NOT NULL DEFAULT '0000-00-00',
  `sueldoa` decimal(11,2) DEFAULT NULL,
  `sueldo` decimal(11,2) DEFAULT NULL,
  `observ1` char(46) DEFAULT NULL,
  `oberv2` char(46) DEFAULT NULL,
  PRIMARY KEY (`codigo`,`fecha`),
  KEY `codigo` (`codigo`,`fecha`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.banc
CREATE TABLE IF NOT EXISTS `banc` (
  `codbanc` varchar(5) NOT NULL DEFAULT '',
  `numcuent` varchar(25) DEFAULT NULL,
  `tbanco` char(3) DEFAULT NULL,
  `banco` varchar(200) DEFAULT NULL,
  `dire1` varchar(40) DEFAULT NULL,
  `dire2` varchar(40) DEFAULT NULL,
  `telefono` varchar(40) DEFAULT NULL,
  `nombre` varchar(200) DEFAULT NULL,
  `proxch` varchar(12) DEFAULT NULL,
  `dbporcen` decimal(5,2) DEFAULT NULL,
  `dbcta` varchar(5) DEFAULT NULL,
  `dbgas` varchar(6) DEFAULT NULL,
  `moneda` char(2) DEFAULT NULL,
  `saldo` decimal(14,2) DEFAULT NULL,
  `cuenta` varchar(50) DEFAULT NULL,
  `impucu` varchar(15) DEFAULT NULL,
  `comicu` varchar(15) DEFAULT NULL,
  `comipr` varchar(15) DEFAULT NULL,
  `gastoidb` varchar(6) DEFAULT NULL,
  `gastocom` varchar(6) DEFAULT NULL,
  `codprv` varchar(5) DEFAULT NULL,
  `depto` char(2) DEFAULT NULL,
  `sucur` char(2) DEFAULT NULL,
  `activo` char(1) DEFAULT NULL,
  `tipocta` char(1) DEFAULT NULL,
  `fapertura` date DEFAULT NULL,
  `fcierre` date DEFAULT NULL,
  `fondo` varchar(25) DEFAULT NULL,
  `fondo2` varchar(25) DEFAULT NULL,
  `cuentaac` varchar(50) DEFAULT NULL,
  `refe` text,
  `caduca` int(11) NOT NULL DEFAULT '30',
  `intervenido` text,
  `titular` mediumtext,
  `ano` varchar(4) DEFAULT NULL,
  `lmayanchocheque` decimal(19,2) DEFAULT '24.00',
  PRIMARY KEY (`codbanc`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.bancse
CREATE TABLE IF NOT EXISTS `bancse` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codbanc` varchar(10) DEFAULT NULL,
  `mes` char(2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.bcta
CREATE TABLE IF NOT EXISTS `bcta` (
  `codigo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `denominacion` varchar(200) DEFAULT NULL,
  `cuenta` varchar(20) DEFAULT NULL,
  `descrip` text,
  `tipo` char(1) DEFAULT NULL,
  `deuda` decimal(19,2) DEFAULT '0.00',
  `saldo` decimal(19,2) DEFAULT '0.00',
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.bienes
CREATE TABLE IF NOT EXISTS `bienes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero` char(4) DEFAULT NULL,
  `grupo` char(6) DEFAULT NULL,
  `subgrupo` char(6) DEFAULT NULL,
  `depen` varchar(4) DEFAULT NULL,
  `tipo` varchar(10) DEFAULT NULL,
  `descripcion` mediumtext,
  `serial1` varchar(30) DEFAULT NULL,
  `serial2` varchar(30) DEFAULT NULL,
  `marca` varchar(30) DEFAULT NULL,
  `modelo` varchar(50) DEFAULT NULL,
  `costo` decimal(19,2) DEFAULT NULL,
  `barras` char(15) DEFAULT NULL,
  `estadop` varchar(200) DEFAULT NULL,
  `denomi` varchar(200) DEFAULT NULL,
  `uso` tinytext,
  `estado` varchar(200) DEFAULT NULL,
  `municipio` varchar(200) DEFAULT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `areatot` varchar(100) DEFAULT NULL,
  `areacons` varchar(100) DEFAULT NULL,
  `npisos` varchar(100) DEFAULT NULL,
  `areatcons` varchar(100) DEFAULT NULL,
  `areaanex` varchar(100) DEFAULT NULL,
  `destruc` varchar(100) DEFAULT NULL,
  `dpisos` varchar(100) DEFAULT NULL,
  `dparedes` varchar(100) DEFAULT NULL,
  `dtechos` varchar(100) DEFAULT NULL,
  `dpuertas` varchar(100) DEFAULT NULL,
  `dventanas` varchar(100) DEFAULT NULL,
  `dservicios` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.bitacora
CREATE TABLE IF NOT EXISTS `bitacora` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `usuario` varchar(50) DEFAULT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `actividad` text,
  `comentario` text,
  `revisado` char(1) DEFAULT NULL,
  `evaluacion` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.bi_bienes
CREATE TABLE IF NOT EXISTS `bi_bienes` (
  `id` varchar(8) NOT NULL DEFAULT '',
  `codigo` varchar(20) DEFAULT NULL,
  `grupo` varchar(4) DEFAULT NULL,
  `subgrupo` varchar(4) DEFAULT NULL,
  `seccion` varchar(4) DEFAULT NULL,
  `numero` varchar(8) DEFAULT NULL,
  `descrip` tinytext,
  `alma` varchar(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.bi_conc
CREATE TABLE IF NOT EXISTS `bi_conc` (
  `codigo` char(2) NOT NULL DEFAULT '',
  `denomi` varchar(500) DEFAULT NULL,
  `tipo` char(1) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.bi_edificio
CREATE TABLE IF NOT EXISTS `bi_edificio` (
  `id` char(10) NOT NULL DEFAULT '',
  `codigo` varchar(20) DEFAULT NULL,
  `grupo` varchar(4) DEFAULT NULL,
  `subgrupo` varchar(4) DEFAULT NULL,
  `seccion` varchar(4) DEFAULT NULL,
  `numero` varchar(8) DEFAULT NULL,
  `alma` varchar(4) DEFAULT NULL,
  `monto` decimal(19,2) DEFAULT NULL,
  `expediente` char(10) DEFAULT NULL,
  `est_propietario` tinytext,
  `denominacion` tinytext,
  `uso` varchar(150) DEFAULT NULL,
  `estado` char(50) DEFAULT NULL,
  `municipio` char(50) DEFAULT NULL,
  `direccion` tinytext,
  `area_terre` char(20) DEFAULT NULL,
  `area_ocup` char(20) DEFAULT NULL,
  `num_pisos` char(3) DEFAULT NULL,
  `area_tpisos` char(20) DEFAULT NULL,
  `area_anexa` char(20) DEFAULT NULL,
  `pared_carga` char(3) DEFAULT NULL,
  `madera` char(3) DEFAULT NULL,
  `otro_estruc` char(50) DEFAULT NULL,
  `metalica` char(3) DEFAULT NULL,
  `concreto` char(3) DEFAULT NULL,
  `cemento` char(3) DEFAULT NULL,
  `tierra` char(3) DEFAULT NULL,
  `ladrillo` char(3) DEFAULT NULL,
  `granito` char(3) DEFAULT NULL,
  `mosaico` char(3) DEFAULT NULL,
  `otro_pisos` char(50) DEFAULT NULL,
  `bloques_arci` char(3) DEFAULT NULL,
  `bloques_conc` char(3) DEFAULT NULL,
  `ladrillos` char(3) DEFAULT NULL,
  `p_madera` char(3) DEFAULT NULL,
  `p_metalica` char(3) DEFAULT NULL,
  `otro_pared` char(50) DEFAULT NULL,
  `t_metalico` char(3) DEFAULT NULL,
  `asbesto` char(3) DEFAULT NULL,
  `teja_concreto` char(3) DEFAULT NULL,
  `teja_cana_ar` char(3) DEFAULT NULL,
  `platabanda` char(3) DEFAULT NULL,
  `otro_techo` char(50) DEFAULT NULL,
  `pu_madera` char(3) DEFAULT NULL,
  `pu_metalico` char(3) DEFAULT NULL,
  `sanitarios` char(3) DEFAULT NULL,
  `cocinas` char(3) DEFAULT NULL,
  `agua` char(3) DEFAULT NULL,
  `electri` char(3) DEFAULT NULL,
  `telefono` char(3) DEFAULT NULL,
  `aire_acon` char(3) DEFAULT NULL,
  `ascensores` char(3) DEFAULT NULL,
  `otro_servicios` char(50) DEFAULT NULL,
  `patios` char(3) DEFAULT NULL,
  `jardines` char(3) DEFAULT NULL,
  `estaciona` char(3) DEFAULT NULL,
  `otro_anexo` char(50) DEFAULT NULL,
  `linderos` varchar(100) DEFAULT NULL,
  `estudio_legal` varchar(200) DEFAULT NULL,
  `valor_contable` varchar(100) DEFAULT NULL,
  `fecha_adqu` date DEFAULT NULL,
  `fecha_cont` date DEFAULT NULL,
  `valor_adqu` char(20) DEFAULT NULL,
  `avaluo_pro` varchar(100) DEFAULT NULL,
  `planos` varchar(200) DEFAULT NULL,
  `valor_mejoras` char(20) DEFAULT NULL,
  `realizado` char(20) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `expediente` (`expediente`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.bi_id
CREATE TABLE IF NOT EXISTS `bi_id` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `usuario` char(10) DEFAULT NULL,
  `tipo` char(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.bi_ittrasla
CREATE TABLE IF NOT EXISTS `bi_ittrasla` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero` int(11) DEFAULT NULL,
  `descrip` tinytext,
  `bien` varchar(12) DEFAULT NULL,
  `monto` decimal(19,2) DEFAULT '0.00',
  `codigo` varchar(20) DEFAULT NULL,
  `grupo` varchar(4) DEFAULT NULL,
  `subgrupo` varchar(4) DEFAULT NULL,
  `seccion` varchar(4) DEFAULT NULL,
  `numerob` varchar(12) DEFAULT NULL,
  `concepto` char(2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.bi_linea
CREATE TABLE IF NOT EXISTS `bi_linea` (
  `grupo` char(2) DEFAULT NULL,
  `subgrupo` char(4) DEFAULT NULL,
  `seccion` char(4) DEFAULT NULL,
  `codigo` char(4) NOT NULL DEFAULT '',
  `descrip` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.bi_moto
CREATE TABLE IF NOT EXISTS `bi_moto` (
  `id` char(8) NOT NULL DEFAULT '',
  `codigo` varchar(20) DEFAULT NULL,
  `grupo` varchar(4) DEFAULT NULL,
  `subgrupo` varchar(4) DEFAULT NULL,
  `seccion` varchar(4) DEFAULT NULL,
  `numero` varchar(8) DEFAULT NULL,
  `alma` varchar(4) DEFAULT NULL,
  `monto` decimal(19,2) DEFAULT NULL,
  `expediente` char(8) DEFAULT NULL,
  `marca` char(30) DEFAULT NULL,
  `modelo` char(50) DEFAULT NULL,
  `anio` char(4) DEFAULT NULL,
  `color` char(30) DEFAULT NULL,
  `placa` char(12) DEFAULT NULL,
  `tipo` char(12) DEFAULT NULL,
  `serial_car` char(50) DEFAULT NULL,
  `serial_motor` char(50) DEFAULT NULL,
  `ubica` char(100) DEFAULT NULL,
  `depende` char(100) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `sistema_e` char(2) DEFAULT NULL,
  `bobina` char(2) DEFAULT NULL,
  `bujias` char(2) DEFAULT NULL,
  `carburador` char(2) DEFAULT NULL,
  `filtro_aire` char(2) DEFAULT NULL,
  `filtro_gaso` char(2) DEFAULT NULL,
  `motor` char(2) DEFAULT NULL,
  `regulador` char(2) DEFAULT NULL,
  `frenos_d` char(2) DEFAULT NULL,
  `frenos_t` char(2) DEFAULT NULL,
  `embrague` char(2) DEFAULT NULL,
  `pulmon` char(2) DEFAULT NULL,
  `bateria` char(2) DEFAULT NULL,
  `cambios` char(2) DEFAULT NULL,
  `memoria` char(2) DEFAULT NULL,
  `gua_c` char(2) DEFAULT NULL,
  `gua_f` char(2) DEFAULT NULL,
  `pedal` char(2) DEFAULT NULL,
  `bomba` char(2) DEFAULT NULL,
  `sistema_r` char(2) DEFAULT NULL,
  `cadena` char(2) DEFAULT NULL,
  `tacometro` char(2) DEFAULT NULL,
  `delantero` char(2) DEFAULT NULL,
  `trasero` char(2) DEFAULT NULL,
  `rin_d` char(2) DEFAULT NULL,
  `rin_t` char(2) DEFAULT NULL,
  `pito` char(2) DEFAULT NULL,
  `sirena` char(2) DEFAULT NULL,
  `reloj_t` char(2) DEFAULT NULL,
  `casco` char(2) DEFAULT NULL,
  `alarma` char(2) DEFAULT NULL,
  `levas` char(2) DEFAULT NULL,
  `bases_l` char(2) DEFAULT NULL,
  `protec_l` char(2) DEFAULT NULL,
  `tapa_acei` char(2) DEFAULT NULL,
  `tubo_esc` char(2) DEFAULT NULL,
  `tapas_lat` char(2) DEFAULT NULL,
  `latoneria` char(2) DEFAULT NULL,
  `guarda_d` char(2) DEFAULT NULL,
  `guarda_t` char(2) DEFAULT NULL,
  `tanque` char(2) DEFAULT NULL,
  `tapa_tan` char(2) DEFAULT NULL,
  `bastones` char(2) DEFAULT NULL,
  `pintura` char(2) DEFAULT NULL,
  `cruce_dd` char(2) DEFAULT NULL,
  `cruce_di` char(2) DEFAULT NULL,
  `cruce_td` char(2) DEFAULT NULL,
  `cruce_ti` char(2) DEFAULT NULL,
  `silvin` char(2) DEFAULT NULL,
  `stop_t` char(2) DEFAULT NULL,
  `cojin` char(2) DEFAULT NULL,
  `retrovisor_d` char(2) DEFAULT NULL,
  `retrovisor_i` char(2) DEFAULT NULL,
  `luces_da` char(2) DEFAULT NULL,
  `luces_db` char(2) DEFAULT NULL,
  `luces_stop` char(2) DEFAULT NULL,
  `luces_cruce_dd` char(2) DEFAULT NULL,
  `luces_cruce_di` char(2) DEFAULT NULL,
  `luces_cruce_td` char(2) DEFAULT NULL,
  `luces_cruce_ti` char(2) DEFAULT NULL,
  `estrobert` char(2) DEFAULT NULL,
  `luces_freno` char(2) DEFAULT NULL,
  `kilo` char(20) DEFAULT NULL,
  `bat_marca` char(50) DEFAULT NULL,
  `bat_serial` char(50) DEFAULT NULL,
  `estado_moto` char(2) DEFAULT NULL,
  `observa` text,
  `inspector` char(50) DEFAULT NULL,
  `conductor` char(50) DEFAULT NULL,
  `jefe_uv` char(50) DEFAULT NULL,
  `jefe_depen` char(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `expediente` (`expediente`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.bi_muebles
CREATE TABLE IF NOT EXISTS `bi_muebles` (
  `id` varchar(8) NOT NULL DEFAULT '',
  `codigo` varchar(20) DEFAULT NULL,
  `grupo` varchar(4) DEFAULT NULL,
  `subgrupo` varchar(4) DEFAULT NULL,
  `seccion` varchar(4) DEFAULT NULL,
  `linea` varchar(4) DEFAULT NULL,
  `numero` varchar(8) DEFAULT NULL,
  `descrip` tinytext,
  `alma` varchar(4) DEFAULT NULL,
  `monto` decimal(19,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.bi_seccion
CREATE TABLE IF NOT EXISTS `bi_seccion` (
  `grupo` char(2) DEFAULT NULL,
  `subgrupo` char(4) DEFAULT NULL,
  `codigo` char(4) NOT NULL DEFAULT '',
  `descrip` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.bi_subgrupo
CREATE TABLE IF NOT EXISTS `bi_subgrupo` (
  `grupo` char(2) DEFAULT NULL,
  `codigo` char(4) NOT NULL DEFAULT '',
  `descrip` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.bi_terreno
CREATE TABLE IF NOT EXISTS `bi_terreno` (
  `id` char(10) NOT NULL DEFAULT '',
  `codigo` varchar(20) DEFAULT NULL,
  `grupo` varchar(4) DEFAULT NULL,
  `subgrupo` varchar(4) DEFAULT NULL,
  `seccion` varchar(4) DEFAULT NULL,
  `numero` varchar(8) DEFAULT NULL,
  `alma` char(4) DEFAULT NULL,
  `monto` decimal(19,2) NOT NULL DEFAULT '0.00',
  `expediente` char(10) DEFAULT NULL,
  `est_propietario` char(50) DEFAULT NULL,
  `denominacion` char(100) DEFAULT NULL,
  `u_agri` char(5) DEFAULT NULL,
  `u_gana` char(5) DEFAULT NULL,
  `u_misto` char(5) DEFAULT NULL,
  `otro_uso` text,
  `municipio` char(50) DEFAULT NULL,
  `direccion` char(200) DEFAULT NULL,
  `hectarea` char(20) DEFAULT NULL,
  `metros` char(20) DEFAULT NULL,
  `area_const` char(20) DEFAULT NULL,
  `t_plana` char(20) DEFAULT NULL,
  `p_plana` char(4) DEFAULT NULL,
  `t_splana` char(20) DEFAULT NULL,
  `p_splana` char(4) DEFAULT NULL,
  `t_pendi` char(20) DEFAULT NULL,
  `p_pendi` char(4) DEFAULT NULL,
  `t_mpendi` char(20) DEFAULT NULL,
  `p_mpendi` char(4) DEFAULT NULL,
  `topo_total` char(20) DEFAULT NULL,
  `topo_ptotal` char(20) DEFAULT NULL,
  `permanencia` char(20) DEFAULT NULL,
  `p_permanencia` char(4) DEFAULT NULL,
  `a_defores` char(20) DEFAULT NULL,
  `p_defores` char(4) DEFAULT NULL,
  `bosques` char(20) DEFAULT NULL,
  `p_bosques` char(4) DEFAULT NULL,
  `incultas` char(20) DEFAULT NULL,
  `p_incultas` char(4) DEFAULT NULL,
  `no_aprove` char(20) DEFAULT NULL,
  `pno_aprove` char(4) DEFAULT NULL,
  `naturales` char(20) DEFAULT NULL,
  `p_naturales` char(4) DEFAULT NULL,
  `cultivos` char(20) DEFAULT NULL,
  `p_cultivos` char(4) DEFAULT NULL,
  `pot_total` char(20) DEFAULT NULL,
  `pot_ptotal` char(4) DEFAULT NULL,
  `rios` char(50) DEFAULT NULL,
  `manantial` char(50) DEFAULT NULL,
  `canales` char(50) DEFAULT NULL,
  `embalse` char(50) DEFAULT NULL,
  `pozo` char(50) DEFAULT NULL,
  `acued` char(50) DEFAULT NULL,
  `otro_agua` char(50) DEFAULT NULL,
  `c_long` char(20) DEFAULT NULL,
  `c_estan` char(50) DEFAULT NULL,
  `c_material` char(50) DEFAULT NULL,
  `v_interiores` char(200) DEFAULT NULL,
  `otra_bien` text,
  `linderos` text,
  `estudio_legal` text,
  `fecha_adq` date DEFAULT NULL,
  `valor_adq` char(20) DEFAULT NULL,
  `fecha_m` date DEFAULT NULL,
  `valor_m` char(20) DEFAULT NULL,
  `mejoras` text,
  `valor_conta` char(20) DEFAULT NULL,
  `avaluo` text,
  `planos` text,
  `preparado` char(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `expediente` (`expediente`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.bi_trasla
CREATE TABLE IF NOT EXISTS `bi_trasla` (
  `numero` int(11) NOT NULL AUTO_INCREMENT,
  `concepto` tinytext,
  `fecha` date DEFAULT NULL,
  `status` char(2) DEFAULT NULL,
  `envia` varchar(4) DEFAULT NULL,
  `recibe` varchar(4) DEFAULT NULL,
  `tipo` char(1) DEFAULT NULL,
  `alma` char(4) DEFAULT NULL,
  PRIMARY KEY (`numero`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.bi_vehi
CREATE TABLE IF NOT EXISTS `bi_vehi` (
  `id` char(8) NOT NULL DEFAULT '',
  `codigo` varchar(20) DEFAULT NULL,
  `grupo` varchar(4) DEFAULT NULL,
  `subgrupo` varchar(4) DEFAULT NULL,
  `seccion` varchar(4) DEFAULT NULL,
  `numero` varchar(8) DEFAULT NULL,
  `alma` varchar(4) DEFAULT NULL,
  `monto` decimal(19,2) DEFAULT NULL,
  `expediente` char(8) DEFAULT NULL,
  `marca` char(30) DEFAULT NULL,
  `modelo` char(50) DEFAULT NULL,
  `anio` char(4) DEFAULT NULL,
  `color` char(30) DEFAULT NULL,
  `placa` char(12) DEFAULT NULL,
  `tipo` char(12) DEFAULT NULL,
  `serial_car` char(50) DEFAULT NULL,
  `serial_motor` char(50) DEFAULT NULL,
  `ubica` char(100) DEFAULT NULL,
  `depende` char(100) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `arranque` char(2) DEFAULT NULL,
  `alternador` char(2) DEFAULT NULL,
  `bobina` char(2) DEFAULT NULL,
  `inyectores` char(2) DEFAULT NULL,
  `cable_distri` char(2) DEFAULT NULL,
  `distri` char(2) DEFAULT NULL,
  `bujias` char(2) DEFAULT NULL,
  `carburador` char(2) DEFAULT NULL,
  `filtro_aire` char(2) DEFAULT NULL,
  `filtro_gaso` char(2) DEFAULT NULL,
  `motor` char(2) DEFAULT NULL,
  `diferencial` char(2) DEFAULT NULL,
  `caja_veloci` char(2) DEFAULT NULL,
  `bomba_frenos` char(2) DEFAULT NULL,
  `bomba_direc` char(2) DEFAULT NULL,
  `bomba_agua` char(2) DEFAULT NULL,
  `bomba_gaso` char(2) DEFAULT NULL,
  `frenos_d` char(2) DEFAULT NULL,
  `frenos_t` char(2) DEFAULT NULL,
  `embrague` char(2) DEFAULT NULL,
  `v_aceite_m` char(2) DEFAULT NULL,
  `v_aceite_c` char(2) DEFAULT NULL,
  `radiador` char(2) DEFAULT NULL,
  `tapas_radia` char(2) DEFAULT NULL,
  `compresor` char(2) DEFAULT NULL,
  `bateria` char(2) DEFAULT NULL,
  `correas` char(2) DEFAULT NULL,
  `carter` char(2) DEFAULT NULL,
  `tren_d` char(2) DEFAULT NULL,
  `delantero_d` char(2) DEFAULT NULL,
  `delantero_i` char(2) DEFAULT NULL,
  `trasero_d` char(2) DEFAULT NULL,
  `trasero_i` char(2) DEFAULT NULL,
  `rin_dd` char(2) DEFAULT NULL,
  `rin_di` char(2) DEFAULT NULL,
  `rin_td` char(2) DEFAULT NULL,
  `rin_ti` char(2) DEFAULT NULL,
  `repuesto` char(2) DEFAULT NULL,
  `rin_repu` char(2) DEFAULT NULL,
  `trian` char(2) DEFAULT NULL,
  `gato` char(2) DEFAULT NULL,
  `llave` char(2) DEFAULT NULL,
  `radio` char(2) DEFAULT NULL,
  `repro` char(2) DEFAULT NULL,
  `corneta` char(2) DEFAULT NULL,
  `pito` char(2) DEFAULT NULL,
  `sirena` char(2) DEFAULT NULL,
  `antena` char(2) DEFAULT NULL,
  `alfombra` char(2) DEFAULT NULL,
  `cables_aux` char(2) DEFAULT NULL,
  `reloj_t` char(2) DEFAULT NULL,
  `alarma` char(2) DEFAULT NULL,
  `techo` char(2) DEFAULT NULL,
  `capo` char(2) DEFAULT NULL,
  `maleta` char(2) DEFAULT NULL,
  `pisos` char(2) DEFAULT NULL,
  `parrilla` char(2) DEFAULT NULL,
  `platinas` char(2) DEFAULT NULL,
  `puerta_dd` char(2) DEFAULT NULL,
  `puerta_di` char(2) DEFAULT NULL,
  `puerta_td` char(2) DEFAULT NULL,
  `puerta_ti` char(2) DEFAULT NULL,
  `puerta_pos` char(2) DEFAULT NULL,
  `guarda_dd` char(2) DEFAULT NULL,
  `guarda_di` char(2) DEFAULT NULL,
  `guarda_td` char(2) DEFAULT NULL,
  `guarda_ti` char(2) DEFAULT NULL,
  `para_del` char(2) DEFAULT NULL,
  `para_tra` char(2) DEFAULT NULL,
  `pintura` char(2) DEFAULT NULL,
  `cruce_dd` char(2) DEFAULT NULL,
  `cruce_di` char(2) DEFAULT NULL,
  `cruce_td` char(2) DEFAULT NULL,
  `cruce_ti` char(2) DEFAULT NULL,
  `ter_stop` char(2) DEFAULT NULL,
  `asiento_dd` char(2) DEFAULT NULL,
  `asiento_di` char(2) DEFAULT NULL,
  `asiento_tra` char(2) DEFAULT NULL,
  `t_techo` char(2) DEFAULT NULL,
  `t_puerta_dd` char(2) DEFAULT NULL,
  `t_puerta_di` char(2) DEFAULT NULL,
  `t_puerta_td` char(2) DEFAULT NULL,
  `t_puerta_ti` char(2) DEFAULT NULL,
  `t_puerta_pos` char(2) DEFAULT NULL,
  `parabrisa` char(2) DEFAULT NULL,
  `v_trasero` char(2) DEFAULT NULL,
  `v_puerta_dd` char(2) DEFAULT NULL,
  `v_puerta_di` char(2) DEFAULT NULL,
  `v_puerta_td` char(2) DEFAULT NULL,
  `v_puerta_ti` char(2) DEFAULT NULL,
  `v_puerta_pos` char(2) DEFAULT NULL,
  `lateral_dd` char(2) DEFAULT NULL,
  `lateral_di` char(2) DEFAULT NULL,
  `lateral_td` char(2) DEFAULT NULL,
  `lateral_ti` char(2) DEFAULT NULL,
  `retrovisor_d` char(2) DEFAULT NULL,
  `retrovisor_i` char(2) DEFAULT NULL,
  `retrovisor_c` char(2) DEFAULT NULL,
  `l_parabrisa_d` char(2) DEFAULT NULL,
  `l_parabrisa_i` char(2) DEFAULT NULL,
  `l_parabrisa_t` char(2) DEFAULT NULL,
  `luces_dda` char(2) DEFAULT NULL,
  `luces_ddb` char(2) DEFAULT NULL,
  `luces_dia` char(2) DEFAULT NULL,
  `luces_dib` char(2) DEFAULT NULL,
  `luces_stop_td` char(2) DEFAULT NULL,
  `luces_stop_ti` char(2) DEFAULT NULL,
  `luces_cruce_dd` char(2) DEFAULT NULL,
  `luces_cruce_di` char(2) DEFAULT NULL,
  `luces_cruce_td` char(2) DEFAULT NULL,
  `luces_cruce_ti` char(2) DEFAULT NULL,
  `luces_retro` char(2) DEFAULT NULL,
  `luces_coc` char(2) DEFAULT NULL,
  `interna` char(2) DEFAULT NULL,
  `emergencia` char(2) DEFAULT NULL,
  `luces_freno` char(2) DEFAULT NULL,
  `bat_marca` char(50) DEFAULT NULL,
  `bat_serial` char(50) DEFAULT NULL,
  `estado_vehi` char(2) DEFAULT NULL,
  `observa` text,
  `inspector` char(50) DEFAULT NULL,
  `conductor` char(50) DEFAULT NULL,
  `jefe_uv` char(50) DEFAULT NULL,
  `jefe_depen` char(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `expediente` (`expediente`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.cacc
CREATE TABLE IF NOT EXISTS `cacc` (
  `codigo` varchar(15) NOT NULL DEFAULT '',
  `nacional` char(1) DEFAULT NULL,
  `cedula` varchar(10) DEFAULT NULL,
  `fecha` date NOT NULL DEFAULT '0000-00-00',
  `hora` time NOT NULL DEFAULT '00:00:00',
  PRIMARY KEY (`codigo`,`fecha`,`hora`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.caccm
CREATE TABLE IF NOT EXISTS `caccm` (
  `codigo` varchar(15) DEFAULT NULL,
  `fecha` date NOT NULL,
  `dentra1` time NOT NULL,
  `dsali1` time NOT NULL,
  `dentra2` time NOT NULL,
  `dsali2` time NOT NULL,
  `entra1` time NOT NULL,
  `sali1` time NOT NULL,
  `entra2` time NOT NULL,
  `sali2` time NOT NULL,
  `dtotal` decimal(19,2) NOT NULL,
  `total` decimal(19,2) NOT NULL,
  `justi` decimal(19,2) NOT NULL,
  `extra` decimal(19,2) NOT NULL,
  `nregis` int(11) NOT NULL,
  KEY `codigo` (`codigo`),
  KEY `fecha` (`fecha`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.carg
CREATE TABLE IF NOT EXISTS `carg` (
  `cargo` char(8) NOT NULL DEFAULT '',
  `descrip` text,
  `sueldo` decimal(17,2) DEFAULT NULL,
  `codigoadm` varchar(25) DEFAULT NULL,
  `codigopres` varchar(25) DEFAULT NULL,
  `fondo` varchar(25) DEFAULT NULL,
  `cantidad` int(11) DEFAULT '0',
  PRIMARY KEY (`cargo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.casi
CREATE TABLE IF NOT EXISTS `casi` (
  `numero` int(11) NOT NULL AUTO_INCREMENT,
  `comprob` varchar(45) NOT NULL DEFAULT '',
  `fecha` date NOT NULL DEFAULT '0000-00-00',
  `descrip` varchar(60) DEFAULT NULL,
  `total` decimal(19,2) DEFAULT NULL,
  `debe` decimal(19,2) DEFAULT NULL,
  `haber` decimal(19,2) DEFAULT NULL,
  `status` char(2) DEFAULT NULL,
  `tipo` varchar(10) DEFAULT NULL,
  `origen` varchar(20) DEFAULT NULL,
  `transac` varchar(8) DEFAULT NULL,
  `usuario` varchar(4) DEFAULT NULL,
  `estampa` date DEFAULT NULL,
  `hora` varchar(8) DEFAULT NULL,
  PRIMARY KEY (`comprob`),
  KEY `comprorigen` (`numero`,`origen`),
  KEY `fecha` (`fecha`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.casise
CREATE TABLE IF NOT EXISTS `casise` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `ano` char(4) DEFAULT NULL,
  `mes` char(2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.catalogo
CREATE TABLE IF NOT EXISTS `catalogo` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(200) DEFAULT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `contenido` text,
  `estampa` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `id_2` (`id`,`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.caub
CREATE TABLE IF NOT EXISTS `caub` (
  `ubica` varchar(4) NOT NULL DEFAULT '',
  `ubides` varchar(30) DEFAULT NULL,
  `gasto` char(1) DEFAULT NULL,
  `cu_cost` varchar(15) DEFAULT NULL,
  `cu_caja` varchar(15) DEFAULT NULL,
  `invfis` char(1) DEFAULT NULL,
  `sucursal` char(2) DEFAULT NULL,
  `url` varchar(120) DEFAULT NULL,
  `odbc` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ubica`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.ccomunicacion
CREATE TABLE IF NOT EXISTS `ccomunicacion` (
  `idCaracter` int(2) NOT NULL AUTO_INCREMENT,
  `nombreCarter` varchar(35) DEFAULT NULL,
  `descripcion` varchar(254) DEFAULT NULL,
  PRIMARY KEY (`idCaracter`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Tabla para almacenar la lista de Caracteres de las comunic';

-- Data exporting was unselected.


-- Dumping structure for table tortuga.cdisp
CREATE TABLE IF NOT EXISTS `cdisp` (
  `numero` varchar(11) NOT NULL DEFAULT '',
  `fecha` date DEFAULT NULL,
  `ano` char(4) DEFAULT NULL,
  `uejecuta` char(8) DEFAULT NULL,
  `tipo` char(1) DEFAULT NULL,
  `uadministra` char(8) DEFAULT NULL,
  `reque` text,
  `tdisp` decimal(19,2) DEFAULT NULL,
  `tsoli` decimal(19,2) DEFAULT NULL,
  `status` char(1) DEFAULT NULL,
  `fondo` varchar(20) DEFAULT NULL,
  `fanulado` date DEFAULT NULL,
  `ffinal` date DEFAULT NULL,
  PRIMARY KEY (`numero`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.cemp
CREATE TABLE IF NOT EXISTS `cemp` (
  `row_id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` char(4) DEFAULT NULL,
  `empresa` char(40) DEFAULT NULL,
  `dire1` char(40) DEFAULT NULL,
  `dire2` char(40) DEFAULT NULL,
  `inicio` date DEFAULT NULL,
  `final` date DEFAULT NULL,
  `formato` char(17) DEFAULT NULL,
  `resultado` char(15) DEFAULT NULL,
  `patrimo` char(1) DEFAULT NULL,
  `inv_ini` char(15) DEFAULT NULL,
  `director` char(58) DEFAULT NULL,
  `rif` char(10) DEFAULT NULL,
  `nit` char(10) DEFAULT NULL,
  `ordend` char(1) DEFAULT NULL,
  `ordena` char(1) DEFAULT NULL,
  PRIMARY KEY (`row_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.civa
CREATE TABLE IF NOT EXISTS `civa` (
  `fecha` date NOT NULL DEFAULT '0000-00-00',
  `tasa` decimal(6,2) DEFAULT NULL,
  `redutasa` decimal(6,2) DEFAULT NULL,
  `sobretasa` decimal(6,2) DEFAULT NULL,
  PRIMARY KEY (`fecha`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.clase
CREATE TABLE IF NOT EXISTS `clase` (
  `codigo` varchar(10) NOT NULL DEFAULT '',
  `nombre` varchar(100) DEFAULT NULL,
  `monto` decimal(19,2) DEFAULT '0.00',
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.claseo
CREATE TABLE IF NOT EXISTS `claseo` (
  `codigo` char(1) NOT NULL DEFAULT '',
  `nombre` char(20) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.clasevehi
CREATE TABLE IF NOT EXISTS `clasevehi` (
  `codigo` int(4) NOT NULL AUTO_INCREMENT,
  `nombre` char(50) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.conc
CREATE TABLE IF NOT EXISTS `conc` (
  `concepto` char(4) NOT NULL DEFAULT '',
  `tipo` char(1) DEFAULT NULL,
  `descrip` char(35) DEFAULT NULL,
  `aplica` char(3) DEFAULT NULL,
  `grupo` char(4) DEFAULT NULL,
  `encab1` char(12) DEFAULT NULL,
  `encab2` char(12) DEFAULT NULL,
  `formula` char(150) DEFAULT NULL,
  `cuenta` char(15) DEFAULT NULL,
  `contra` char(15) DEFAULT NULL,
  `tipod` char(1) DEFAULT NULL,
  `ctade` char(6) DEFAULT NULL,
  `codigopres` varchar(17) DEFAULT NULL,
  `codigoadm` varchar(25) DEFAULT NULL,
  `fondo` varchar(25) DEFAULT NULL,
  `ordinal` char(3) DEFAULT NULL,
  `tipoa` char(1) DEFAULT NULL,
  `ctaac` char(6) DEFAULT NULL,
  `liquida` char(1) DEFAULT 'N',
  `cod_prov` varchar(5) DEFAULT NULL,
  `salarial` char(1) DEFAULT 'N',
  PRIMARY KEY (`concepto`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.concilia
CREATE TABLE IF NOT EXISTS `concilia` (
  `codbanc` varchar(10) NOT NULL DEFAULT '',
  `tipo_doc` char(2) NOT NULL DEFAULT '',
  `cheque` varchar(50) NOT NULL DEFAULT '',
  `fecha` date DEFAULT NULL,
  `observa` text,
  `debe` decimal(19,2) DEFAULT '0.00',
  `haber` decimal(19,2) DEFAULT '0.00',
  `saldo` decimal(19,2) DEFAULT '0.00',
  PRIMARY KEY (`codbanc`,`cheque`,`tipo_doc`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.concilia2
CREATE TABLE IF NOT EXISTS `concilia2` (
  `fecha` varchar(255) DEFAULT NULL,
  `cheque` varchar(255) DEFAULT NULL,
  `concepto` varchar(255) DEFAULT NULL,
  `ingreso` varchar(255) DEFAULT NULL,
  `egreso` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.contribu
CREATE TABLE IF NOT EXISTS `contribu` (
  `codigo` char(6) NOT NULL DEFAULT '',
  `nombre` char(100) DEFAULT NULL,
  `rifci` char(13) DEFAULT NULL,
  `nacionali` char(10) DEFAULT NULL,
  `localidad` char(2) DEFAULT NULL,
  `direccion` text,
  `telefono` char(50) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.contribuyente
CREATE TABLE IF NOT EXISTS `contribuyente` (
  `codcon` int(6) NOT NULL DEFAULT '0',
  `cedrif` char(12) DEFAULT NULL,
  `nombre` text,
  `nacionali` char(15) DEFAULT NULL,
  `direccion` text,
  `tlfhab` char(15) DEFAULT NULL,
  `tlfcel` char(15) DEFAULT NULL,
  PRIMARY KEY (`codcon`),
  UNIQUE KEY `cedrif` (`cedrif`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.COSTOS
CREATE TABLE IF NOT EXISTS `COSTOS` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `numero` varbinary(11) NOT NULL DEFAULT '',
  `alma` varchar(10) DEFAULT NULL,
  `tipo` varchar(1) NOT NULL DEFAULT '',
  `fecha` date DEFAULT NULL,
  `codigo` varchar(15) DEFAULT NULL,
  `cantidad` decimal(20,2) DEFAULT '0.00',
  `acumulado` decimal(38,2) DEFAULT '0.00',
  `precio` decimal(19,2) DEFAULT '0.00',
  `promedio` decimal(38,2) DEFAULT '0.00',
  `cant_anteri` decimal(38,2) DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.cpla
CREATE TABLE IF NOT EXISTS `cpla` (
  `codigo` varchar(25) NOT NULL DEFAULT '',
  `denominacion` varchar(250) DEFAULT NULL,
  `aplicacion` text,
  `nivel` int(4) unsigned DEFAULT NULL,
  `grupo` varchar(5) DEFAULT NULL,
  `fcreacion` date DEFAULT NULL,
  `felimina` date DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.CRS
CREATE TABLE IF NOT EXISTS `CRS` (
  `numero` int(11) NOT NULL DEFAULT '0' COMMENT 'Nro de La Orden de Compra',
  `crs` decimal(19,2) unsigned DEFAULT '0.00',
  `fdesem` date DEFAULT NULL,
  `factura` char(12) DEFAULT NULL,
  `controlfac` varchar(12) DEFAULT NULL,
  `fechafac` date DEFAULT NULL COMMENT 'fecha de la Factura',
  `fcrs` date DEFAULT NULL,
  `cod_prov` varchar(5) DEFAULT NULL,
  `codigoadm` varchar(12) DEFAULT NULL,
  `fondo` varchar(20) DEFAULT NULL,
  `proveed` varchar(5) DEFAULT NULL,
  `nombre` varchar(60) DEFAULT NULL,
  `cate` varchar(20) DEFAULT NULL,
  `copre` varchar(11) DEFAULT NULL,
  `rif` varchar(12) DEFAULT NULL,
  `tipo` char(1) DEFAULT NULL,
  `grupo` varchar(4) DEFAULT NULL,
  `gr_desc` varchar(25) DEFAULT NULL,
  `direc1` varchar(105) DEFAULT NULL,
  `direc2` varchar(105) DEFAULT NULL,
  `dcredito` decimal(3,0) DEFAULT '0',
  `despacho` decimal(3,0) DEFAULT NULL,
  `direc3` varchar(105) DEFAULT NULL,
  `telefono` varchar(40) DEFAULT NULL,
  `contacto` varchar(40) DEFAULT NULL,
  `cuenta` varchar(15) DEFAULT NULL,
  `cliente` varchar(5) DEFAULT NULL,
  `observa` text,
  `ncorto` varchar(20) DEFAULT NULL,
  `nit` varchar(12) DEFAULT NULL,
  `codigo` varchar(10) DEFAULT NULL,
  `email` varchar(30) DEFAULT NULL,
  `url` varchar(30) DEFAULT NULL,
  `ocompra` char(1) DEFAULT NULL,
  `banco1` char(3) DEFAULT NULL,
  `cuenta1` varchar(25) DEFAULT NULL,
  `banco2` char(3) DEFAULT NULL,
  `cuenta2` varchar(25) DEFAULT NULL,
  `tiva` char(1) DEFAULT NULL,
  `nomfis` varchar(200) DEFAULT NULL,
  `visita` varchar(9) DEFAULT NULL,
  `reteiva` decimal(5,2) unsigned DEFAULT NULL,
  `anti` decimal(19,2) unsigned DEFAULT NULL,
  `demos` decimal(19,2) unsigned DEFAULT NULL,
  `maximo` decimal(19,2) unsigned DEFAULT NULL,
  `rnc` varchar(50) DEFAULT NULL,
  `activo` varchar(1) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.cuentas
CREATE TABLE IF NOT EXISTS `cuentas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` varchar(20) DEFAULT NULL,
  `codigo1` varchar(20) DEFAULT NULL,
  `codigo2` varchar(20) DEFAULT NULL,
  `cuenta1` varchar(25) DEFAULT NULL,
  `cuenta2` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.cuentasnuevas
CREATE TABLE IF NOT EXISTS `cuentasnuevas` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `a2012` varchar(50) DEFAULT NULL,
  `a2013` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.d094
CREATE TABLE IF NOT EXISTS `d094` (
  `codigo` varchar(45) NOT NULL DEFAULT '',
  `denominacion` varchar(250) DEFAULT NULL,
  `monto` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.d108de
CREATE TABLE IF NOT EXISTS `d108de` (
  `codigo` varchar(50) DEFAULT NULL,
  `denomi` varchar(250) DEFAULT NULL,
  `monto` varchar(250) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.d108para
CREATE TABLE IF NOT EXISTS `d108para` (
  `codigo` varchar(50) DEFAULT NULL,
  `denomi` varchar(250) DEFAULT NULL,
  `monto` varchar(250) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.DBFSIPRED01
CREATE TABLE IF NOT EXISTS `DBFSIPRED01` (
  `SECTOR` text,
  `PROGRA` text,
  `SUBPRO` text,
  `NIVJER` text,
  `DENOMI` text,
  `MORDIN` text,
  `MCOORD` text,
  `MTOTAL` text,
  `UNIDEJ1` text,
  `UNIDEJ2` text,
  `NPRACT` text,
  `deleted` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.DBFSIPRED03
CREATE TABLE IF NOT EXISTS `DBFSIPRED03` (
  `SECTOR` text,
  `PROGRA` text,
  `SUBPRO` text,
  `PROYEC` text,
  `ACTIVI` text,
  `NIVJER` text,
  `DENOMI` text,
  `UNIDEJ` text,
  `UNIDEJ2` text,
  `ESPEC1` text,
  `ESPEC2` text,
  `ESPEC3` text,
  `deleted` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.DBFSIPRED08
CREATE TABLE IF NOT EXISTS `DBFSIPRED08` (
  `SECTOR` text,
  `PROGRA` text,
  `SUBPRO` text,
  `PROYEC` text,
  `ACTIVI` text,
  `CODPAR` text,
  `ORDINA` text,
  `SUBORD` text,
  `NIVJER` text,
  `DENOMI` text,
  `PARMOV` text,
  `ASILEY` text,
  `DEFICI` text,
  `CODORG` text,
  `CODTER` text,
  `APTORG` text,
  `APTMUN` text,
  `MARCA` text,
  `CODIGO` text,
  `OBSERV` text,
  `deleted` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.decreto51
CREATE TABLE IF NOT EXISTS `decreto51` (
  `codigo` varchar(100) DEFAULT NULL,
  `denominacion` varchar(100) DEFAULT NULL,
  `monto` varchar(100) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.depa
CREATE TABLE IF NOT EXISTS `depa` (
  `division` char(8) NOT NULL DEFAULT '',
  `descrip` char(30) DEFAULT NULL,
  `departa` char(8) NOT NULL DEFAULT '',
  `depadesc` char(30) DEFAULT NULL,
  `enlace` char(3) DEFAULT NULL,
  PRIMARY KEY (`division`,`departa`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.dept
CREATE TABLE IF NOT EXISTS `dept` (
  `codigo` char(2) NOT NULL DEFAULT '',
  `departam` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.desem
CREATE TABLE IF NOT EXISTS `desem` (
  `numero` int(11) NOT NULL AUTO_INCREMENT,
  `cod_prov` varchar(5) DEFAULT NULL,
  `fdesem` date DEFAULT NULL,
  `monto` decimal(19,2) DEFAULT '0.00',
  `status` char(2) DEFAULT NULL,
  `total` decimal(19,2) DEFAULT '0.00',
  `totalch` decimal(19,2) DEFAULT '0.00',
  `id` int(11) DEFAULT NULL,
  `tcrs` decimal(19,2) DEFAULT NULL,
  `ttimbre` decimal(19,2) DEFAULT NULL,
  `tmunicipal` decimal(19,2) DEFAULT NULL,
  `tislr` decimal(19,2) DEFAULT NULL,
  `triva` decimal(19,2) DEFAULT NULL,
  `total2` decimal(19,2) DEFAULT NULL,
  `otrasrete` decimal(19,2) DEFAULT NULL,
  PRIMARY KEY (`numero`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.divi
CREATE TABLE IF NOT EXISTS `divi` (
  `division` char(8) NOT NULL DEFAULT '',
  `descrip` char(30) DEFAULT NULL,
  `codigoadm` varchar(20) DEFAULT NULL,
  `fondo` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`division`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.doc_campos
CREATE TABLE IF NOT EXISTS `doc_campos` (
  `tabla` varchar(64) NOT NULL DEFAULT '',
  `campo` varchar(64) NOT NULL DEFAULT '',
  `type` varchar(45) DEFAULT NULL,
  `null` varchar(45) DEFAULT NULL,
  `key` varchar(45) DEFAULT NULL,
  `default` varchar(45) DEFAULT NULL,
  `extra` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`tabla`,`campo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.doc_modulos
CREATE TABLE IF NOT EXISTS `doc_modulos` (
  `modulo` varchar(10) NOT NULL DEFAULT '',
  `referen` mediumtext,
  `estado` varchar(20) DEFAULT NULL,
  `ubicacion` varchar(50) DEFAULT NULL,
  `grafico` mediumtext,
  `implementacion` mediumtext,
  PRIMARY KEY (`modulo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.doc_tablas
CREATE TABLE IF NOT EXISTS `doc_tablas` (
  `nombre` varchar(64) NOT NULL DEFAULT '',
  `referen` longtext,
  PRIMARY KEY (`nombre`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.dpbi
CREATE TABLE IF NOT EXISTS `dpbi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(20) DEFAULT NULL,
  `ante` varchar(20) DEFAULT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `descrip` varchar(500) DEFAULT NULL,
  `cuenta` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.dpresu01
CREATE TABLE IF NOT EXISTS `dpresu01` (
  `row_id` int(11) NOT NULL AUTO_INCREMENT,
  `codorg` char(2) DEFAULT NULL,
  `sector` char(2) DEFAULT NULL,
  `progra` char(2) DEFAULT NULL,
  `subpro` char(2) DEFAULT NULL,
  `proyec` char(2) DEFAULT NULL,
  `activi` char(2) DEFAULT NULL,
  `codpar` char(13) DEFAULT NULL,
  `ordina` char(3) DEFAULT NULL,
  `subord` char(3) DEFAULT NULL,
  `denomi` char(50) DEFAULT NULL,
  `nivjer` int(11) DEFAULT NULL,
  `parmov` char(1) DEFAULT NULL,
  `codter` char(7) DEFAULT NULL,
  `secuen` int(11) DEFAULT NULL,
  `crdpre` double DEFAULT NULL,
  `crdact` double DEFAULT NULL,
  `compmes` double DEFAULT NULL,
  `causmes` double DEFAULT NULL,
  `pagomes` double DEFAULT NULL,
  `compacu` double DEFAULT NULL,
  `causacu` double DEFAULT NULL,
  `pagoacu` double DEFAULT NULL,
  `saldact` double DEFAULT NULL,
  `trasfav` double DEFAULT NULL,
  `trascon` double DEFAULT NULL,
  `crdadic` double DEFAULT NULL,
  `precomp` double DEFAULT NULL,
  `saldisp` double DEFAULT NULL,
  `marca` char(1) DEFAULT NULL,
  PRIMARY KEY (`row_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.dpresu03
CREATE TABLE IF NOT EXISTS `dpresu03` (
  `row_id` int(11) NOT NULL AUTO_INCREMENT,
  `nrocomp` char(8) DEFAULT NULL,
  `codpre` char(29) DEFAULT NULL,
  `codmov` char(2) DEFAULT NULL,
  `condmov` int(11) DEFAULT NULL,
  `fecmov` date DEFAULT NULL,
  `refmov` char(8) DEFAULT NULL,
  `concep` char(60) DEFAULT NULL,
  `concep2` char(60) DEFAULT NULL,
  `concep3` char(60) DEFAULT NULL,
  `nomben` char(32) DEFAULT NULL,
  `ceduben` char(12) DEFAULT NULL,
  `montcom` double DEFAULT NULL,
  `increme` double DEFAULT NULL,
  `disminu` double DEFAULT NULL,
  `libecom` double DEFAULT NULL,
  `saldcom` double DEFAULT NULL,
  `nopaper` int(11) DEFAULT NULL,
  `mopaper` double DEFAULT NULL,
  `fepades` date DEFAULT NULL,
  `fepahas` date DEFAULT NULL,
  `frecpag` char(2) DEFAULT NULL,
  `noanul` char(8) DEFAULT NULL,
  `noresol` char(8) DEFAULT NULL,
  `unidsol` char(50) DEFAULT NULL,
  `codprov` char(6) DEFAULT NULL,
  `nparpre` int(11) DEFAULT NULL,
  `secuen` int(11) DEFAULT NULL,
  `totrete` double DEFAULT NULL,
  `salrete` double DEFAULT NULL,
  PRIMARY KEY (`row_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.dpresu04
CREATE TABLE IF NOT EXISTS `dpresu04` (
  `row_id` int(11) NOT NULL AUTO_INCREMENT,
  `codpre` char(29) DEFAULT NULL,
  `secuen` int(11) DEFAULT NULL,
  `nromov` char(8) DEFAULT NULL,
  `codmov` char(2) DEFAULT NULL,
  `nordpag` char(8) DEFAULT NULL,
  `fecmov` date DEFAULT NULL,
  `concep` char(60) DEFAULT NULL,
  `modpre` double DEFAULT NULL,
  `crdact` double DEFAULT NULL,
  `moncomp` double DEFAULT NULL,
  `saldisp` double DEFAULT NULL,
  `moncaus` double DEFAULT NULL,
  `monpago` double DEFAULT NULL,
  `seccomp` int(11) DEFAULT NULL,
  `seccaus` int(11) DEFAULT NULL,
  `codusu` char(4) DEFAULT NULL,
  `regfec` date DEFAULT NULL,
  `reghor` char(8) DEFAULT NULL,
  PRIMARY KEY (`row_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.dpto
CREATE TABLE IF NOT EXISTS `dpto` (
  `tipo` char(1) DEFAULT NULL,
  `depto` char(3) NOT NULL DEFAULT '',
  `descrip` varchar(30) DEFAULT NULL,
  `cu_venta` varchar(15) DEFAULT NULL,
  `cu_inve` varchar(15) DEFAULT NULL,
  `cu_cost` varchar(15) DEFAULT NULL,
  `cu_devo` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`depto`),
  UNIQUE KEY `depto` (`depto`),
  KEY `depto_2` (`depto`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Departamentos de Inv';

-- Data exporting was unselected.


-- Dumping structure for table tortuga.econo
CREATE TABLE IF NOT EXISTS `econo` (
  `numero` varchar(11) NOT NULL DEFAULT '',
  `fecha` date DEFAULT NULL,
  `concepto` text,
  `total` decimal(19,2) DEFAULT '0.00',
  `status` char(2) DEFAULT 'P',
  `fondo` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`numero`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.ejemplo
CREATE TABLE IF NOT EXISTS `ejemplo` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) DEFAULT NULL,
  `naci` date NOT NULL,
  `sexo` char(1) DEFAULT NULL,
  `civil` char(1) DEFAULT NULL,
  `usuario` varchar(12) DEFAULT NULL,
  `color` varchar(12) DEFAULT NULL,
  `piel` varchar(1) DEFAULT NULL,
  `trabaja` char(1) DEFAULT NULL,
  `sueldo` decimal(19,2) NOT NULL,
  `observa` text,
  `blog` longtext,
  `modifi` date NOT NULL,
  `foto` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.est
CREATE TABLE IF NOT EXISTS `est` (
  `a` varchar(11) DEFAULT NULL,
  `d` char(50) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.estruadm
CREATE TABLE IF NOT EXISTS `estruadm` (
  `codigo` varchar(12) NOT NULL DEFAULT '',
  `denominacion` varchar(200) DEFAULT NULL,
  `tipo` char(1) DEFAULT NULL,
  `asignacion` decimal(19,2) DEFAULT '0.00',
  `aumento` decimal(19,2) DEFAULT '0.00',
  `disminucion` decimal(19,2) DEFAULT '0.00',
  `comprometido` decimal(19,2) DEFAULT '0.00',
  `causado` decimal(19,2) DEFAULT '0.00',
  `uejecutora` char(8) DEFAULT NULL COMMENT 'Unidad Ejecutora',
  `fresponsable` char(100) DEFAULT NULL COMMENT 'Funcionario Responsable',
  `descripcion` mediumtext COMMENT 'Descripcion',
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.estruadmresp
CREATE TABLE IF NOT EXISTS `estruadmresp` (
  `codigo` varchar(12) NOT NULL DEFAULT '',
  `denominacion` varchar(200) DEFAULT NULL,
  `tipo` char(1) DEFAULT NULL,
  `asignacion` decimal(19,2) DEFAULT '0.00',
  `aumento` decimal(19,2) DEFAULT '0.00',
  `disminucion` decimal(19,2) DEFAULT '0.00',
  `comprometido` decimal(19,2) DEFAULT '0.00',
  `causado` decimal(19,2) DEFAULT '0.00',
  `uejecutora` char(4) DEFAULT NULL COMMENT 'Unidad Ejecutora',
  `fresponsable` char(100) DEFAULT NULL COMMENT 'Funcionario Responsable',
  `descripcion` mediumtext COMMENT 'Descripcion',
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.e_adm
CREATE TABLE IF NOT EXISTS `e_adm` (
  `row_id` int(11) NOT NULL AUTO_INCREMENT,
  `cod_part` char(20) DEFAULT NULL,
  `desc_pres` char(50) DEFAULT NULL,
  PRIMARY KEY (`row_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.e_adm2
CREATE TABLE IF NOT EXISTS `e_adm2` (
  `b` int(11) NOT NULL DEFAULT '0',
  `c` char(20) DEFAULT NULL,
  `d` char(50) DEFAULT NULL,
  `a` int(10) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.fondo
CREATE TABLE IF NOT EXISTS `fondo` (
  `fondo` varchar(20) NOT NULL DEFAULT '',
  `descrip` varchar(100) DEFAULT NULL,
  `cuenta` varchar(25) DEFAULT NULL,
  `cuentap` varchar(25) DEFAULT NULL,
  `partidaiva` varchar(25) DEFAULT NULL,
  `fcuenta` varchar(25) DEFAULT NULL,
  `fcuentap` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`fondo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.formatos
CREATE TABLE IF NOT EXISTS `formatos` (
  `nombre` varchar(50) NOT NULL DEFAULT '',
  `forma` longtext,
  `proteo` longtext,
  `harbour` mediumtext,
  `descrip` varchar(200) DEFAULT NULL,
  `forma1` longtext,
  PRIMARY KEY (`nombre`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.grbi
CREATE TABLE IF NOT EXISTS `grbi` (
  `grupo` char(6) NOT NULL DEFAULT '',
  `nombre` varchar(50) DEFAULT NULL,
  `cuenta` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`grupo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.grpr
CREATE TABLE IF NOT EXISTS `grpr` (
  `grupo` varchar(4) NOT NULL DEFAULT '',
  `gr_desc` varchar(25) DEFAULT NULL,
  `cuenta` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`grupo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.grup
CREATE TABLE IF NOT EXISTS `grup` (
  `grupo` varchar(4) NOT NULL DEFAULT '',
  `nom_grup` mediumtext,
  `tipo` char(1) DEFAULT NULL,
  `linea` char(2) DEFAULT NULL,
  `cu_inve` varchar(15) DEFAULT NULL,
  `cu_cost` varchar(15) DEFAULT NULL,
  `cu_venta` varchar(15) DEFAULT NULL,
  `cu_devo` varchar(15) DEFAULT NULL,
  `depto` char(2) DEFAULT NULL,
  `comision` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`grupo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.horarios
CREATE TABLE IF NOT EXISTS `horarios` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `codigo` char(4) DEFAULT NULL,
  `denomi` varchar(50) DEFAULT NULL,
  `turno` char(1) DEFAULT NULL,
  `entrada` char(4) DEFAULT NULL,
  `salida` char(4) DEFAULT NULL,
  `temporal` char(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.imp_concepto
CREATE TABLE IF NOT EXISTS `imp_concepto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `varchar` varchar(100) CHARACTER SET utf32 DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.imp_ingresos
CREATE TABLE IF NOT EXISTS `imp_ingresos` (
  `fecha` date NOT NULL,
  `concepto` varchar(100) NOT NULL,
  `monto` decimal(19,2) DEFAULT '0.00',
  PRIMARY KEY (`fecha`,`concepto`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.imp_salidas
CREATE TABLE IF NOT EXISTS `imp_salidas` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `texto` text,
  `tiempo` int(11) DEFAULT '5',
  `animacion` smallint(6) DEFAULT '1',
  `tamano` smallint(6) DEFAULT '5',
  `orden` smallint(6) DEFAULT NULL,
  `velocidad` smallint(6) DEFAULT '0',
  `condicion` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.ingmbanc
CREATE TABLE IF NOT EXISTS `ingmbanc` (
  `ingreso` int(11) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codmbanc` int(11) DEFAULT NULL,
  `numero` int(11) DEFAULT NULL,
  `codbanc` varchar(10) DEFAULT NULL,
  `tipo_doc` char(2) DEFAULT NULL,
  `cheque` mediumtext,
  `monto` decimal(19,2) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `destino` char(1) DEFAULT NULL,
  `benefi` mediumtext,
  `observa` mediumtext,
  `multiple` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.ingpresup
CREATE TABLE IF NOT EXISTS `ingpresup` (
  `codigoadm` varchar(12) NOT NULL DEFAULT '',
  `fondo` varchar(20) NOT NULL DEFAULT '',
  `codigopres` varchar(25) NOT NULL DEFAULT '',
  `estimado` decimal(19,2) DEFAULT '0.00',
  `recaudado` decimal(19,2) DEFAULT '0.00',
  `denominacion` mediumtext,
  `cuenta` varchar(25) DEFAULT NULL,
  `codigopres_r` varchar(25) DEFAULT NULL,
  `refe` mediumtext,
  `tipo` char(1) DEFAULT NULL,
  PRIMARY KEY (`codigopres`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.ingresos
CREATE TABLE IF NOT EXISTS `ingresos` (
  `numero` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `total` double(19,2) DEFAULT NULL,
  `status` varchar(2) DEFAULT NULL,
  `mbanc_id` int(11) DEFAULT NULL,
  `totalch` double(19,2) DEFAULT NULL,
  `concepto` mediumtext,
  `tbruto` decimal(19,2) DEFAULT NULL,
  `tdcto` decimal(19,2) DEFAULT NULL,
  `recibido` mediumtext,
  `fpago` varchar(50) DEFAULT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `npago` mediumtext,
  `recibo` varchar(50) DEFAULT NULL,
  `planillas` mediumtext,
  `ano` varchar(100) DEFAULT NULL,
  `mes` varchar(100) DEFAULT NULL,
  `quincena` varchar(100) DEFAULT NULL,
  `articulos` mediumtext,
  `tipod` char(2) DEFAULT 'I',
  `segun` mediumtext,
  PRIMARY KEY (`numero`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.inmueble
CREATE TABLE IF NOT EXISTS `inmueble` (
  `id` int(11) NOT NULL DEFAULT '0',
  `contribu` char(6) DEFAULT NULL,
  `ctainos` char(7) DEFAULT NULL,
  `direccion` char(50) DEFAULT NULL,
  `no_predio` char(10) DEFAULT NULL,
  `sector` char(2) DEFAULT NULL,
  `tipo_in` char(25) DEFAULT NULL,
  `no_hab` int(11) DEFAULT NULL,
  `clase` char(1) DEFAULT NULL,
  `tipo` char(1) DEFAULT NULL,
  `monto` double DEFAULT NULL,
  `registrado` char(1) DEFAULT NULL,
  `deuda` double DEFAULT NULL,
  `enero` double DEFAULT NULL,
  `febrero` double DEFAULT NULL,
  `marzo` double DEFAULT NULL,
  `abril` double DEFAULT NULL,
  `mayo` double DEFAULT NULL,
  `junio` double DEFAULT NULL,
  `julio` double DEFAULT NULL,
  `agosto` double DEFAULT NULL,
  `septiembre` double DEFAULT NULL,
  `octubre` double DEFAULT NULL,
  `noviembre` double DEFAULT NULL,
  `diciembre` double DEFAULT NULL,
  `deudacan` double DEFAULT NULL,
  `total` double DEFAULT NULL,
  `agua` double DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.inrecibo
CREATE TABLE IF NOT EXISTS `inrecibo` (
  `codigo` int(6) NOT NULL AUTO_INCREMENT,
  `cedula` char(12) DEFAULT NULL,
  `numero` char(15) DEFAULT NULL,
  `benefi` char(30) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `monto` double DEFAULT NULL,
  `direcc` char(50) DEFAULT NULL,
  `registra` char(1) DEFAULT NULL,
  `motivo` char(50) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.internet
CREATE TABLE IF NOT EXISTS `internet` (
  `nombre` varchar(20) NOT NULL DEFAULT '',
  `lista` mediumtext,
  `descrip` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`nombre`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.intramenu
CREATE TABLE IF NOT EXISTS `intramenu` (
  `modulo` varchar(10) DEFAULT NULL,
  `titulo` varchar(60) DEFAULT NULL,
  `mensaje` varchar(120) DEFAULT NULL,
  `panel` varchar(30) DEFAULT NULL,
  `ejecutar` varchar(80) DEFAULT NULL,
  `target` varchar(10) DEFAULT NULL,
  `imagen` varchar(100) DEFAULT NULL,
  `visible` char(1) DEFAULT NULL,
  `pertenece` varchar(10) DEFAULT NULL,
  `orden` tinyint(4) DEFAULT NULL,
  `ancho` int(10) unsigned DEFAULT '800',
  `alto` int(10) unsigned DEFAULT '600',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `modulo` (`modulo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.intrarepo
CREATE TABLE IF NOT EXISTS `intrarepo` (
  `nombre` varchar(71) NOT NULL DEFAULT '',
  `modulo` varchar(10) NOT NULL DEFAULT '',
  `titulo` varchar(100) DEFAULT NULL,
  `mensaje` varchar(200) DEFAULT NULL,
  `activo` char(1) DEFAULT NULL,
  PRIMARY KEY (`nombre`,`modulo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.intrasida
CREATE TABLE IF NOT EXISTS `intrasida` (
  `usuario` char(20) NOT NULL DEFAULT '',
  `id` int(12) unsigned NOT NULL,
  `modulo` varchar(10) DEFAULT NULL,
  `acceso` char(1) DEFAULT NULL,
  PRIMARY KEY (`usuario`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.invehiculo
CREATE TABLE IF NOT EXISTS `invehiculo` (
  `codvehiculo` int(6) NOT NULL AUTO_INCREMENT,
  `codclase` int(4) DEFAULT '0',
  `codmarca` int(4) DEFAULT '0',
  `asocia` int(4) DEFAULT '0',
  `tipo` char(30) DEFAULT NULL,
  `modelo` char(50) DEFAULT NULL,
  `color` char(30) DEFAULT NULL,
  `capaci` int(2) DEFAULT '0',
  `monto` double DEFAULT NULL,
  `ultanio` char(10) DEFAULT NULL,
  `serialmot` char(30) DEFAULT NULL,
  `panterior` char(20) DEFAULT NULL,
  `pactual` char(20) DEFAULT NULL,
  `anio` char(10) DEFAULT NULL,
  `peso` double DEFAULT NULL,
  `serialcar` char(30) DEFAULT NULL,
  `deudant` double DEFAULT NULL,
  PRIMARY KEY (`codvehiculo`),
  KEY `codclase` (`codclase`),
  KEY `codmarca` (`codmarca`),
  KEY `asocia` (`asocia`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.ISLR
CREATE TABLE IF NOT EXISTS `ISLR` (
  `ocompra` varchar(12) NOT NULL DEFAULT '',
  `numero` varchar(12) NOT NULL DEFAULT '',
  `reten` decimal(19,2) DEFAULT NULL,
  `fdesem` date DEFAULT NULL,
  `factura` char(12) DEFAULT NULL,
  `controlfac` varchar(12) DEFAULT NULL,
  `fechafac` date DEFAULT NULL,
  `fislr` date DEFAULT NULL,
  `cod_prov` varchar(5) DEFAULT NULL,
  `nombre` varchar(500) DEFAULT NULL,
  `rif` varchar(12) DEFAULT NULL,
  `codigoadm` varchar(12) DEFAULT NULL,
  `fondo` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.itabonos
CREATE TABLE IF NOT EXISTS `itabonos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `abono` int(11) NOT NULL,
  `recibo` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.itanoprox
CREATE TABLE IF NOT EXISTS `itanoprox` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero` varchar(9) DEFAULT NULL,
  `codigoadm` varchar(12) DEFAULT NULL,
  `fondo` varchar(20) DEFAULT NULL,
  `codigopres` varchar(17) DEFAULT NULL,
  `ordinal` char(3) DEFAULT NULL,
  `unidad` varchar(20) DEFAULT NULL,
  `denomi` text,
  `descrip` text,
  `descripd` text,
  `cant` decimal(19,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `numero` (`numero`),
  KEY `codigopres` (`codigopres`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.itaudis
CREATE TABLE IF NOT EXISTS `itaudis` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero` int(11) DEFAULT NULL,
  `codigopres` varchar(25) DEFAULT NULL,
  `denomi` varchar(150) DEFAULT NULL,
  `monto` decimal(19,2) DEFAULT NULL,
  `ordinal` varchar(4) DEFAULT NULL,
  `codigoadm` varchar(20) DEFAULT NULL,
  `fondo` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `numero_codigopres` (`numero`,`codigopres`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.itcasi
CREATE TABLE IF NOT EXISTS `itcasi` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL DEFAULT '0000-00-00',
  `numero` int(11) NOT NULL DEFAULT '0',
  `origen` char(20) DEFAULT NULL,
  `cuenta` char(30) DEFAULT NULL,
  `referen` mediumtext,
  `concepto` mediumtext,
  `debe` decimal(19,2) NOT NULL DEFAULT '0.00',
  `haber` decimal(19,2) NOT NULL DEFAULT '0.00',
  `ccosto` char(12) DEFAULT NULL,
  `sucursal` char(12) DEFAULT NULL,
  `comprob` varchar(30) DEFAULT NULL,
  `mbanc_id` mediumtext,
  PRIMARY KEY (`id`),
  KEY `comprob` (`comprob`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.itcdisp
CREATE TABLE IF NOT EXISTS `itcdisp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero` varchar(11) DEFAULT NULL,
  `codigoadm` varchar(12) DEFAULT NULL,
  `fondo` varchar(20) DEFAULT NULL,
  `codigopres` varchar(17) DEFAULT NULL,
  `ordinal` char(3) DEFAULT NULL,
  `disp` decimal(19,2) DEFAULT NULL,
  `soli` decimal(19,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.itecono
CREATE TABLE IF NOT EXISTS `itecono` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero` varchar(11) DEFAULT NULL,
  `codigoadm` varchar(12) DEFAULT NULL,
  `fondo` varchar(20) DEFAULT NULL,
  `codigopres` varchar(17) DEFAULT NULL,
  `monto` decimal(19,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.itfac
CREATE TABLE IF NOT EXISTS `itfac` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `numero` varchar(11) NOT NULL,
  `factura` varchar(50) DEFAULT NULL,
  `controlfac` varchar(50) DEFAULT NULL,
  `fechafac` date DEFAULT NULL,
  `ivaa` decimal(19,2) unsigned DEFAULT NULL,
  `ivag` decimal(19,4) unsigned DEFAULT NULL,
  `ivar` decimal(19,2) unsigned DEFAULT NULL,
  `exento` decimal(19,2) unsigned DEFAULT NULL,
  `subtotal` decimal(19,3) unsigned DEFAULT NULL,
  `total` decimal(19,4) unsigned DEFAULT NULL,
  `total2` decimal(19,4) unsigned DEFAULT NULL,
  `reteiva` decimal(19,2) unsigned DEFAULT '0.00',
  `reten` decimal(19,2) unsigned DEFAULT '0.00',
  `impmunicipal` decimal(19,2) unsigned DEFAULT '0.00',
  `imptimbre` decimal(19,2) unsigned DEFAULT '0.00',
  `breten` decimal(19,2) unsigned DEFAULT '0.00',
  `creten` decimal(19,2) unsigned DEFAULT '0.00',
  `nocompra` varchar(12) DEFAULT NULL,
  `uivaa` char(1) DEFAULT NULL,
  `uivag` char(1) DEFAULT NULL,
  `uivar` char(1) DEFAULT NULL,
  `ureten` char(1) DEFAULT NULL,
  `uimptimbre` char(1) DEFAULT NULL,
  `uimpmunicipal` char(1) DEFAULT NULL,
  `preteiva_prov` decimal(19,2) DEFAULT NULL,
  `basei` decimal(19,2) DEFAULT NULL,
  `otrasrete` decimal(19,2) DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.itingresos
CREATE TABLE IF NOT EXISTS `itingresos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero` int(11) DEFAULT NULL,
  `codigopres` varchar(25) DEFAULT NULL,
  `monto` decimal(19,2) DEFAULT NULL,
  `referen1` varchar(500) DEFAULT NULL,
  `referen2` varchar(500) DEFAULT NULL,
  `denomi` mediumtext,
  `bruto` decimal(19,2) DEFAULT NULL,
  `dcto` decimal(19,2) DEFAULT NULL,
  `ingreso` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.itmuebles
CREATE TABLE IF NOT EXISTS `itmuebles` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `expediente` char(10) DEFAULT NULL,
  `grupo` char(50) DEFAULT NULL,
  `subgrupo` char(50) DEFAULT NULL,
  `seccion` char(50) DEFAULT NULL,
  `num_iden` char(10) DEFAULT NULL,
  `descrip` char(10) DEFAULT NULL,
  `valor` char(10) DEFAULT NULL,
  `cantidad` int(10) DEFAULT '0',
  `color` char(20) DEFAULT NULL,
  `modelo` char(20) DEFAULT NULL,
  `codigo` char(8) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `expediente` (`expediente`),
  KEY `codigo` (`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.itnoco
CREATE TABLE IF NOT EXISTS `itnoco` (
  `codigo` char(5) NOT NULL DEFAULT '',
  `concepto` char(4) NOT NULL DEFAULT '',
  `descrip` char(35) DEFAULT NULL,
  `tipo` char(1) DEFAULT NULL,
  `grupo` char(4) DEFAULT NULL,
  `orden` int(11) DEFAULT NULL,
  PRIMARY KEY (`codigo`,`concepto`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.itocompra
CREATE TABLE IF NOT EXISTS `itocompra` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero` varchar(20) DEFAULT NULL COMMENT 'Numero de la Orden',
  `descripcion` mediumtext,
  `unidad` varchar(10) DEFAULT NULL,
  `cantidad` decimal(10,2) DEFAULT NULL,
  `precio` decimal(19,2) DEFAULT NULL,
  `importe` decimal(19,2) DEFAULT NULL,
  `iva` decimal(6,2) DEFAULT '0.00' COMMENT 'Tasa de IVA',
  `usaislr` char(1) DEFAULT NULL COMMENT 'Tasa de IVA',
  `islr` decimal(19,2) DEFAULT '0.00' COMMENT 'Tasa de IVA',
  `partida` varchar(25) DEFAULT NULL COMMENT 'Partida Presupuestaria',
  `odirect` int(10) unsigned DEFAULT NULL,
  `islrid` int(4) unsigned DEFAULT NULL,
  `preten` decimal(19,2) unsigned DEFAULT '0.00',
  `ordinal` char(4) DEFAULT NULL,
  `codigoadm` varchar(12) DEFAULT NULL,
  `fondo` varchar(20) DEFAULT NULL,
  `esiva` char(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `numero` (`numero`),
  KEY `partida` (`partida`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.itodirect
CREATE TABLE IF NOT EXISTS `itodirect` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero` varchar(12) DEFAULT NULL COMMENT 'Numero de la Orden',
  `codigoadm` varchar(15) DEFAULT NULL COMMENT 'Numero de la Orden',
  `fondo` varchar(20) DEFAULT NULL COMMENT 'Numero de la Orden',
  `partida` varchar(25) DEFAULT NULL COMMENT 'Partida Presupuestaria',
  `ordinal` varchar(4) DEFAULT NULL,
  `descripcion` varchar(80) DEFAULT NULL,
  `unidad` varchar(10) DEFAULT NULL,
  `cantidad` decimal(10,2) DEFAULT NULL,
  `precio` decimal(19,2) DEFAULT NULL,
  `importe` decimal(19,2) DEFAULT NULL,
  `iva` decimal(6,2) DEFAULT '0.00' COMMENT 'Tasa de IVA',
  `devo` decimal(19,2) DEFAULT NULL,
  `numfac` char(12) DEFAULT NULL,
  `cotrolfac` varchar(12) DEFAULT NULL,
  `fechafac` date DEFAULT NULL,
  `codprov` varchar(16) DEFAULT NULL,
  `subt` decimal(19,2) unsigned DEFAULT NULL,
  `total` decimal(19,2) unsigned DEFAULT NULL,
  `iva2` decimal(19,2) DEFAULT NULL,
  `islrid` int(4) unsigned DEFAULT NULL,
  `preten` decimal(19,2) unsigned DEFAULT NULL,
  `islr` decimal(19,2) unsigned DEFAULT NULL,
  `usaislr` char(1) DEFAULT NULL,
  `esiva` char(1) DEFAULT NULL,
  `ocompra` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.itotrabajo
CREATE TABLE IF NOT EXISTS `itotrabajo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero` varchar(20) DEFAULT NULL COMMENT 'Numero de la Orden',
  `descripcion` mediumtext,
  `unidad` varchar(10) DEFAULT NULL,
  `cantidad` decimal(10,2) DEFAULT NULL,
  `precio` decimal(19,2) DEFAULT NULL,
  `importe` decimal(19,2) DEFAULT NULL,
  `iva` decimal(6,2) DEFAULT '0.00' COMMENT 'Tasa de IVA',
  `usaislr` char(1) DEFAULT NULL COMMENT 'Tasa de IVA',
  `islr` decimal(19,2) DEFAULT '0.00' COMMENT 'Tasa de IVA',
  `partida` varchar(25) DEFAULT NULL COMMENT 'Partida Presupuestaria',
  `odirect` int(10) unsigned DEFAULT NULL,
  `islrid` int(4) unsigned DEFAULT NULL,
  `preten` decimal(19,2) unsigned DEFAULT '0.00',
  `ordinal` char(4) DEFAULT NULL,
  `codigoadm` varchar(12) DEFAULT NULL,
  `fondo` varchar(20) DEFAULT NULL,
  `esiva` char(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `numero` (`numero`),
  KEY `partida` (`partida`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.itreinte
CREATE TABLE IF NOT EXISTS `itreinte` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero` varchar(12) DEFAULT NULL,
  `codigoadm` varchar(12) DEFAULT NULL,
  `fondo` varchar(20) DEFAULT NULL,
  `codigopres` varchar(17) DEFAULT NULL,
  `ordinal` char(3) DEFAULT NULL,
  `monto` decimal(19,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `numero` (`numero`),
  KEY `codigopres` (`codigopres`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.itrendi
CREATE TABLE IF NOT EXISTS `itrendi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero` int(11) DEFAULT NULL COMMENT 'Numero de la Orden',
  `codigoadm` varchar(12) DEFAULT NULL,
  `fondo` varchar(20) DEFAULT NULL,
  `codigopres` varchar(17) DEFAULT NULL,
  `ordinal` char(3) DEFAULT NULL,
  `descripcion` varchar(80) DEFAULT NULL,
  `unidad` varchar(10) DEFAULT NULL,
  `cantidad` decimal(10,2) DEFAULT NULL,
  `precio` decimal(19,2) DEFAULT NULL,
  `importe` decimal(19,2) DEFAULT NULL,
  `iva` decimal(6,2) DEFAULT '0.00' COMMENT 'Tasa de IVA',
  `devo` decimal(19,2) DEFAULT NULL,
  `factura` char(12) DEFAULT NULL,
  `controlfac` varchar(12) DEFAULT NULL,
  `fechafac` date DEFAULT NULL,
  `cod_prov` varchar(16) DEFAULT NULL,
  `subtotal` decimal(19,2) unsigned DEFAULT NULL,
  `total` decimal(19,2) unsigned DEFAULT NULL,
  `iva2` decimal(19,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.itrequi
CREATE TABLE IF NOT EXISTS `itrequi` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `numero` int(11) DEFAULT NULL,
  `cantidad` decimal(10,2) DEFAULT NULL,
  `unidad` varchar(10) DEFAULT NULL,
  `descrip` varchar(150) DEFAULT NULL,
  `precio` decimal(19,2) DEFAULT NULL,
  `importe` decimal(19,2) DEFAULT NULL,
  `partida` varchar(17) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.itsumine
CREATE TABLE IF NOT EXISTS `itsumine` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero` int(11) NOT NULL,
  `codigo` varchar(15) DEFAULT NULL,
  `descripcion` varchar(100) DEFAULT NULL,
  `cantidad` decimal(19,2) DEFAULT NULL,
  `unidad` varchar(45) DEFAULT NULL,
  `solicitado` decimal(19,2) DEFAULT NULL,
  `costo` decimal(19,2) DEFAULT NULL,
  `temp` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `numero` (`numero`,`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.itsuminr
CREATE TABLE IF NOT EXISTS `itsuminr` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero` int(11) NOT NULL,
  `precio` decimal(19,2) unsigned DEFAULT NULL,
  `codigo` varchar(15) DEFAULT NULL,
  `descripcion` varchar(100) DEFAULT NULL,
  `cantidad` decimal(19,2) DEFAULT NULL,
  `total` decimal(19,2) unsigned DEFAULT NULL,
  `temp` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.ittrami
CREATE TABLE IF NOT EXISTS `ittrami` (
  `numero` int(11) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigoadm` varchar(12) DEFAULT NULL,
  `fondo` varchar(20) DEFAULT NULL,
  `codigopres` varchar(17) DEFAULT NULL,
  `ordinal` char(3) DEFAULT NULL,
  `descripcion` varchar(80) DEFAULT NULL,
  `importe` decimal(19,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.ittrasla
CREATE TABLE IF NOT EXISTS `ittrasla` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero` int(11) unsigned DEFAULT NULL,
  `codigoadm` varchar(15) DEFAULT NULL,
  `fondo` varchar(20) DEFAULT NULL,
  `codigopres` varchar(25) DEFAULT NULL,
  `ordinal` char(3) DEFAULT NULL,
  `denomi` varchar(150) DEFAULT NULL,
  `disminucion` decimal(19,2) DEFAULT '0.00',
  `aumento` decimal(19,2) DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.line
CREATE TABLE IF NOT EXISTS `line` (
  `linea` char(2) NOT NULL DEFAULT '',
  `descrip` char(30) DEFAULT NULL,
  `cu_cost` char(15) DEFAULT NULL,
  `cu_inve` char(15) DEFAULT NULL,
  `cu_venta` char(15) DEFAULT NULL,
  `cu_devo` char(15) DEFAULT NULL,
  `depto` char(2) DEFAULT NULL,
  PRIMARY KEY (`linea`),
  UNIQUE KEY `linea` (`linea`),
  KEY `linea_2` (`linea`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Liineas de Inventario';

-- Data exporting was unselected.


-- Dumping structure for table tortuga.local
CREATE TABLE IF NOT EXISTS `local` (
  `codigo` char(2) NOT NULL DEFAULT '',
  `nombre` char(20) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.logusu
CREATE TABLE IF NOT EXISTS `logusu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(12) DEFAULT NULL,
  `fecha` date NOT NULL DEFAULT '0000-00-00',
  `hora` time NOT NULL DEFAULT '00:00:00',
  `modulo` varchar(20) DEFAULT NULL,
  `comenta` mediumtext,
  `extra` text,
  `pasada` char(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.marc
CREATE TABLE IF NOT EXISTS `marc` (
  `marca` char(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`marca`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.marca
CREATE TABLE IF NOT EXISTS `marca` (
  `marca` char(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`marca`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.marcavehi
CREATE TABLE IF NOT EXISTS `marcavehi` (
  `codigo` int(4) NOT NULL AUTO_INCREMENT,
  `marca` char(30) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.mbanc
CREATE TABLE IF NOT EXISTS `mbanc` (
  `id` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
  `codbanc` varchar(10) DEFAULT NULL,
  `cod_prov` varchar(5) DEFAULT NULL,
  `tipo_doc` char(2) DEFAULT NULL,
  `cheque` mediumtext,
  `abonado` decimal(17,2) DEFAULT NULL,
  `tipo` char(2) DEFAULT NULL COMMENT 'A (pmov) B (odirect)  C(movi) D (devo) E (ppro)  F (opago)  R (reinte)',
  `numero` varchar(12) DEFAULT NULL COMMENT 'depende de tipo',
  `fecha` date NOT NULL DEFAULT '0000-00-00',
  `rel` varchar(1000) DEFAULT NULL,
  `fechapago` date DEFAULT NULL,
  `monto` decimal(17,2) DEFAULT NULL,
  `observa` mediumtext,
  `observa2` mediumtext,
  `status` char(2) DEFAULT NULL,
  `benefi` varchar(100) DEFAULT NULL,
  `usuario` varchar(4) DEFAULT NULL,
  `estampa` timestamp NULL DEFAULT NULL,
  `uejecutora` char(4) DEFAULT NULL,
  `devo` int(11) unsigned DEFAULT NULL,
  `periodo` date DEFAULT NULL,
  `islrid` int(10) unsigned DEFAULT NULL,
  `anulado` char(1) DEFAULT NULL,
  `anuladopor` varchar(20) DEFAULT NULL,
  `concilia` char(1) DEFAULT 'N',
  `fconcilia` date DEFAULT NULL,
  `bcta` int(10) unsigned DEFAULT NULL,
  `ffirma` date DEFAULT NULL,
  `fentrega` date DEFAULT NULL,
  `fdevo` date DEFAULT NULL,
  `fcajrecibe` date DEFAULT NULL,
  `fcajdevo` date DEFAULT NULL,
  `desem` int(11) DEFAULT NULL,
  `sta` char(3) DEFAULT NULL,
  `destino` varchar(1) DEFAULT NULL,
  `relch` int(11) DEFAULT NULL,
  `liable` char(1) DEFAULT NULL,
  `fliable` date DEFAULT NULL,
  `coding` int(11) DEFAULT NULL,
  `staing` char(1) DEFAULT NULL,
  `fecha2` date DEFAULT NULL,
  `multiple` int(11) DEFAULT NULL,
  `caduco` char(1) DEFAULT 'N',
  `pcodbanc` varchar(10) DEFAULT NULL,
  `deid` int(11) DEFAULT NULL,
  `paid` int(11) DEFAULT NULL,
  `cheque2` varchar(50) DEFAULT NULL,
  `observacaj` mediumtext,
  `estampaentrega` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `desem` (`desem`),
  KEY `cheque` (`cheque`(333))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.mbancm
CREATE TABLE IF NOT EXISTS `mbancm` (
  `numero` int(10) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(50) DEFAULT NULL,
  `fecha` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`numero`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.mbancncf
CREATE TABLE IF NOT EXISTS `mbancncf` (
  `tipo_doc` char(2) DEFAULT NULL,
  `fconcilia` date DEFAULT NULL,
  `codbanc` varchar(10) DEFAULT NULL,
  `observa` longtext,
  `cheque` longtext,
  `fecha` date NOT NULL DEFAULT '0000-00-00',
  `monto` decimal(18,2) DEFAULT NULL,
  `status` char(2) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.mbancnoc
CREATE TABLE IF NOT EXISTS `mbancnoc` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `codbanc` varchar(10) DEFAULT NULL,
  `tipo_doc` char(2) DEFAULT NULL,
  `cheque` varchar(255) DEFAULT NULL,
  `fecha` date NOT NULL DEFAULT '0000-00-00',
  `monto` decimal(17,2) DEFAULT NULL,
  `observa` mediumtext,
  `status` char(2) DEFAULT NULL,
  `usuario` varchar(4) DEFAULT NULL,
  `estampa` timestamp NULL DEFAULT NULL,
  `concilia` char(1) DEFAULT NULL,
  `fconcilia` date DEFAULT NULL,
  `bcta` int(11) DEFAULT NULL,
  `fecha2` date DEFAULT NULL,
  `concepto` text,
  `id_mbanc` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `codbanc_tipo_doc_cheque` (`codbanc`,`tipo_doc`,`cheque`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.meco
CREATE TABLE IF NOT EXISTS `meco` (
  `fondo` varchar(20) NOT NULL DEFAULT '',
  `fondonom` varchar(100) DEFAULT NULL,
  `codigoadm` varchar(20) NOT NULL DEFAULT '',
  `denoadm` varchar(200) DEFAULT NULL,
  `codigopres` varchar(254) NOT NULL DEFAULT '',
  `ordinal` varchar(4) NOT NULL DEFAULT '',
  `denopres` varchar(250) DEFAULT NULL,
  `aumento` decimal(64,2) DEFAULT NULL,
  `traslados` decimal(64,2) DEFAULT NULL,
  `disminucion` decimal(64,2) DEFAULT NULL,
  `comprometido` decimal(65,8) DEFAULT NULL,
  `causado` decimal(65,8) DEFAULT NULL,
  `opago` decimal(65,8) DEFAULT NULL,
  `pagado` decimal(65,8) DEFAULT NULL,
  `tipo` varchar(1) NOT NULL DEFAULT '',
  PRIMARY KEY (`fondo`,`codigoadm`,`codigopres`,`ordinal`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.modbus
CREATE TABLE IF NOT EXISTS `modbus` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uri` varchar(50) DEFAULT NULL,
  `idm` varchar(50) DEFAULT NULL,
  `parametros` mediumtext,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.movi
CREATE TABLE IF NOT EXISTS `movi` (
  `numero` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` char(1) DEFAULT NULL,
  `codbanc` char(2) DEFAULT NULL,
  `cod_prov` char(5) DEFAULT NULL,
  `uejecutora` char(4) DEFAULT NULL,
  `beneficiario` varchar(50) DEFAULT NULL,
  `observa` mediumtext,
  `monto` decimal(19,2) DEFAULT NULL,
  `saldo` decimal(19,2) unsigned DEFAULT '0.00',
  `fecha` date DEFAULT NULL,
  `status` char(2) DEFAULT NULL,
  PRIMARY KEY (`numero`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.muro
CREATE TABLE IF NOT EXISTS `muro` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `envia` varchar(15) DEFAULT NULL,
  `recibe` varchar(15) DEFAULT NULL,
  `mensaje` mediumtext,
  `estampa` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.nanoprox
CREATE TABLE IF NOT EXISTS `nanoprox` (
  `numero` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` char(10) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`numero`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.ncdisp
CREATE TABLE IF NOT EXISTS `ncdisp` (
  `numero` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` char(10) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`numero`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.necono
CREATE TABLE IF NOT EXISTS `necono` (
  `numero` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` char(10) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`numero`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.negocio
CREATE TABLE IF NOT EXISTS `negocio` (
  `codigo` char(5) NOT NULL DEFAULT '',
  `nombre` char(20) DEFAULT NULL,
  `monto` double DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.noco
CREATE TABLE IF NOT EXISTS `noco` (
  `codigo` char(5) NOT NULL DEFAULT '',
  `tipo` char(1) DEFAULT NULL,
  `nombre` char(40) DEFAULT NULL,
  `observa1` char(40) DEFAULT NULL,
  `observa2` char(40) DEFAULT NULL,
  `codigoadm` varchar(15) DEFAULT NULL,
  `fondo` varchar(20) DEFAULT NULL,
  `modo` char(1) DEFAULT '1' COMMENT '1 es normal, 2 los montos y partidas los toma a partir de los cargos',
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.nocompra
CREATE TABLE IF NOT EXISTS `nocompra` (
  `numero` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` char(10) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`numero`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.nocompracom
CREATE TABLE IF NOT EXISTS `nocompracom` (
  `numero` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` char(10) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`numero`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.nocompracon
CREATE TABLE IF NOT EXISTS `nocompracon` (
  `numero` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` char(10) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`numero`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.nocompraobra
CREATE TABLE IF NOT EXISTS `nocompraobra` (
  `numero` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` char(10) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`numero`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.nocompraservi
CREATE TABLE IF NOT EXISTS `nocompraservi` (
  `numero` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` char(10) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`numero`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.nocompratra
CREATE TABLE IF NOT EXISTS `nocompratra` (
  `numero` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` char(10) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`numero`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.nodirect
CREATE TABLE IF NOT EXISTS `nodirect` (
  `numero` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` char(10) CHARACTER SET utf32 DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`numero`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.nomi
CREATE TABLE IF NOT EXISTS `nomi` (
  `numero` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `descrip` varchar(500) DEFAULT NULL,
  `asig` decimal(19,2) DEFAULT NULL,
  `rete` decimal(19,2) DEFAULT NULL,
  `total` decimal(19,2) DEFAULT NULL,
  `status` char(1) DEFAULT NULL,
  `fcomprome` date DEFAULT NULL,
  `opago` varchar(12) DEFAULT NULL,
  `compromiso` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`numero`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.nomina
CREATE TABLE IF NOT EXISTS `nomina` (
  `numero` int(11) DEFAULT NULL,
  `frecuencia` char(1) DEFAULT NULL,
  `contrato` char(8) DEFAULT NULL,
  `depto` char(8) DEFAULT NULL,
  `codigo` char(15) DEFAULT NULL,
  `nombre` char(30) DEFAULT NULL,
  `concepto` char(4) DEFAULT NULL,
  `tipo` char(1) DEFAULT NULL,
  `descrip` char(35) DEFAULT NULL,
  `grupo` char(4) DEFAULT NULL,
  `formula` char(120) DEFAULT NULL,
  `monto` double DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `cuota` int(11) DEFAULT NULL,
  `cuotat` int(11) DEFAULT NULL,
  `valor` decimal(17,2) DEFAULT '0.00',
  `estampa` date DEFAULT NULL,
  `usuario` char(12) DEFAULT NULL,
  `transac` char(8) DEFAULT NULL,
  `hora` char(8) DEFAULT NULL,
  `fechap` date DEFAULT NULL,
  `trabaja` char(8) DEFAULT NULL,
  `nomi` int(11) NOT NULL AUTO_INCREMENT,
  `denomi` varchar(50) DEFAULT NULL,
  `total` decimal(19,2) NOT NULL DEFAULT '0.00',
  `modo` char(1) NOT NULL,
  `orden` int(11) DEFAULT NULL,
  `vari1` varchar(50) DEFAULT NULL,
  `vari2` varchar(50) DEFAULT NULL,
  `vari3` varchar(50) DEFAULT NULL,
  `vari4` varchar(50) DEFAULT NULL,
  `vari5` varchar(50) DEFAULT NULL,
  `vari6` varchar(50) DEFAULT NULL,
  `vari7` varchar(50) DEFAULT NULL,
  `vari8` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`nomi`),
  KEY `numero` (`numero`),
  KEY `codigo` (`codigo`),
  KEY `concepto` (`concepto`),
  KEY `fecha` (`fecha`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.notabu
CREATE TABLE IF NOT EXISTS `notabu` (
  `contrato` char(5) NOT NULL DEFAULT '',
  `ano` decimal(2,0) NOT NULL DEFAULT '0',
  `mes` decimal(2,0) NOT NULL DEFAULT '0',
  `dia` decimal(2,0) NOT NULL DEFAULT '0',
  `preaviso` decimal(5,2) DEFAULT '0.00',
  `vacacion` decimal(5,2) DEFAULT '0.00',
  `bonovaca` decimal(5,2) DEFAULT '0.00',
  `antiguedad` decimal(5,2) DEFAULT '0.00',
  `utilidades` decimal(5,2) DEFAULT '0.00',
  `prima` decimal(2,0) DEFAULT '0',
  PRIMARY KEY (`contrato`,`ano`,`mes`,`dia`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.notrabajo
CREATE TABLE IF NOT EXISTS `notrabajo` (
  `numero` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` char(10) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`numero`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.nreinte
CREATE TABLE IF NOT EXISTS `nreinte` (
  `numero` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` char(10) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`numero`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.nriva
CREATE TABLE IF NOT EXISTS `nriva` (
  `numero` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` char(10) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`numero`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.ntrami
CREATE TABLE IF NOT EXISTS `ntrami` (
  `numero` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` char(10) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`numero`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.ntransac
CREATE TABLE IF NOT EXISTS `ntransac` (
  `numero` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` char(10) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`numero`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.obra
CREATE TABLE IF NOT EXISTS `obra` (
  `numero` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `codigoadm` varchar(12) DEFAULT NULL,
  `fondo` varchar(20) DEFAULT NULL,
  `codigopres` varchar(15) DEFAULT NULL,
  `ordinal` varchar(4) DEFAULT NULL,
  `contrato` varchar(10) DEFAULT NULL,
  `cod_prov` varchar(5) DEFAULT NULL,
  `monto` decimal(19,2) unsigned DEFAULT NULL,
  `demostrado` decimal(19,2) unsigned DEFAULT '0.00',
  `fecha` date DEFAULT NULL,
  `observa` mediumtext,
  `status` char(2) DEFAULT NULL,
  `uejecutora` char(4) DEFAULT NULL,
  `reteiva_prov` decimal(19,2) unsigned DEFAULT '0.00',
  `pagado` decimal(19,2) unsigned DEFAULT '0.00',
  `anticipo` decimal(19,2) unsigned DEFAULT '0.00',
  `porcent` decimal(19,2) unsigned DEFAULT '0.00',
  `antiodirect` int(10) unsigned DEFAULT NULL,
  `pagoviejo` decimal(10,0) unsigned DEFAULT NULL,
  `fcomprome` date DEFAULT NULL,
  PRIMARY KEY (`numero`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.ocompra
CREATE TABLE IF NOT EXISTS `ocompra` (
  `numero` varchar(12) NOT NULL DEFAULT '' COMMENT 'Nro de La Orden de Compra',
  `controlord` char(12) DEFAULT NULL COMMENT 'Control Fiscal de La OC (preimpreso en el papel)',
  `fecha` date DEFAULT NULL COMMENT 'Fecha de emision de la OC',
  `tipo` char(10) DEFAULT NULL COMMENT 'tipo si es  COMPRA o SERVICO',
  `uejecutora` char(4) DEFAULT NULL COMMENT 'Unidad Ejecutora',
  `usaislr` char(1) DEFAULT NULL COMMENT 'Unidad Ejecutora',
  `usolicita` char(4) DEFAULT NULL COMMENT 'Unidad Solicitante',
  `estadmin` varchar(12) DEFAULT NULL,
  `fondo` varchar(20) DEFAULT NULL COMMENT 'Origen de Fondo',
  `cod_prov` char(5) DEFAULT NULL COMMENT 'Proveedor',
  `nombre` varchar(80) DEFAULT NULL,
  `beneficiario` varchar(50) DEFAULT NULL COMMENT 'pago a nombre del beneficiario',
  `factura` char(12) DEFAULT NULL COMMENT 'Numero de Factura',
  `controlfac` varchar(12) DEFAULT NULL COMMENT 'Control Fiscal de la factura',
  `fechapago` date DEFAULT NULL,
  `fechafac` date DEFAULT NULL COMMENT 'fecha de la Factura',
  `subtotal` decimal(19,2) DEFAULT '0.00',
  `exento` decimal(19,2) DEFAULT '0.00',
  `odirect` int(10) unsigned DEFAULT NULL,
  `ivag` decimal(19,2) DEFAULT '0.00' COMMENT 'Iva Tasa General',
  `tivag` decimal(19,2) unsigned DEFAULT '0.00',
  `mivag` decimal(19,2) unsigned DEFAULT '0.00',
  `ivar` decimal(19,2) DEFAULT '0.00' COMMENT 'Iva tasa reducida',
  `tivar` decimal(19,2) unsigned DEFAULT '0.00',
  `mivar` decimal(19,2) unsigned DEFAULT '0.00',
  `ivaa` decimal(19,2) DEFAULT '0.00' COMMENT 'Iva Tasa Adicional',
  `tivaa` decimal(19,2) unsigned DEFAULT '0.00',
  `mivaa` decimal(19,2) unsigned DEFAULT '0.00',
  `total` decimal(19,2) DEFAULT '0.00' COMMENT 'total orden',
  `abonado` decimal(19,2) unsigned DEFAULT '0.00',
  `creten` char(4) DEFAULT NULL COMMENT 'Codigo de Retencion de ISLR',
  `breten` decimal(19,2) DEFAULT '0.00' COMMENT 'Base de Retencion',
  `alireten` decimal(19,2) unsigned DEFAULT NULL,
  `susreten` decimal(19,2) unsigned DEFAULT NULL,
  `reteiva` decimal(19,2) DEFAULT '0.00' COMMENT 'Retencion de IVA',
  `reten` decimal(19,2) DEFAULT '0.00' COMMENT 'Retencion de ISLR',
  `observa` longtext COMMENT 'Observaciones',
  `anulado` date DEFAULT NULL COMMENT 'Fecha de Anulacion',
  `status` char(1) DEFAULT NULL COMMENT 'en Proceso, Comprometida, Anulado',
  `user_comp` varchar(30) DEFAULT NULL,
  `date_comp` date DEFAULT NULL,
  `impmunicipal` decimal(19,2) unsigned DEFAULT '0.00',
  `imptimbre` decimal(19,2) unsigned DEFAULT '0.00',
  `reteiva_prov` decimal(5,2) unsigned DEFAULT NULL,
  `total2` decimal(19,2) unsigned DEFAULT '0.00',
  `user` varchar(20) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `reverso` int(11) DEFAULT NULL,
  `fcomprome` date DEFAULT NULL,
  `fcausado` date DEFAULT NULL,
  `fislr` date DEFAULT NULL,
  `opislr` date DEFAULT NULL,
  `lentrega` mediumtext,
  `certificado` varchar(11) DEFAULT NULL,
  `simptimbre` char(1) DEFAULT NULL,
  `ftimbre` date DEFAULT NULL,
  `mtimbre` int(11) DEFAULT NULL,
  `mislr` int(11) DEFAULT NULL,
  `mmuni` int(11) DEFAULT NULL,
  `mcrs` int(11) DEFAULT NULL,
  `pentret` char(1) DEFAULT NULL,
  `pentrec` varchar(5) DEFAULT NULL,
  `fpagot` char(1) DEFAULT NULL,
  `fpagoc` varchar(5) DEFAULT NULL,
  `compromiso` varchar(15) DEFAULT NULL,
  `mexento` decimal(19,2) DEFAULT '0.00',
  `concepto` mediumtext,
  `proced` varchar(100) DEFAULT NULL,
  `aplica` varchar(20) DEFAULT NULL,
  `otrasrete` decimal(19,2) DEFAULT NULL,
  `redondear` varchar(2) DEFAULT NULL,
  `modalidad` varchar(50) DEFAULT '',
  `formaentrega` varchar(50) DEFAULT '',
  `condiciones` mediumtext,
  `nocompra` varchar(12) DEFAULT NULL,
  PRIMARY KEY (`numero`),
  KEY `numero` (`numero`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.ocomrapid
CREATE TABLE IF NOT EXISTS `ocomrapid` (
  `numero` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tipo` char(1) DEFAULT NULL,
  `fecha` date NOT NULL,
  `cod_prov` char(5) DEFAULT NULL,
  `placa` varchar(15) DEFAULT NULL,
  `litros` decimal(19,2) DEFAULT '0.00',
  `solicitante` varchar(50) DEFAULT NULL,
  `status` char(1) DEFAULT NULL COMMENT 'C cancelado,P pendiente',
  `concepto` varchar(250) DEFAULT NULL COMMENT 'C cancelado,P pendiente',
  `monto` decimal(19,2) NOT NULL DEFAULT '0.00' COMMENT 'C cancelado,P pendiente',
  `opago` int(11) NOT NULL DEFAULT '0' COMMENT 'C cancelado,P pendiente',
  PRIMARY KEY (`numero`),
  FULLTEXT KEY `cod_prov` (`cod_prov`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='ordenes de compra de gasolina';

-- Data exporting was unselected.


-- Dumping structure for table tortuga.odirect
CREATE TABLE IF NOT EXISTS `odirect` (
  `numero` varchar(12) NOT NULL COMMENT 'Nro de La Orden Pago',
  `obr` int(10) unsigned DEFAULT NULL,
  `cajach` int(11) unsigned DEFAULT NULL,
  `nomina` varchar(12) DEFAULT NULL,
  `controlord` char(12) DEFAULT NULL COMMENT 'Control Fiscal de La OC (preimpreso en el papel)',
  `fecha` date DEFAULT NULL COMMENT 'Fecha de emision de la OC',
  `tipo` char(10) DEFAULT NULL COMMENT 'tipo si es  COMPRA o SERVICO',
  `compra` varchar(10) DEFAULT NULL,
  `uejecutora` char(4) DEFAULT NULL COMMENT 'Unidad Ejecutora',
  `estadmin` varchar(12) DEFAULT NULL,
  `fondo` varchar(20) DEFAULT NULL COMMENT 'Origen de Fondo',
  `cod_prov` char(5) DEFAULT NULL COMMENT 'Proveedor',
  `nombre` varchar(80) DEFAULT NULL,
  `beneficiario` varchar(50) DEFAULT NULL COMMENT 'pago a nombre del beneficiario',
  `factura` char(12) DEFAULT NULL COMMENT 'Numero de Factura',
  `controlfac` varchar(12) DEFAULT NULL COMMENT 'Control Fiscal de la factura',
  `fechafac` date DEFAULT NULL COMMENT 'fecha de la Factura',
  `subtotal` decimal(19,2) DEFAULT '0.00',
  `exento` decimal(19,2) DEFAULT '0.00',
  `ivag` decimal(19,2) DEFAULT '0.00' COMMENT 'Iva Tasa General',
  `tivag` decimal(19,2) unsigned DEFAULT '0.00',
  `mivag` decimal(19,2) unsigned DEFAULT '0.00',
  `ivar` decimal(19,2) DEFAULT '0.00' COMMENT 'Iva tasa reducida',
  `tivar` decimal(19,2) unsigned DEFAULT '0.00',
  `mivar` decimal(19,2) unsigned DEFAULT '0.00',
  `ivaa` decimal(19,2) DEFAULT '0.00' COMMENT 'Iva Tasa Adicional',
  `tivaa` decimal(19,2) unsigned DEFAULT '0.00',
  `mivaa` decimal(19,2) unsigned DEFAULT '0.00',
  `pago` decimal(19,2) DEFAULT '0.00' COMMENT 'total orden',
  `creten` char(4) DEFAULT NULL COMMENT 'Codigo de Retencion de ISLR',
  `breten` decimal(19,2) DEFAULT '0.00' COMMENT 'Base de Retencion',
  `reteiva` decimal(19,2) DEFAULT '0.00' COMMENT 'Retencion de IVA',
  `reten` decimal(19,2) DEFAULT '0.00' COMMENT 'Retencion de ISLR',
  `total` decimal(19,2) unsigned DEFAULT NULL,
  `total2` decimal(19,2) unsigned DEFAULT '0.00',
  `iva` decimal(19,2) unsigned DEFAULT NULL,
  `observa` longtext COMMENT 'Observaciones',
  `anulado` date DEFAULT NULL COMMENT 'Fecha de Anulacion',
  `status` char(2) DEFAULT NULL COMMENT 'en Proceso, Comprometida, Anulado',
  `user_comp` varchar(30) DEFAULT NULL,
  `date_comp` date DEFAULT NULL,
  `reteiva_prov` decimal(5,2) unsigned DEFAULT NULL,
  `devo` decimal(19,2) DEFAULT NULL,
  `abonado` decimal(19,2) unsigned DEFAULT NULL,
  `oper` char(1) DEFAULT NULL,
  `mbanc` int(11) unsigned DEFAULT NULL,
  `movi` int(11) DEFAULT NULL,
  `iva2` decimal(19,2) DEFAULT NULL,
  `multiple` char(1) DEFAULT NULL,
  `impmunicipal` decimal(19,2) unsigned DEFAULT '0.00',
  `crs` decimal(19,2) unsigned DEFAULT '0.00',
  `imptimbre` decimal(19,2) unsigned DEFAULT '0.00',
  `pcrs` decimal(19,2) unsigned DEFAULT '0.00',
  `pimptimbre` decimal(19,2) unsigned DEFAULT '0.00',
  `pimpmunicipal` decimal(19,2) unsigned DEFAULT '0.00',
  `retenomina` decimal(19,2) unsigned DEFAULT '0.00',
  `montocontrato` decimal(19,2) unsigned DEFAULT '0.00',
  `amortiza` decimal(19,2) unsigned DEFAULT '0.00',
  `porcent` decimal(19,2) unsigned DEFAULT '0.00',
  `anticipo` char(1) DEFAULT NULL,
  `fpagado` date DEFAULT NULL,
  `fopago` date DEFAULT NULL,
  `reverso` int(50) DEFAULT NULL,
  `opislr` int(11) DEFAULT NULL,
  `opcrs` int(11) DEFAULT NULL,
  `opmunicipal` int(11) DEFAULT NULL,
  `optimbre` int(11) DEFAULT NULL,
  `fislr` date DEFAULT NULL,
  `fcrs` date DEFAULT NULL,
  `fmunicipal` date DEFAULT NULL,
  `ftimbre` date DEFAULT NULL,
  `mtimbre` int(11) DEFAULT NULL,
  `mislr` int(11) DEFAULT NULL,
  `mmuni` int(11) DEFAULT NULL,
  `mcrs` int(11) DEFAULT NULL,
  `cod_prov2` varchar(5) DEFAULT NULL,
  `tipoc` varchar(2) DEFAULT NULL,
  `otrasrete` decimal(19,2) DEFAULT NULL,
  `redondear` char(2) DEFAULT NULL,
  `fanulado` date DEFAULT NULL,
  `observacaj` mediumtext,
  `fapagado` date DEFAULT NULL,
  `facaduca` date DEFAULT NULL,
  `preten` decimal(19,2) DEFAULT '0.00',
  PRIMARY KEY (`numero`),
  KEY `numero` (`numero`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.ordinal
CREATE TABLE IF NOT EXISTS `ordinal` (
  `codigoadm` varchar(15) NOT NULL DEFAULT '',
  `fondo` varchar(20) NOT NULL DEFAULT '',
  `codigopres` varchar(15) NOT NULL DEFAULT '',
  `ordinal` varchar(4) NOT NULL DEFAULT '',
  `denominacion` varchar(100) DEFAULT NULL,
  `asignacion` decimal(19,2) unsigned DEFAULT '0.00',
  `aumento` decimal(19,2) unsigned DEFAULT '0.00',
  `disminucion` decimal(19,2) unsigned DEFAULT '0.00',
  `traslados` decimal(19,2) DEFAULT '0.00',
  `comprometido` decimal(19,2) unsigned DEFAULT '0.00',
  `causado` decimal(19,2) unsigned DEFAULT '0.00',
  `opago` decimal(19,2) unsigned DEFAULT '0.00',
  `pagado` decimal(19,2) unsigned DEFAULT '0.00',
  PRIMARY KEY (`codigopres`,`ordinal`,`codigoadm`,`fondo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.ordinalante
CREATE TABLE IF NOT EXISTS `ordinalante` (
  `codigoadm` varchar(15) NOT NULL DEFAULT '',
  `fondo` varchar(20) NOT NULL DEFAULT '',
  `codigopres` varchar(15) NOT NULL DEFAULT '',
  `ordinal` varchar(4) NOT NULL DEFAULT '',
  `denominacion` varchar(100) DEFAULT NULL,
  `asignacion` decimal(19,2) unsigned DEFAULT '0.00',
  `aumento` decimal(19,2) unsigned DEFAULT '0.00',
  `disminucion` decimal(19,2) unsigned DEFAULT '0.00',
  `traslados` decimal(19,2) unsigned DEFAULT '0.00',
  `comprometido` decimal(19,2) unsigned DEFAULT '0.00',
  `causado` decimal(19,2) unsigned DEFAULT '0.00',
  `opago` decimal(19,2) unsigned DEFAULT '0.00',
  `pagado` decimal(19,2) unsigned DEFAULT '0.00' COMMENT '0',
  PRIMARY KEY (`codigopres`,`ordinal`,`codigoadm`,`fondo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.otrabajo
CREATE TABLE IF NOT EXISTS `otrabajo` (
  `numero` varchar(12) NOT NULL DEFAULT '' COMMENT 'Nro de La Orden de Compra',
  `controlord` char(12) DEFAULT NULL COMMENT 'Control Fiscal de La OC (preimpreso en el papel)',
  `fecha` date DEFAULT NULL COMMENT 'Fecha de emision de la OC',
  `tipo` char(10) DEFAULT NULL COMMENT 'tipo si es  COMPRA o SERVICO',
  `uejecutora` char(4) DEFAULT NULL COMMENT 'Unidad Ejecutora',
  `usaislr` char(1) DEFAULT NULL COMMENT 'Unidad Ejecutora',
  `usolicita` char(4) DEFAULT NULL COMMENT 'Unidad Solicitante',
  `estadmin` varchar(12) DEFAULT NULL,
  `fondo` varchar(20) DEFAULT NULL COMMENT 'Origen de Fondo',
  `cod_prov` char(5) DEFAULT NULL COMMENT 'Proveedor',
  `nombre` varchar(80) DEFAULT NULL,
  `beneficiario` varchar(50) DEFAULT NULL COMMENT 'pago a nombre del beneficiario',
  `factura` char(12) DEFAULT NULL COMMENT 'Numero de Factura',
  `controlfac` varchar(12) DEFAULT NULL COMMENT 'Control Fiscal de la factura',
  `fechapago` date DEFAULT NULL,
  `fechafac` date DEFAULT NULL COMMENT 'fecha de la Factura',
  `subtotal` decimal(19,2) DEFAULT '0.00',
  `exento` decimal(19,2) DEFAULT '0.00',
  `odirect` int(10) unsigned DEFAULT NULL,
  `ivag` decimal(19,2) DEFAULT '0.00' COMMENT 'Iva Tasa General',
  `tivag` decimal(19,2) unsigned DEFAULT '0.00',
  `mivag` decimal(19,2) unsigned DEFAULT '0.00',
  `ivar` decimal(19,2) DEFAULT '0.00' COMMENT 'Iva tasa reducida',
  `tivar` decimal(19,2) unsigned DEFAULT '0.00',
  `mivar` decimal(19,2) unsigned DEFAULT '0.00',
  `ivaa` decimal(19,2) DEFAULT '0.00' COMMENT 'Iva Tasa Adicional',
  `tivaa` decimal(19,2) unsigned DEFAULT '0.00',
  `mivaa` decimal(19,2) unsigned DEFAULT '0.00',
  `total` decimal(19,2) DEFAULT '0.00' COMMENT 'total orden',
  `abonado` decimal(19,2) unsigned DEFAULT '0.00',
  `creten` char(4) DEFAULT NULL COMMENT 'Codigo de Retencion de ISLR',
  `breten` decimal(19,2) DEFAULT '0.00' COMMENT 'Base de Retencion',
  `alireten` decimal(19,2) unsigned DEFAULT NULL,
  `susreten` decimal(19,2) unsigned DEFAULT NULL,
  `reteiva` decimal(19,2) DEFAULT '0.00' COMMENT 'Retencion de IVA',
  `reten` decimal(19,2) DEFAULT '0.00' COMMENT 'Retencion de ISLR',
  `observa` longtext COMMENT 'Observaciones',
  `anulado` date DEFAULT NULL COMMENT 'Fecha de Anulacion',
  `status` char(1) DEFAULT NULL COMMENT 'en Proceso, Comprometida, Anulado',
  `user_comp` varchar(30) DEFAULT NULL,
  `date_comp` date DEFAULT NULL,
  `impmunicipal` decimal(19,2) unsigned DEFAULT '0.00',
  `imptimbre` decimal(19,2) unsigned DEFAULT '0.00',
  `reteiva_prov` decimal(5,2) unsigned DEFAULT NULL,
  `total2` decimal(19,2) unsigned DEFAULT '0.00',
  `user` varchar(20) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `reverso` int(11) DEFAULT NULL,
  `fcomprome` date DEFAULT NULL,
  `fcausado` date DEFAULT NULL,
  `fislr` date DEFAULT NULL,
  `opislr` date DEFAULT NULL,
  `lentrega` mediumtext,
  `certificado` varchar(11) DEFAULT NULL,
  `simptimbre` char(1) DEFAULT NULL,
  `ftimbre` date DEFAULT NULL,
  `mtimbre` int(11) DEFAULT NULL,
  `mislr` int(11) DEFAULT NULL,
  `mmuni` int(11) DEFAULT NULL,
  `mcrs` int(11) DEFAULT NULL,
  `pentret` char(1) DEFAULT NULL,
  `pentrec` varchar(5) DEFAULT NULL,
  `fpagot` char(1) DEFAULT NULL,
  `fpagoc` varchar(5) DEFAULT NULL,
  `compromiso` varchar(15) DEFAULT NULL,
  `mexento` decimal(19,2) DEFAULT '0.00',
  `concepto` mediumtext,
  `proced` varchar(100) DEFAULT NULL,
  `aplica` varchar(20) DEFAULT NULL,
  `otrasrete` decimal(19,2) DEFAULT NULL,
  `redondear` varchar(2) DEFAULT NULL,
  `modalidad` varchar(50) DEFAULT '',
  `formaentrega` varchar(50) DEFAULT '',
  `condiciones` mediumtext,
  `nocompra` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`numero`),
  KEY `numero` (`numero`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.otrosnomi
CREATE TABLE IF NOT EXISTS `otrosnomi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero` int(11) NOT NULL DEFAULT '0',
  `nomina` int(11) DEFAULT NULL,
  `codigoadm` varchar(15) DEFAULT NULL,
  `fondo` varchar(20) DEFAULT NULL,
  `codigopres` varchar(17) DEFAULT NULL,
  `ordinal` char(3) DEFAULT NULL,
  `cod_prov` varchar(5) DEFAULT NULL,
  `monto` decimal(19,2) DEFAULT NULL,
  `opago` int(1) DEFAULT NULL,
  `status` char(1) DEFAULT 'P',
  `nombre` varchar(100) DEFAULT NULL,
  `mbanc` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.pacom
CREATE TABLE IF NOT EXISTS `pacom` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pago` varchar(12) DEFAULT NULL,
  `compra` varchar(12) DEFAULT NULL,
  `monto` decimal(19,2) unsigned DEFAULT NULL,
  `todos` char(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `pago` (`pago`),
  KEY `compra` (`compra`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.pades
CREATE TABLE IF NOT EXISTS `pades` (
  `pago` varchar(12) NOT NULL,
  `desem` int(11) NOT NULL,
  PRIMARY KEY (`pago`,`desem`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.patente
CREATE TABLE IF NOT EXISTS `patente` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tarjeta` char(6) DEFAULT NULL,
  `licencia` char(6) DEFAULT NULL,
  `nombre_pro` char(25) DEFAULT NULL,
  `cedula` varchar(20) DEFAULT NULL,
  `nacionali` char(10) DEFAULT NULL,
  `razon` char(50) DEFAULT NULL,
  `dir_neg` char(50) DEFAULT NULL,
  `dir_pro` char(50) DEFAULT NULL,
  `telefonos` char(15) DEFAULT NULL,
  `capital` double DEFAULT NULL,
  `monto` double DEFAULT NULL,
  `fecha_es` date DEFAULT NULL,
  `oficio` char(30) DEFAULT NULL,
  `local` char(2) DEFAULT NULL,
  `negocio` char(5) DEFAULT NULL,
  `registrado` char(1) DEFAULT NULL,
  `deuda` double DEFAULT NULL,
  `enero` double DEFAULT NULL,
  `febrero` double DEFAULT NULL,
  `marzo` double DEFAULT NULL,
  `abril` double DEFAULT NULL,
  `mayo` double DEFAULT NULL,
  `junio` double DEFAULT NULL,
  `julio` double DEFAULT NULL,
  `agosto` double DEFAULT NULL,
  `septiembre` double DEFAULT NULL,
  `octubre` double DEFAULT NULL,
  `noviembre` double DEFAULT NULL,
  `diciembre` double DEFAULT NULL,
  `deudacan` double DEFAULT NULL,
  `total` double DEFAULT NULL,
  `observa` mediumtext,
  `clase` char(1) DEFAULT NULL,
  `tipo` char(1) DEFAULT NULL,
  `catastro` char(10) DEFAULT NULL,
  `publicidad` char(30) DEFAULT NULL,
  `repreced` mediumtext,
  `expclasi` mediumtext,
  `exphor` mediumtext,
  `kardex` mediumtext,
  `contribu` varchar(6) NOT NULL,
  `recibo` int(11) DEFAULT NULL,
  `declaracion` decimal(19,2) DEFAULT NULL,
  `repre` mediumtext,
  `nro` mediumtext,
  `fexpedicion` date DEFAULT NULL,
  `fvencimiento` date DEFAULT NULL,
  `actual` decimal(19,2) DEFAULT '0.00',
  `ajustado` decimal(19,2) DEFAULT '0.00',
  `neto` decimal(19,2) DEFAULT '0.00',
  `objeto` mediumtext,
  `archivo` mediumtext,
  `utribu` varchar(50) DEFAULT NULL,
  `fotorga` date DEFAULT NULL,
  `factu` date DEFAULT NULL,
  `cantfol` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.pers
CREATE TABLE IF NOT EXISTS `pers` (
  `codigo` varchar(15) NOT NULL DEFAULT '',
  `nacional` char(1) DEFAULT NULL,
  `cedula` varchar(10) DEFAULT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `nombre2` varchar(30) DEFAULT NULL,
  `apellido` varchar(30) DEFAULT NULL,
  `apellido2` varchar(30) DEFAULT NULL,
  `direc1` text,
  `direc2` varchar(30) DEFAULT NULL COMMENT 'Direccion 1',
  `direc3` varchar(30) DEFAULT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  `nacimi` date DEFAULT NULL,
  `sso` varchar(11) DEFAULT NULL,
  `sexo` char(1) DEFAULT NULL,
  `civil` char(1) DEFAULT NULL,
  `depto` varchar(8) DEFAULT NULL,
  `cargo` varchar(8) DEFAULT NULL,
  `sueldo` decimal(13,2) DEFAULT '0.00',
  `ingreso` date DEFAULT NULL,
  `retiro` date DEFAULT NULL,
  `tipo` char(1) DEFAULT NULL,
  `contrato` varchar(5) DEFAULT NULL,
  `dialib` char(2) DEFAULT NULL,
  `status` char(1) DEFAULT NULL,
  `formacob` char(1) DEFAULT NULL,
  `banco` varchar(15) DEFAULT NULL,
  `cutipo` char(1) DEFAULT NULL,
  `cuenta` varchar(20) DEFAULT NULL,
  `vari1` varchar(50) DEFAULT '0.00',
  `vari2` varchar(50) DEFAULT '0.00',
  `vari3` varchar(50) DEFAULT '0.00',
  `vari4` varchar(50) DEFAULT NULL,
  `vari5` varchar(50) DEFAULT NULL,
  `vari6` varchar(50) DEFAULT '0.00',
  `vari7` varchar(50) DEFAULT '0.00',
  `vari8` varchar(50) DEFAULT '0.00',
  `vari9` varchar(50) DEFAULT '0.00',
  `vari10` varchar(50) DEFAULT '0.00',
  `vari11` varchar(50) DEFAULT '0.00',
  `vari12` varchar(50) DEFAULT '0.00',
  `rif` varchar(50) DEFAULT NULL,
  `uaumento` decimal(17,2) DEFAULT '0.00',
  `formato` varchar(10) DEFAULT NULL,
  `dialab` char(2) DEFAULT NULL,
  `xdialab` char(2) DEFAULT NULL,
  `sucursal` char(2) DEFAULT NULL,
  `divi` varchar(8) DEFAULT NULL,
  `carnet` varchar(10) DEFAULT NULL,
  `enlace` varchar(5) DEFAULT NULL,
  `estampa` date DEFAULT NULL,
  `usuario` varchar(12) DEFAULT NULL,
  `hora` varchar(8) DEFAULT NULL,
  `transac` varchar(8) DEFAULT NULL,
  `cuentab` varchar(20) DEFAULT NULL,
  `profes` varchar(8) DEFAULT NULL,
  `niveled` char(2) DEFAULT NULL,
  `vence` date DEFAULT NULL,
  `horario` char(4) DEFAULT NULL,
  `numcuent` varchar(30) DEFAULT NULL,
  `tipocuent` char(1) DEFAULT NULL,
  `nombcuent` varchar(100) DEFAULT NULL,
  `cod_banc` char(3) DEFAULT NULL,
  `cicuent` varchar(15) DEFAULT NULL,
  `codigoadm` varchar(25) DEFAULT NULL,
  `fondo` varchar(25) DEFAULT NULL,
  `codigopres` varchar(25) DEFAULT NULL,
  `email` varchar(250) DEFAULT NULL,
  `tipoe` varchar(10) DEFAULT NULL,
  `observa` mediumtext,
  `forcob` text,
  `antiguedadap` decimal(19,2) DEFAULT '0.00',
  `fechajubipensi` date DEFAULT NULL,
  `tipemp` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.posicion
CREATE TABLE IF NOT EXISTS `posicion` (
  `codigo` varchar(10) NOT NULL,
  `posicion` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.ppla
CREATE TABLE IF NOT EXISTS `ppla` (
  `codigo` varchar(15) NOT NULL DEFAULT '',
  `denominacion` varchar(250) DEFAULT NULL,
  `aplicacion` mediumtext,
  `nivel` int(4) DEFAULT '0',
  `cta_contable` varchar(20) DEFAULT NULL,
  `movimiento` char(1) DEFAULT NULL,
  PRIMARY KEY (`codigo`),
  KEY `movimiento` (`movimiento`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.pplaxls
CREATE TABLE IF NOT EXISTS `pplaxls` (
  `partida` varchar(255) DEFAULT NULL,
  `generica` varchar(255) DEFAULT NULL,
  `especifica` varchar(255) DEFAULT NULL,
  `subespecifica` varchar(255) DEFAULT NULL,
  `denominacion` mediumtext
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.prenom
CREATE TABLE IF NOT EXISTS `prenom` (
  `contrato` char(8) DEFAULT NULL,
  `codigo` char(15) NOT NULL DEFAULT '',
  `nombre` varchar(255) DEFAULT NULL,
  `concepto` char(4) NOT NULL DEFAULT '',
  `tipo` char(1) DEFAULT NULL,
  `descrip` char(35) DEFAULT NULL,
  `grupo` char(4) DEFAULT NULL,
  `formula` text,
  `monto` decimal(17,2) DEFAULT '0.00',
  `fecha` date DEFAULT NULL,
  `cuota` int(11) DEFAULT NULL,
  `cuotat` int(11) DEFAULT NULL,
  `valor` decimal(17,2) DEFAULT '0.00',
  `adicional` decimal(17,1) DEFAULT '0.0',
  `fechap` date DEFAULT NULL,
  `trabaja` char(8) DEFAULT NULL,
  `pprome` int(6) DEFAULT NULL,
  `modo` char(1) DEFAULT NULL,
  `orden` int(11) DEFAULT NULL,
  `vari1` varchar(50) DEFAULT NULL,
  `vari2` varchar(50) DEFAULT NULL,
  `vari3` varchar(50) DEFAULT NULL,
  `vari4` varchar(50) DEFAULT NULL,
  `vari5` varchar(50) DEFAULT NULL,
  `vari6` varchar(50) DEFAULT NULL,
  `vari7` varchar(50) DEFAULT NULL,
  `vari8` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`codigo`,`concepto`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.PRENOM2
CREATE TABLE IF NOT EXISTS `PRENOM2` (
  `contrato` char(8) DEFAULT NULL,
  `codigo` char(15) NOT NULL DEFAULT '',
  `nombre` char(30) DEFAULT NULL,
  `concepto` char(4) NOT NULL DEFAULT '',
  `tipo` char(1) DEFAULT NULL,
  `descrip` char(35) DEFAULT NULL,
  `grupo` char(4) DEFAULT NULL,
  `formula` char(120) DEFAULT NULL,
  `monto` decimal(17,2) DEFAULT '0.00',
  `fecha` date DEFAULT NULL,
  `cuota` int(11) DEFAULT NULL,
  `cuotat` int(11) DEFAULT NULL,
  `valor` decimal(17,2) DEFAULT '0.00',
  `adicional` decimal(17,1) DEFAULT '0.0',
  `fechap` date DEFAULT NULL,
  `trabaja` char(8) DEFAULT NULL,
  `pprome` int(6) DEFAULT NULL,
  `modo` char(1) DEFAULT NULL,
  `orden` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.PRENOM5
CREATE TABLE IF NOT EXISTS `PRENOM5` (
  `contrato` char(8) DEFAULT NULL,
  `codigo` char(15) NOT NULL DEFAULT '',
  `nombre` char(30) DEFAULT NULL,
  `concepto` char(4) NOT NULL DEFAULT '',
  `tipo` char(1) DEFAULT NULL,
  `descrip` char(35) DEFAULT NULL,
  `grupo` char(4) DEFAULT NULL,
  `formula` char(120) DEFAULT NULL,
  `monto` decimal(17,2) DEFAULT '0.00',
  `fecha` date DEFAULT NULL,
  `cuota` int(11) DEFAULT NULL,
  `cuotat` int(11) DEFAULT NULL,
  `valor` decimal(17,2) DEFAULT '0.00',
  `adicional` decimal(17,1) DEFAULT '0.0',
  `fechap` date DEFAULT NULL,
  `trabaja` char(8) DEFAULT NULL,
  `pprome` int(6) DEFAULT NULL,
  `modo` char(1) DEFAULT NULL,
  `orden` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.pres
CREATE TABLE IF NOT EXISTS `pres` (
  `cod_cli` char(5) NOT NULL DEFAULT '',
  `tipo_doc` char(2) NOT NULL DEFAULT '',
  `numero` char(8) NOT NULL DEFAULT '',
  `fecha` date DEFAULT NULL,
  `codigo` char(15) DEFAULT NULL,
  `nombre` char(30) DEFAULT NULL,
  `monto` decimal(14,2) DEFAULT '0.00',
  `nroctas` decimal(2,0) DEFAULT NULL,
  `cuota` decimal(14,2) DEFAULT '0.00',
  `apartir` date DEFAULT NULL,
  `cadano` char(1) DEFAULT NULL,
  `observ1` char(46) DEFAULT NULL,
  `oberv2` char(46) DEFAULT NULL,
  PRIMARY KEY (`cod_cli`,`tipo_doc`,`numero`),
  KEY `codigo` (`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.prescerrado
CREATE TABLE IF NOT EXISTS `prescerrado` (
  `tipo` varchar(20) DEFAULT NULL,
  `codigoadm` varchar(12) DEFAULT NULL,
  `codigopres` varchar(15) DEFAULT NULL,
  `asignacion` decimal(19,2) DEFAULT '0.00',
  `aumento` decimal(19,2) DEFAULT '0.00',
  `disminucion` decimal(19,2) DEFAULT '0.00',
  `comprometido` decimal(19,2) DEFAULT '0.00',
  `causado` decimal(19,2) DEFAULT '0.00',
  `recibido` decimal(19,2) unsigned DEFAULT '0.00',
  `opago` decimal(19,2) unsigned DEFAULT '0.00',
  `pagado` decimal(19,2) DEFAULT '0.00',
  `nivel` int(4) DEFAULT NULL,
  `ano` char(6) DEFAULT NULL,
  `movimiento` char(1) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.PRESUMEN
CREATE TABLE IF NOT EXISTS `PRESUMEN` (
  `codigoadm` varchar(20) DEFAULT NULL,
  `fondo` varchar(20) DEFAULT NULL,
  `codigopres` varchar(25) DEFAULT NULL,
  `ordinal` char(0) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `comprometido` decimal(42,2) DEFAULT NULL,
  `causado` decimal(43,2) DEFAULT NULL,
  `opago` decimal(43,2) DEFAULT NULL,
  `pagado` decimal(43,2) DEFAULT NULL,
  `aumento` decimal(43,2) DEFAULT NULL,
  `disminucion` decimal(43,2) DEFAULT NULL,
  `traslados` decimal(43,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.presupuesto
CREATE TABLE IF NOT EXISTS `presupuesto` (
  `codigoadm` varchar(12) NOT NULL DEFAULT '',
  `tipo` varchar(20) NOT NULL DEFAULT '',
  `codigopres` varchar(25) NOT NULL DEFAULT '',
  `asignacion` decimal(19,2) DEFAULT '0.00',
  `aumento` decimal(19,2) DEFAULT '0.00',
  `disminucion` decimal(19,2) DEFAULT '0.00',
  `traslados` decimal(19,2) DEFAULT '0.00',
  `apartado` decimal(19,2) DEFAULT '0.00',
  `comprometido` decimal(19,2) DEFAULT '0.00',
  `causado` decimal(19,2) DEFAULT '0.00',
  `recibido` decimal(19,2) unsigned DEFAULT NULL,
  `opago` decimal(19,2) unsigned DEFAULT '0.00',
  `pagado` decimal(19,2) DEFAULT '0.00',
  `nivel` char(1) DEFAULT NULL,
  `denominacion` text,
  `movimiento` char(1) DEFAULT NULL,
  `uejecutora` varchar(8) DEFAULT NULL,
  `orden` int(11) DEFAULT NULL,
  `gasinv` char(1) DEFAULT 'G',
  PRIMARY KEY (`codigoadm`,`tipo`,`codigopres`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.presupuestoante
CREATE TABLE IF NOT EXISTS `presupuestoante` (
  `codigoadm` varchar(12) NOT NULL DEFAULT '',
  `tipo` varchar(20) NOT NULL DEFAULT '',
  `codigopres` varchar(15) NOT NULL DEFAULT '',
  `asignacion` decimal(19,2) DEFAULT '0.00',
  `aumento` decimal(19,2) DEFAULT '0.00',
  `disminucion` decimal(19,2) DEFAULT '0.00',
  `traslados` decimal(19,2) DEFAULT '0.00',
  `comprometido` decimal(19,2) DEFAULT '0.00',
  `causado` decimal(19,2) DEFAULT '0.00',
  `recibido` decimal(19,2) unsigned DEFAULT NULL,
  `opago` decimal(19,2) unsigned DEFAULT '0.00',
  `pagado` decimal(19,2) DEFAULT '0.00',
  `nivel` char(1) DEFAULT NULL,
  PRIMARY KEY (`tipo`,`codigoadm`,`codigopres`),
  KEY `CodigoAdm` (`codigoadm`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.presusol
CREATE TABLE IF NOT EXISTS `presusol` (
  `tipo` varchar(20) NOT NULL DEFAULT '',
  `codigoadm` varchar(12) NOT NULL DEFAULT '',
  `codigopres` varchar(15) NOT NULL DEFAULT '',
  `asignacion` decimal(19,2) DEFAULT '0.00',
  `solicitado` decimal(19,2) DEFAULT '0.00',
  `nivel` char(1) DEFAULT NULL,
  `movimiento` char(1) DEFAULT NULL,
  `denominacion` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`tipo`,`codigoadm`,`codigopres`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.pretab
CREATE TABLE IF NOT EXISTS `pretab` (
  `codigo` char(15) NOT NULL DEFAULT '',
  `frec` char(1) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `nombre` char(30) DEFAULT NULL,
  `total` decimal(17,2) DEFAULT '0.00',
  `c0001` decimal(17,2) DEFAULT '0.00',
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.prioridad
CREATE TABLE IF NOT EXISTS `prioridad` (
  `id` int(10) NOT NULL DEFAULT '0',
  `descripcion` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.procesos
CREATE TABLE IF NOT EXISTS `procesos` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) DEFAULT NULL,
  `procedimiento` mediumtext,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.prof
CREATE TABLE IF NOT EXISTS `prof` (
  `codigo` varchar(8) NOT NULL DEFAULT '',
  `profesion` text,
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.publicidad
CREATE TABLE IF NOT EXISTS `publicidad` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `archivo` varchar(100) DEFAULT NULL,
  `bgcolor` varchar(7) DEFAULT NULL,
  `prob` float unsigned DEFAULT NULL,
  `estampa` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `descrip` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`,`archivo`),
  KEY `id_2` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.recibo
CREATE TABLE IF NOT EXISTS `recibo` (
  `numero` varchar(10) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` date DEFAULT NULL,
  `contribu` char(6) DEFAULT NULL,
  `tipo` char(3) DEFAULT NULL,
  `monto` decimal(19,2) NOT NULL DEFAULT '0.00',
  `observa` mediumtext NOT NULL,
  `direccion` mediumtext,
  `nombre` varchar(200) DEFAULT NULL,
  `rifci` varchar(13) DEFAULT NULL,
  `nacionali` varchar(10) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `user` varchar(50) DEFAULT NULL,
  `estampa` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `patente` int(11) DEFAULT NULL,
  `inmueble` int(11) DEFAULT NULL,
  `vehiculo` int(11) DEFAULT NULL,
  `declaracion` decimal(19,2) DEFAULT '0.00',
  `fexp` date DEFAULT NULL,
  `fven` date DEFAULT NULL,
  `abono` int(11) DEFAULT NULL,
  `oper` varchar(50) NOT NULL DEFAULT '',
  `status` char(1) NOT NULL DEFAULT 'P',
  `tasam` varchar(20) NOT NULL DEFAULT '0.00',
  `razonsocial` varchar(50) DEFAULT NULL,
  `rif` varchar(20) DEFAULT NULL,
  `nomfis` varchar(100) DEFAULT NULL,
  `efectos` varchar(200) DEFAULT NULL,
  `recibo` varchar(20) DEFAULT NULL,
  `solvencia` varchar(20) DEFAULT NULL,
  `efectos2` varchar(100) DEFAULT NULL,
  `concsolv` mediumtext,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.reglascont
CREATE TABLE IF NOT EXISTS `reglascont` (
  `modulo` varchar(20) NOT NULL DEFAULT '',
  `regla` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `tabla` varchar(20) DEFAULT NULL,
  `descripcion` mediumtext,
  `fecha` varchar(255) DEFAULT NULL,
  `comprob` varchar(255) DEFAULT NULL,
  `origen` varchar(255) DEFAULT NULL,
  `condicion` mediumtext,
  `agrupar` varchar(255) DEFAULT NULL,
  `cuenta` mediumtext,
  `referen` varchar(255) DEFAULT NULL,
  `concepto` mediumtext,
  `debe` varchar(255) DEFAULT NULL,
  `haber` varchar(255) DEFAULT NULL,
  `ccosto` varchar(255) DEFAULT NULL,
  `sucursal` varchar(255) DEFAULT NULL,
  `fuente` varchar(255) DEFAULT NULL,
  `control` varchar(50) DEFAULT NULL,
  `mbanc_id` mediumtext,
  `auxiliar` mediumtext,
  `havin` text,
  PRIMARY KEY (`modulo`,`regla`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.reinte
CREATE TABLE IF NOT EXISTS `reinte` (
  `numero` varchar(12) NOT NULL DEFAULT '',
  `fecha` date DEFAULT NULL,
  `uejecuta` char(4) DEFAULT NULL,
  `uadministra` char(4) DEFAULT NULL,
  `concepto` text,
  `status` char(2) DEFAULT NULL,
  `usuario` varchar(12) DEFAULT NULL COMMENT 'aa',
  `total` varchar(45) DEFAULT NULL,
  `comping` varchar(12) DEFAULT NULL,
  PRIMARY KEY (`numero`),
  KEY `uejecuta` (`uejecuta`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Reintegros presupuestarios';

-- Data exporting was unselected.


-- Dumping structure for table tortuga.relch
CREATE TABLE IF NOT EXISTS `relch` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(45) DEFAULT NULL,
  `estampa` datetime DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `destino` varchar(45) DEFAULT NULL,
  `numero` varchar(45) DEFAULT NULL,
  `status` char(1) DEFAULT NULL,
  `fondo` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.rendi
CREATE TABLE IF NOT EXISTS `rendi` (
  `numero` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Nro de La Orden de Compra',
  `cajach` int(11) NOT NULL DEFAULT '0' COMMENT 'Nro de La Orden de Compra',
  `controlord` char(12) DEFAULT NULL COMMENT 'Control Fiscal de La OC (preimpreso en el papel)',
  `fecha` date DEFAULT NULL COMMENT 'Fecha de emision de la OC',
  `tipo` char(10) DEFAULT NULL COMMENT 'tipo si es  COMPRA o SERVICO',
  `compra` int(10) unsigned DEFAULT NULL,
  `uejecutora` char(4) DEFAULT NULL COMMENT 'Unidad Ejecutora',
  `estadmin` varchar(12) DEFAULT NULL,
  `fondo` varchar(20) DEFAULT NULL COMMENT 'Origen de Fondo',
  `cod_prov` char(5) DEFAULT NULL COMMENT 'Proveedor',
  `nombre` varchar(80) DEFAULT NULL,
  `beneficiario` varchar(50) DEFAULT NULL COMMENT 'pago a nombre del beneficiario',
  `factura` char(12) DEFAULT NULL COMMENT 'Numero de Factura',
  `controlfac` varchar(12) DEFAULT NULL COMMENT 'Control Fiscal de la factura',
  `fechafac` date DEFAULT NULL COMMENT 'fecha de la Factura',
  `subtotal` decimal(19,2) DEFAULT '0.00',
  `exento` decimal(19,2) DEFAULT '0.00',
  `ivag` decimal(19,2) DEFAULT '0.00' COMMENT 'Iva Tasa General',
  `ivar` decimal(19,2) DEFAULT '0.00' COMMENT 'Iva tasa reducida',
  `ivaa` decimal(19,2) DEFAULT '0.00' COMMENT 'Iva Tasa Adicional',
  `pago` decimal(19,2) DEFAULT '0.00' COMMENT 'total orden',
  `creten` char(4) DEFAULT NULL COMMENT 'Codigo de Retencion de ISLR',
  `breten` decimal(19,2) DEFAULT '0.00' COMMENT 'Base de Retencion',
  `reteiva` decimal(19,2) DEFAULT '0.00' COMMENT 'Retencion de IVA',
  `reten` decimal(19,2) DEFAULT '0.00' COMMENT 'Retencion de ISLR',
  `total` decimal(19,2) unsigned DEFAULT NULL,
  `iva` decimal(19,2) unsigned DEFAULT NULL,
  `observa` longtext COMMENT 'Observaciones',
  `anulado` date DEFAULT NULL COMMENT 'Fecha de Anulacion',
  `status` char(2) DEFAULT NULL COMMENT 'en Proceso, Comprometida, Anulado',
  `user_comp` varchar(30) DEFAULT NULL,
  `date_comp` date DEFAULT NULL,
  `reteiva_prov` decimal(5,2) unsigned DEFAULT NULL,
  `devo` decimal(19,2) DEFAULT NULL,
  `abonado` decimal(19,2) unsigned DEFAULT NULL,
  `oper` char(1) DEFAULT '',
  `mbanc` int(11) unsigned DEFAULT NULL,
  `movi` int(11) DEFAULT NULL,
  `iva2` decimal(19,2) DEFAULT NULL,
  `anticipo` int(10) unsigned DEFAULT NULL,
  `frendi` date DEFAULT NULL,
  PRIMARY KEY (`numero`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.reportes
CREATE TABLE IF NOT EXISTS `reportes` (
  `nombre` varchar(50) NOT NULL DEFAULT '',
  `reporte` mediumtext,
  `proteo` mediumtext,
  `harbour` mediumtext,
  PRIMARY KEY (`nombre`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.repotra
CREATE TABLE IF NOT EXISTS `repotra` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `estampa` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fecha` date DEFAULT NULL,
  `t1horae` varchar(8) DEFAULT NULL,
  `t1horas` varchar(8) DEFAULT NULL,
  `empresa` varchar(50) DEFAULT NULL,
  `tecnico1` varchar(50) DEFAULT NULL,
  `informe` mediumtext,
  `observa` mediumtext,
  `t1tipoe` varchar(10) DEFAULT NULL,
  `t1tipos` varchar(10) DEFAULT NULL,
  `nombre` varchar(60) DEFAULT NULL,
  `t2horae` varchar(8) DEFAULT NULL,
  `t2horas` varchar(8) DEFAULT NULL,
  `t2tipoe` varchar(10) DEFAULT NULL,
  `t2tipos` varchar(10) DEFAULT NULL,
  `tecnico2` varchar(50) DEFAULT NULL,
  `tecnico3` varchar(50) DEFAULT NULL,
  `cobrado` char(2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.requi
CREATE TABLE IF NOT EXISTS `requi` (
  `numero` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fecha` date DEFAULT NULL,
  `uejecuta` char(4) DEFAULT NULL,
  `uadministra` tinyint(3) unsigned DEFAULT NULL,
  `responsable` varchar(50) DEFAULT NULL,
  `objetivo` longtext,
  `tcantidad` decimal(19,2) DEFAULT '0.00',
  `timporte` decimal(19,2) DEFAULT '0.00',
  `status` char(2) DEFAULT NULL,
  `ocompra` int(10) unsigned DEFAULT NULL,
  `estadmin` varchar(12) DEFAULT NULL,
  `fondo` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`numero`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.rete
CREATE TABLE IF NOT EXISTS `rete` (
  `codigo` varchar(4) NOT NULL DEFAULT '',
  `activida` tinytext,
  `tari1` decimal(10,2) DEFAULT '0.00',
  `pama1` decimal(13,2) DEFAULT '0.00',
  `tari2` decimal(13,2) DEFAULT '0.00',
  `pama2` decimal(13,2) DEFAULT '0.00',
  `tari3` decimal(13,2) DEFAULT '0.00',
  `pama3` decimal(13,2) DEFAULT '0.00',
  `tipo` varchar(5) DEFAULT NULL,
  `numeral` char(6) DEFAULT NULL,
  `auxi` tinytext,
  `base1` decimal(9,2) DEFAULT '0.00',
  `porcentsustra` decimal(9,4) DEFAULT '0.0000',
  `seniat` varchar(4) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.retenomi
CREATE TABLE IF NOT EXISTS `retenomi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero` int(11) NOT NULL DEFAULT '0',
  `nomina` int(11) DEFAULT NULL,
  `codigoadm` varchar(15) DEFAULT NULL,
  `fondo` varchar(20) DEFAULT NULL,
  `codigopres` varchar(17) DEFAULT NULL,
  `ordinal` char(3) DEFAULT NULL,
  `cod_prov` varchar(5) DEFAULT NULL,
  `monto` decimal(19,2) DEFAULT NULL,
  `opago` int(1) DEFAULT NULL,
  `status` char(1) DEFAULT 'P',
  `nombre` varchar(100) DEFAULT NULL,
  `mbanc` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.riva
CREATE TABLE IF NOT EXISTS `riva` (
  `nrocomp` varchar(8) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ocompra` varchar(12) DEFAULT NULL,
  `odirect` varchar(12) DEFAULT NULL,
  `itfac` int(11) unsigned DEFAULT NULL,
  `rendi` varchar(12) DEFAULT NULL,
  `emision` date DEFAULT NULL,
  `periodo` char(8) DEFAULT NULL,
  `tipo_doc` char(2) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `numero` char(20) DEFAULT NULL,
  `nfiscal` char(20) DEFAULT NULL,
  `afecta` char(8) DEFAULT NULL,
  `clipro` char(5) DEFAULT NULL,
  `nombre` char(40) DEFAULT NULL,
  `rif` char(14) DEFAULT NULL,
  `exento` decimal(15,2) DEFAULT NULL,
  `tasa` decimal(5,2) DEFAULT NULL,
  `general` decimal(15,2) DEFAULT NULL,
  `geneimpu` decimal(15,2) DEFAULT NULL,
  `tasaadic` decimal(5,2) DEFAULT NULL,
  `adicional` decimal(15,2) DEFAULT NULL,
  `adicimpu` decimal(15,2) DEFAULT NULL,
  `tasaredu` decimal(5,2) DEFAULT NULL,
  `reducida` decimal(15,2) DEFAULT NULL,
  `reduimpu` decimal(15,2) DEFAULT NULL,
  `stotal` decimal(15,2) DEFAULT NULL,
  `impuesto` decimal(15,2) DEFAULT NULL,
  `gtotal` decimal(15,2) DEFAULT NULL,
  `reiva` decimal(15,2) DEFAULT NULL,
  `transac` char(8) DEFAULT NULL,
  `estampa` date DEFAULT NULL,
  `hora` char(8) DEFAULT NULL,
  `usuario` char(12) DEFAULT NULL,
  `reteiva_prov` decimal(19,2) unsigned DEFAULT NULL,
  `ffactura` date DEFAULT '0000-00-00',
  `status` char(2) DEFAULT NULL,
  `mbanc` int(11) unsigned DEFAULT NULL,
  `banc` varchar(50) DEFAULT NULL,
  `numcuent` varchar(50) DEFAULT NULL,
  `codbanc` varchar(50) DEFAULT NULL,
  `pagado` varchar(10) DEFAULT NULL,
  `codigoadm` varchar(15) DEFAULT NULL,
  `fondo` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.ri_clase
CREATE TABLE IF NOT EXISTS `ri_clase` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) DEFAULT NULL,
  `monto` decimal(19,2) DEFAULT '0.00',
  `monto2` decimal(19,2) DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.ri_clasea
CREATE TABLE IF NOT EXISTS `ri_clasea` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) DEFAULT NULL,
  `monto` decimal(19,2) DEFAULT '0.00',
  `monto2` decimal(19,2) DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.rp_tipos
CREATE TABLE IF NOT EXISTS `rp_tipos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(10) NOT NULL DEFAULT '',
  `descrip` varchar(100) DEFAULT NULL,
  `monto` decimal(19,2) DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.rv_clase
CREATE TABLE IF NOT EXISTS `rv_clase` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(10) NOT NULL DEFAULT '',
  `descrip` varchar(100) DEFAULT NULL,
  `monto` decimal(19,2) DEFAULT '0.00',
  `monto2` decimal(19,2) DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.rv_marca
CREATE TABLE IF NOT EXISTS `rv_marca` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descrip` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.rv_modelo
CREATE TABLE IF NOT EXISTS `rv_modelo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_marca` int(11) NOT NULL,
  `descrip` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.rv_tipo
CREATE TABLE IF NOT EXISTS `rv_tipo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descrip` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.r_abonos
CREATE TABLE IF NOT EXISTS `r_abonos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `estampa` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `totrecibos` decimal(19,2) DEFAULT '0.00',
  `totmbanc` decimal(19,2) DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.r_abonosit
CREATE TABLE IF NOT EXISTS `r_abonosit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `abono` int(11) NOT NULL,
  `recibo` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.r_actividad
CREATE TABLE IF NOT EXISTS `r_actividad` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descrip` text,
  `codigo` varchar(10) DEFAULT NULL,
  `monto` decimal(19,2) DEFAULT '0.00',
  `monto2` decimal(19,2) DEFAULT '0.00',
  `aforo` decimal(19,2) DEFAULT '0.00',
  `mintribu` decimal(19,2) DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.r_caja
CREATE TABLE IF NOT EXISTS `r_caja` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_contador` int(11) DEFAULT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `proxnumero` int(11) DEFAULT '1',
  `punto_codbanc` varchar(5) DEFAULT NULL,
  `defecto_codbanc` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.r_cerrar
CREATE TABLE IF NOT EXISTS `r_cerrar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.r_conc
CREATE TABLE IF NOT EXISTS `r_conc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_presup` int(11) NOT NULL,
  `denomi` varchar(80) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK__r_presup` (`id_presup`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.r_concit
CREATE TABLE IF NOT EXISTS `r_concit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_conc` int(11) NOT NULL,
  `ano` int(11) NOT NULL,
  `frecuencia` smallint(6) NOT NULL DEFAULT '0',
  `freval` smallint(6) NOT NULL,
  `acronimo` varchar(50) NOT NULL,
  `denomi` varchar(80) NOT NULL,
  `formula` text NOT NULL,
  `requiere` varchar(50) DEFAULT NULL,
  `modo` varchar(10) DEFAULT NULL,
  `expira` char(1) DEFAULT NULL,
  `deleted` bit(1) DEFAULT b'0',
  PRIMARY KEY (`id`),
  KEY `FK__r_conc` (`id_conc`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.r_contador
CREATE TABLE IF NOT EXISTS `r_contador` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) DEFAULT NULL,
  `proxnumero` int(11) DEFAULT NULL,
  `serie` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.r_contribu
CREATE TABLE IF NOT EXISTS `r_contribu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` char(1) DEFAULT 'S',
  `rifci` varchar(12) DEFAULT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `id_parroquia` int(11) DEFAULT NULL,
  `id_zona` int(11) DEFAULT NULL,
  `id_sector` int(11) DEFAULT NULL,
  `dir1` varchar(255) DEFAULT NULL,
  `dir2` varchar(255) DEFAULT NULL,
  `dir3` varchar(255) DEFAULT NULL,
  `dir4` varchar(255) NOT NULL,
  `patente` char(1) DEFAULT 'N',
  `nro` varchar(10) DEFAULT NULL,
  `id_negocio` int(11) DEFAULT NULL,
  `id_repre` int(11) DEFAULT NULL,
  `objeto` text,
  `observa` text,
  `archivo` varchar(50) DEFAULT NULL,
  `reg_nro` varchar(50) DEFAULT NULL,
  `reg_tomo` varchar(50) DEFAULT NULL,
  `reg_fecha` varchar(50) DEFAULT NULL,
  `p_tipo` varchar(50) DEFAULT NULL,
  `rif` varchar(12) DEFAULT NULL,
  `nomfis` varchar(100) DEFAULT NULL,
  `activo` char(1) DEFAULT 'S',
  PRIMARY KEY (`id`),
  KEY `rifci` (`rifci`),
  KEY `nombre` (`nombre`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.r_contribuit
CREATE TABLE IF NOT EXISTS `r_contribuit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_contribu` int(11) DEFAULT NULL,
  `id_contribuit` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.r_cxc
CREATE TABLE IF NOT EXISTS `r_cxc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_contribu` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `numero` varchar(12) DEFAULT NULL,
  `rifci` varchar(12) DEFAULT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `monto` decimal(19,2) DEFAULT '0.00',
  `id_parroquia` int(11) DEFAULT NULL,
  `parroquia` varchar(100) DEFAULT NULL,
  `id_zona` int(11) DEFAULT NULL,
  `zona` varchar(100) DEFAULT NULL,
  `dir1` varchar(255) DEFAULT NULL,
  `dir2` varchar(255) DEFAULT NULL,
  `dir3` varchar(255) DEFAULT NULL,
  `dir4` varchar(255) DEFAULT NULL,
  `razon` varchar(255) DEFAULT NULL,
  `solvencia` varchar(10) DEFAULT NULL,
  `solvenciab` varchar(10) DEFAULT NULL,
  `licores` varchar(10) DEFAULT NULL,
  `caja` int(11) DEFAULT NULL,
  `estampa` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.r_cxcit
CREATE TABLE IF NOT EXISTS `r_cxcit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_cxc` int(11) DEFAULT NULL,
  `id_concit` int(11) DEFAULT NULL,
  `id_conc` int(11) DEFAULT NULL,
  `id_vehiculo` int(11) DEFAULT NULL,
  `id_inmueble` int(11) DEFAULT NULL,
  `id_publicidad` int(11) DEFAULT NULL,
  `ano` int(11) DEFAULT NULL,
  `frecuencia` smallint(6) DEFAULT '0',
  `freval` smallint(6) DEFAULT NULL,
  `base` decimal(19,2) DEFAULT '0.00',
  `monto` decimal(19,2) DEFAULT '0.00',
  `observa` varchar(255) DEFAULT NULL,
  `acronimo` varchar(50) NOT NULL,
  `denomi` varchar(80) NOT NULL,
  `i_id_parroquia` int(11) DEFAULT NULL,
  `i_parroquia` varchar(100) DEFAULT NULL,
  `i_id_zona` int(11) DEFAULT NULL,
  `i_zona` varchar(100) DEFAULT NULL,
  `i_dir1` varchar(255) DEFAULT NULL,
  `i_dir2` varchar(255) DEFAULT NULL,
  `i_dir3` varchar(255) DEFAULT NULL,
  `i_dir4` varchar(255) DEFAULT NULL,
  `v_placa` varchar(12) DEFAULT NULL,
  `i_catastro` varchar(20) DEFAULT NULL,
  `requiere` varchar(20) DEFAULT NULL,
  `modo` varchar(10) DEFAULT NULL,
  `partida` varchar(20) DEFAULT NULL,
  `v_marca` varchar(50) DEFAULT NULL,
  `v_modelo` varchar(50) DEFAULT NULL,
  `partida_denomi` varchar(100) DEFAULT NULL,
  `conc_denomi` varchar(100) DEFAULT NULL,
  `p_id_tipo` int(11) DEFAULT NULL,
  `p_tipo_descrip` varchar(100) DEFAULT NULL,
  `expira` char(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.r_c_1
CREATE TABLE IF NOT EXISTS `r_c_1` (
  `numero` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` char(10) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`numero`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.r_inmueble
CREATE TABLE IF NOT EXISTS `r_inmueble` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_contribu` int(11) DEFAULT NULL,
  `id_parroquia` int(11) DEFAULT NULL,
  `id_zona` int(11) DEFAULT NULL,
  `id_clase` int(11) DEFAULT NULL,
  `id_clasea` int(11) DEFAULT NULL,
  `id_negocio` int(11) DEFAULT NULL,
  `catastro` varchar(50) DEFAULT NULL,
  `techo` char(1) DEFAULT NULL,
  `mt2` decimal(19,2) DEFAULT '0.00',
  `monto` decimal(19,2) DEFAULT '0.00',
  `dir1` varchar(255) DEFAULT NULL,
  `dir2` varchar(255) DEFAULT NULL,
  `dir3` varchar(255) DEFAULT NULL,
  `dir4` varchar(255) DEFAULT NULL,
  `tipoi` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.r_interes
CREATE TABLE IF NOT EXISTS `r_interes` (
  `ano` int(11) NOT NULL,
  `mes` int(11) NOT NULL,
  `monto` decimal(19,2) DEFAULT NULL,
  PRIMARY KEY (`ano`,`mes`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.r_mbanc
CREATE TABLE IF NOT EXISTS `r_mbanc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `abono` int(11) NOT NULL,
  `codmbanc` int(11) NOT NULL,
  `codbanc` varchar(10) NOT NULL,
  `tipo_doc` char(2) NOT NULL,
  `cheque` text NOT NULL,
  `monto` decimal(19,2) NOT NULL,
  `fecha` date NOT NULL,
  `concepto` text NOT NULL,
  `id_mbancrel` int(11) DEFAULT NULL,
  `id_mbanc` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_mbancrel` (`id_mbancrel`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.r_mbancrel
CREATE TABLE IF NOT EXISTS `r_mbancrel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codbanc` varchar(10) NOT NULL,
  `tipo_doc` char(2) NOT NULL,
  `cheque` text NOT NULL,
  `monto` decimal(19,2) NOT NULL,
  `total` decimal(19,2) NOT NULL,
  `fecha` date NOT NULL,
  `fechaing` date NOT NULL,
  `concepto` text NOT NULL,
  `id_mbanc` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.r_negocio
CREATE TABLE IF NOT EXISTS `r_negocio` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descrip` text,
  `codigo` varchar(10) DEFAULT NULL,
  `monto` decimal(19,2) DEFAULT '0.00',
  `monto2` decimal(19,2) DEFAULT '0.00',
  `aforo` decimal(19,2) DEFAULT '0.00',
  `mintribu` decimal(19,2) DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.r_otrospagos
CREATE TABLE IF NOT EXISTS `r_otrospagos` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `numero` varchar(15) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `rifci` varchar(20) DEFAULT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `concepto` varchar(255) DEFAULT NULL,
  `monto` decimal(19,2) DEFAULT '0.00',
  `observa` text,
  PRIMARY KEY (`id`),
  KEY `rifci` (`rifci`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.r_presup
CREATE TABLE IF NOT EXISTS `r_presup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partida` varchar(13) NOT NULL,
  `denomi` varchar(80) NOT NULL,
  `estimado` decimal(19,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `partida` (`partida`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.r_publicidad
CREATE TABLE IF NOT EXISTS `r_publicidad` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_contribu` int(11) DEFAULT NULL,
  `id_tipo` int(11) DEFAULT NULL,
  `id_parroquia` int(11) DEFAULT NULL,
  `id_zona` int(11) DEFAULT NULL,
  `id_sector` int(11) DEFAULT NULL,
  `dir1` varchar(255) DEFAULT NULL,
  `dir2` varchar(255) DEFAULT NULL,
  `dir3` varchar(255) DEFAULT NULL,
  `dir4` varchar(255) NOT NULL,
  `alto` decimal(10,2) NOT NULL DEFAULT '0.00',
  `ancho` decimal(10,2) NOT NULL DEFAULT '0.00',
  `dimension` decimal(10,2) NOT NULL DEFAULT '0.00',
  `descrip` text,
  `ultano` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_tipo` (`id_tipo`),
  KEY `id_parroquia` (`id_parroquia`),
  KEY `id_zona` (`id_zona`),
  KEY `id_sector` (`id_sector`),
  KEY `id_contribu` (`id_contribu`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.r_recibo
CREATE TABLE IF NOT EXISTS `r_recibo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_contribu` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `numero` varchar(12) DEFAULT NULL,
  `rifci` varchar(12) DEFAULT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `monto` decimal(19,2) DEFAULT '0.00',
  `id_parroquia` int(11) DEFAULT NULL,
  `parroquia` varchar(100) DEFAULT NULL,
  `id_zona` int(11) DEFAULT NULL,
  `zona` varchar(100) DEFAULT NULL,
  `dir1` varchar(255) DEFAULT NULL,
  `dir2` varchar(255) DEFAULT NULL,
  `dir3` varchar(255) DEFAULT NULL,
  `dir4` varchar(255) DEFAULT NULL,
  `razon` varchar(255) DEFAULT NULL,
  `solvencia` varchar(10) DEFAULT NULL,
  `solvenciab` varchar(10) DEFAULT NULL,
  `licores` varchar(10) DEFAULT NULL,
  `caja` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.r_reciboit
CREATE TABLE IF NOT EXISTS `r_reciboit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_recibo` int(11) DEFAULT NULL,
  `id_concit` int(11) DEFAULT NULL,
  `id_conc` int(11) DEFAULT NULL,
  `id_cxcit` int(11) DEFAULT NULL,
  `id_vehiculo` int(11) DEFAULT NULL,
  `id_inmueble` int(11) DEFAULT NULL,
  `id_publicidad` int(11) DEFAULT NULL,
  `ano` int(11) DEFAULT NULL,
  `frecuencia` smallint(1) DEFAULT '0',
  `freval` smallint(6) DEFAULT NULL,
  `base` decimal(19,2) DEFAULT '0.00',
  `monto` decimal(19,2) DEFAULT '0.00',
  `observa` varchar(255) DEFAULT NULL,
  `acronimo` varchar(50) NOT NULL,
  `denomi` varchar(80) NOT NULL,
  `i_id_parroquia` int(11) DEFAULT NULL,
  `i_parroquia` varchar(100) DEFAULT NULL,
  `i_id_zona` int(11) DEFAULT NULL,
  `i_zona` varchar(100) DEFAULT NULL,
  `i_dir1` varchar(255) DEFAULT NULL,
  `i_dir2` varchar(255) DEFAULT NULL,
  `i_dir3` varchar(255) DEFAULT NULL,
  `i_dir4` varchar(255) DEFAULT NULL,
  `v_placa` varchar(12) DEFAULT NULL,
  `i_catastro` varchar(20) DEFAULT NULL,
  `requiere` varchar(20) DEFAULT NULL,
  `modo` varchar(10) DEFAULT NULL,
  `partida` varchar(20) DEFAULT NULL,
  `v_marca` varchar(50) DEFAULT NULL,
  `v_modelo` varchar(50) DEFAULT NULL,
  `partida_denomi` varchar(100) DEFAULT NULL,
  `conc_denomi` varchar(100) DEFAULT NULL,
  `p_id_tipo` int(11) DEFAULT NULL,
  `p_tipo_descrip` varchar(100) DEFAULT NULL,
  `expira` char(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_cxcit` (`id_cxcit`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.r_sector
CREATE TABLE IF NOT EXISTS `r_sector` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descrip` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.r_vehiculo
CREATE TABLE IF NOT EXISTS `r_vehiculo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_contribu` int(11) NOT NULL,
  `id_tipo` int(11) DEFAULT NULL,
  `id_marca` int(11) DEFAULT NULL,
  `id_modelo` int(11) DEFAULT NULL,
  `id_clase` int(11) NOT NULL,
  `placa` varchar(12) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `capacidad` int(11) DEFAULT NULL,
  `ejes` int(11) DEFAULT NULL,
  `ano` smallint(6) DEFAULT NULL,
  `peso` decimal(19,2) DEFAULT NULL,
  `serialc` varchar(20) DEFAULT NULL,
  `serialm` varchar(20) DEFAULT NULL,
  `descrip` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for view tortuga.r_v_conc
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `r_v_conc` (
	`id` INT(11) NOT NULL,
	`id_conc` INT(11) NOT NULL,
	`ano` INT(11) NOT NULL,
	`acronimo` VARCHAR(50) NOT NULL COLLATE 'utf8_general_ci',
	`denomi` VARCHAR(80) NOT NULL COLLATE 'utf8_general_ci',
	`requiere` VARCHAR(50) NULL COLLATE 'utf8_general_ci',
	`denomiconc` VARCHAR(80) NULL COLLATE 'utf8_general_ci',
	`partida` VARCHAR(13) NOT NULL COLLATE 'utf8_general_ci',
	`denopart` VARCHAR(80) NOT NULL COLLATE 'utf8_general_ci',
	`modo` VARCHAR(10) NULL COLLATE 'utf8_general_ci',
	`frecuencia` SMALLINT(6) NOT NULL,
	`freval` SMALLINT(6) NOT NULL,
	`expira` CHAR(1) NULL COLLATE 'utf8_general_ci'
) ENGINE=MyISAM;


-- Dumping structure for view tortuga.r_v_contribu
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `r_v_contribu` (
	`id` INT(11) NOT NULL,
	`rifci` VARCHAR(12) NULL COLLATE 'utf8_general_ci',
	`nombre` VARCHAR(100) NULL COLLATE 'utf8_general_ci',
	`telefono` VARCHAR(50) NULL COLLATE 'utf8_general_ci',
	`id_parroquia` INT(11) NULL,
	`id_zona` INT(11) NULL,
	`dir1` VARCHAR(255) NULL COLLATE 'utf8_general_ci',
	`dir2` VARCHAR(255) NULL COLLATE 'utf8_general_ci',
	`dir3` VARCHAR(255) NULL COLLATE 'utf8_general_ci',
	`dir4` VARCHAR(255) NOT NULL COLLATE 'utf8_general_ci',
	`patente` CHAR(1) NULL COLLATE 'utf8_general_ci',
	`nro` VARCHAR(10) NULL COLLATE 'utf8_general_ci',
	`id_negocio` INT(11) NULL,
	`id_repre` INT(11) NULL,
	`objeto` TEXT NULL COLLATE 'utf8_general_ci',
	`observa` TEXT NULL COLLATE 'utf8_general_ci',
	`archivo` VARCHAR(50) NULL COLLATE 'utf8_general_ci',
	`parroquia` VARCHAR(50) NULL COLLATE 'utf8_general_ci',
	`zona` VARCHAR(100) NULL COLLATE 'utf8_general_ci',
	`zona_monto` DECIMAL(19,2) NULL,
	`negocio_monto` DECIMAL(19,2) NULL,
	`negocio_monto2` DECIMAL(19,2) NULL
) ENGINE=MyISAM;


-- Dumping structure for view tortuga.r_v_inmueble
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `r_v_inmueble` (
	`id` INT(11) NOT NULL,
	`catastro` VARCHAR(50) NULL COLLATE 'utf8_general_ci',
	`parroquia` VARCHAR(50) NULL COLLATE 'utf8_general_ci',
	`zona` VARCHAR(100) NULL COLLATE 'utf8_general_ci',
	`dir1` VARCHAR(255) NULL COLLATE 'utf8_general_ci',
	`dir2` VARCHAR(255) NULL COLLATE 'utf8_general_ci',
	`dir3` VARCHAR(255) NULL COLLATE 'utf8_general_ci',
	`dir4` VARCHAR(255) NULL COLLATE 'utf8_general_ci',
	`mt2` DECIMAL(19,2) NULL,
	`techo` CHAR(1) NULL COLLATE 'utf8_general_ci',
	`techodecrip` VARCHAR(10) NULL COLLATE 'utf8_general_ci',
	`monto` DECIMAL(19,2) NULL,
	`id_contribu` INT(11) NULL,
	`direccion` TEXT NULL COLLATE 'utf8_general_ci',
	`id_parroquia` INT(11) NULL,
	`id_zona` INT(11) NULL,
	`zona_monto` DECIMAL(19,2) NULL,
	`id_clase` INT(11) NULL,
	`clase` VARCHAR(255) NULL COLLATE 'utf8_general_ci',
	`clase_monto` DECIMAL(19,2) NULL,
	`tipoi` VARCHAR(255) NULL COLLATE 'utf8_general_ci',
	`id_clasea` INT(11) NULL,
	`clasea` VARCHAR(255) NULL COLLATE 'utf8_general_ci',
	`clasea_monto` DECIMAL(19,2) NULL,
	`clase_monto2` DECIMAL(19,2) NULL,
	`clasea_monto2` DECIMAL(19,2) NULL,
	`negocio_monto` DECIMAL(19,2) NULL,
	`negocio_monto2` DECIMAL(19,2) NULL
) ENGINE=MyISAM;


-- Dumping structure for view tortuga.r_v_publicidad
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `r_v_publicidad` (
	`id` INT(11) NOT NULL,
	`id_contribu` INT(11) NULL,
	`id_tipo` INT(11) NULL,
	`id_parroquia` INT(11) NULL,
	`id_zona` INT(11) NULL,
	`id_sector` INT(11) NULL,
	`direccion` TEXT NULL COLLATE 'utf8_general_ci',
	`dir1` VARCHAR(255) NULL COLLATE 'utf8_general_ci',
	`dir2` VARCHAR(255) NULL COLLATE 'utf8_general_ci',
	`dir3` VARCHAR(255) NULL COLLATE 'utf8_general_ci',
	`dir4` VARCHAR(255) NOT NULL COLLATE 'utf8_general_ci',
	`alto` DECIMAL(10,2) NOT NULL,
	`ancho` DECIMAL(10,2) NOT NULL,
	`codigo` VARCHAR(10) NOT NULL COLLATE 'utf8_general_ci',
	`descrip` VARCHAR(100) NULL COLLATE 'utf8_general_ci',
	`monto` DECIMAL(19,2) NULL,
	`parroquia` VARCHAR(50) NULL COLLATE 'utf8_general_ci',
	`zona` VARCHAR(100) NULL COLLATE 'utf8_general_ci'
) ENGINE=MyISAM;


-- Dumping structure for view tortuga.r_v_vehiculo
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `r_v_vehiculo` (
	`capacidad` INT(11) NULL,
	`id` INT(11) NOT NULL,
	`placa` VARCHAR(12) NULL COLLATE 'utf8_general_ci',
	`ano` SMALLINT(6) NULL,
	`color` VARCHAR(50) NULL COLLATE 'utf8_general_ci',
	`marca` VARCHAR(100) NULL COLLATE 'utf8_general_ci',
	`modelo` VARCHAR(100) NULL COLLATE 'utf8_general_ci',
	`tipo` VARCHAR(100) NULL COLLATE 'utf8_general_ci',
	`clase` VARCHAR(100) NULL COLLATE 'utf8_general_ci',
	`id_tipo` INT(11) NULL,
	`id_marca` INT(11) NULL,
	`id_modelo` INT(11) NULL,
	`id_clase` INT(11) NOT NULL,
	`id_contribu` INT(11) NOT NULL
) ENGINE=MyISAM;


-- Dumping structure for view tortuga.r_v_xcobrar
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `r_v_xcobrar` (
	`id` INT(11) NOT NULL,
	`id_contribu` INT(11) NOT NULL,
	`fecha` DATE NOT NULL,
	`numero` VARCHAR(12) NULL COLLATE 'utf8_general_ci',
	`rifci` VARCHAR(12) NULL COLLATE 'utf8_general_ci',
	`nombre` VARCHAR(100) NULL COLLATE 'utf8_general_ci',
	`telefono` VARCHAR(50) NULL COLLATE 'utf8_general_ci',
	`monto` DECIMAL(19,2) NULL,
	`id_parroquia` INT(11) NULL,
	`parroquia` VARCHAR(100) NULL COLLATE 'utf8_general_ci',
	`id_zona` INT(11) NULL,
	`zona` VARCHAR(100) NULL COLLATE 'utf8_general_ci',
	`dir1` VARCHAR(255) NULL COLLATE 'utf8_general_ci',
	`dir2` VARCHAR(255) NULL COLLATE 'utf8_general_ci',
	`dir3` VARCHAR(255) NULL COLLATE 'utf8_general_ci',
	`dir4` VARCHAR(255) NULL COLLATE 'utf8_general_ci',
	`razon` VARCHAR(255) NULL COLLATE 'utf8_general_ci',
	`solvencia` VARCHAR(10) NULL COLLATE 'utf8_general_ci',
	`solvenciab` VARCHAR(10) NULL COLLATE 'utf8_general_ci',
	`licores` VARCHAR(10) NULL COLLATE 'utf8_general_ci',
	`caja` INT(11) NULL
) ENGINE=MyISAM;


-- Dumping structure for view tortuga.r_v_xrecaudar
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `r_v_xrecaudar` (
	`id` INT(11) NOT NULL,
	`ano` INT(11) NOT NULL,
	`acronimo` VARCHAR(50) NOT NULL COLLATE 'utf8_general_ci',
	`denomi` VARCHAR(80) NOT NULL COLLATE 'utf8_general_ci',
	`requiere` VARCHAR(50) NULL COLLATE 'utf8_general_ci',
	`id_inmueble` INT(11) NULL,
	`catastro` VARCHAR(50) NULL COLLATE 'utf8_general_ci',
	`id_vehiculo` INT(11) NULL,
	`placa` VARCHAR(12) NULL COLLATE 'utf8_general_ci',
	`id_contribu` INT(11) NULL,
	`observa` TEXT NULL COLLATE 'utf8_general_ci',
	`formula` TEXT NOT NULL COLLATE 'utf8_general_ci'
) ENGINE=MyISAM;


-- Dumping structure for table tortuga.r_zona
CREATE TABLE IF NOT EXISTS `r_zona` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descrip` varchar(100) NOT NULL,
  `monto` decimal(19,2) NOT NULL DEFAULT '0.00',
  `monto2` decimal(19,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.sbanc
CREATE TABLE IF NOT EXISTS `sbanc` (
  `codbanc` varchar(5) NOT NULL DEFAULT '',
  `ano` char(4) NOT NULL DEFAULT '',
  `mes` char(2) NOT NULL DEFAULT '',
  `saldo` decimal(19,2) DEFAULT NULL,
  `fecha` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`codbanc`,`fecha`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.sectores
CREATE TABLE IF NOT EXISTS `sectores` (
  `id_sect` int(11) NOT NULL AUTO_INCREMENT,
  `sect_pres` char(2) DEFAULT NULL,
  `nomb_sect` varchar(80) DEFAULT NULL,
  PRIMARY KEY (`id_sect`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.serie
CREATE TABLE IF NOT EXISTS `serie` (
  `valor` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hexa` varchar(11) DEFAULT NULL,
  PRIMARY KEY (`valor`),
  KEY `valor` (`valor`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.serie2
CREATE TABLE IF NOT EXISTS `serie2` (
  `valor` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hexa` varchar(11) DEFAULT NULL,
  PRIMARY KEY (`valor`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.sfpa
CREATE TABLE IF NOT EXISTS `sfpa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `abono` int(11) NOT NULL,
  `codmbanc` int(11) NOT NULL,
  `codbanc` varchar(10) NOT NULL,
  `tipo_doc` char(2) NOT NULL,
  `cheque` text NOT NULL,
  `monto` decimal(19,2) NOT NULL,
  `fecha` date NOT NULL,
  `observa` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.sintesis
CREATE TABLE IF NOT EXISTS `sintesis` (
  `idSintesis` int(11) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(35) NOT NULL,
  `Descripcion` varchar(255) NOT NULL,
  `prioridad` int(11) NOT NULL,
  PRIMARY KEY (`idSintesis`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Tabla para normalizar las sintesis de la comunicacion';

-- Data exporting was unselected.


-- Dumping structure for table tortuga.sinv
CREATE TABLE IF NOT EXISTS `sinv` (
  `codigo` varchar(15) NOT NULL DEFAULT '',
  `grupo` varchar(4) DEFAULT NULL,
  `descrip` varchar(45) DEFAULT NULL,
  `descrip2` varchar(45) DEFAULT NULL,
  `unidad` varchar(8) DEFAULT NULL,
  `ubica` varchar(9) DEFAULT NULL,
  `tipo` varchar(8) DEFAULT NULL,
  `clave` varchar(8) DEFAULT NULL,
  `comision` decimal(5,2) DEFAULT NULL,
  `enlace` varchar(15) DEFAULT NULL,
  `prov1` varchar(5) DEFAULT NULL,
  `prepro1` decimal(10,2) DEFAULT NULL,
  `pfecha1` date DEFAULT NULL,
  `prov2` varchar(5) DEFAULT NULL,
  `prepro2` decimal(10,2) DEFAULT NULL,
  `pfecha2` date DEFAULT NULL,
  `prov3` varchar(5) DEFAULT NULL,
  `prepro3` decimal(10,2) DEFAULT NULL,
  `pfecha3` date DEFAULT NULL,
  `pond` decimal(13,2) DEFAULT NULL,
  `ultimo` decimal(13,2) DEFAULT NULL,
  `pvp_s` decimal(15,2) DEFAULT NULL,
  `pvp_bs` decimal(10,2) DEFAULT NULL,
  `pvpprc` decimal(6,2) DEFAULT NULL,
  `contbs` decimal(10,2) DEFAULT NULL,
  `contprc` decimal(6,2) DEFAULT NULL,
  `mayobs` decimal(10,2) DEFAULT NULL,
  `mayoprc` decimal(6,2) DEFAULT NULL,
  `exmin` decimal(12,3) DEFAULT NULL,
  `exord` decimal(12,3) DEFAULT NULL,
  `existen` decimal(12,3) DEFAULT NULL,
  `fechav` date DEFAULT NULL,
  `fechac` date DEFAULT NULL,
  `iva` decimal(6,2) DEFAULT NULL,
  `fracci` int(4) DEFAULT NULL,
  `codbar` int(7) DEFAULT NULL,
  `barras` varchar(15) DEFAULT NULL,
  `exmax` decimal(12,3) DEFAULT NULL,
  `margen1` decimal(6,2) DEFAULT NULL,
  `margen2` decimal(6,2) DEFAULT NULL,
  `margen3` decimal(6,2) DEFAULT NULL,
  `margen4` decimal(6,2) DEFAULT NULL,
  `base1` decimal(13,2) DEFAULT NULL,
  `base2` decimal(13,2) DEFAULT NULL,
  `base3` decimal(13,2) DEFAULT NULL,
  `base4` decimal(13,2) DEFAULT NULL,
  `precio1` decimal(13,2) DEFAULT NULL,
  `precio2` decimal(13,2) DEFAULT NULL,
  `precio3` decimal(13,2) DEFAULT NULL,
  `precio4` decimal(13,2) DEFAULT NULL,
  `serial` char(1) DEFAULT NULL,
  `tdecimal` char(1) DEFAULT NULL,
  `activo` char(1) DEFAULT NULL,
  `dolar` decimal(13,2) DEFAULT NULL,
  `redecen` char(1) DEFAULT NULL,
  `formcal` char(1) DEFAULT NULL,
  `fordeci` int(2) DEFAULT NULL,
  `garantia` int(3) DEFAULT NULL,
  `costotal` decimal(19,2) DEFAULT NULL,
  `fechac2` date DEFAULT NULL,
  `peso` decimal(12,3) DEFAULT NULL,
  `alterno` varchar(15) DEFAULT NULL,
  `derivado` varchar(15) DEFAULT '',
  `cantderi` decimal(10,2) DEFAULT '0.00',
  `pondcal` decimal(12,2) DEFAULT NULL,
  `aumento` decimal(7,2) DEFAULT '0.00',
  `clase` char(1) DEFAULT NULL,
  `exdes` decimal(12,3) DEFAULT '0.000',
  `marca` varchar(22) DEFAULT NULL,
  `modelo` varchar(20) DEFAULT NULL,
  `oferta` decimal(17,2) DEFAULT '0.00',
  `fdesde` date DEFAULT '0000-00-00',
  `fhasta` date DEFAULT '0000-00-00',
  `ppos1` decimal(15,2) DEFAULT '0.00',
  `ppos2` decimal(15,2) DEFAULT '0.00',
  `ppos3` decimal(15,2) DEFAULT '0.00',
  `ppos4` decimal(15,2) DEFAULT '0.00',
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `gasto` char(6) DEFAULT '',
  `linea` char(2) DEFAULT NULL,
  `depto` char(3) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo` (`codigo`),
  KEY `grupo` (`grupo`,`codigo`),
  FULLTEXT KEY `descrip` (`descrip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.sinvactu
CREATE TABLE IF NOT EXISTS `sinvactu` (
  `codigo` varchar(15) NOT NULL DEFAULT '',
  `descrip` varchar(45) DEFAULT NULL,
  `clave` varchar(8) DEFAULT NULL,
  `descrip2` varchar(45) DEFAULT NULL,
  `antdescrip2` varchar(45) DEFAULT NULL,
  `grupo` varchar(4) DEFAULT NULL,
  `costo` decimal(13,2) unsigned DEFAULT NULL,
  `precio1` decimal(13,2) unsigned DEFAULT NULL,
  `antcosto` decimal(13,2) unsigned DEFAULT NULL,
  `antprecio1` decimal(13,2) unsigned DEFAULT NULL,
  `iva` decimal(6,2) unsigned DEFAULT NULL,
  `antiva` decimal(6,2) unsigned DEFAULT NULL,
  `precio2` decimal(13,2) DEFAULT NULL,
  `precio3` decimal(13,2) DEFAULT NULL,
  `precio4` decimal(13,2) unsigned DEFAULT NULL,
  `base1` decimal(13,2) unsigned DEFAULT NULL,
  `base2` decimal(13,2) DEFAULT NULL,
  `base3` decimal(13,2) unsigned DEFAULT NULL,
  `base4` decimal(13,2) unsigned DEFAULT NULL,
  `margen1` decimal(13,2) unsigned DEFAULT NULL,
  `margen2` decimal(13,2) unsigned DEFAULT NULL,
  `margen3` decimal(13,2) unsigned DEFAULT NULL,
  `margen4` decimal(13,2) unsigned DEFAULT NULL,
  `antdescrip` varchar(45) DEFAULT NULL,
  `antclave` varchar(8) DEFAULT NULL,
  `antgrupo` varchar(4) DEFAULT NULL,
  `antprecio2` decimal(13,2) unsigned DEFAULT NULL,
  `antprecio3` decimal(13,2) unsigned DEFAULT NULL,
  `antprecio4` decimal(13,2) unsigned DEFAULT NULL,
  `antbase1` decimal(13,2) unsigned DEFAULT NULL,
  `antbase2` decimal(13,2) unsigned DEFAULT NULL,
  `antbase3` decimal(13,2) unsigned DEFAULT NULL,
  `antbase4` decimal(13,2) unsigned DEFAULT NULL,
  `antmargen1` decimal(13,2) unsigned DEFAULT NULL,
  `antmargen2` decimal(13,2) unsigned DEFAULT NULL,
  `antmargen3` decimal(13,2) unsigned DEFAULT NULL,
  `antmargen4` decimal(13,2) unsigned DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.sinvfot
CREATE TABLE IF NOT EXISTS `sinvfot` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sinv_id` int(10) unsigned NOT NULL,
  `codigo` varchar(15) DEFAULT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `alto_px` smallint(5) unsigned DEFAULT NULL,
  `ancho_px` smallint(6) DEFAULT NULL,
  `ruta` varchar(100) DEFAULT NULL,
  `comentario` mediumtext,
  `estampa` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `principal` varchar(3) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `foto` (`codigo`,`nombre`),
  KEY `id_2` (`id`,`codigo`),
  KEY `sinv_id` (`sinv_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.sipred03
CREATE TABLE IF NOT EXISTS `sipred03` (
  `row_id` int(11) NOT NULL AUTO_INCREMENT,
  `sector` char(2) DEFAULT NULL,
  `progra` char(2) DEFAULT NULL,
  `subpro` char(2) DEFAULT NULL,
  `proyec` char(2) DEFAULT NULL,
  `activi` char(2) DEFAULT NULL,
  `nivjer` int(11) DEFAULT NULL,
  `denomi` char(50) DEFAULT NULL,
  `unidej` char(40) DEFAULT NULL,
  `unidej2` char(40) DEFAULT NULL,
  `espec1` char(14) DEFAULT NULL,
  `espec2` char(14) DEFAULT NULL,
  `espec3` char(14) DEFAULT NULL,
  PRIMARY KEY (`row_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.solicitudes
CREATE TABLE IF NOT EXISTS `solicitudes` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `personas` int(10) DEFAULT NULL,
  `ccomunicaciones` int(10) DEFAULT NULL,
  `sintesis` int(10) DEFAULT NULL,
  `prioridad` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.solvencia
CREATE TABLE IF NOT EXISTS `solvencia` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `tipo` varchar(20) DEFAULT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `cedula` varchar(100) DEFAULT NULL,
  `rif` varchar(100) DEFAULT NULL,
  `direccion` varchar(100) DEFAULT NULL,
  `concepto` varchar(200) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `contribu` varchar(6) DEFAULT NULL,
  `numero` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.sprv
CREATE TABLE IF NOT EXISTS `sprv` (
  `proveed` varchar(5) NOT NULL DEFAULT '',
  `nombre` varchar(500) DEFAULT NULL,
  `cate` varchar(20) DEFAULT NULL,
  `copre` varchar(11) DEFAULT NULL,
  `rif` varchar(12) DEFAULT NULL,
  `tipo` char(1) DEFAULT NULL,
  `grupo` varchar(4) DEFAULT NULL,
  `gr_desc` varchar(25) DEFAULT NULL,
  `direc1` text,
  `direc2` varchar(105) DEFAULT NULL,
  `dcredito` decimal(3,0) DEFAULT '0',
  `despacho` decimal(3,0) DEFAULT NULL,
  `direc3` varchar(105) DEFAULT NULL,
  `telefono` varchar(40) DEFAULT NULL,
  `contacto` varchar(100) DEFAULT NULL,
  `cuenta` varchar(15) DEFAULT NULL,
  `cliente` varchar(5) DEFAULT NULL,
  `observa` mediumtext,
  `numcuent` varchar(50) DEFAULT NULL,
  `ncorto` varchar(20) DEFAULT NULL,
  `nit` varchar(12) DEFAULT NULL,
  `codigo` varchar(10) DEFAULT NULL,
  `email` varchar(30) DEFAULT NULL,
  `url` varchar(30) DEFAULT NULL,
  `ocompra` char(1) DEFAULT NULL,
  `objeto` mediumtext,
  `banco1` char(3) DEFAULT NULL,
  `cuenta1` varchar(25) DEFAULT NULL,
  `banco2` char(3) DEFAULT NULL,
  `cuenta2` varchar(25) DEFAULT NULL,
  `tiva` char(1) DEFAULT 'N',
  `nomfis` varchar(200) DEFAULT NULL,
  `visita` varchar(9) DEFAULT NULL,
  `reteiva` decimal(5,2) unsigned DEFAULT NULL,
  `maximo` decimal(19,2) NOT NULL DEFAULT '0.00',
  `anti` decimal(19,2) NOT NULL DEFAULT '0.00',
  `demos` decimal(19,2) NOT NULL DEFAULT '0.00',
  `rnc` varchar(20) DEFAULT NULL,
  `activo` char(1) DEFAULT NULL,
  `vencernc` date DEFAULT NULL,
  `ingreso` date DEFAULT NULL,
  `vence` date DEFAULT NULL,
  `contaci` varchar(20) DEFAULT NULL,
  `cod_prov` varchar(5) DEFAULT NULL,
  `concepto` mediumtext,
  `conceptof` mediumtext,
  PRIMARY KEY (`proveed`),
  KEY `proveed` (`proveed`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for procedure tortuga.sp_banc_recalculo
DELIMITER //
CREATE DEFINER=`datasis`@`localhost` PROCEDURE `sp_banc_recalculo`()
BEGIN
drop table if exists tsaldo;
CREATE TABLE `tsaldo` (
	`codbanc` VARCHAR(10) NOT NULL,
	`saldo` DECIMAL(40,2) NULL DEFAULT NULL,
	PRIMARY KEY (`codbanc`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM;
INSERT INTO tsaldo
SELECT 
a.codbanc,SUM(IF(a.tipo_doc IN ('CH','ND'),-1*a.monto,IF(a.tipo_doc IN ('NC','DP'),a.monto,0))) saldo
 FROM mbanc a
 JOIN banc b ON a.codbanc=b.codbanc
  WHERE status IN ('NC','A2','E2','J2') GROUP BY codbanc;
  

INSERT IGNORE INTO tsaldo SELECT codbanc,0 FROM banc;
  
update banc a join tsaldo b ON a.codbanc=b.codbanc
set a.saldo = b.saldo;
END//
DELIMITER ;


-- Dumping structure for procedure tortuga.sp_recalculo
DELIMITER //
CREATE DEFINER=`datasis`@`localhost` PROCEDURE `sp_recalculo`()
BEGIN



UPDATE presupuesto SET comprometido =0,causado=0,opago=0,pagado=0,aumento=0,disminucion=0,traslados=0;

DROP TABLE IF EXISTS PRESUMEN;



CREATE TABLE PRESUMEN

SELECT codigoadm, fondo,codigopres,

sum(comprometido) as comprometido, sum(causado) causado, sum(opago) opago, sum(pagado) pagado, sum(aumento) aumento, sum(disminucion) disminucion, sum(traslados) traslados  

FROM view_pres a

WHERE (SELECT COUNT(*) FROM ordinal c WHERE a.codigoadm=c.codigoadm AND a.fondo=c.fondo AND a.codigopres=c.codigopres)=0 AND (comprometido<>0 OR causado<>0 OR opago<>0 OR pagado<>0 OR aumento<>0 OR disminucion<>0 OR traslados<>0)

GROUP BY codigoadm, fondo ,codigopres;



UPDATE PRESUMEN a JOIN presupuesto b ON a.codigoadm=b.codigoadm AND a.fondo=b.tipo AND a.codigopres=b.codigopres

SET b.comprometido=a.comprometido, b.causado=a.causado, b.opago=a.opago, b.pagado=a.pagado, b.aumento=a.aumento, b.disminucion=a.disminucion, b.traslados=a.traslados ;



DROP TABLE IF EXISTS PRESUMEN;



UPDATE ordinal SET comprometido =0,causado=0,opago=0,pagado=0,aumento=0,disminucion=0,traslados=0;





CREATE TABLE PRESUMEN

SELECT codigoadm, fondo,codigopres,ordinal,

sum(comprometido) as comprometido, sum(causado) causado, sum(opago) opago, sum(pagado) pagado, sum(aumento) aumento, sum(disminucion) disminucion, sum(traslados) traslados  

FROM view_pres a

WHERE (SELECT COUNT(*) FROM ordinal c WHERE a.codigoadm=c.codigoadm AND a.fondo=c.fondo AND a.codigopres=c.codigopres)>0 AND (comprometido<>0 OR causado<>0 OR opago<>0 OR pagado<>0 OR aumento<>0 OR disminucion<>0 OR traslados<>0)

GROUP BY codigoadm, fondo ,codigopres,ordinal;



UPDATE PRESUMEN a JOIN ordinal b ON a.codigoadm=b.codigoadm AND a.fondo=b.fondo AND a.codigopres=b.codigopres AND a.ordinal = b.ordinal

SET b.comprometido=a.comprometido, b.causado=a.causado, b.opago=a.opago, b.pagado=a.pagado, b.aumento=a.aumento, b.disminucion=a.disminucion, b.traslados=a.traslados;





END//
DELIMITER ;


-- Dumping structure for table tortuga.sumi
CREATE TABLE IF NOT EXISTS `sumi` (
  `codigo` varchar(15) NOT NULL DEFAULT '',
  `grupo` varchar(4) DEFAULT NULL,
  `descrip` text,
  `unidad` varchar(8) DEFAULT NULL,
  `ubica` varchar(9) DEFAULT NULL,
  `tipo` varchar(8) DEFAULT NULL,
  `pond` decimal(13,2) DEFAULT '0.00',
  `ultimo` decimal(13,2) DEFAULT '0.00',
  `exmin` decimal(12,3) DEFAULT '0.000',
  `exord` decimal(12,3) DEFAULT '0.000',
  `existen` decimal(12,3) DEFAULT '0.000',
  `codbar` int(7) DEFAULT NULL,
  `barras` varchar(15) DEFAULT NULL,
  `exmax` decimal(12,3) DEFAULT NULL,
  `serial` char(1) DEFAULT NULL,
  `tdecimal` char(1) DEFAULT NULL,
  `activo` char(1) DEFAULT NULL,
  `marca` varchar(22) DEFAULT NULL,
  `modelo` varchar(20) DEFAULT NULL,
  `linea` char(2) DEFAULT NULL,
  `depto` char(3) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo` (`codigo`),
  KEY `grupo` (`grupo`,`codigo`),
  FULLTEXT KEY `descrip` (`descrip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.sumine
CREATE TABLE IF NOT EXISTS `sumine` (
  `numero` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` date DEFAULT NULL,
  `proveed` varchar(5) DEFAULT NULL,
  `observacion` mediumtext,
  `tcantidad` decimal(12,3) unsigned DEFAULT '0.000',
  `status` char(1) DEFAULT 'P',
  `alma` varchar(4) DEFAULT NULL,
  `caub` varchar(4) DEFAULT NULL,
  `conc` int(11) DEFAULT NULL,
  `temp` int(11) DEFAULT NULL,
  PRIMARY KEY (`numero`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.suminr
CREATE TABLE IF NOT EXISTS `suminr` (
  `numero` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` date DEFAULT NULL,
  `proveed` varchar(5) DEFAULT NULL,
  `observacion` mediumtext,
  `tcantidad` decimal(12,3) unsigned DEFAULT '0.000',
  `total` decimal(19,2) unsigned DEFAULT NULL,
  `status` char(1) NOT NULL DEFAULT 'P',
  `alma` varchar(4) DEFAULT NULL,
  `caub` varchar(4) DEFAULT NULL,
  `conc` int(11) DEFAULT NULL,
  `temp` int(11) DEFAULT NULL,
  PRIMARY KEY (`numero`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.su_alma
CREATE TABLE IF NOT EXISTS `su_alma` (
  `codigo` varchar(4) NOT NULL,
  `descrip` varchar(200) DEFAULT NULL,
  `uejecuta` varchar(4) DEFAULT NULL,
  `cuenta` varchar(45) DEFAULT NULL,
  `direc` text,
  `gralma` varchar(4) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.su_caub
CREATE TABLE IF NOT EXISTS `su_caub` (
  `codigo` varchar(4) NOT NULL,
  `descrip` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.su_conc
CREATE TABLE IF NOT EXISTS `su_conc` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `descrip` mediumtext,
  `tipo` char(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.su_costo
CREATE TABLE IF NOT EXISTS `su_costo` (
  `numero` int(11) NOT NULL DEFAULT '0',
  `tipo` varchar(1) NOT NULL DEFAULT '',
  `fecha` date DEFAULT NULL,
  `codigo` varchar(15) DEFAULT NULL,
  `cantidad` decimal(22,2) DEFAULT NULL,
  `precio` decimal(19,2) DEFAULT NULL,
  `total` decimal(19,2) DEFAULT NULL,
  `cp` decimal(40,4) DEFAULT NULL,
  `promedio` decimal(23,4) DEFAULT NULL,
  `saldo` decimal(43,6) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.su_gralma
CREATE TABLE IF NOT EXISTS `su_gralma` (
  `codigo` char(4) NOT NULL,
  `descrip` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.su_itsumi
CREATE TABLE IF NOT EXISTS `su_itsumi` (
  `codigo` varchar(15) NOT NULL,
  `alma` varchar(4) NOT NULL,
  `cantidad` decimal(19,3) DEFAULT '0.000',
  PRIMARY KEY (`codigo`,`alma`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.su_ittrasla
CREATE TABLE IF NOT EXISTS `su_ittrasla` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero` int(11) unsigned DEFAULT NULL,
  `codigo` varchar(4) DEFAULT NULL,
  `cant` decimal(19,2) DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.su_trasla
CREATE TABLE IF NOT EXISTS `su_trasla` (
  `numero` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` date DEFAULT NULL,
  `concepto` varchar(200) DEFAULT NULL,
  `total` decimal(19,2) DEFAULT '0.00',
  `status` char(1) DEFAULT 'P',
  `de` varchar(4) DEFAULT NULL,
  `para` varchar(4) DEFAULT NULL,
  PRIMARY KEY (`numero`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.t5571
CREATE TABLE IF NOT EXISTS `t5571` (
  `a` varchar(255) DEFAULT NULL,
  `b` varchar(255) DEFAULT NULL,
  `c` varchar(255) DEFAULT NULL,
  `d` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.t5572
CREATE TABLE IF NOT EXISTS `t5572` (
  `a` varchar(255) DEFAULT NULL,
  `b` varchar(255) DEFAULT NULL,
  `c` varchar(255) DEFAULT NULL,
  `d` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.tban
CREATE TABLE IF NOT EXISTS `tban` (
  `cod_banc` varchar(3) NOT NULL DEFAULT '',
  `nomb_banc` tinytext,
  `url` varchar(30) DEFAULT NULL,
  `debito` decimal(6,2) DEFAULT NULL,
  `comitc` decimal(6,2) DEFAULT NULL,
  `comitd` decimal(6,2) DEFAULT NULL,
  `impuesto` decimal(6,2) DEFAULT NULL,
  `tipotra` char(2) NOT NULL DEFAULT 'NC',
  `formaca` varchar(6) DEFAULT 'NETA',
  `formacheque` varchar(25) DEFAULT 'CHEQUE',
  `abreviatura` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`cod_banc`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.tiket
CREATE TABLE IF NOT EXISTS `tiket` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `padre` char(1) DEFAULT NULL,
  `pertenece` bigint(20) unsigned DEFAULT NULL,
  `prioridad` smallint(5) unsigned DEFAULT NULL,
  `usuario` varchar(50) DEFAULT NULL,
  `contenido` mediumtext,
  `estampa` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `actualizado` timestamp NULL DEFAULT NULL,
  `estado` char(1) DEFAULT 'N',
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.tiketc
CREATE TABLE IF NOT EXISTS `tiketc` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `padre` char(1) DEFAULT NULL,
  `pertenece` bigint(20) unsigned DEFAULT NULL,
  `prioridad` smallint(5) unsigned DEFAULT NULL,
  `usuario` varchar(50) DEFAULT NULL,
  `contenido` mediumtext,
  `estampa` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `actualizado` timestamp NULL DEFAULT NULL,
  `estado` char(1) DEFAULT 'N',
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.tiketp
CREATE TABLE IF NOT EXISTS `tiketp` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `estampa` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fecha` date DEFAULT NULL,
  `codigo` varchar(20) DEFAULT NULL,
  `empresa` varchar(100) DEFAULT NULL,
  `tiket` mediumtext,
  `usuario` varchar(20) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `asignacion` varchar(20) DEFAULT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.tingresos
CREATE TABLE IF NOT EXISTS `tingresos` (
  `codigo` char(2) NOT NULL,
  `descrip` varchar(100) DEFAULT NULL,
  `grupo` char(1) DEFAULT NULL,
  `descripcion` mediumtext,
  `titu1` mediumtext,
  `titu2` mediumtext,
  `codigopres` mediumtext,
  `monto` decimal(19,2) DEFAULT '0.00',
  `contador` varchar(50) DEFAULT NULL,
  `prefijo` varchar(50) DEFAULT NULL,
  `EMPIEZA` int(11) NOT NULL,
  `activo` char(1) DEFAULT 'S',
  `formato` varchar(20) DEFAULT NULL,
  `descrip2` varchar(250) DEFAULT NULL,
  `codigo2` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.tipo
CREATE TABLE IF NOT EXISTS `tipo` (
  `tipo` char(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`tipo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.tipoe
CREATE TABLE IF NOT EXISTS `tipoe` (
  `codigo` varchar(10) NOT NULL DEFAULT '',
  `tipo` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.tipoin
CREATE TABLE IF NOT EXISTS `tipoin` (
  `tipoin` char(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`tipoin`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.tipot
CREATE TABLE IF NOT EXISTS `tipot` (
  `codigo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tipo` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.tpersonas
CREATE TABLE IF NOT EXISTS `tpersonas` (
  `idTipo` int(11) NOT NULL AUTO_INCREMENT,
  `naturaleza` varchar(35) NOT NULL,
  `nombre` varchar(35) NOT NULL,
  `descripcion` varchar(254) NOT NULL,
  PRIMARY KEY (`idTipo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Tabla donde se almacenan los tipos de personas existentes';

-- Data exporting was unselected.


-- Dumping structure for table tortuga.trami
CREATE TABLE IF NOT EXISTS `trami` (
  `numero` int(11) NOT NULL AUTO_INCREMENT,
  `compromiso` char(12) NOT NULL,
  `fecha` date NOT NULL,
  `cod_prov` char(5) NOT NULL,
  `concepto` mediumtext NOT NULL,
  `monto` decimal(19,2) NOT NULL,
  `status` char(2) NOT NULL DEFAULT 'P',
  `fondo` varchar(20) NOT NULL,
  `fcomprome` date NOT NULL,
  `fpagado` date NOT NULL,
  PRIMARY KEY (`numero`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.trasla
CREATE TABLE IF NOT EXISTS `trasla` (
  `numero` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` date DEFAULT NULL,
  `motivo` varchar(200) DEFAULT NULL,
  `tdisminucion` decimal(19,2) DEFAULT '0.00',
  `taumento` decimal(19,2) unsigned DEFAULT '0.00',
  `status` char(1) DEFAULT 'P',
  `ftrasla` date DEFAULT NULL,
  `nrooficio` varchar(50) DEFAULT NULL,
  `resolu` varchar(50) DEFAULT NULL,
  `fresolu` date DEFAULT NULL,
  PRIMARY KEY (`numero`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.tsaldo
CREATE TABLE IF NOT EXISTS `tsaldo` (
  `codbanc` varchar(10) NOT NULL,
  `saldo` decimal(40,2) DEFAULT NULL,
  PRIMARY KEY (`codbanc`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.ttributo
CREATE TABLE IF NOT EXISTS `ttributo` (
  `codigo` int(2) NOT NULL AUTO_INCREMENT,
  `nombre` char(50) DEFAULT '0',
  `cancelado` char(1) DEFAULT '0',
  `mensaje` char(10) DEFAULT '0',
  `monto` double DEFAULT '0',
  `emireci` char(1) DEFAULT '0',
  `deudante` char(1) DEFAULT '0',
  `agua` double DEFAULT '0',
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.uadministra
CREATE TABLE IF NOT EXISTS `uadministra` (
  `codigoejec` char(4) NOT NULL DEFAULT '',
  `codigo` char(4) NOT NULL DEFAULT '',
  `nombre` varchar(100) DEFAULT NULL,
  `director` varchar(100) DEFAULT NULL,
  `funciones` mediumtext,
  PRIMARY KEY (`codigoejec`,`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.uejecutora
CREATE TABLE IF NOT EXISTS `uejecutora` (
  `codigo` char(8) NOT NULL DEFAULT '',
  `nombre` varchar(100) DEFAULT NULL,
  `director` varchar(100) DEFAULT NULL,
  `funciones` mediumtext,
  `cuenta` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.uejecutoraresp
CREATE TABLE IF NOT EXISTS `uejecutoraresp` (
  `codigo` char(4) NOT NULL DEFAULT '',
  `nombre` varchar(100) DEFAULT NULL,
  `director` varchar(100) DEFAULT NULL,
  `funciones` mediumtext,
  `cuenta` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.unidad
CREATE TABLE IF NOT EXISTS `unidad` (
  `unidades` char(8) NOT NULL DEFAULT '',
  PRIMARY KEY (`unidades`),
  UNIQUE KEY `unidades` (`unidades`),
  KEY `unidades_2` (`unidades`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.usuario
CREATE TABLE IF NOT EXISTS `usuario` (
  `us_codigo` char(12) NOT NULL DEFAULT '',
  `us_nombre` char(30) DEFAULT NULL,
  `us_clave` text,
  `us_fechae` date DEFAULT NULL,
  `us_horae` char(8) DEFAULT NULL,
  `us_fechas` date DEFAULT NULL,
  `us_horas` char(8) DEFAULT NULL,
  `supervisor` char(1) NOT NULL DEFAULT 'N',
  `vendedor` char(5) NOT NULL DEFAULT '',
  `cajero` char(5) DEFAULT NULL,
  `internet` char(1) DEFAULT 'N',
  `caja` int(11) DEFAULT NULL,
  `usachat` char(1) DEFAULT 'S',
  PRIMARY KEY (`us_codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.utribu
CREATE TABLE IF NOT EXISTS `utribu` (
  `ano` varchar(10) NOT NULL,
  `valor` decimal(19,2) DEFAULT NULL,
  PRIMARY KEY (`ano`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.valores
CREATE TABLE IF NOT EXISTS `valores` (
  `nombre` varchar(100) NOT NULL DEFAULT '',
  `valor` varchar(254) DEFAULT NULL,
  `descrip` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`nombre`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Valores Varios';

-- Data exporting was unselected.


-- Dumping structure for table tortuga.vehiculo
CREATE TABLE IF NOT EXISTS `vehiculo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contribu` char(6) DEFAULT NULL,
  `clase` char(10) DEFAULT NULL,
  `marca` char(10) DEFAULT NULL,
  `tipo` char(10) DEFAULT NULL,
  `modelo` char(10) DEFAULT NULL,
  `color` char(20) DEFAULT NULL,
  `capacidad` int(11) DEFAULT NULL,
  `ejes` int(11) DEFAULT NULL,
  `serial_m` char(15) DEFAULT NULL,
  `placa_ant` char(7) DEFAULT NULL,
  `placa_act` char(9) DEFAULT NULL,
  `ano` char(4) DEFAULT NULL,
  `peso` decimal(19,2) DEFAULT NULL,
  `serial_c` char(15) DEFAULT NULL,
  `monto` double DEFAULT NULL,
  `deuda` double DEFAULT NULL,
  `ult_ano` char(4) DEFAULT NULL,
  `registrado` char(1) DEFAULT NULL,
  `asovehi` char(2) DEFAULT NULL,
  `tri1` double DEFAULT NULL,
  `tri2` double DEFAULT NULL,
  `tri3` double DEFAULT NULL,
  `tri4` double DEFAULT NULL,
  `deudacan` double DEFAULT NULL,
  `total` double DEFAULT NULL,
  `recibo` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for view tortuga.view_pres
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `view_pres` (
	`denopart` TEXT NULL COLLATE 'utf8_general_ci',
	`denoadm` VARCHAR(200) NULL COLLATE 'utf8_general_ci',
	`denofondo` VARCHAR(100) NULL COLLATE 'utf8_general_ci',
	`nombre` VARCHAR(500) NULL COLLATE 'utf8_general_ci',
	`fanulado` DATE NULL,
	`fapagado` DATE NULL,
	`decreto` VARCHAR(50) NULL COLLATE 'utf8_general_ci',
	`compromiso` VARCHAR(20) NULL COLLATE 'utf8_general_ci',
	`fecha` VARCHAR(10) NULL COLLATE 'utf8mb4_general_ci',
	`des` MEDIUMTEXT NULL COLLATE 'utf8_general_ci',
	`observa` LONGTEXT NULL COLLATE 'utf8_general_ci',
	`cod_prov` VARCHAR(5) NULL COLLATE 'utf8_general_ci',
	`numero` VARCHAR(20) NULL COLLATE 'utf8_general_ci',
	`status` VARCHAR(2) NULL COLLATE 'utf8_general_ci',
	`modo` VARCHAR(14) NULL COLLATE 'utf8_general_ci',
	`codigoadm` VARCHAR(20) NULL COLLATE 'utf8_general_ci',
	`fondo` VARCHAR(20) NULL COLLATE 'utf8_general_ci',
	`codigopres` VARCHAR(25) NULL COLLATE 'utf8_general_ci',
	`ordinal` CHAR(0) NOT NULL COLLATE 'utf8mb4_general_ci',
	`faudis` DATE NULL,
	`ftrasla` DATE NULL,
	`fapartado` DATE NULL,
	`fcomprome` DATE NULL,
	`fcausado` DATE NULL,
	`fopago` DATE NULL,
	`fpagado` DATE NULL,
	`frendi` DATE NULL,
	`apartado` DECIMAL(21,2) NULL,
	`comprometido` DECIMAL(20,2) NULL,
	`causado` DECIMAL(21,2) NULL,
	`opago` DECIMAL(21,2) NULL,
	`pagado` DECIMAL(21,2) NULL,
	`aumento` DECIMAL(21,2) NULL,
	`disminucion` DECIMAL(21,2) NULL,
	`traslados` DECIMAL(21,2) NULL,
	`asignacion` DECIMAL(19,2) NULL,
	`capartado` DECIMAL(25,2) NULL,
	`ccomprometido` DECIMAL(25,2) NULL,
	`ccausado` DECIMAL(25,2) NULL,
	`copago` DECIMAL(25,2) NULL,
	`cpagado` DECIMAL(25,2) NULL
) ENGINE=MyISAM;


-- Dumping structure for view tortuga.view_pres_f
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `view_pres_f` (
	`fanulado` DATE NULL,
	`fapagado` DATE NULL,
	`decreto` VARCHAR(50) NULL COLLATE 'utf8_general_ci',
	`compromiso` VARCHAR(20) NULL COLLATE 'utf8_general_ci',
	`fecha` VARCHAR(10) NULL COLLATE 'utf8mb4_general_ci',
	`des` MEDIUMTEXT NULL COLLATE 'utf8_general_ci',
	`observa` LONGTEXT NULL COLLATE 'utf8_general_ci',
	`cod_prov` VARCHAR(5) NULL COLLATE 'utf8_general_ci',
	`numero` VARCHAR(20) NULL COLLATE 'utf8_general_ci',
	`status` VARCHAR(2) NULL COLLATE 'utf8_general_ci',
	`modo` VARCHAR(14) NULL COLLATE 'utf8_general_ci',
	`codigoadm` VARCHAR(20) NULL COLLATE 'utf8_general_ci',
	`tipo` VARCHAR(20) NULL COLLATE 'utf8_general_ci',
	`codigopres` VARCHAR(25) NULL COLLATE 'utf8_general_ci',
	`ordinal` CHAR(0) NOT NULL COLLATE 'utf8mb4_general_ci',
	`faudis` DATE NULL,
	`ftrasla` DATE NULL,
	`fapartado` DATE NULL,
	`fcomprome` DATE NULL,
	`fcausado` DATE NULL,
	`fopago` DATE NULL,
	`fpagado` DATE NULL,
	`frendi` DATE NULL,
	`apartado` DECIMAL(21,2) NULL,
	`comprometido` DECIMAL(20,2) NULL,
	`causado` DECIMAL(21,2) NULL,
	`opago` DECIMAL(21,2) NULL,
	`pagado` DECIMAL(21,2) NULL,
	`aumento` DECIMAL(21,2) NULL,
	`disminucion` DECIMAL(21,2) NULL,
	`traslados` DECIMAL(21,2) NULL,
	`asignacion` DECIMAL(19,2) NULL,
	`capartado` DECIMAL(24,2) NULL,
	`ccomprometido` DECIMAL(24,2) NULL,
	`ccausado` DECIMAL(24,2) NULL,
	`copago` DECIMAL(24,2) NULL,
	`cpagado` DECIMAL(24,2) NULL,
	`denofondo` VARCHAR(100) NULL COLLATE 'utf8_general_ci'
) ENGINE=MyISAM;


-- Dumping structure for view tortuga.view_pres_p
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `view_pres_p` (
	`fanulado` DATE NULL,
	`fapagado` DATE NULL,
	`decreto` VARCHAR(50) NULL COLLATE 'utf8_general_ci',
	`compromiso` VARCHAR(20) NULL COLLATE 'utf8_general_ci',
	`fecha` VARCHAR(10) NULL COLLATE 'utf8mb4_general_ci',
	`des` MEDIUMTEXT NULL COLLATE 'utf8_general_ci',
	`observa` LONGTEXT NULL COLLATE 'utf8_general_ci',
	`cod_prov` VARCHAR(5) NULL COLLATE 'utf8_general_ci',
	`numero` VARCHAR(20) NULL COLLATE 'utf8_general_ci',
	`status` VARCHAR(2) NULL COLLATE 'utf8_general_ci',
	`modo` VARCHAR(14) NULL COLLATE 'utf8_general_ci',
	`codigoadm` VARCHAR(20) NULL COLLATE 'utf8_general_ci',
	`tipo` VARCHAR(20) NULL COLLATE 'utf8_general_ci',
	`codigopres` VARCHAR(25) NULL COLLATE 'utf8_general_ci',
	`ordinal` CHAR(0) NOT NULL COLLATE 'utf8mb4_general_ci',
	`faudis` DATE NULL,
	`ftrasla` DATE NULL,
	`fapartado` DATE NULL,
	`fcomprome` DATE NULL,
	`fcausado` DATE NULL,
	`fopago` DATE NULL,
	`fpagado` DATE NULL,
	`frendi` DATE NULL,
	`apartado` DECIMAL(21,2) NULL,
	`comprometido` DECIMAL(20,2) NULL,
	`causado` DECIMAL(21,2) NULL,
	`opago` DECIMAL(21,2) NULL,
	`pagado` DECIMAL(21,2) NULL,
	`aumento` DECIMAL(21,2) NULL,
	`disminucion` DECIMAL(21,2) NULL,
	`traslados` DECIMAL(21,2) NULL,
	`asignacion` DECIMAL(19,2) NULL,
	`capartado` DECIMAL(24,2) NULL,
	`ccomprometido` DECIMAL(24,2) NULL,
	`ccausado` DECIMAL(24,2) NULL,
	`copago` DECIMAL(24,2) NULL,
	`cpagado` DECIMAL(24,2) NULL,
	`denopart` TEXT NULL COLLATE 'utf8_general_ci'
) ENGINE=MyISAM;


-- Dumping structure for view tortuga.view_pres_s1
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `view_pres_s1` (
	`fanulado` DATE NULL,
	`fapagado` DATE NULL,
	`decreto` VARCHAR(50) NULL COLLATE 'utf8_general_ci',
	`compromiso` VARCHAR(20) NULL COLLATE 'utf8_general_ci',
	`fecha` VARCHAR(10) NULL COLLATE 'utf8mb4_general_ci',
	`des` MEDIUMTEXT NULL COLLATE 'utf8_general_ci',
	`observa` LONGTEXT NULL COLLATE 'utf8_general_ci',
	`cod_prov` VARCHAR(5) NULL COLLATE 'utf8_general_ci',
	`numero` VARCHAR(20) NULL COLLATE 'utf8_general_ci',
	`status` VARCHAR(2) NULL COLLATE 'utf8_general_ci',
	`modo` VARCHAR(14) NULL COLLATE 'utf8_general_ci',
	`codigoadm` VARCHAR(20) NULL COLLATE 'utf8_general_ci',
	`tipo` VARCHAR(20) NULL COLLATE 'utf8_general_ci',
	`codigopres` VARCHAR(25) NULL COLLATE 'utf8_general_ci',
	`ordinal` CHAR(0) NOT NULL COLLATE 'utf8mb4_general_ci',
	`faudis` DATE NULL,
	`ftrasla` DATE NULL,
	`fapartado` DATE NULL,
	`fcomprome` DATE NULL,
	`fcausado` DATE NULL,
	`fopago` DATE NULL,
	`fpagado` DATE NULL,
	`frendi` DATE NULL,
	`apartado` DECIMAL(21,2) NULL,
	`comprometido` DECIMAL(20,2) NULL,
	`causado` DECIMAL(21,2) NULL,
	`opago` DECIMAL(21,2) NULL,
	`pagado` DECIMAL(21,2) NULL,
	`aumento` DECIMAL(21,2) NULL,
	`disminucion` DECIMAL(21,2) NULL,
	`traslados` DECIMAL(21,2) NULL,
	`asignacion` DECIMAL(19,2) NULL
) ENGINE=MyISAM;


-- Dumping structure for view tortuga.view_pres_s2
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `view_pres_s2` (
	`fanulado` DATE NULL,
	`fapagado` DATE NULL,
	`decreto` VARCHAR(50) NULL COLLATE 'utf8_general_ci',
	`compromiso` VARCHAR(20) NULL COLLATE 'utf8_general_ci',
	`fecha` VARCHAR(10) NULL COLLATE 'utf8mb4_general_ci',
	`des` MEDIUMTEXT NULL COLLATE 'utf8_general_ci',
	`observa` LONGTEXT NULL COLLATE 'utf8_general_ci',
	`cod_prov` VARCHAR(5) NULL COLLATE 'utf8_general_ci',
	`numero` VARCHAR(20) NULL COLLATE 'utf8_general_ci',
	`status` VARCHAR(2) NULL COLLATE 'utf8_general_ci',
	`modo` VARCHAR(14) NULL COLLATE 'utf8_general_ci',
	`codigoadm` VARCHAR(20) NULL COLLATE 'utf8_general_ci',
	`tipo` VARCHAR(20) NULL COLLATE 'utf8_general_ci',
	`codigopres` VARCHAR(25) NULL COLLATE 'utf8_general_ci',
	`ordinal` CHAR(0) NOT NULL COLLATE 'utf8mb4_general_ci',
	`faudis` DATE NULL,
	`ftrasla` DATE NULL,
	`fapartado` DATE NULL,
	`fcomprome` DATE NULL,
	`fcausado` DATE NULL,
	`fopago` DATE NULL,
	`fpagado` DATE NULL,
	`frendi` DATE NULL,
	`apartado` DECIMAL(21,2) NULL,
	`comprometido` DECIMAL(20,2) NULL,
	`causado` DECIMAL(21,2) NULL,
	`opago` DECIMAL(21,2) NULL,
	`pagado` DECIMAL(21,2) NULL,
	`aumento` DECIMAL(21,2) NULL,
	`disminucion` DECIMAL(21,2) NULL,
	`traslados` DECIMAL(21,2) NULL,
	`asignacion` DECIMAL(19,2) NULL,
	`capartado` DECIMAL(24,2) NULL,
	`ccomprometido` DECIMAL(24,2) NULL,
	`ccausado` DECIMAL(24,2) NULL,
	`copago` DECIMAL(24,2) NULL,
	`cpagado` DECIMAL(24,2) NULL
) ENGINE=MyISAM;


-- Dumping structure for view tortuga.view_pres_s3
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `view_pres_s3` (
	`fanulado` DATE NULL,
	`fapagado` DATE NULL,
	`decreto` VARCHAR(50) NULL COLLATE 'utf8_general_ci',
	`compromiso` VARCHAR(20) NULL COLLATE 'utf8_general_ci',
	`fecha` VARCHAR(10) NULL COLLATE 'utf8mb4_general_ci',
	`des` MEDIUMTEXT NULL COLLATE 'utf8_general_ci',
	`observa` LONGTEXT NULL COLLATE 'utf8_general_ci',
	`cod_prov` VARCHAR(5) NULL COLLATE 'utf8_general_ci',
	`numero` VARCHAR(20) NULL COLLATE 'utf8_general_ci',
	`status` VARCHAR(2) NULL COLLATE 'utf8_general_ci',
	`modo` VARCHAR(14) NULL COLLATE 'utf8_general_ci',
	`codigoadm` VARCHAR(20) NULL COLLATE 'utf8_general_ci',
	`tipo` VARCHAR(20) NULL COLLATE 'utf8_general_ci',
	`codigopres` VARCHAR(25) NULL COLLATE 'utf8_general_ci',
	`ordinal` CHAR(0) NOT NULL COLLATE 'utf8mb4_general_ci',
	`faudis` DATE NULL,
	`ftrasla` DATE NULL,
	`fapartado` DATE NULL,
	`fcomprome` DATE NULL,
	`fcausado` DATE NULL,
	`fopago` DATE NULL,
	`fpagado` DATE NULL,
	`frendi` DATE NULL,
	`apartado` DECIMAL(21,2) NULL,
	`comprometido` DECIMAL(20,2) NULL,
	`causado` DECIMAL(21,2) NULL,
	`opago` DECIMAL(21,2) NULL,
	`pagado` DECIMAL(21,2) NULL,
	`aumento` DECIMAL(21,2) NULL,
	`disminucion` DECIMAL(21,2) NULL,
	`traslados` DECIMAL(21,2) NULL,
	`asignacion` DECIMAL(19,2) NULL,
	`capartado` DECIMAL(25,2) NULL,
	`ccomprometido` DECIMAL(25,2) NULL,
	`ccausado` DECIMAL(25,2) NULL,
	`copago` DECIMAL(25,2) NULL,
	`cpagado` DECIMAL(25,2) NULL
) ENGINE=MyISAM;


-- Dumping structure for view tortuga.view_retenciones
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `view_retenciones` (
	`desembolso` INT(11) NOT NULL,
	`ordpago` VARCHAR(12) NOT NULL COLLATE 'utf8_general_ci',
	`ordcompra` VARCHAR(12) NOT NULL COLLATE 'utf8_general_ci',
	`multiple` VARCHAR(11) NOT NULL COLLATE 'utf8mb4_general_ci',
	`cod_prov` VARCHAR(5) NULL COLLATE 'utf8_general_ci',
	`factura` VARCHAR(50) NULL COLLATE 'utf8_general_ci',
	`controlfac` VARCHAR(50) NULL COLLATE 'utf8_general_ci',
	`fechafac` DATE NULL,
	`total2` DECIMAL(21,4) UNSIGNED NULL,
	`reteiva` DECIMAL(19,2) NULL,
	`reten` DECIMAL(19,2) NULL,
	`impmunicipal` DECIMAL(19,2) UNSIGNED NULL,
	`imptimbre` DECIMAL(19,2) UNSIGNED NULL,
	`total` DECIMAL(21,4) NULL,
	`nrocomp` VARCHAR(8) NULL COLLATE 'utf8_general_ci',
	`breten` DECIMAL(19,2) NULL,
	`subtotal` DECIMAL(20,3) NULL,
	`creten` CHAR(20) NULL COLLATE 'utf8_general_ci',
	`rif` VARCHAR(12) NULL COLLATE 'utf8_general_ci',
	`nombre` VARCHAR(500) NULL COLLATE 'utf8_general_ci',
	`direccion` MEDIUMTEXT NULL COLLATE 'utf8_general_ci',
	`fdesem` DATE NULL
) ENGINE=MyISAM;


-- Dumping structure for view tortuga.view_sipres
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `view_sipres` (
	`compromiso` CHAR(8) NULL COLLATE 'utf8_general_ci',
	`codigoadm` VARCHAR(11) NULL COLLATE 'utf8_general_ci',
	`codigopres` VARCHAR(17) NULL COLLATE 'utf8_general_ci',
	`monto` DOUBLE NULL,
	`fecha` DATE NULL,
	`concepto` CHAR(60) NULL COLLATE 'utf8_general_ci'
) ENGINE=MyISAM;


-- Dumping structure for view tortuga.view_sumi_saldo
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `view_sumi_saldo` (
	`caub` VARCHAR(4) NULL COLLATE 'utf8_general_ci',
	`codigo` VARCHAR(15) NULL COLLATE 'utf8_general_ci',
	`cantidad` DECIMAL(42,2) NULL,
	`descrip` TEXT NULL COLLATE 'utf8_general_ci',
	`descripalma` VARCHAR(200) NULL COLLATE 'utf8_general_ci',
	`unidad` VARCHAR(8) NULL COLLATE 'utf8_general_ci'
) ENGINE=MyISAM;


-- Dumping structure for view tortuga.view_sumi_saldo2
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `view_sumi_saldo2` (
	`caub` VARCHAR(4) NULL COLLATE 'utf8_general_ci',
	`codigo` VARCHAR(15) NULL COLLATE 'utf8_general_ci',
	`cantidad` DECIMAL(20,2) NULL
) ENGINE=MyISAM;


-- Dumping structure for view tortuga.view_su_itsumi
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `view_su_itsumi` (
	`unidad` VARCHAR(8) NULL COLLATE 'utf8_general_ci',
	`codigo` VARCHAR(15) NOT NULL COLLATE 'utf8_general_ci',
	`descrip` TEXT NULL COLLATE 'utf8_general_ci',
	`alma` VARCHAR(4) NULL COLLATE 'utf8_general_ci',
	`almacen` VARCHAR(200) NULL COLLATE 'utf8_general_ci',
	`cantidad` DECIMAL(19,3) NULL
) ENGINE=MyISAM;


-- Dumping structure for table tortuga.vi_parroquia
CREATE TABLE IF NOT EXISTS `vi_parroquia` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.vi_personas
CREATE TABLE IF NOT EXISTS `vi_personas` (
  `nacional` char(1) NOT NULL DEFAULT 'V',
  `cedula` int(8) NOT NULL,
  `nombre1` varchar(50) DEFAULT NULL,
  `nombre2` varchar(50) DEFAULT NULL,
  `apellido1` varchar(50) NOT NULL,
  `apellido2` varchar(50) DEFAULT NULL,
  `sexo` char(1) DEFAULT NULL,
  `fnacimiento` date DEFAULT NULL,
  `telefono` bigint(20) DEFAULT NULL,
  `hijos` tinyint(4) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `id_parroquia` smallint(6) NOT NULL,
  `dir1` varchar(255) DEFAULT NULL,
  `dir2` varchar(255) DEFAULT NULL,
  `dir3` varchar(255) DEFAULT NULL,
  `dir4` varchar(255) DEFAULT NULL,
  `dirdesde` date DEFAULT NULL,
  `trabaja` char(2) DEFAULT NULL,
  `trabajad` date DEFAULT NULL,
  `oficio` varchar(255) DEFAULT NULL,
  `discapacidad` char(2) DEFAULT 'NO',
  `discapacidadd` varchar(255) DEFAULT NULL,
  `padren` varchar(255) DEFAULT 'V',
  `pcedula` int(8) DEFAULT NULL,
  `madren` varchar(255) DEFAULT 'V',
  `mcedula` int(8) DEFAULT NULL,
  `faov` char(1) DEFAULT NULL,
  `faovultimo` date DEFAULT NULL,
  `id_ccomunal` int(11) DEFAULT NULL,
  `id_ocivh` int(11) DEFAULT NULL,
  `sueldo` decimal(19,2) DEFAULT '0.00',
  `twitter` varchar(50) DEFAULT NULL,
  `gmvv` varchar(50) DEFAULT NULL,
  `mihogar` varchar(50) DEFAULT NULL,
  `vivienda` varchar(50) DEFAULT NULL,
  `estadocivil` varchar(50) DEFAULT NULL,
  `pension` decimal(19,2) DEFAULT '0.00',
  `amormayor` decimal(19,2) DEFAULT '0.00',
  `madresbarrios` decimal(19,2) DEFAULT '0.00',
  `ribas` decimal(19,2) DEFAULT '0.00',
  `fundaayacucho` decimal(19,2) DEFAULT '0.00',
  `hijosvzla` decimal(19,2) DEFAULT '0.00',
  `jubilado` decimal(19,2) DEFAULT '0.00',
  PRIMARY KEY (`cedula`),
  KEY `cedula` (`cedula`),
  KEY `id_parroquia` (`id_parroquia`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.vi_solicitud
CREATE TABLE IF NOT EXISTS `vi_solicitud` (
  `numero` int(8) NOT NULL AUTO_INCREMENT,
  `cedula` int(8) NOT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `situacion` varchar(50) DEFAULT NULL,
  `riesgo` varchar(50) DEFAULT NULL,
  `techo` varchar(50) DEFAULT NULL,
  `techoc` varchar(50) DEFAULT NULL,
  `piso` varchar(50) DEFAULT NULL,
  `pisoc` varchar(50) DEFAULT NULL,
  `pared` varchar(50) DEFAULT NULL,
  `paredc` varchar(50) DEFAULT NULL,
  `ablancas` varchar(50) DEFAULT NULL,
  `ablancasc` varchar(50) DEFAULT NULL,
  `aservidas` varchar(50) DEFAULT NULL,
  `aservidasc` varchar(50) DEFAULT NULL,
  `electrificacion` varchar(50) DEFAULT NULL,
  `electrificacionc` varchar(50) DEFAULT NULL,
  `vialidad` varchar(50) DEFAULT NULL,
  `vialidadc` varchar(50) DEFAULT NULL,
  `aseo` varchar(50) DEFAULT NULL,
  `gas` varchar(50) DEFAULT NULL,
  `telefonia` varchar(50) DEFAULT NULL,
  `transporte` varchar(50) DEFAULT NULL,
  `cedulapropietario` int(8) DEFAULT NULL,
  `terrenopropio` char(2) DEFAULT NULL,
  `id_parraoquia_terreno` int(11) DEFAULT NULL,
  `dim_ancho` decimal(19,2) DEFAULT '0.00',
  `dim_largo` decimal(19,2) DEFAULT '0.00',
  `observa` text,
  `fechainspeccion` date DEFAULT NULL,
  `banos` int(11) DEFAULT NULL,
  `habitaciones` int(11) DEFAULT NULL,
  `mtsconst` int(11) DEFAULT NULL,
  `status` char(2) DEFAULT NULL,
  `estadovivienda` varchar(50) DEFAULT NULL,
  `rectecnicas` text,
  `condterreno` varchar(50) DEFAULT NULL,
  `dimfrente` decimal(19,2) DEFAULT '0.00',
  `dimfondo` decimal(19,2) DEFAULT '0.00',
  `dimderecho` decimal(19,2) DEFAULT '0.00',
  `dimizquierdo` decimal(19,2) DEFAULT '0.00',
  `obsetecnica` text,
  PRIMARY KEY (`numero`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.vi_solicitudit
CREATE TABLE IF NOT EXISTS `vi_solicitudit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero` int(8) DEFAULT NULL,
  `cedulap` int(8) DEFAULT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `parentesco` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cedulas` (`numero`),
  KEY `cedulap` (`cedulap`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table tortuga.vi_solicitudm
CREATE TABLE IF NOT EXISTS `vi_solicitudm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero` int(8) DEFAULT NULL,
  `codigo` varchar(15) DEFAULT NULL,
  `descrip` varchar(255) DEFAULT NULL,
  `unidad` varchar(255) DEFAULT NULL,
  `cantidad` decimal(19,2) DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `numero` (`numero`),
  KEY `codigo` (`codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf32;

-- Data exporting was unselected.


-- Dumping structure for view tortuga.v_bienes
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `v_bienes` (
	`id` VARCHAR(10) NOT NULL COLLATE 'utf8_general_ci',
	`codigo` VARCHAR(20) NULL COLLATE 'utf8_general_ci',
	`grupo` VARCHAR(4) NULL COLLATE 'utf8_general_ci',
	`subgrupo` VARCHAR(4) NULL COLLATE 'utf8_general_ci',
	`seccion` VARCHAR(4) NULL COLLATE 'utf8_general_ci',
	`numero` VARCHAR(8) NULL COLLATE 'utf8_general_ci',
	`descrip` TINYTEXT NULL COLLATE 'utf8_general_ci',
	`alma` VARCHAR(4) NULL COLLATE 'utf8_general_ci',
	`monto` DECIMAL(19,2) NULL
) ENGINE=MyISAM;


-- Dumping structure for view tortuga.v_casi
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `v_casi` (
	`fecha` DATE NOT NULL,
	`status` CHAR(2) NULL COLLATE 'utf8_general_ci',
	`cuenta` CHAR(30) NULL COLLATE 'utf8_general_ci',
	`denominacion` VARCHAR(250) NULL COLLATE 'utf8_general_ci',
	`monto` DECIMAL(42,2) NULL,
	`debe` DECIMAL(41,2) NULL,
	`haber` DECIMAL(41,2) NULL
) ENGINE=MyISAM;


-- Dumping structure for view tortuga.v_casic2
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `v_casic2` (
	`fecha` DATE NOT NULL,
	`status` CHAR(2) NULL COLLATE 'utf8_general_ci',
	`cuenta` CHAR(30) NULL COLLATE 'utf8_general_ci',
	`denominacion` VARCHAR(250) NULL COLLATE 'utf8_general_ci',
	`monto` DECIMAL(42,2) NULL,
	`debe` DECIMAL(41,2) NULL,
	`haber` DECIMAL(41,2) NULL
) ENGINE=MyISAM;


-- Dumping structure for view tortuga.v_casid
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `v_casid` (
	`comprob` VARCHAR(45) NOT NULL COLLATE 'utf8_general_ci',
	`tipo` VARCHAR(10) NULL COLLATE 'utf8_general_ci',
	`fecha` DATE NOT NULL,
	`status` CHAR(2) NULL COLLATE 'utf8_general_ci',
	`cuenta` CHAR(30) NULL COLLATE 'utf8_general_ci',
	`denominacion` VARCHAR(250) NULL COLLATE 'utf8_general_ci',
	`monto` DECIMAL(20,2) NOT NULL,
	`debe` DECIMAL(19,2) NOT NULL,
	`haber` DECIMAL(19,2) NOT NULL
) ENGINE=MyISAM;


-- Dumping structure for view tortuga.v_casidc2
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `v_casidc2` (
	`tipo` VARCHAR(10) NULL COLLATE 'utf8_general_ci',
	`fecha` DATE NOT NULL,
	`status` CHAR(2) NULL COLLATE 'utf8_general_ci',
	`cuenta` CHAR(30) NULL COLLATE 'utf8_general_ci',
	`denominacion` VARCHAR(250) NULL COLLATE 'utf8_general_ci',
	`monto` DECIMAL(20,2) NOT NULL,
	`debe` DECIMAL(19,2) NOT NULL,
	`haber` DECIMAL(19,2) NOT NULL
) ENGINE=MyISAM;


-- Dumping structure for view tortuga.v_comproxcausar
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `v_comproxcausar` (
	`ocompra` VARCHAR(20) NULL COLLATE 'utf8_general_ci',
	`compromiso` VARCHAR(15) NULL COLLATE 'utf8_general_ci',
	`opago` VARCHAR(12) NULL COLLATE 'utf8_general_ci',
	`esiva` CHAR(1) NULL COLLATE 'utf8_general_ci',
	`codigoadm` VARCHAR(15) NULL COLLATE 'utf8_general_ci',
	`codigopres` VARCHAR(25) NULL COLLATE 'utf8_general_ci',
	`fondo` VARCHAR(20) NULL COLLATE 'utf8_general_ci',
	`compras` DECIMAL(63,2) NULL,
	`pagos` DECIMAL(63,2) NULL,
	`xcausar` DECIMAL(64,2) NULL
) ENGINE=MyISAM;


-- Dumping structure for view tortuga.v_comproxcausar_encab
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `v_comproxcausar_encab` (
	`ocompra` VARCHAR(20) NULL COLLATE 'utf8_general_ci',
	`compromiso` VARCHAR(15) NULL COLLATE 'utf8_general_ci',
	`opago` VARCHAR(12) NULL COLLATE 'utf8_general_ci',
	`codigoadm` VARCHAR(15) NULL COLLATE 'utf8_general_ci',
	`codigopres` VARCHAR(25) NULL COLLATE 'utf8_general_ci',
	`fondo` VARCHAR(20) NULL COLLATE 'utf8_general_ci',
	`compras` DECIMAL(63,2) NULL,
	`pagos` DECIMAL(63,2) NULL,
	`xcausar` DECIMAL(64,2) NULL
) ENGINE=MyISAM;


-- Dumping structure for view tortuga.v_comproxcausar_s1
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `v_comproxcausar_s1` (
	`ocompra` VARCHAR(20) NULL COLLATE 'utf8_general_ci',
	`compromiso` VARCHAR(15) NULL COLLATE 'utf8_general_ci',
	`opago` VARCHAR(12) NULL COLLATE 'utf8_general_ci',
	`esiva` CHAR(1) NULL COLLATE 'utf8_general_ci',
	`codigoadm` VARCHAR(15) NULL COLLATE 'utf8_general_ci',
	`codigopres` VARCHAR(25) NULL COLLATE 'utf8_general_ci',
	`fondo` VARCHAR(20) NULL COLLATE 'utf8_general_ci',
	`compras` DECIMAL(41,2) NULL,
	`pagos` DECIMAL(41,2) NULL
) ENGINE=MyISAM;


-- Dumping structure for view tortuga.v_estruadm
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `v_estruadm` (
	`codigo` VARCHAR(8) NOT NULL COLLATE 'utf8_general_ci',
	`denominacion` VARCHAR(200) NULL COLLATE 'utf8_general_ci'
) ENGINE=MyISAM;


-- Dumping structure for view tortuga.v_ingresos
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `v_ingresos` (
	`codigo` VARCHAR(25) NOT NULL COLLATE 'utf8_general_ci',
	`denominacion` MEDIUMTEXT NULL COLLATE 'utf8_general_ci',
	`tipo` CHAR(1) NULL COLLATE 'utf8_general_ci'
) ENGINE=MyISAM;


-- Dumping structure for view tortuga.v_localizador
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `v_localizador` (
	`desembolso` BIGINT(20) UNSIGNED NULL,
	`mstatus` CHAR(2) NULL COLLATE 'utf8_general_ci',
	`ordpago` VARCHAR(12) NULL COLLATE 'utf8_general_ci',
	`itfac` VARCHAR(11) NOT NULL COLLATE 'utf8mb4_general_ci',
	`pstatus` CHAR(2) NULL COLLATE 'utf8_general_ci',
	`ordcompra` VARCHAR(12) NOT NULL COLLATE 'utf8_general_ci',
	`ostatus` VARCHAR(1) NULL COLLATE 'utf8_general_ci',
	`multiple` VARCHAR(11) NOT NULL COLLATE 'utf8mb4_general_ci',
	`cod_prov` CHAR(5) NULL COLLATE 'utf8_general_ci',
	`factura` VARCHAR(50) NULL COLLATE 'utf8_general_ci',
	`controlfac` VARCHAR(50) NULL COLLATE 'utf8_general_ci',
	`fechafac` DATE NULL,
	`total2` DECIMAL(21,4) UNSIGNED NULL,
	`reteiva` DECIMAL(19,2) NULL,
	`reten` DECIMAL(19,2) NULL,
	`impmunicipal` DECIMAL(19,2) UNSIGNED NULL,
	`imptimbre` DECIMAL(19,2) UNSIGNED NULL,
	`total` DECIMAL(21,4) NULL,
	`breten` DECIMAL(19,2) NULL,
	`subtotal` DECIMAL(20,3) NULL,
	`creten` CHAR(20) NULL COLLATE 'utf8_general_ci',
	`fecha` DATE NULL,
	`ffirma` DATE NULL,
	`fentrega` DATE NULL
) ENGINE=MyISAM;


-- Dumping structure for view tortuga.v_mbanc
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `v_mbanc` (
	`id` BIGINT(11) UNSIGNED NOT NULL,
	`codbanc` VARCHAR(10) NULL COLLATE 'utf8_general_ci',
	`cod_prov` VARCHAR(5) NULL COLLATE 'utf8_general_ci',
	`tipo_doc` CHAR(2) NULL COLLATE 'utf8_general_ci',
	`cheque` MEDIUMTEXT NULL COLLATE 'utf8_general_ci',
	`abonado` DECIMAL(17,2) NULL,
	`tipo` CHAR(2) NULL COMMENT 'A (pmov) B (odirect)  C(movi) D (devo) E (ppro)  F (opago)  R (reinte)' COLLATE 'utf8_general_ci',
	`numero` VARCHAR(12) NULL COMMENT 'depende de tipo' COLLATE 'utf8_general_ci',
	`fecha` VARCHAR(10) NULL COLLATE 'utf8mb4_general_ci',
	`rel` VARCHAR(1000) NULL COLLATE 'utf8_general_ci',
	`fechapago` DATE NULL,
	`monto` DECIMAL(17,2) NULL,
	`observa` MEDIUMTEXT NULL COLLATE 'utf8_general_ci',
	`status` CHAR(2) NULL COLLATE 'utf8_general_ci',
	`benefi` VARCHAR(100) NULL COLLATE 'utf8_general_ci',
	`usuario` VARCHAR(4) NULL COLLATE 'utf8_general_ci',
	`estampa` TIMESTAMP NULL,
	`uejecutora` CHAR(4) NULL COLLATE 'utf8_general_ci',
	`devo` INT(11) UNSIGNED NULL,
	`periodo` DATE NULL,
	`islrid` INT(10) UNSIGNED NULL,
	`anulado` CHAR(1) NULL COLLATE 'utf8_general_ci',
	`anuladopor` VARCHAR(20) NULL COLLATE 'utf8_general_ci',
	`concilia` CHAR(1) NULL COLLATE 'utf8_general_ci',
	`fconcilia` DATE NULL,
	`bcta` INT(10) UNSIGNED NULL,
	`ffirma` DATE NULL,
	`fentrega` DATE NULL,
	`fdevo` DATE NULL,
	`fcajrecibe` DATE NULL,
	`fcajdevo` DATE NULL,
	`desem` INT(11) NULL,
	`sta` CHAR(3) NULL COLLATE 'utf8_general_ci',
	`destino` VARCHAR(1) NULL COLLATE 'utf8_general_ci',
	`relch` INT(11) NULL,
	`liable` CHAR(1) NULL COLLATE 'utf8_general_ci',
	`fliable` DATE NULL,
	`coding` INT(11) NULL,
	`numcuent` VARCHAR(25) NULL COLLATE 'utf8_general_ci',
	`saldo` DECIMAL(14,2) NULL,
	`banco` VARCHAR(200) NULL COLLATE 'utf8_general_ci'
) ENGINE=MyISAM;


-- Dumping structure for view tortuga.v_mbancm
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `v_mbancm` (
	`multiple` INT(11) NULL,
	`codbanc` VARCHAR(10) NULL COLLATE 'utf8_general_ci',
	`tipo_doc` CHAR(2) NULL COLLATE 'utf8_general_ci',
	`cheque` TEXT NULL COLLATE 'utf8_general_ci',
	`fecha` VARCHAR(10) NULL COLLATE 'utf8mb4_general_ci',
	`monto` DECIMAL(39,2) NULL,
	`observa` MEDIUMTEXT NULL COLLATE 'utf8_general_ci',
	`numcuent` VARCHAR(25) NULL COLLATE 'utf8_general_ci',
	`banco` VARCHAR(200) NULL COLLATE 'utf8_general_ci',
	`benefi` VARCHAR(100) NULL COLLATE 'utf8_general_ci'
) ENGINE=MyISAM;


-- Dumping structure for view tortuga.v_numcuent
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `v_numcuent` (
	`codigo` VARCHAR(5) NOT NULL COLLATE 'utf8_general_ci',
	`descrip` VARCHAR(500) NULL COLLATE 'utf8_general_ci',
	`descrip2` TINYTEXT NULL COLLATE 'utf8_general_ci',
	`numcuent` VARCHAR(50) NULL COLLATE 'utf8_general_ci'
) ENGINE=MyISAM;


-- Dumping structure for view tortuga.v_pagonom
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `v_pagonom` (
	`numero` INT(11) NULL,
	`opago` VARCHAR(12) NULL COLLATE 'utf8_general_ci',
	`fecha` DATE NULL,
	`asig` DECIMAL(19,2) NULL,
	`rete` DECIMAL(19,2) NULL,
	`status` CHAR(2) NULL COLLATE 'utf8_general_ci',
	`descrip` LONGTEXT NULL COLLATE 'utf8_general_ci'
) ENGINE=MyISAM;


-- Dumping structure for view tortuga.v_pagos
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `v_pagos` (
	`fapagado` DATE NULL,
	`fanulado` DATE NULL,
	`tipoc` VARCHAR(2) NULL COLLATE 'utf8_general_ci',
	`fpagado` DATE NULL,
	`total` DECIMAL(19,2) NULL,
	`total2` DECIMAL(19,2) UNSIGNED NULL,
	`compromiso` VARCHAR(341) NULL COLLATE 'utf8_general_ci',
	`cod_prov` CHAR(5) NULL COLLATE 'utf8_general_ci',
	`numeron` DOUBLE NULL,
	`numero` VARCHAR(12) NULL COLLATE 'utf8_general_ci',
	`fecha` DATE NULL,
	`codigoadm` VARCHAR(15) NULL COLLATE 'utf8_general_ci',
	`fondo` VARCHAR(20) NULL COLLATE 'utf8_general_ci',
	`codigopres` VARCHAR(25) NULL COLLATE 'utf8_general_ci',
	`importe` DECIMAL(19,2) NULL,
	`status` CHAR(2) NULL COLLATE 'utf8_general_ci',
	`observa` LONGTEXT NULL COLLATE 'utf8_general_ci',
	`denominacion` TEXT NULL COLLATE 'utf8_general_ci',
	`nombre` VARCHAR(500) NULL COLLATE 'utf8_general_ci',
	`codigo` VARCHAR(41) NULL COLLATE 'utf8_general_ci'
) ENGINE=MyISAM;


-- Dumping structure for view tortuga.v_pagossolo
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `v_pagossolo` (
	`fapagado` DATE NULL,
	`fanulado` DATE NULL,
	`cod_prov2` VARCHAR(5) NULL COLLATE 'utf8_general_ci',
	`tipoc` VARCHAR(2) NULL COLLATE 'utf8_general_ci',
	`fpagado` DATE NULL,
	`total` DECIMAL(19,2) NULL,
	`total2` DECIMAL(19,2) UNSIGNED NULL,
	`compromiso` VARCHAR(341) NULL COLLATE 'utf8_general_ci',
	`cod_prov` CHAR(5) NULL COLLATE 'utf8_general_ci',
	`numeron` DOUBLE NULL,
	`numero` VARCHAR(12) NULL COLLATE 'utf8_general_ci',
	`fecha` DATE NULL,
	`codigoadm` VARCHAR(15) NULL COLLATE 'utf8_general_ci',
	`fondo` VARCHAR(20) NULL COLLATE 'utf8_general_ci',
	`codigopres` VARCHAR(25) NULL COLLATE 'utf8_general_ci',
	`importe` DECIMAL(19,2) NULL,
	`status` CHAR(2) NULL COLLATE 'utf8_general_ci',
	`observa` LONGTEXT NULL COLLATE 'utf8_general_ci',
	`codigo` VARCHAR(41) NULL COLLATE 'utf8_general_ci'
) ENGINE=MyISAM;


-- Dumping structure for view tortuga.v_pagos_encab
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `v_pagos_encab` (
	`numero` VARCHAR(12) NOT NULL COLLATE 'utf8_general_ci',
	`fecha` DATE NULL,
	`compra` VARCHAR(10) NULL COLLATE 'utf8_general_ci',
	`cod_prov` CHAR(5) NULL COLLATE 'utf8_general_ci',
	`subtotal` DECIMAL(19,2) NULL,
	`exento` DECIMAL(19,2) NULL,
	`ivag` DECIMAL(19,2) NULL,
	`tivag` DECIMAL(19,2) UNSIGNED NULL,
	`mivag` DECIMAL(19,2) UNSIGNED NULL,
	`ivar` DECIMAL(19,2) NULL,
	`tivar` DECIMAL(19,2) UNSIGNED NULL,
	`mivar` DECIMAL(19,2) UNSIGNED NULL,
	`ivaa` DECIMAL(19,2) NULL,
	`tivaa` DECIMAL(19,2) UNSIGNED NULL,
	`mivaa` DECIMAL(19,2) UNSIGNED NULL,
	`pago` DECIMAL(19,2) NULL,
	`creten` CHAR(4) NULL COLLATE 'utf8_general_ci',
	`breten` DECIMAL(19,2) NULL,
	`reteiva` DECIMAL(19,2) NULL,
	`reten` DECIMAL(19,2) NULL,
	`total` DECIMAL(19,2) UNSIGNED NULL,
	`total2` DECIMAL(19,2) UNSIGNED NULL,
	`iva` DECIMAL(19,2) UNSIGNED NULL,
	`observa` LONGTEXT NULL COLLATE 'utf8_general_ci',
	`anulado` DATE NULL,
	`status` CHAR(2) NULL COLLATE 'utf8_general_ci',
	`reteiva_prov` DECIMAL(5,2) UNSIGNED NULL,
	`impmunicipal` DECIMAL(19,2) UNSIGNED NULL,
	`crs` DECIMAL(19,2) UNSIGNED NULL,
	`imptimbre` DECIMAL(19,2) UNSIGNED NULL,
	`pcrs` DECIMAL(19,2) UNSIGNED NULL,
	`pimptimbre` DECIMAL(19,2) UNSIGNED NULL,
	`pimpmunicipal` DECIMAL(19,2) UNSIGNED NULL,
	`retenomina` DECIMAL(19,2) UNSIGNED NULL,
	`amortiza` DECIMAL(19,2) UNSIGNED NULL,
	`porcent` DECIMAL(19,2) UNSIGNED NULL,
	`anticipo` CHAR(1) NULL COLLATE 'utf8_general_ci',
	`fpagado` DATE NULL,
	`fislr` DATE NULL,
	`fcrs` DATE NULL,
	`fmunicipal` DATE NULL,
	`ftimbre` DATE NULL,
	`mtimbre` INT(11) NULL,
	`mislr` INT(11) NULL,
	`mmuni` INT(11) NULL,
	`mcrs` INT(11) NULL,
	`cod_prov2` VARCHAR(5) NULL COLLATE 'utf8_general_ci',
	`tipoc` VARCHAR(2) NULL COLLATE 'utf8_general_ci',
	`otrasrete` DECIMAL(19,2) NULL,
	`fanulado` DATE NULL,
	`observacaj` MEDIUMTEXT NULL COLLATE 'utf8_general_ci',
	`fapagado` DATE NULL,
	`fondo` VARCHAR(20) NULL COLLATE 'utf8_general_ci',
	`nombre` VARCHAR(500) NULL COLLATE 'utf8_general_ci'
) ENGINE=MyISAM;


-- Dumping structure for view tortuga.v_presaldo
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `v_presaldo` (
	`codigoadm` VARCHAR(12) NOT NULL COLLATE 'utf8_general_ci',
	`fondo` VARCHAR(20) NOT NULL COLLATE 'utf8_general_ci',
	`codigo` VARCHAR(25) NOT NULL COLLATE 'utf8_general_ci',
	`denominacion` TEXT NULL COLLATE 'utf8_general_ci',
	`apartado` DECIMAL(19,2) NULL,
	`saldo` DECIMAL(23,2) NULL,
	`movimiento` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_general_ci',
	`ordinal` CHAR(0) NOT NULL COLLATE 'utf8mb4_general_ci'
) ENGINE=MyISAM;


-- Dumping structure for view tortuga.v_presaldoante
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `v_presaldoante` (
	`codigoadm` VARCHAR(12) NOT NULL COLLATE 'utf8_general_ci',
	`fondo` VARCHAR(20) NOT NULL COLLATE 'utf8_general_ci',
	`codigo` VARCHAR(15) NOT NULL COLLATE 'utf8_general_ci',
	`denominacion` VARCHAR(250) NULL COLLATE 'utf8_general_ci',
	`saldo` DECIMAL(23,2) NULL,
	`movimiento` CHAR(1) NULL COLLATE 'utf8_general_ci',
	`ordinal` VARCHAR(4) NULL COLLATE 'utf8_general_ci'
) ENGINE=MyISAM;


-- Dumping structure for view tortuga.v_retenciones
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `v_retenciones` (
	`desembolso` INT(11) NOT NULL,
	`ordpago` VARCHAR(12) NOT NULL COLLATE 'utf8_general_ci',
	`ordcompra` CHAR(0) NOT NULL COLLATE 'utf8mb4_general_ci',
	`multiple` VARCHAR(11) NOT NULL COLLATE 'utf8mb4_general_ci',
	`cod_prov` VARCHAR(5) NULL COLLATE 'utf8_general_ci',
	`factura` VARCHAR(50) NULL COLLATE 'utf8_general_ci',
	`controlfac` VARCHAR(50) NULL COLLATE 'utf8_general_ci',
	`fechafac` DATE NULL,
	`total2` DECIMAL(21,4) UNSIGNED NULL,
	`reteiva` DECIMAL(19,2) NULL,
	`reten` DECIMAL(19,2) NULL,
	`impmunicipal` DECIMAL(19,2) UNSIGNED NULL,
	`imptimbre` DECIMAL(19,2) UNSIGNED NULL,
	`total` DECIMAL(21,4) UNSIGNED NULL,
	`nrocomp` VARCHAR(8) NULL COLLATE 'utf8_general_ci',
	`breten` DECIMAL(19,2) NULL,
	`subtotal` DECIMAL(20,3) NULL,
	`creten` VARCHAR(4) NULL COLLATE 'utf8_general_ci',
	`rif` VARCHAR(12) NULL COLLATE 'utf8_general_ci',
	`nombre` VARCHAR(500) NULL COLLATE 'utf8_general_ci',
	`direccion` MEDIUMTEXT NULL COLLATE 'utf8_general_ci',
	`fdesem` DATE NULL,
	`crs` DECIMAL(19,2) NULL,
	`otrasrete` DECIMAL(19,2) NULL,
	`iva` DECIMAL(23,4) NULL,
	`exento` DECIMAL(19,2) NULL,
	`preten` DECIMAL(19,2) NULL,
	`basei` DECIMAL(18,2) NULL
) ENGINE=MyISAM;


-- Dumping structure for view tortuga.v_retencionesislr
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `v_retencionesislr` (
	`desembolso` INT(11) NOT NULL,
	`ordpago` VARCHAR(12) NOT NULL COLLATE 'utf8_general_ci',
	`ordcompra` CHAR(0) NOT NULL COLLATE 'utf8mb4_general_ci',
	`multiple` VARCHAR(11) NOT NULL COLLATE 'utf8mb4_general_ci',
	`cod_prov` VARCHAR(5) NULL COLLATE 'utf8_general_ci',
	`factura` VARCHAR(50) NULL COLLATE 'utf8_general_ci',
	`controlfac` VARCHAR(50) NULL COLLATE 'utf8_general_ci',
	`fechafac` DATE NULL,
	`total2` DECIMAL(41,4) NULL,
	`breten` DECIMAL(41,2) NULL,
	`subtotal` DECIMAL(41,3) NULL,
	`creten` VARCHAR(4) NULL COLLATE 'utf8_general_ci',
	`rif` VARCHAR(12) NULL COLLATE 'utf8_general_ci',
	`nombre` VARCHAR(500) NULL COLLATE 'utf8_general_ci',
	`direccion` MEDIUMTEXT NULL COLLATE 'utf8_general_ci',
	`fdesem` DATE NULL,
	`iva` DECIMAL(45,4) NULL,
	`exento` DECIMAL(41,2) NULL,
	`preten` DECIMAL(19,2) NULL,
	`reten` DECIMAL(19,2) NULL
) ENGINE=MyISAM;


-- Dumping structure for view tortuga.v_retencionesislrd
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `v_retencionesislrd` (
	`desembolso` INT(11) NOT NULL,
	`ordpago` VARCHAR(12) NOT NULL COLLATE 'utf8_general_ci',
	`ordcompra` CHAR(0) NOT NULL COLLATE 'utf8mb4_general_ci',
	`multiple` VARCHAR(11) NOT NULL COLLATE 'utf8mb4_general_ci',
	`cod_prov` VARCHAR(5) NULL COLLATE 'utf8_general_ci',
	`factura` VARCHAR(50) NULL COLLATE 'utf8_general_ci',
	`controlfac` VARCHAR(50) NULL COLLATE 'utf8_general_ci',
	`fechafac` DATE NULL,
	`total2` DECIMAL(21,4) UNSIGNED NULL,
	`breten` DECIMAL(19,2) NULL,
	`subtotal` DECIMAL(20,3) NULL,
	`creten` VARCHAR(4) NULL COLLATE 'utf8_general_ci',
	`rif` VARCHAR(12) NULL COLLATE 'utf8_general_ci',
	`nombre` VARCHAR(500) NULL COLLATE 'utf8_general_ci',
	`direccion` MEDIUMTEXT NULL COLLATE 'utf8_general_ci',
	`fdesem` DATE NULL,
	`iva` DECIMAL(23,4) NULL,
	`exento` DECIMAL(19,2) NULL,
	`preten` DECIMAL(19,2) NULL,
	`reten` DECIMAL(19,2) NULL
) ENGINE=MyISAM;


-- Dumping structure for trigger tortuga.r_abonosit_after_delete
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='';
DELIMITER //
CREATE TRIGGER `r_abonosit_after_delete` AFTER DELETE ON `r_abonosit` FOR EACH ROW BEGIN
UPDATE r_abonos SET totrecibos=(SELECT SUM(monto) FROM r_recibo JOIN r_abonosit ON r_recibo.id=r_abonosit.recibo WHERE r_abonosit.abono=OLD.abono) WHERE id=OLD.abono;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;


-- Dumping structure for trigger tortuga.r_abonosit_after_insert
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='';
DELIMITER //
CREATE TRIGGER `r_abonosit_after_insert` AFTER INSERT ON `r_abonosit` FOR EACH ROW BEGIN
UPDATE r_abonos SET totrecibos=(SELECT SUM(monto) FROM r_recibo JOIN r_abonosit ON r_recibo.id=r_abonosit.recibo WHERE r_abonosit.abono=NEW.abono) WHERE id=NEW.abono;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;


-- Dumping structure for trigger tortuga.r_abonosit_after_update
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='';
DELIMITER //
CREATE TRIGGER `r_abonosit_after_update` AFTER UPDATE ON `r_abonosit` FOR EACH ROW BEGIN
UPDATE r_abonos  SET totrecibos=(SELECT SUM(monto) FROM r_recibo JOIN r_abonosit ON r_recibo.id=r_abonosit.recibo WHERE r_abonosit.abono=NEW.abono) WHERE id=NEW.abono;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;


-- Dumping structure for trigger tortuga.r_mbanc_after_delete
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='';
DELIMITER //
CREATE TRIGGER `r_mbanc_after_delete` AFTER DELETE ON `r_mbanc` FOR EACH ROW BEGIN

END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;


-- Dumping structure for trigger tortuga.r_mbanc_after_insert
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='';
DELIMITER //
CREATE TRIGGER `r_mbanc_after_insert` AFTER INSERT ON `r_mbanc` FOR EACH ROW BEGIN

END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;


-- Dumping structure for trigger tortuga.r_mbanc_after_update
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='';
DELIMITER //
CREATE TRIGGER `r_mbanc_after_update` AFTER UPDATE ON `r_mbanc` FOR EACH ROW BEGIN

END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;


-- Dumping structure for trigger tortuga.r_reciboit_after_delete
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='';
DELIMITER //
CREATE TRIGGER `r_reciboit_after_delete` AFTER DELETE ON `r_reciboit` FOR EACH ROW BEGIN


END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;


-- Dumping structure for trigger tortuga.r_reciboit_after_insert
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='';
DELIMITER //
CREATE TRIGGER `r_reciboit_after_insert` AFTER INSERT ON `r_reciboit` FOR EACH ROW BEGIN

END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;


-- Dumping structure for trigger tortuga.r_reciboit_after_update
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='';
DELIMITER //
CREATE TRIGGER `r_reciboit_after_update` AFTER UPDATE ON `r_reciboit` FOR EACH ROW BEGIN


END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;


-- Dumping structure for view tortuga.r_v_conc
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `r_v_conc`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `r_v_conc` AS select `a`.`id` AS `id`,`a`.`id_conc` AS `id_conc`,`a`.`ano` AS `ano`,`a`.`acronimo` AS `acronimo`,`a`.`denomi` AS `denomi`,`a`.`requiere` AS `requiere`,`b`.`denomi` AS `denomiconc`,`c`.`partida` AS `partida`,`c`.`denomi` AS `denopart`,`a`.`modo` AS `modo`,`a`.`frecuencia` AS `frecuencia`,`a`.`freval` AS `freval`,`a`.`expira` AS `expira` from ((`r_concit` `a` join `r_conc` `b` on((`a`.`id_conc` = `b`.`id`))) join `r_presup` `c` on((`b`.`id_presup` = `c`.`id`)));


-- Dumping structure for view tortuga.r_v_contribu
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `r_v_contribu`;
CREATE ALGORITHM=UNDEFINED DEFINER=`datasis`@`localhost` SQL SECURITY DEFINER VIEW `r_v_contribu` AS select `a`.`id` AS `id`,`a`.`rifci` AS `rifci`,`a`.`nombre` AS `nombre`,`a`.`telefono` AS `telefono`,`a`.`id_parroquia` AS `id_parroquia`,`a`.`id_zona` AS `id_zona`,`a`.`dir1` AS `dir1`,`a`.`dir2` AS `dir2`,`a`.`dir3` AS `dir3`,`a`.`dir4` AS `dir4`,`a`.`patente` AS `patente`,`a`.`nro` AS `nro`,`a`.`id_negocio` AS `id_negocio`,`a`.`id_repre` AS `id_repre`,`a`.`objeto` AS `objeto`,`a`.`observa` AS `observa`,`a`.`archivo` AS `archivo`,`b`.`nombre` AS `parroquia`,`c`.`descrip` AS `zona`,`c`.`monto` AS `zona_monto`,`d`.`monto` AS `negocio_monto`,`d`.`monto2` AS `negocio_monto2` from (((`r_contribu` `a` left join `vi_parroquia` `b` on((`a`.`id_parroquia` = `b`.`id`))) left join `r_zona` `c` on((`a`.`id_zona` = `c`.`id`))) left join `r_negocio` `d` on((`a`.`id_negocio` = `d`.`id`)));


-- Dumping structure for view tortuga.r_v_inmueble
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `r_v_inmueble`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `r_v_inmueble` AS select `r_inmueble`.`id` AS `id`,`r_inmueble`.`catastro` AS `catastro`,`vi_parroquia`.`nombre` AS `parroquia`,`r_zona`.`descrip` AS `zona`,`r_inmueble`.`dir1` AS `dir1`,`r_inmueble`.`dir2` AS `dir2`,`r_inmueble`.`dir3` AS `dir3`,`r_inmueble`.`dir4` AS `dir4`,`r_inmueble`.`mt2` AS `mt2`,`r_inmueble`.`techo` AS `techo`,if((`r_inmueble`.`techo` = 'A'),'ZINC',if((`r_inmueble`.`techo` = 'B'),'PLATABANDA',if((`r_inmueble`.`techo` = 'C'),'2 PLANTAS',if((`r_inmueble`.`techo` = 'D'),'RANCHO',`r_inmueble`.`techo`)))) AS `techodecrip`,`r_inmueble`.`monto` AS `monto`,`r_inmueble`.`id_contribu` AS `id_contribu`,concat_ws(' ',`vi_parroquia`.`nombre`,`r_zona`.`descrip`,`r_inmueble`.`dir1`,`r_inmueble`.`dir2`,`r_inmueble`.`dir3`,`r_inmueble`.`dir4`) AS `direccion`,`r_inmueble`.`id_parroquia` AS `id_parroquia`,`r_inmueble`.`id_zona` AS `id_zona`,`r_zona`.`monto` AS `zona_monto`,`ri_clase`.`id` AS `id_clase`,`ri_clase`.`nombre` AS `clase`,`ri_clase`.`monto` AS `clase_monto`,`r_inmueble`.`tipoi` AS `tipoi`,`r_inmueble`.`id_clasea` AS `id_clasea`,`ri_clasea`.`nombre` AS `clasea`,`ri_clasea`.`monto` AS `clasea_monto`,`ri_clase`.`monto2` AS `clase_monto2`,`ri_clasea`.`monto2` AS `clasea_monto2`,`r_negocio`.`monto` AS `negocio_monto`,`r_negocio`.`monto2` AS `negocio_monto2` from (((((`r_inmueble` left join `vi_parroquia` on((`r_inmueble`.`id_parroquia` = `vi_parroquia`.`id`))) left join `r_zona` on((`r_inmueble`.`id_zona` = `r_zona`.`id`))) left join `ri_clase` on((`r_inmueble`.`id_clase` = `ri_clase`.`id`))) left join `ri_clasea` on((`r_inmueble`.`id_clasea` = `ri_clasea`.`id`))) left join `r_negocio` on((`r_inmueble`.`id_negocio` = `r_negocio`.`id`))) order by `r_inmueble`.`id`;


-- Dumping structure for view tortuga.r_v_publicidad
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `r_v_publicidad`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `r_v_publicidad` AS select `a`.`id` AS `id`,`a`.`id_contribu` AS `id_contribu`,`a`.`id_tipo` AS `id_tipo`,`a`.`id_parroquia` AS `id_parroquia`,`a`.`id_zona` AS `id_zona`,`a`.`id_sector` AS `id_sector`,concat_ws(' ',`c`.`nombre`,`d`.`descrip`,`a`.`dir1`,`a`.`dir2`,`a`.`dir3`,`a`.`dir4`) AS `direccion`,`a`.`dir1` AS `dir1`,`a`.`dir2` AS `dir2`,`a`.`dir3` AS `dir3`,`a`.`dir4` AS `dir4`,`a`.`alto` AS `alto`,`a`.`ancho` AS `ancho`,`b`.`codigo` AS `codigo`,`b`.`descrip` AS `descrip`,`b`.`monto` AS `monto`,`c`.`nombre` AS `parroquia`,`d`.`descrip` AS `zona` from (((`r_publicidad` `a` join `rp_tipos` `b` on((`a`.`id_tipo` = `b`.`id`))) left join `vi_parroquia` `c` on((`a`.`id_parroquia` = `c`.`id`))) left join `r_zona` `d` on((`a`.`id_zona` = `d`.`id`)));


-- Dumping structure for view tortuga.r_v_vehiculo
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `r_v_vehiculo`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `r_v_vehiculo` AS select `r_vehiculo`.`capacidad` AS `capacidad`,`r_vehiculo`.`id` AS `id`,`r_vehiculo`.`placa` AS `placa`,`r_vehiculo`.`ano` AS `ano`,`r_vehiculo`.`color` AS `color`,`rv_marca`.`descrip` AS `marca`,`rv_modelo`.`descrip` AS `modelo`,`rv_tipo`.`descrip` AS `tipo`,`rv_clase`.`descrip` AS `clase`,`r_vehiculo`.`id_tipo` AS `id_tipo`,`r_vehiculo`.`id_marca` AS `id_marca`,`r_vehiculo`.`id_modelo` AS `id_modelo`,`r_vehiculo`.`id_clase` AS `id_clase`,`r_vehiculo`.`id_contribu` AS `id_contribu` from ((((`r_vehiculo` left join `rv_marca` on((`r_vehiculo`.`id_marca` = `rv_marca`.`id`))) left join `rv_modelo` on((`r_vehiculo`.`id_modelo` = `rv_modelo`.`id`))) left join `rv_tipo` on((`r_vehiculo`.`id_tipo` = `rv_tipo`.`id`))) left join `rv_clase` on((`r_vehiculo`.`id_clase` = `rv_clase`.`id`))) order by `r_vehiculo`.`id`;


-- Dumping structure for view tortuga.r_v_xcobrar
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `r_v_xcobrar`;
CREATE ALGORITHM=UNDEFINED DEFINER=`datasis`@`localhost` SQL SECURITY DEFINER VIEW `r_v_xcobrar` AS select `a`.`id` AS `id`,`a`.`id_contribu` AS `id_contribu`,`a`.`fecha` AS `fecha`,`a`.`numero` AS `numero`,`a`.`rifci` AS `rifci`,`a`.`nombre` AS `nombre`,`a`.`telefono` AS `telefono`,`a`.`monto` AS `monto`,`a`.`id_parroquia` AS `id_parroquia`,`a`.`parroquia` AS `parroquia`,`a`.`id_zona` AS `id_zona`,`a`.`zona` AS `zona`,`a`.`dir1` AS `dir1`,`a`.`dir2` AS `dir2`,`a`.`dir3` AS `dir3`,`a`.`dir4` AS `dir4`,`a`.`razon` AS `razon`,`a`.`solvencia` AS `solvencia`,`a`.`solvenciab` AS `solvenciab`,`a`.`licores` AS `licores`,`a`.`caja` AS `caja` from (`r_recibo` `a` left join `r_abonosit` `b` on((`a`.`id` = `b`.`recibo`))) where isnull(`b`.`recibo`);


-- Dumping structure for view tortuga.r_v_xrecaudar
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `r_v_xrecaudar`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `r_v_xrecaudar` AS select `b`.`id` AS `id`,`b`.`ano` AS `ano`,`b`.`acronimo` AS `acronimo`,`b`.`denomi` AS `denomi`,`b`.`requiere` AS `requiere`,`a`.`id` AS `id_inmueble`,`a`.`catastro` AS `catastro`,NULL AS `id_vehiculo`,NULL AS `placa`,`a`.`id_contribu` AS `id_contribu`,`d`.`direccion` AS `observa`,`b`.`formula` AS `formula` from (((`r_inmueble` `a` join `r_v_inmueble` `d` on((`a`.`id` = `d`.`id`))) join `r_concit` `b` on((1 = 1))) left join `r_reciboit` `c` on(((`b`.`id` = `c`.`id_concit`) and (`a`.`id` = `c`.`id_inmueble`)))) where ((`b`.`requiere` = 'INMUEBLE') and (`b`.`ano` > 0) and isnull(`c`.`id`)) union all select `b`.`id` AS `id`,`b`.`ano` AS `ano`,`b`.`acronimo` AS `acronimo`,`b`.`denomi` AS `denomi`,`b`.`requiere` AS `requiere`,NULL AS `id_inmueble`,NULL AS `catastro`,`a`.`id` AS `id_vehiculo`,`a`.`placa` AS `placa`,`a`.`id_contribu` AS `id_contribu`,'' AS `observa`,`b`.`formula` AS `formula` from ((`r_vehiculo` `a` join `r_concit` `b` on((1 = 1))) left join `r_reciboit` `c` on(((`b`.`id` = `c`.`id_concit`) and (`a`.`id` = `c`.`id_vehiculo`)))) where ((`b`.`requiere` = 'VEHICULO') and (`b`.`ano` > 0) and isnull(`c`.`id`)) union all select `b`.`id` AS `id`,`b`.`ano` AS `ano`,`b`.`acronimo` AS `acronimo`,`b`.`denomi` AS `denomi`,`b`.`requiere` AS `requiere`,NULL AS `id_inmueble`,NULL AS `catastro`,NULL AS `id_vehiculo`,NULL AS `placa`,`a`.`id` AS `id_contribu`,'' AS `observa`,`b`.`formula` AS `formula` from (((`r_contribu` `a` join `r_concit` `b` on((1 = 1))) left join `r_reciboit` `c` on((`b`.`id` = `c`.`id_concit`))) left join `r_recibo` `d` on(((`c`.`id_recibo` = `d`.`id`) and (`a`.`id` = `d`.`id_contribu`)))) where ((length(`b`.`requiere`) = 0) and (`b`.`ano` > 0) and isnull(`d`.`id`));


-- Dumping structure for view tortuga.view_pres
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `view_pres`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `view_pres` AS select `b`.`denominacion` AS `denopart`,`c`.`denominacion` AS `denoadm`,`d`.`descrip` AS `denofondo`,`e`.`nombre` AS `nombre`,`a`.`fanulado` AS `fanulado`,`a`.`fapagado` AS `fapagado`,`a`.`decreto` AS `decreto`,`a`.`compromiso` AS `compromiso`,`a`.`fecha` AS `fecha`,`a`.`des` AS `des`,`a`.`observa` AS `observa`,`a`.`cod_prov` AS `cod_prov`,`a`.`numero` AS `numero`,`a`.`status` AS `status`,`a`.`modo` AS `modo`,`a`.`codigoadm` AS `codigoadm`,`a`.`tipo` AS `fondo`,`a`.`codigopres` AS `codigopres`,`a`.`ordinal` AS `ordinal`,`a`.`faudis` AS `faudis`,`a`.`ftrasla` AS `ftrasla`,`a`.`fapartado` AS `fapartado`,`a`.`fcomprome` AS `fcomprome`,`a`.`fcausado` AS `fcausado`,`a`.`fopago` AS `fopago`,`a`.`fpagado` AS `fpagado`,`a`.`frendi` AS `frendi`,`a`.`apartado` AS `apartado`,`a`.`comprometido` AS `comprometido`,`a`.`causado` AS `causado`,`a`.`opago` AS `opago`,`a`.`pagado` AS `pagado`,`a`.`aumento` AS `aumento`,`a`.`disminucion` AS `disminucion`,`a`.`traslados` AS `traslados`,`a`.`asignacion` AS `asignacion`,`a`.`capartado` AS `capartado`,`a`.`ccomprometido` AS `ccomprometido`,`a`.`ccausado` AS `ccausado`,`a`.`copago` AS `copago`,`a`.`cpagado` AS `cpagado` from ((((`view_pres_s3` `a` join `presupuesto` `b` on(((`a`.`codigoadm` = `b`.`codigoadm`) and (`a`.`codigopres` = `b`.`codigopres`) and (`a`.`tipo` = `b`.`tipo`)))) left join `estruadm` `c` on((`b`.`codigoadm` = `c`.`codigo`))) left join `fondo` `d` on((`b`.`tipo` = `d`.`fondo`))) left join `sprv` `e` on((`a`.`cod_prov` = `e`.`proveed`)));


-- Dumping structure for view tortuga.view_pres_f
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `view_pres_f`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `view_pres_f` AS select `a`.`fanulado` AS `fanulado`,`a`.`fapagado` AS `fapagado`,`a`.`decreto` AS `decreto`,`a`.`compromiso` AS `compromiso`,`a`.`fecha` AS `fecha`,`a`.`des` AS `des`,`a`.`observa` AS `observa`,`a`.`cod_prov` AS `cod_prov`,`a`.`numero` AS `numero`,`a`.`status` AS `status`,`a`.`modo` AS `modo`,`a`.`codigoadm` AS `codigoadm`,`a`.`tipo` AS `tipo`,`a`.`codigopres` AS `codigopres`,`a`.`ordinal` AS `ordinal`,`a`.`faudis` AS `faudis`,`a`.`ftrasla` AS `ftrasla`,`a`.`fapartado` AS `fapartado`,`a`.`fcomprome` AS `fcomprome`,`a`.`fcausado` AS `fcausado`,`a`.`fopago` AS `fopago`,`a`.`fpagado` AS `fpagado`,`a`.`frendi` AS `frendi`,`a`.`apartado` AS `apartado`,`a`.`comprometido` AS `comprometido`,`a`.`causado` AS `causado`,`a`.`opago` AS `opago`,`a`.`pagado` AS `pagado`,`a`.`aumento` AS `aumento`,`a`.`disminucion` AS `disminucion`,`a`.`traslados` AS `traslados`,`a`.`asignacion` AS `asignacion`,`a`.`capartado` AS `capartado`,`a`.`ccomprometido` AS `ccomprometido`,`a`.`ccausado` AS `ccausado`,`a`.`copago` AS `copago`,`a`.`cpagado` AS `cpagado`,`b`.`descrip` AS `denofondo` from (`view_pres_s2` `a` join `fondo` `b` on((`a`.`tipo` = `b`.`fondo`)));


-- Dumping structure for view tortuga.view_pres_p
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `view_pres_p`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `view_pres_p` AS select `a`.`fanulado` AS `fanulado`,`a`.`fapagado` AS `fapagado`,`a`.`decreto` AS `decreto`,`a`.`compromiso` AS `compromiso`,`a`.`fecha` AS `fecha`,`a`.`des` AS `des`,`a`.`observa` AS `observa`,`a`.`cod_prov` AS `cod_prov`,`a`.`numero` AS `numero`,`a`.`status` AS `status`,`a`.`modo` AS `modo`,`a`.`codigoadm` AS `codigoadm`,`a`.`tipo` AS `tipo`,`a`.`codigopres` AS `codigopres`,`a`.`ordinal` AS `ordinal`,`a`.`faudis` AS `faudis`,`a`.`ftrasla` AS `ftrasla`,`a`.`fapartado` AS `fapartado`,`a`.`fcomprome` AS `fcomprome`,`a`.`fcausado` AS `fcausado`,`a`.`fopago` AS `fopago`,`a`.`fpagado` AS `fpagado`,`a`.`frendi` AS `frendi`,`a`.`apartado` AS `apartado`,`a`.`comprometido` AS `comprometido`,`a`.`causado` AS `causado`,`a`.`opago` AS `opago`,`a`.`pagado` AS `pagado`,`a`.`aumento` AS `aumento`,`a`.`disminucion` AS `disminucion`,`a`.`traslados` AS `traslados`,`a`.`asignacion` AS `asignacion`,`a`.`capartado` AS `capartado`,`a`.`ccomprometido` AS `ccomprometido`,`a`.`ccausado` AS `ccausado`,`a`.`copago` AS `copago`,`a`.`cpagado` AS `cpagado`,`b`.`denominacion` AS `denopart` from (`view_pres_s2` `a` join `presupuesto` `b` on(((`a`.`codigoadm` = `b`.`codigoadm`) and (`a`.`tipo` = `b`.`tipo`) and (`a`.`codigopres` = `b`.`codigopres`))));


-- Dumping structure for view tortuga.view_pres_s1
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `view_pres_s1`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `view_pres_s1` AS select NULL AS `fanulado`,NULL AS `fapagado`,'' AS `decreto`,'' AS `compromiso`,'2011-01-01' AS `fecha`,'' AS `des`,'' AS `observa`,'' AS `cod_prov`,0 AS `numero`,'' AS `status`,'Asignacion' AS `modo`,`a`.`codigoadm` AS `codigoadm`,`a`.`tipo` AS `tipo`,`a`.`codigopres` AS `codigopres`,'' AS `ordinal`,NULL AS `faudis`,NULL AS `ftrasla`,NULL AS `fapartado`,NULL AS `fcomprome`,NULL AS `fcausado`,NULL AS `fopago`,NULL AS `fpagado`,NULL AS `frendi`,0 AS `apartado`,0 AS `comprometido`,0 AS `causado`,0 AS `opago`,0 AS `pagado`,0 AS `aumento`,0 AS `disminucion`,0 AS `traslados`,`a`.`asignacion` AS `asignacion` from `presupuesto` `a` where (`a`.`asignacion` > 0) union all select NULL AS `fanulado`,NULL AS `fapagado`,'' AS `decreto`,'' AS `compromiso`,`b`.`fecha` AS `fecha`,'' AS `des`,`b`.`concepto` AS `observa`,'' AS `cod_prov`,`a`.`numero` AS `numero`,`b`.`status` AS `status`,'Reintegro' AS `modo`,`a`.`codigoadm` AS `codigoadm`,`a`.`fondo` AS `fondo`,`a`.`codigopres` AS `codigopres`,'' AS `ordinal`,NULL AS `faudis`,NULL AS `ftrasla`,NULL AS `fapartado`,if((`b`.`status` = 'C2'),`b`.`fecha`,NULL) AS `fcomprome`,if((`b`.`status` in ('C2','C1','T2')),`b`.`fecha`,NULL) AS `fcausado`,if((`b`.`status` in ('C2','C1','T2','O2','T1')),`b`.`fecha`,NULL) AS `fopago`,if((`b`.`status` in ('C2','E2','O2','T2','O1','T1','C1')),`b`.`fecha`,NULL) AS `fpagado`,`b`.`fecha` AS `frendi`,0 AS `apartado`,if((`b`.`status` = 'C2'),(-(1) * `a`.`monto`),0) AS `comprometido`,if((`b`.`status` in ('C2','C1','T2')),(-(1) * `a`.`monto`),0) AS `causado`,if((`b`.`status` in ('C2','C1','T2','O2','T1')),(-(1) * `a`.`monto`),0) AS `opago`,if((`b`.`status` in ('C2','E2','O2','T2','O1','T1','C1')),(-(1) * `a`.`monto`),0) AS `pagado`,0 AS `aumento`,0 AS `disminucion`,0 AS `traslados`,0 AS `asignacion` from (`itreinte` `a` join `reinte` `b` on((`a`.`numero` = `b`.`numero`))) where (`b`.`status` not in ('P','E1')) union all select NULL AS `fanulado`,NULL AS `fapagado`,'' AS `decreto`,'' AS `compromiso`,`a`.`fecha` AS `fecha`,'' AS `des`,`a`.`observa` AS `observa`,`a`.`cod_prov` AS `cod_prov`,`a`.`numero` AS `numero`,`a`.`status` AS `status`,'Obra' AS `modo`,`a`.`codigoadm` AS `codigoadm`,`a`.`fondo` AS `fondo`,`a`.`codigopres` AS `codigopres`,'' AS `ordinal`,NULL AS `faudis`,NULL AS `ftrasla`,NULL AS `fapartado`,`a`.`fcomprome` AS `fcomprome`,NULL AS `fcausado`,NULL AS `fopago`,NULL AS `fpagado`,NULL AS `frendi`,0 AS `apartado`,(`a`.`monto` - `a`.`pagoviejo`) AS `comprometido`,0 AS `causado`,0 AS `opago`,0 AS `pagado`,0 AS `aumento`,0 AS `disminucion`,0 AS `traslados`,0 AS `asignacion` from `obra` `a` where (`a`.`status` in ('O2','O4')) union all select `b`.`fanulado` AS `fanulado`,`b`.`fapagado` AS `fapagado`,'' AS `decreto`,'' AS `compromiso`,`b`.`fecha` AS `fecha`,'' AS `des`,`b`.`observa` AS `observa`,`b`.`cod_prov` AS `cod_prov`,`b`.`numero` AS `numero`,`b`.`status` AS `status`,'OP. Obra' AS `OP. Obra`,`a`.`codigoadm` AS `codigoadm`,`a`.`fondo` AS `fondo`,`a`.`codigopres` AS `codigopres`,'' AS `ordinal`,NULL AS `faudis`,NULL AS `ftrasla`,NULL AS `fapartado`,NULL AS `fcomprome`,`b`.`fecha` AS `fcausado`,`b`.`fecha` AS `fopago`,`b`.`fpagado` AS `fpagado`,NULL AS `frendi`,0 AS `apartado`,0 AS `comprometido`,if(((`b`.`fecha` < `b`.`fanulado`) or isnull(`b`.`fanulado`)),(`b`.`total2` - `b`.`amortiza`),0) AS `causado`,if(((`b`.`fecha` < `b`.`fanulado`) or isnull(`b`.`fanulado`)),(`b`.`total2` - `b`.`amortiza`),0) AS `opago`,if(((`b`.`fpagado` < `b`.`fapagado`) or (isnull(`b`.`fapagado`) and (`b`.`status` = 'O3'))),(`b`.`total2` - `b`.`amortiza`),0) AS `pagado`,0 AS `aumento`,0 AS `disminucion`,0 AS `traslados`,0 AS `asignacion` from (`obra` `a` join `odirect` `b` on((`a`.`numero` = `b`.`obr`))) where (`b`.`status` in ('O2','O3','OA')) union all select `b`.`fanulado` AS `fanulado`,`b`.`fapagado` AS `fapagado`,'' AS `decreto`,'' AS `compromiso`,`b`.`fecha` AS `fecha`,'' AS `des`,`b`.`observa` AS `observa`,`b`.`cod_prov` AS `cod_prov`,`b`.`numero` AS `numero`,`b`.`status` AS `status`,'OP. Obra' AS `OP. Obra`,`a`.`codigoadm` AS `codigoadm`,`a`.`fondo` AS `fondo`,`a`.`codigopres` AS `codigopres`,'' AS `ordinal`,NULL AS `faudis`,NULL AS `ftrasla`,NULL AS `fapartado`,NULL AS `fcomprome`,`b`.`fanulado` AS `fcausado`,`b`.`fanulado` AS `fopago`,`b`.`fapagado` AS `fpagado`,NULL AS `frendi`,0 AS `apartado`,0 AS `comprometido`,if((`b`.`fanulado` > `b`.`fecha`),(-(1) * (`b`.`total2` - `b`.`amortiza`)),0) AS `causado`,if((`b`.`fanulado` > `b`.`fecha`),(-(1) * (`b`.`total2` - `b`.`amortiza`)),0) AS `opago`,if((`b`.`fapagado` > `b`.`fpagado`),(-(1) * (`b`.`total2` - `b`.`amortiza`)),0) AS `pagado`,0 AS `aumento`,0 AS `disminucion`,0 AS `traslados`,0 AS `asignacion` from (`obra` `a` join `odirect` `b` on((`a`.`numero` = `b`.`obr`))) where ((`b`.`status` in ('O2','O3','OA')) and ((`b`.`fanulado` > `b`.`fecha`) or (`b`.`fapagado` > `b`.`fpagado`))) union all select NULL AS `fanulado`,NULL AS `fapagado`,'' AS `decreto`,`b`.`compromiso` AS `compromiso`,`b`.`fecha` AS `fecha`,'' AS `des`,`b`.`descrip` AS `descrip`,'' AS `q`,`a`.`numero` AS `numero`,`b`.`status` AS `status`,'Nomina' AS `Nomina`,`a`.`codigoadm` AS `codigoadm`,`a`.`fondo` AS `fondo`,`a`.`codigopres` AS `codigopres`,'' AS `ordinal`,NULL AS `faudis`,NULL AS `ftrasla`,NULL AS `fapartado`,`b`.`fcomprome` AS `fcomprome`,NULL AS `fcausado`,NULL AS `foapgo`,NULL AS `fpagado`,NULL AS `frendi`,0 AS `apartado`,`a`.`monto` AS `comprometido`,0 AS `causado`,0 AS `opago`,0 AS `pagado`,0 AS `aumento`,0 AS `disminucion`,0 AS `traslados`,0 AS `asignacion` from (`asignomi` `a` join `nomi` `b` on((`a`.`numero` = `b`.`numero`))) where (`b`.`status` in ('C','D','O','E')) union all select `b`.`fanulado` AS `fanulado`,`b`.`fapagado` AS `fapagado`,'' AS `decreto`,'' AS `compromiso`,`b`.`fecha` AS `fecha`,`a`.`descripcion` AS `descripcion`,`b`.`observa` AS `observa`,if((length(`b`.`cod_prov2`) > 0),`b`.`cod_prov2`,`b`.`cod_prov`) AS `cod_prov`,`a`.`numero` AS `numero`,`b`.`status` AS `status`,'OP. Nomina' AS `OP. Nomina`,`a`.`codigoadm` AS `codigoadm`,`a`.`fondo` AS `fondo`,`a`.`partida` AS `partida`,'' AS `ordinal`,NULL AS `faudis`,NULL AS `ftrasla`,NULL AS `fapartado`,NULL AS `fcomprome`,`b`.`fecha` AS `fcausado`,`b`.`fecha` AS `foapgo`,`b`.`fpagado` AS `fpagado`,NULL AS `frendi`,0 AS `apartado`,0 AS `comprometido`,if(((`b`.`fecha` < `b`.`fanulado`) or isnull(`b`.`fanulado`)),`a`.`importe`,0) AS `causado`,if(((`b`.`fecha` < `b`.`fanulado`) or isnull(`b`.`fanulado`)),`a`.`importe`,0) AS `opago`,if(((`b`.`fpagado` < `b`.`fapagado`) or (isnull(`b`.`fapagado`) and (`b`.`status` = 'K3'))),`a`.`importe`,0) AS `pagado`,0 AS `aumento`,0 AS `disminucion`,0 AS `traslados`,0 AS `asignacion` from (`itodirect` `a` join `odirect` `b` on((`a`.`numero` = `b`.`numero`))) where (`b`.`status` in ('K2','K3','KA')) union all select `b`.`fanulado` AS `fanulado`,`b`.`fapagado` AS `fapagado`,'' AS `decreto`,'' AS `compromiso`,`b`.`fecha` AS `fecha`,`a`.`descripcion` AS `descripcion`,`b`.`observa` AS `observa`,if((length(`b`.`cod_prov2`) > 0),`b`.`cod_prov2`,`b`.`cod_prov`) AS `cod_prov`,`a`.`numero` AS `numero`,`b`.`status` AS `status`,'OP. Nomina' AS `OP. Nomina`,`a`.`codigoadm` AS `codigoadm`,`a`.`fondo` AS `fondo`,`a`.`partida` AS `partida`,'' AS `ordinal`,NULL AS `faudis`,NULL AS `ftrasla`,NULL AS `fapartado`,NULL AS `fcomprome`,`b`.`fanulado` AS `fcausado`,`b`.`fanulado` AS `fopago`,`b`.`fapagado` AS `fpagado`,NULL AS `frendi`,0 AS `apartado`,0 AS `comprometido`,if((`b`.`fanulado` > `b`.`fecha`),(-(1) * `a`.`importe`),0) AS `causado`,if((`b`.`fanulado` > `b`.`fecha`),(-(1) * `a`.`importe`),0) AS `opago`,if((`b`.`fapagado` > `b`.`fpagado`),(-(1) * `a`.`importe`),0) AS `pagado`,0 AS `aumento`,0 AS `disminucion`,0 AS `traslados`,0 AS `asignacion` from (`itodirect` `a` join `odirect` `b` on((`a`.`numero` = `b`.`numero`))) where ((`b`.`status` in ('K2','K3','KA')) and ((`b`.`fanulado` > `b`.`fecha`) or (`b`.`fapagado` > `b`.`fpagado`))) union all select NULL AS `fanulado`,NULL AS `fapagado`,'' AS `decreto`,`b`.`compromiso` AS `compromiso`,`b`.`fecha` AS `fecha`,`a`.`descripcion` AS `descripcion`,`b`.`observa` AS `observa`,`b`.`cod_prov` AS `cod_prov`,`a`.`numero` AS `numero`,`b`.`status` AS `status`,'Compra' AS `Compra`,`a`.`codigoadm` AS `codigoadm`,`a`.`fondo` AS `fondo`,`a`.`partida` AS `partida`,'' AS `ordinal`,NULL AS `faudis`,NULL AS `ftrasla`,NULL AS `fapartado`,`b`.`fcomprome` AS `fcomprome`,if((`b`.`status` <> 'C'),`b`.`fcausado`,NULL) AS `fcausado`,NULL AS `foapgo`,NULL AS `fpagado`,NULL AS `frendi`,0 AS `apartado`,if((`b`.`status` = 'X'),(-(1) * `a`.`importe`),`a`.`importe`) AS `comprometido`,if((`b`.`status` = 'X'),(-(1) * `a`.`importe`),if((`b`.`status` in ('T','O','E')),`a`.`importe`,0)) AS `causado`,0 AS `opago`,0 AS `pagado`,0 AS `aumento`,0 AS `disminucion`,0 AS `traslados`,0 AS `asignacion` from (`itocompra` `a` join `ocompra` `b` on((`a`.`numero` = `b`.`numero`))) where (`b`.`status` in ('C','T','O','E','X','Y')) union all select `d`.`fanulado` AS `fanulado`,`d`.`fapagado` AS `fapagado`,'' AS `decreto`,'' AS `compromiso`,`d`.`fecha` AS `fecha`,`a`.`descripcion` AS `descripcion`,`d`.`observa` AS `observa`,if((length(`d`.`cod_prov2`) > 0),`d`.`cod_prov2`,`d`.`cod_prov`) AS `cod_prov`,`d`.`numero` AS `numero`,`d`.`status` AS `status`,'OP. Compra' AS `OP. Compra`,`a`.`codigoadm` AS `codigoadm`,`a`.`fondo` AS `fondo`,`a`.`partida` AS `partida`,'' AS `ordinal`,NULL AS `faudis`,NULL AS `ftrasla`,NULL AS `fapartado`,NULL AS `fcomprome`,NULL AS `fcausado`,`d`.`fecha` AS `fopago`,`d`.`fpagado` AS `fpagado`,NULL AS `frendi`,0 AS `apartado`,0 AS `comprometido`,0 AS `causado`,if(((`d`.`fecha` < `d`.`fanulado`) or isnull(`d`.`fanulado`)),`a`.`importe`,0) AS `opago`,if(((`d`.`fpagado` < `d`.`fapagado`) or (isnull(`d`.`fapagado`) and (`d`.`status` = 'F3'))),`a`.`importe`,0) AS `pagado`,0 AS `aumento`,0 AS `disminucion`,0 AS `traslados`,0 AS `asignacion` from (((`itocompra` `a` join `ocompra` `b` on((`a`.`numero` = `b`.`numero`))) join `pacom` `c` on((`b`.`numero` = `c`.`compra`))) join `odirect` `d` on((`c`.`pago` = `d`.`numero`))) where (`d`.`status` in ('F2','F3','FA')) union all select `d`.`fanulado` AS `fanulado`,`d`.`fapagado` AS `fapagado`,'' AS `decreto`,'' AS `compromiso`,`d`.`fecha` AS `fecha`,`a`.`descripcion` AS `descripcion`,`d`.`observa` AS `observa`,if((length(`d`.`cod_prov2`) > 0),`d`.`cod_prov2`,`d`.`cod_prov`) AS `cod_prov`,`d`.`numero` AS `numero`,`d`.`status` AS `status`,'OP. Compra' AS `OP. Compra`,`a`.`codigoadm` AS `codigoadm`,`a`.`fondo` AS `fondo`,`a`.`partida` AS `partida`,'' AS `ordinal`,NULL AS `faudis`,NULL AS `ftrasla`,NULL AS `fapartado`,NULL AS `fcomprome`,NULL AS `fcausado`,`d`.`fanulado` AS `fopago`,`d`.`fapagado` AS `fpagado`,NULL AS `frendi`,0 AS `apartado`,0 AS `comprometido`,0 AS `causado`,if((`d`.`fanulado` > `d`.`fecha`),(-(1) * `a`.`importe`),0) AS `opago`,if((`d`.`fapagado` > `d`.`fpagado`),(-(1) * `a`.`importe`),0) AS `pagado`,0 AS `aumento`,0 AS `disminucion`,0 AS `traslados`,0 AS `asignacion` from (((`itocompra` `a` join `ocompra` `b` on((`a`.`numero` = `b`.`numero`))) join `pacom` `c` on((`b`.`numero` = `c`.`compra`))) join `odirect` `d` on((`c`.`pago` = `d`.`numero`))) where ((`d`.`status` in ('F2','F3','FA')) and ((`d`.`fanulado` > `d`.`fecha`) or (`d`.`fapagado` > `d`.`fpagado`))) union all select `b`.`fanulado` AS `fanulado`,`b`.`fapagado` AS `fapagado`,'' AS `decreto`,'' AS `compromiso`,`b`.`fecha` AS `fecha`,`a`.`descripcion` AS `descripcion`,`b`.`observa` AS `observa`,`b`.`cod_prov` AS `cod_prov`,`a`.`numero` AS `numero`,`b`.`status` AS `status`,'OP. Directo' AS `OP. Directo`,`a`.`codigoadm` AS `codigoadm`,`a`.`fondo` AS `fondo`,`a`.`partida` AS `partida`,'' AS `ordinal`,NULL AS `faudis`,NULL AS `ftrasla`,NULL AS `fapartado`,`b`.`fecha` AS `fcomprome`,`b`.`fecha` AS `fcausado`,`b`.`fecha` AS `fopago`,`b`.`fpagado` AS `fpagado`,NULL AS `frendi`,0 AS `apartado`,if(((`b`.`fecha` < `b`.`fanulado`) or isnull(`b`.`fanulado`)),`a`.`importe`,0) AS `comprometido`,if(((`b`.`fecha` < `b`.`fanulado`) or isnull(`b`.`fanulado`)),`a`.`importe`,0) AS `causado`,if(((`b`.`fecha` < `b`.`fanulado`) or isnull(`b`.`fanulado`)),`a`.`importe`,0) AS `opago`,if(((`b`.`fpagado` < `b`.`fapagado`) or (isnull(`b`.`fapagado`) and (`b`.`status` = 'B3'))),`a`.`importe`,0) AS `pagado`,0 AS `aumento`,0 AS `disminucion`,0 AS `traslados`,0 AS `asignacion` from (`itodirect` `a` join `odirect` `b` on((`a`.`numero` = `b`.`numero`))) where (`b`.`status` in ('B2','B3','BA')) union all select `b`.`fanulado` AS `fanulado`,`b`.`fapagado` AS `fapagado`,'' AS `decreto`,'' AS `compromiso`,`b`.`fecha` AS `fecha`,`a`.`descripcion` AS `descripcion`,`b`.`observa` AS `observa`,`b`.`cod_prov` AS `cod_prov`,`a`.`numero` AS `numero`,`b`.`status` AS `status`,'OP. Directo' AS `OP. Directo`,`a`.`codigoadm` AS `codigoadm`,`a`.`fondo` AS `fondo`,`a`.`partida` AS `partida`,'' AS `ordinal`,NULL AS `faudis`,NULL AS `ftrasla`,NULL AS `fapartado`,`b`.`fanulado` AS `fcomprome`,`b`.`fanulado` AS `fcausado`,`b`.`fanulado` AS `fopago`,`b`.`fapagado` AS `fpagado`,NULL AS `frendi`,0 AS `apartado`,if((`b`.`fanulado` > `b`.`fecha`),(-(1) * `a`.`importe`),0) AS `comprometido`,if((`b`.`fanulado` > `b`.`fecha`),(-(1) * `a`.`importe`),0) AS `causado`,if((`b`.`fanulado` > `b`.`fecha`),(-(1) * `a`.`importe`),0) AS `opago`,if((`b`.`fapagado` > `b`.`fpagado`),(-(1) * `a`.`importe`),0) AS `pagado`,0 AS `aumento`,0 AS `disminucion`,0 AS `traslados`,0 AS `asignacion` from (`itodirect` `a` join `odirect` `b` on((`a`.`numero` = `b`.`numero`))) where ((`b`.`status` in ('B2','B3','BA')) and ((`b`.`fanulado` > `b`.`fecha`) or (`b`.`fapagado` > `b`.`fpagado`))) union all select `b`.`fanulado` AS `fanulado`,`b`.`fapagado` AS `fapagado`,'' AS `decreto`,'' AS `compromiso`,`b`.`fecha` AS `fecha`,`a`.`descripcion` AS `descripcion`,`b`.`observa` AS `observa`,`b`.`cod_prov` AS `cod_prov`,`a`.`numero` AS `numero`,`b`.`status` AS `status`,'OP. Contrato' AS `OP. Directo`,`a`.`codigoadm` AS `codigoadm`,`a`.`fondo` AS `fondo`,`a`.`partida` AS `partida`,'' AS `ordinal`,NULL AS `faudis`,NULL AS `ftrasla`,NULL AS `fapartado`,NULL AS `fcomprome`,`b`.`fecha` AS `fcausado`,`b`.`fecha` AS `fopago`,`b`.`fpagado` AS `fpagado`,NULL AS `frendi`,0 AS `apartado`,0 AS `comprometido`,if(((`b`.`fecha` < `b`.`fanulado`) or isnull(`b`.`fanulado`)),`a`.`importe`,0) AS `causado`,if(((`b`.`fecha` < `b`.`fanulado`) or isnull(`b`.`fanulado`)),`a`.`importe`,0) AS `opago`,if(((`b`.`fpagado` < `b`.`fapagado`) or (isnull(`b`.`fapagado`) and (`b`.`status` = 'C3'))),`a`.`importe`,0) AS `pagado`,0 AS `aumento`,0 AS `disminucion`,0 AS `traslados`,0 AS `asignacion` from (`itodirect` `a` join `odirect` `b` on((`a`.`numero` = `b`.`numero`))) where (`b`.`status` in ('C2','CX','CY','C3')) union all select `b`.`fanulado` AS `fanulado`,`b`.`fapagado` AS `fapagado`,'' AS `decreto`,'' AS `compromiso`,`b`.`fecha` AS `fecha`,`a`.`descripcion` AS `descripcion`,`b`.`observa` AS `observa`,`b`.`cod_prov` AS `cod_prov`,`a`.`numero` AS `numero`,`b`.`status` AS `status`,'OP. Contrato' AS `OP. Directo`,`a`.`codigoadm` AS `codigoadm`,`a`.`fondo` AS `fondo`,`a`.`partida` AS `partida`,'' AS `ordinal`,NULL AS `faudis`,NULL AS `ftrasla`,NULL AS `fapartado`,NULL AS `fcomprome`,`b`.`fanulado` AS `fcausado`,`b`.`fanulado` AS `fopago`,`b`.`fapagado` AS `fpagado`,NULL AS `frendi`,0 AS `apartado`,0 AS `comprometido`,if((`b`.`fanulado` > `b`.`fecha`),(-(1) * `a`.`importe`),0) AS `causado`,if((`b`.`fanulado` > `b`.`fecha`),(-(1) * `a`.`importe`),0) AS `opago`,if((`b`.`fapagado` > `b`.`fpagado`),(-(1) * `a`.`importe`),0) AS `pagado`,0 AS `aumento`,0 AS `disminucion`,0 AS `traslados`,0 AS `asignacion` from (`itodirect` `a` join `odirect` `b` on((`a`.`numero` = `b`.`numero`))) where ((`b`.`status` in ('C2','CX','CY','C3')) and ((`b`.`fanulado` > `b`.`fecha`) or (`b`.`fapagado` > `b`.`fpagado`))) union all select NULL AS `fanulado`,NULL AS `fapagado`,`b`.`nrooficio` AS `decreto`,'' AS `compromiso`,`b`.`fecha` AS `fecha`,`a`.`denomi` AS `denomi`,`b`.`motivo` AS `motivo`,'' AS `a`,`a`.`numero` AS `numero`,`b`.`status` AS `status`,`b`.`tipo` AS `tipo`,`a`.`codigoadm` AS `codigoadm`,`a`.`fondo` AS `fondo`,`a`.`codigopres` AS `codigopres`,'' AS `ordinal`,`b`.`fecha` AS `faudis`,NULL AS `ftrasla`,NULL AS `fapartado`,NULL AS `fcomprome`,NULL AS `fcausado`,NULL AS `fopago`,NULL AS `fpagado`,NULL AS `frendi`,0 AS `apartado`,0 AS `comprometido`,0 AS `causado`,0 AS `opago`,0 AS `pagado`,(`a`.`monto` * (substr(`b`.`tipo`,1,1) = 'A')) AS `aumento`,(`a`.`monto` * (substr(`b`.`tipo`,1,1) = 'D')) AS `disminucion`,0 AS `traslados`,0 AS `asignacion` from (`itaudis` `a` join `audis` `b` on((`a`.`numero` = `b`.`numero`))) where (`b`.`status` = 'C') union all select NULL AS `fanulado`,NULL AS `fapagado`,`b`.`nrooficio` AS `decreto`,'' AS `compromiso`,`b`.`fecha` AS `fecha`,`a`.`denomi` AS `denomi`,`b`.`motivo` AS `motivo`,'' AS `a`,`a`.`numero` AS `numero`,`b`.`status` AS `status`,'Traslado' AS `Traslado`,`a`.`codigoadm` AS `codigoadm`,`a`.`fondo` AS `fondo`,`a`.`codigopres` AS `codigopres`,'' AS `ordinal`,NULL AS `faudis`,`b`.`fecha` AS `ftrasla`,NULL AS `fapartado`,NULL AS `fcomprome`,NULL AS `fcausado`,NULL AS `fopago`,NULL AS `fpagado`,NULL AS `frendi`,0 AS `apartado`,0 AS `comprometido`,0 AS `causado`,0 AS `opago`,0 AS `pagado`,0 AS `aumento`,0 AS `disminucion`,(`a`.`aumento` - `a`.`disminucion`) AS `traslados`,0 AS `asignacion` from (`ittrasla` `a` join `trasla` `b` on((`a`.`numero` = `b`.`numero`))) where (`b`.`status` = 'C') union all select NULL AS `fanulado`,NULL AS `fapagado`,'' AS `decreto`,`b`.`compromiso` AS `compromiso`,`b`.`fecha` AS `fecha`,'' AS `des`,`b`.`concepto` AS `observa`,'' AS `cod_prov`,`a`.`numero` AS `numero`,`b`.`status` AS `status`,'Tramitacion' AS `modo`,`a`.`codigoadm` AS `codigoadm`,`a`.`fondo` AS `fondo`,`a`.`codigopres` AS `codigopres`,'' AS `ordinal`,NULL AS `faudis`,NULL AS `ftrasla`,NULL AS `fapartado`,`b`.`fcomprome` AS `fcomprome`,`b`.`fecha` AS `fcausado`,`b`.`fecha` AS `fopago`,`b`.`fpagado` AS `fpagado`,NULL AS `frendi`,0 AS `apartado`,if((`b`.`status` in ('C2','T2','O2','E2','E1','O1','T1')),`a`.`importe`,0) AS `comprometido`,if((`b`.`status` in ('T2','O2','E2','E1','O1')),`a`.`importe`,0) AS `causado`,if((`b`.`status` in ('O2','E2','E1')),`a`.`importe`,0) AS `opago`,if((`b`.`status` = 'E2'),`a`.`importe`,0) AS `pagado`,0 AS `aumento`,0 AS `disminucion`,0 AS `traslados`,0 AS `asignacion` from (`ittrami` `a` join `trami` `b` on((`a`.`numero` = `b`.`numero`))) where (`b`.`status` not in ('P','C1')) union all select NULL AS `fanulado`,NULL AS `fapagado`,'' AS `decreto`,NULL AS `compromiso`,`b`.`fecha` AS `fecha`,NULL AS `descripcion`,`b`.`reque` AS `observa`,NULL AS `cod_prov`,`a`.`numero` AS `numero`,`b`.`status` AS `status`,'Pre-Compromiso' AS `Compra`,`a`.`codigoadm` AS `codigoadm`,`a`.`fondo` AS `fondo`,`a`.`codigopres` AS `partida`,'' AS `ordinal`,NULL AS `faudis`,NULL AS `ftrasla`,`b`.`fecha` AS `fapartado`,NULL AS `fcomprome`,NULL AS `fcausado`,NULL AS `fopago`,NULL AS `fpagado`,NULL AS `frendi`,`a`.`soli` AS `apartado`,0 AS `comprometido`,0 AS `causado`,0 AS `opago`,0 AS `pagado`,0 AS `aumento`,0 AS `disminucion`,0 AS `traslados`,0 AS `asignacion` from (`itcdisp` `a` join `cdisp` `b` on((`a`.`numero` = `b`.`numero`))) where (`b`.`status` in ('C','F','A')) union all select NULL AS `fanulado`,NULL AS `fapagado`,'' AS `decreto`,NULL AS `compromiso`,`b`.`fecha` AS `fecha`,NULL AS `descripcion`,`b`.`reque` AS `observa`,NULL AS `cod_prov`,`a`.`numero` AS `numero`,`b`.`status` AS `status`,'Pre-Compromiso' AS `Compra`,`a`.`codigoadm` AS `codigoadm`,`a`.`fondo` AS `fondo`,`a`.`codigopres` AS `partida`,'' AS `ordinal`,NULL AS `faudis`,NULL AS `ftrasla`,if((`b`.`status` = 'F'),`b`.`ffinal`,`b`.`fanulado`) AS `fapartado`,NULL AS `fcomprome`,NULL AS `fcausado`,NULL AS `fopago`,NULL AS `fpagado`,NULL AS `frendi`,(-(1) * `a`.`soli`) AS `apartado`,0 AS `comprometido`,0 AS `causado`,0 AS `opago`,0 AS `pagado`,0 AS `aumento`,0 AS `disminucion`,0 AS `traslados`,0 AS `asignacion` from (`itcdisp` `a` join `cdisp` `b` on((`a`.`numero` = `b`.`numero`))) where (`b`.`status` in ('F','A')) union all select NULL AS `fanulado`,NULL AS `fapagado`,'' AS `decreto`,NULL AS `compromiso`,`b`.`fecha` AS `fecha`,NULL AS `descripcion`,`b`.`concepto` AS `observa`,NULL AS `cod_prov`,`a`.`numero` AS `numero`,`b`.`status` AS `status`,'Economia' AS `Compra`,`a`.`codigoadm` AS `codigoadm`,`a`.`fondo` AS `fondo`,`a`.`codigopres` AS `partida`,'' AS `ordinal`,NULL AS `faudis`,NULL AS `ftrasla`,NULL AS `fapartado`,`b`.`fecha` AS `fcomprome`,NULL AS `fcausado`,NULL AS `fopago`,NULL AS `fpagado`,NULL AS `frendi`,0 AS `apartado`,(-(1) * `a`.`monto`) AS `comprometido`,0 AS `causado`,0 AS `opago`,0 AS `pagado`,0 AS `aumento`,0 AS `disminucion`,0 AS `traslados`,0 AS `asignacion` from (`itecono` `a` join `econo` `b` on((`a`.`numero` = `b`.`numero`))) where (`b`.`status` = 'C');


-- Dumping structure for view tortuga.view_pres_s2
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `view_pres_s2`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `view_pres_s2` AS select `view_pres_s1`.`fanulado` AS `fanulado`,`view_pres_s1`.`fapagado` AS `fapagado`,`view_pres_s1`.`decreto` AS `decreto`,`view_pres_s1`.`compromiso` AS `compromiso`,`view_pres_s1`.`fecha` AS `fecha`,`view_pres_s1`.`des` AS `des`,`view_pres_s1`.`observa` AS `observa`,`view_pres_s1`.`cod_prov` AS `cod_prov`,`view_pres_s1`.`numero` AS `numero`,`view_pres_s1`.`status` AS `status`,`view_pres_s1`.`modo` AS `modo`,`view_pres_s1`.`codigoadm` AS `codigoadm`,`view_pres_s1`.`tipo` AS `tipo`,`view_pres_s1`.`codigopres` AS `codigopres`,`view_pres_s1`.`ordinal` AS `ordinal`,`view_pres_s1`.`faudis` AS `faudis`,`view_pres_s1`.`ftrasla` AS `ftrasla`,`view_pres_s1`.`fapartado` AS `fapartado`,`view_pres_s1`.`fcomprome` AS `fcomprome`,`view_pres_s1`.`fcausado` AS `fcausado`,`view_pres_s1`.`fopago` AS `fopago`,`view_pres_s1`.`fpagado` AS `fpagado`,`view_pres_s1`.`frendi` AS `frendi`,`view_pres_s1`.`apartado` AS `apartado`,`view_pres_s1`.`comprometido` AS `comprometido`,`view_pres_s1`.`causado` AS `causado`,`view_pres_s1`.`opago` AS `opago`,`view_pres_s1`.`pagado` AS `pagado`,`view_pres_s1`.`aumento` AS `aumento`,`view_pres_s1`.`disminucion` AS `disminucion`,`view_pres_s1`.`traslados` AS `traslados`,`view_pres_s1`.`asignacion` AS `asignacion`,round((((`view_pres_s1`.`aumento` - `view_pres_s1`.`disminucion`) + `view_pres_s1`.`traslados`) - `view_pres_s1`.`apartado`),2) AS `capartado`,round((((`view_pres_s1`.`aumento` - `view_pres_s1`.`disminucion`) + `view_pres_s1`.`traslados`) - `view_pres_s1`.`comprometido`),2) AS `ccomprometido`,round((((`view_pres_s1`.`aumento` - `view_pres_s1`.`disminucion`) + `view_pres_s1`.`traslados`) - `view_pres_s1`.`causado`),2) AS `ccausado`,round((((`view_pres_s1`.`aumento` - `view_pres_s1`.`disminucion`) + `view_pres_s1`.`traslados`) - `view_pres_s1`.`opago`),2) AS `copago`,round((((`view_pres_s1`.`aumento` - `view_pres_s1`.`disminucion`) + `view_pres_s1`.`traslados`) - `view_pres_s1`.`pagado`),2) AS `cpagado` from `view_pres_s1`;


-- Dumping structure for view tortuga.view_pres_s3
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `view_pres_s3`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `view_pres_s3` AS select `todo`.`fanulado` AS `fanulado`,`todo`.`fapagado` AS `fapagado`,`todo`.`decreto` AS `decreto`,`todo`.`compromiso` AS `compromiso`,`todo`.`fecha` AS `fecha`,`todo`.`des` AS `des`,`todo`.`observa` AS `observa`,`todo`.`cod_prov` AS `cod_prov`,`todo`.`numero` AS `numero`,`todo`.`status` AS `status`,`todo`.`modo` AS `modo`,`todo`.`codigoadm` AS `codigoadm`,`todo`.`tipo` AS `tipo`,`todo`.`codigopres` AS `codigopres`,`todo`.`ordinal` AS `ordinal`,`todo`.`faudis` AS `faudis`,`todo`.`ftrasla` AS `ftrasla`,`todo`.`fapartado` AS `fapartado`,`todo`.`fcomprome` AS `fcomprome`,`todo`.`fcausado` AS `fcausado`,`todo`.`fopago` AS `fopago`,`todo`.`fpagado` AS `fpagado`,`todo`.`frendi` AS `frendi`,`todo`.`apartado` AS `apartado`,`todo`.`comprometido` AS `comprometido`,`todo`.`causado` AS `causado`,`todo`.`opago` AS `opago`,`todo`.`pagado` AS `pagado`,`todo`.`aumento` AS `aumento`,`todo`.`disminucion` AS `disminucion`,`todo`.`traslados` AS `traslados`,`todo`.`asignacion` AS `asignacion`,((((`todo`.`asignacion` + `todo`.`aumento`) - `todo`.`disminucion`) + `todo`.`traslados`) - `todo`.`apartado`) AS `capartado`,((((`todo`.`asignacion` + `todo`.`aumento`) - `todo`.`disminucion`) + `todo`.`traslados`) - `todo`.`comprometido`) AS `ccomprometido`,((((`todo`.`asignacion` + `todo`.`aumento`) - `todo`.`disminucion`) + `todo`.`traslados`) - `todo`.`causado`) AS `ccausado`,((((`todo`.`asignacion` + `todo`.`aumento`) - `todo`.`disminucion`) + `todo`.`traslados`) - `todo`.`opago`) AS `copago`,((((`todo`.`asignacion` + `todo`.`aumento`) - `todo`.`disminucion`) + `todo`.`traslados`) - `todo`.`pagado`) AS `cpagado` from `view_pres_s1` `todo`;


-- Dumping structure for view tortuga.view_retenciones
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `view_retenciones`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `view_retenciones` AS select `a`.`numero` AS `desembolso`,`c`.`numero` AS `ordpago`,'' AS `ordcompra`,'' AS `multiple`,`a`.`cod_prov` AS `cod_prov`,`c`.`factura` AS `factura`,`c`.`controlfac` AS `controlfac`,`c`.`fechafac` AS `fechafac`,`c`.`total2` AS `total2`,`c`.`reteiva` AS `reteiva`,`c`.`reten` AS `reten`,`c`.`impmunicipal` AS `impmunicipal`,`c`.`imptimbre` AS `imptimbre`,`c`.`total` AS `total`,`f`.`nrocomp` AS `nrocomp`,`c`.`breten` AS `breten`,`c`.`subtotal` AS `subtotal`,`c`.`creten` AS `creten`,`p`.`rif` AS `rif`,`p`.`nombre` AS `nombre`,concat_ws(' ',`p`.`direc1`,`p`.`direc2`,`p`.`direc3`) AS `direccion`,`a`.`fdesem` AS `fdesem` from ((((`desem` `a` join `pades` `b` on((`a`.`numero` = `b`.`desem`))) join `odirect` `c` on((`c`.`numero` = `b`.`pago`))) left join `sprv` `p` on((`a`.`cod_prov` = `p`.`proveed`))) left join `riva` `f` on(((`c`.`numero` = `f`.`odirect`) and (`f`.`tipo_doc` <> 'FC')))) where ((`c`.`status` = 'O3') and (`a`.`status` = 'D2')) union all select `a`.`numero` AS `desembolso`,`c`.`numero` AS `ordpago`,`e`.`numero` AS `ordcompra`,'' AS `multiple`,`a`.`cod_prov` AS `cod_prov`,`e`.`factura` AS `factura`,`e`.`controlfac` AS `controlfac`,`e`.`fechafac` AS `fechafac`,`e`.`total2` AS `total2`,`e`.`reteiva` AS `reteiva`,`e`.`reten` AS `reten`,`e`.`impmunicipal` AS `impmunicipal`,`e`.`imptimbre` AS `imptimbre`,`e`.`total` AS `total`,`f`.`nrocomp` AS `nrocomp`,`e`.`breten` AS `breten`,`e`.`subtotal` AS `subtotal`,`e`.`creten` AS `creten`,`p`.`rif` AS `rif`,`p`.`nombre` AS `nombre`,concat_ws(' ',`p`.`direc1`,`p`.`direc2`,`p`.`direc3`) AS `direccion`,`a`.`fdesem` AS `fdesem` from ((((((`desem` `a` join `pades` `b` on((`a`.`numero` = `b`.`desem`))) join `odirect` `c` on((`b`.`pago` = `c`.`numero`))) join `pacom` `d` on((`c`.`numero` = `d`.`pago`))) join `ocompra` `e` on((`d`.`compra` = `e`.`numero`))) left join `riva` `f` on(((`e`.`numero` = `f`.`ocompra`) and (`f`.`tipo_doc` <> 'FC')))) left join `sprv` `p` on((`a`.`cod_prov` = `p`.`proveed`))) where ((`c`.`status` = 'F3') and (`a`.`status` = 'D2')) union all select `a`.`numero` AS `desembolso`,`c`.`numero` AS `ordpago`,'' AS `ordcompra`,'' AS `multiple`,`a`.`cod_prov` AS `cod_prov`,`c`.`factura` AS `factura`,`c`.`controlfac` AS `controlfac`,`c`.`fechafac` AS `fechafac`,`c`.`total2` AS `total2`,`c`.`reteiva` AS `reteiva`,`c`.`reten` AS `reten`,`c`.`impmunicipal` AS `impmunicipal`,`c`.`imptimbre` AS `imptimbre`,`c`.`total` AS `total`,`f`.`nrocomp` AS `nrocomp`,`c`.`breten` AS `breten`,`c`.`subtotal` AS `subtotal`,`c`.`creten` AS `creten`,`p`.`rif` AS `rif`,`p`.`nombre` AS `nombre`,concat_ws(' ',`p`.`direc1`,`p`.`direc2`,`p`.`direc3`) AS `direccion`,`a`.`fdesem` AS `fdesem` from ((((`desem` `a` join `pades` `b` on((`a`.`numero` = `b`.`desem`))) join `odirect` `c` on((`b`.`pago` = `c`.`numero`))) left join `riva` `f` on(((`c`.`numero` = `f`.`odirect`) and (`f`.`tipo_doc` <> 'FC')))) left join `sprv` `p` on((`a`.`cod_prov` = `p`.`proveed`))) where ((`c`.`status` = 'B3') and (`a`.`status` = 'D2') and (`c`.`multiple` = 'N')) union all select `a`.`numero` AS `desembolso`,`c`.`numero` AS `ordpago`,'' AS `ordcompra`,`g`.`id` AS `multiple`,`a`.`cod_prov` AS `cod_prov`,`g`.`factura` AS `factura`,`g`.`controlfac` AS `controlfac`,`g`.`fechafac` AS `fechafac`,`g`.`total2` AS `total2`,`g`.`reteiva` AS `reteiva`,`g`.`reten` AS `reten`,`g`.`impmunicipal` AS `impmunicipal`,`g`.`imptimbre` AS `imptimbre`,`g`.`total` AS `total`,`f`.`nrocomp` AS `nrocomp`,`g`.`breten` AS `breten`,`g`.`subtotal` AS `subtotal`,`g`.`creten` AS `creten`,`p`.`rif` AS `rif`,`p`.`nombre` AS `nombre`,concat_ws(' ',`p`.`direc1`,`p`.`direc2`,`p`.`direc3`) AS `direccion`,`a`.`fdesem` AS `fdesem` from (((((`desem` `a` join `pades` `b` on((`a`.`numero` = `b`.`desem`))) join `odirect` `c` on((`b`.`pago` = `c`.`numero`))) join `itfac` `g` on((`c`.`numero` = `g`.`numero`))) left join `riva` `f` on(((`g`.`id` = `f`.`itfac`) and (`f`.`tipo_doc` <> 'FC')))) left join `sprv` `p` on((`a`.`cod_prov` = `p`.`proveed`))) where (((`c`.`status` = 'B3') or (`c`.`status` = 'N3')) and (`a`.`status` = 'D2') and (`c`.`multiple` = 'S'));


-- Dumping structure for view tortuga.view_sipres
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `view_sipres`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `view_sipres` AS select `dpresu04`.`nromov` AS `compromiso`,concat_ws('.',substr(`dpresu04`.`codpre`,1,2),substr(`dpresu04`.`codpre`,3,2),substr(`dpresu04`.`codpre`,5,2),substr(`dpresu04`.`codpre`,7,2)) AS `codigoadm`,concat(substr(`dpresu04`.`codpre`,9,1),'.',substr(`dpresu04`.`codpre`,11,2),'.',substr(`dpresu04`.`codpre`,14,2),'.',substr(`dpresu04`.`codpre`,17,2),'.',substr(`dpresu04`.`codpre`,20,2),convert(if((length(substr(trim(`dpresu04`.`codpre`),22,3)) > 0),'.','') using utf8),if((length(substr(`dpresu04`.`codpre`,22,3)) > 0),substr(`dpresu04`.`codpre`,22,3),'')) AS `codigopres`,(1 * `dpresu04`.`moncomp`) AS `monto`,`dpresu04`.`fecmov` AS `fecha`,`dpresu04`.`concep` AS `concepto` from `dpresu04`;


-- Dumping structure for view tortuga.view_sumi_saldo
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `view_sumi_saldo`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `view_sumi_saldo` AS select `t`.`caub` AS `caub`,`t`.`codigo` AS `codigo`,sum(`t`.`cantidad`) AS `cantidad`,`c`.`descrip` AS `descrip`,`d`.`descrip` AS `descripalma`,`c`.`unidad` AS `unidad` from ((`view_sumi_saldo2` `t` join `sumi` `c` on((`t`.`codigo` = `c`.`codigo`))) join `su_caub` `d` on((`t`.`caub` = `d`.`codigo`))) group by `t`.`caub`,`t`.`codigo`;


-- Dumping structure for view tortuga.view_sumi_saldo2
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `view_sumi_saldo2`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `view_sumi_saldo2` AS select `b`.`caub` AS `caub`,`a`.`codigo` AS `codigo`,`a`.`cantidad` AS `cantidad` from (`itsuminr` `a` join `suminr` `b` on((`a`.`numero` = `b`.`numero`))) where (`b`.`status` = 'C') union all select `b`.`caub` AS `caub`,`a`.`codigo` AS `codigo`,(-(1) * `a`.`cantidad`) AS `-1*a.cantidad` from (`itsumine` `a` join `sumine` `b` on((`a`.`numero` = `b`.`numero`))) where (`b`.`status` = 'C');


-- Dumping structure for view tortuga.view_su_itsumi
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `view_su_itsumi`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `view_su_itsumi` AS select `a`.`unidad` AS `unidad`,`a`.`codigo` AS `codigo`,`a`.`descrip` AS `descrip`,`b`.`alma` AS `alma`,`c`.`descrip` AS `almacen`,`b`.`cantidad` AS `cantidad` from ((`sumi` `a` left join `su_itsumi` `b` on((`a`.`codigo` = `b`.`codigo`))) left join `su_caub` `c` on((`b`.`alma` = `c`.`codigo`)));


-- Dumping structure for view tortuga.v_bienes
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `v_bienes`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `v_bienes` AS select `bi_muebles`.`id` AS `id`,`bi_muebles`.`codigo` AS `codigo`,`bi_muebles`.`grupo` AS `grupo`,`bi_muebles`.`subgrupo` AS `subgrupo`,`bi_muebles`.`seccion` AS `seccion`,`bi_muebles`.`numero` AS `numero`,`bi_muebles`.`descrip` AS `descrip`,`bi_muebles`.`alma` AS `alma`,`bi_muebles`.`monto` AS `monto` from `bi_muebles` union all select `bi_terreno`.`id` AS `id`,`bi_terreno`.`codigo` AS `codigo`,`bi_terreno`.`grupo` AS `grupo`,`bi_terreno`.`subgrupo` AS `subgrupo`,`bi_terreno`.`seccion` AS `seccion`,`bi_terreno`.`numero` AS `numero`,`bi_terreno`.`denominacion` AS `descrip`,`bi_terreno`.`alma` AS `alma`,`bi_terreno`.`monto` AS `monto` from `bi_terreno` union all select `bi_edificio`.`id` AS `id`,`bi_edificio`.`codigo` AS `codigo`,`bi_edificio`.`grupo` AS `grupo`,`bi_edificio`.`subgrupo` AS `subgrupo`,`bi_edificio`.`seccion` AS `seccion`,`bi_edificio`.`numero` AS `numero`,`bi_edificio`.`denominacion` AS `descrip`,`bi_edificio`.`alma` AS `alma`,`bi_edificio`.`monto` AS `monto` from `bi_edificio` union all select `bi_vehi`.`id` AS `id`,`bi_vehi`.`codigo` AS `codigo`,`bi_vehi`.`grupo` AS `grupo`,`bi_vehi`.`subgrupo` AS `subgrupo`,`bi_vehi`.`seccion` AS `seccion`,`bi_vehi`.`numero` AS `numero`,concat_ws(' ',`bi_vehi`.`placa`,`bi_vehi`.`marca`,`bi_vehi`.`modelo`,`bi_vehi`.`anio`,`bi_vehi`.`color`) AS `descrip`,`bi_vehi`.`alma` AS `alma`,`bi_vehi`.`monto` AS `monto` from `bi_vehi` union all select `bi_moto`.`id` AS `id`,`bi_moto`.`codigo` AS `codigo`,`bi_moto`.`grupo` AS `grupo`,`bi_moto`.`subgrupo` AS `subgrupo`,`bi_moto`.`seccion` AS `seccion`,`bi_moto`.`numero` AS `numero`,concat_ws(' ',`bi_moto`.`placa`,`bi_moto`.`marca`,`bi_moto`.`modelo`,`bi_moto`.`anio`,`bi_moto`.`color`) AS `descrip`,`bi_moto`.`alma` AS `alma`,`bi_moto`.`monto` AS `monto` from `bi_moto`;


-- Dumping structure for view tortuga.v_casi
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `v_casi`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `v_casi` AS select `b`.`fecha` AS `fecha`,`b`.`status` AS `status`,`a`.`cuenta` AS `cuenta`,`c`.`denominacion` AS `denominacion`,sum((`a`.`debe` - `a`.`haber`)) AS `monto`,sum(`a`.`debe`) AS `debe`,sum(`a`.`haber`) AS `haber` from ((`casi` `b` join `itcasi` `a` on((`a`.`comprob` = `b`.`comprob`))) left join `cpla` `c` on((`a`.`cuenta` = `c`.`codigo`))) group by `a`.`cuenta`;


-- Dumping structure for view tortuga.v_casic2
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `v_casic2`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `v_casic2` AS select `b`.`fecha` AS `fecha`,`b`.`status` AS `status`,`a`.`cuenta` AS `cuenta`,`c`.`denominacion` AS `denominacion`,sum((`a`.`debe` - `a`.`haber`)) AS `monto`,sum(`a`.`debe`) AS `debe`,sum(`a`.`haber`) AS `haber` from ((`casi` `b` join `itcasi` `a` on((`a`.`comprob` = `b`.`comprob`))) left join `cpla` `c` on((`a`.`cuenta` = `c`.`codigo`))) where (`b`.`status` = 'C2') group by `a`.`cuenta`;


-- Dumping structure for view tortuga.v_casid
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `v_casid`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `v_casid` AS select `b`.`comprob` AS `comprob`,`b`.`tipo` AS `tipo`,`b`.`fecha` AS `fecha`,`b`.`status` AS `status`,`a`.`cuenta` AS `cuenta`,`c`.`denominacion` AS `denominacion`,(`a`.`debe` - `a`.`haber`) AS `monto`,`a`.`debe` AS `debe`,`a`.`haber` AS `haber` from ((`casi` `b` join `itcasi` `a` on((`a`.`comprob` = `b`.`comprob`))) left join `cpla` `c` on((`a`.`cuenta` = `c`.`codigo`)));


-- Dumping structure for view tortuga.v_casidc2
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `v_casidc2`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `v_casidc2` AS select `b`.`tipo` AS `tipo`,`b`.`fecha` AS `fecha`,`b`.`status` AS `status`,`a`.`cuenta` AS `cuenta`,`c`.`denominacion` AS `denominacion`,(`a`.`debe` - `a`.`haber`) AS `monto`,`a`.`debe` AS `debe`,`a`.`haber` AS `haber` from ((`casi` `b` join `itcasi` `a` on((`a`.`comprob` = `b`.`comprob`))) left join `cpla` `c` on((`a`.`cuenta` = `c`.`codigo`))) where (`b`.`status` = 'C2');


-- Dumping structure for view tortuga.v_comproxcausar
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `v_comproxcausar`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `v_comproxcausar` AS select `v_comproxcausar_s1`.`ocompra` AS `ocompra`,`v_comproxcausar_s1`.`compromiso` AS `compromiso`,`v_comproxcausar_s1`.`opago` AS `opago`,`v_comproxcausar_s1`.`esiva` AS `esiva`,`v_comproxcausar_s1`.`codigoadm` AS `codigoadm`,`v_comproxcausar_s1`.`codigopres` AS `codigopres`,`v_comproxcausar_s1`.`fondo` AS `fondo`,sum(`v_comproxcausar_s1`.`compras`) AS `compras`,sum(`v_comproxcausar_s1`.`pagos`) AS `pagos`,(sum(`v_comproxcausar_s1`.`compras`) - sum(`v_comproxcausar_s1`.`pagos`)) AS `xcausar` from `v_comproxcausar_s1` group by `v_comproxcausar_s1`.`ocompra`,`v_comproxcausar_s1`.`codigoadm`,`v_comproxcausar_s1`.`codigopres`,`v_comproxcausar_s1`.`fondo`,(`v_comproxcausar_s1`.`esiva` = 'N') having (`compras` > `pagos`);


-- Dumping structure for view tortuga.v_comproxcausar_encab
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `v_comproxcausar_encab`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `v_comproxcausar_encab` AS select `v_comproxcausar_s1`.`ocompra` AS `ocompra`,`v_comproxcausar_s1`.`compromiso` AS `compromiso`,`v_comproxcausar_s1`.`opago` AS `opago`,`v_comproxcausar_s1`.`codigoadm` AS `codigoadm`,`v_comproxcausar_s1`.`codigopres` AS `codigopres`,`v_comproxcausar_s1`.`fondo` AS `fondo`,sum(`v_comproxcausar_s1`.`compras`) AS `compras`,sum(`v_comproxcausar_s1`.`pagos`) AS `pagos`,(sum(`v_comproxcausar_s1`.`compras`) - sum(`v_comproxcausar_s1`.`pagos`)) AS `xcausar` from `v_comproxcausar_s1` group by `v_comproxcausar_s1`.`ocompra` having (`compras` > `pagos`);


-- Dumping structure for view tortuga.v_comproxcausar_s1
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `v_comproxcausar_s1`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `v_comproxcausar_s1` AS select `a`.`numero` AS `ocompra`,`a`.`compromiso` AS `compromiso`,NULL AS `opago`,`b`.`esiva` AS `esiva`,`b`.`codigoadm` AS `codigoadm`,`b`.`partida` AS `codigopres`,`b`.`fondo` AS `fondo`,sum(`b`.`importe`) AS `compras`,0 AS `pagos` from (`ocompra` `a` join `itocompra` `b` on((`a`.`numero` = `b`.`numero`))) where (`a`.`status` = 'C') group by `b`.`numero`,`b`.`codigoadm`,`b`.`partida`,`b`.`fondo`,(`b`.`esiva` = 'N') union all select `a`.`ocompra` AS `ocompra`,'' AS `compromiso`,`a`.`numero` AS `opago`,`a`.`esiva` AS `esiva`,`a`.`codigoadm` AS `codigoadm`,`a`.`partida` AS `partida`,`a`.`fondo` AS `fondo`,0 AS `compras`,sum(`a`.`importe`) AS `pagos` from (`itodirect` `a` join `odirect` `b` on((`a`.`numero` = `b`.`numero`))) where (`b`.`status` in ('C2','C3')) group by `a`.`ocompra`,`a`.`codigoadm`,`a`.`partida`,`a`.`fondo`,(`a`.`esiva` = 'N');


-- Dumping structure for view tortuga.v_estruadm
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `v_estruadm`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `v_estruadm` AS select concat(substr(`estruadm`.`codigo`,1,2),convert(convert(if((length(`estruadm`.`codigo`) > 2),'.','') using latin1) using utf8),substr(`estruadm`.`codigo`,4,2),convert(convert(if((length(`estruadm`.`codigo`) > 8),'.','') using latin1) using utf8),substr(`estruadm`.`codigo`,10,2)) AS `codigo`,`estruadm`.`denominacion` AS `denominacion` from `estruadm` group by concat(substr(`estruadm`.`codigo`,1,2),convert(convert(if((length(`estruadm`.`codigo`) > 2),'.','') using latin1) using utf8),substr(`estruadm`.`codigo`,4,2),convert(convert(if((length(`estruadm`.`codigo`) > 8),'.','') using latin1) using utf8),substr(`estruadm`.`codigo`,10,2));


-- Dumping structure for view tortuga.v_ingresos
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `v_ingresos`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `v_ingresos` AS select `ingpresup`.`codigopres` AS `codigo`,`ingpresup`.`denominacion` AS `denominacion`,`ingpresup`.`tipo` AS `tipo` from `ingpresup` order by `ingpresup`.`codigopres`;


-- Dumping structure for view tortuga.v_localizador
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `v_localizador`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `v_localizador` AS select `a`.`id` AS `desembolso`,`a`.`status` AS `mstatus`,`c`.`numero` AS `ordpago`,'' AS `itfac`,`c`.`status` AS `pstatus`,`e`.`numero` AS `ordcompra`,`e`.`status` AS `ostatus`,'' AS `multiple`,`e`.`cod_prov` AS `cod_prov`,`e`.`factura` AS `factura`,`e`.`controlfac` AS `controlfac`,`e`.`fechafac` AS `fechafac`,`e`.`total2` AS `total2`,`e`.`reteiva` AS `reteiva`,`e`.`reten` AS `reten`,`e`.`impmunicipal` AS `impmunicipal`,`e`.`imptimbre` AS `imptimbre`,`e`.`total` AS `total`,`e`.`breten` AS `breten`,`e`.`subtotal` AS `subtotal`,`e`.`creten` AS `creten`,`a`.`fecha` AS `fecha`,`a`.`ffirma` AS `ffirma`,`a`.`fentrega` AS `fentrega` from (((((`ocompra` `e` left join `pacom` `d` on((`e`.`numero` = `d`.`compra`))) left join `odirect` `c` on((`d`.`pago` = `c`.`numero`))) left join `pades` `b` on((`c`.`numero` = `b`.`pago`))) left join `desem` `g` on((`b`.`desem` = `g`.`numero`))) left join `mbanc` `a` on((`a`.`desem` = `g`.`numero`))) union all select `a`.`id` AS `desembolso`,`a`.`status` AS `mstatus`,`c`.`numero` AS `ordpago`,'' AS `itfac`,`c`.`status` AS `pstatus`,'' AS `ordcompra`,'' AS `d`,'' AS `multiple`,`c`.`cod_prov` AS `cod_prov`,`c`.`factura` AS `factura`,`c`.`controlfac` AS `controlfac`,`c`.`fechafac` AS `fechafac`,`c`.`total2` AS `total2`,`c`.`reteiva` AS `reteiva`,`c`.`reten` AS `reten`,`c`.`impmunicipal` AS `impmunicipal`,`c`.`imptimbre` AS `imptimbre`,`c`.`total` AS `total`,`c`.`breten` AS `breten`,`c`.`subtotal` AS `subtotal`,`c`.`creten` AS `creten`,`a`.`fecha` AS `fecha`,`a`.`ffirma` AS `ffirma`,`a`.`fentrega` AS `fentrega` from (((`odirect` `c` left join `pades` `b` on((`c`.`numero` = `b`.`pago`))) left join `desem` `g` on((`b`.`desem` = `g`.`numero`))) left join `mbanc` `a` on((`a`.`desem` = `g`.`numero`))) where (`c`.`multiple` = 'N') union all select `a`.`id` AS `desembolso`,`a`.`status` AS `mstatus`,`c`.`numero` AS `ordpago`,`h`.`id` AS `id`,`c`.`status` AS `pstatus`,'' AS `ordcompra`,'' AS `d`,`h`.`id` AS `multiple`,`c`.`cod_prov` AS `cod_prov`,`h`.`factura` AS `factura`,`h`.`controlfac` AS `controlfac`,`h`.`fechafac` AS `fechafac`,`h`.`total2` AS `total2`,`h`.`reteiva` AS `reteiva`,`h`.`reten` AS `reten`,`h`.`impmunicipal` AS `impmunicipal`,`h`.`imptimbre` AS `imptimbre`,`h`.`total` AS `total`,`h`.`breten` AS `breten`,`h`.`subtotal` AS `subtotal`,`h`.`creten` AS `creten`,`a`.`fecha` AS `fecha`,`a`.`ffirma` AS `ffirma`,`a`.`fentrega` AS `fentrega` from ((((`itfac` `h` left join `odirect` `c` on((`h`.`numero` = `c`.`numero`))) left join `pades` `b` on((`c`.`numero` = `b`.`pago`))) left join `desem` `g` on((`b`.`desem` = `g`.`numero`))) left join `mbanc` `a` on((`a`.`desem` = `g`.`numero`))) where (`c`.`multiple` = 'S');


-- Dumping structure for view tortuga.v_mbanc
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `v_mbanc`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `v_mbanc` AS select `mbanc`.`id` AS `id`,`mbanc`.`codbanc` AS `codbanc`,`mbanc`.`cod_prov` AS `cod_prov`,`mbanc`.`tipo_doc` AS `tipo_doc`,`mbanc`.`cheque` AS `cheque`,`mbanc`.`abonado` AS `abonado`,`mbanc`.`tipo` AS `tipo`,`mbanc`.`numero` AS `numero`,date_format(`mbanc`.`fecha`,'%d/%m/%Y') AS `fecha`,`mbanc`.`rel` AS `rel`,`mbanc`.`fechapago` AS `fechapago`,`mbanc`.`monto` AS `monto`,`mbanc`.`observa` AS `observa`,`mbanc`.`status` AS `status`,`mbanc`.`benefi` AS `benefi`,`mbanc`.`usuario` AS `usuario`,`mbanc`.`estampa` AS `estampa`,`mbanc`.`uejecutora` AS `uejecutora`,`mbanc`.`devo` AS `devo`,`mbanc`.`periodo` AS `periodo`,`mbanc`.`islrid` AS `islrid`,`mbanc`.`anulado` AS `anulado`,`mbanc`.`anuladopor` AS `anuladopor`,`mbanc`.`concilia` AS `concilia`,`mbanc`.`fconcilia` AS `fconcilia`,`mbanc`.`bcta` AS `bcta`,`mbanc`.`ffirma` AS `ffirma`,`mbanc`.`fentrega` AS `fentrega`,`mbanc`.`fdevo` AS `fdevo`,`mbanc`.`fcajrecibe` AS `fcajrecibe`,`mbanc`.`fcajdevo` AS `fcajdevo`,`mbanc`.`desem` AS `desem`,`mbanc`.`sta` AS `sta`,`mbanc`.`destino` AS `destino`,`mbanc`.`relch` AS `relch`,`mbanc`.`liable` AS `liable`,`mbanc`.`fliable` AS `fliable`,`mbanc`.`coding` AS `coding`,`banc`.`numcuent` AS `numcuent`,`banc`.`saldo` AS `saldo`,`banc`.`banco` AS `banco` from (`mbanc` join `banc` on((`mbanc`.`codbanc` = `banc`.`codbanc`)));


-- Dumping structure for view tortuga.v_mbancm
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `v_mbancm`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `v_mbancm` AS select `a`.`multiple` AS `multiple`,`a`.`codbanc` AS `codbanc`,`a`.`tipo_doc` AS `tipo_doc`,group_concat(`a`.`cheque` separator ',') AS `cheque`,date_format(`a`.`fecha`,'%d/%m/%Y') AS `fecha`,sum(`a`.`monto`) AS `monto`,`a`.`observa` AS `observa`,`b`.`numcuent` AS `numcuent`,`b`.`banco` AS `banco`,`a`.`benefi` AS `benefi` from (`mbanc` `a` join `banc` `b` on((`a`.`codbanc` = `b`.`codbanc`))) where ((`a`.`multiple` > 0) and (`a`.`status` in ('J2','A2'))) group by `a`.`multiple`,`a`.`tipo_doc`,`a`.`codbanc`,`a`.`fecha` order by `a`.`multiple` desc;


-- Dumping structure for view tortuga.v_numcuent
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `v_numcuent`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `v_numcuent` AS select `a`.`codbanc` AS `codigo`,`a`.`banco` AS `descrip`,`b`.`nomb_banc` AS `descrip2`,`a`.`numcuent` AS `numcuent` from (`banc` `a` join `tban` `b` on((`a`.`tbanco` = `b`.`cod_banc`))) union all select `sprv`.`proveed` AS `proveed`,`sprv`.`nombre` AS `nombre`,`sprv`.`contacto` AS `contacto`,`sprv`.`numcuent` AS `numcuent` from `sprv`;


-- Dumping structure for view tortuga.v_pagonom
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `v_pagonom`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_pagonom` AS select `b`.`numero` AS `numero`,`a`.`numero` AS `opago`,`a`.`fecha` AS `fecha`,`a`.`total2` AS `asig`,`a`.`retenomina` AS `rete`,`a`.`status` AS `status`,`a`.`observa` AS `descrip` from (`odirect` `a` left join `nomi` `b` on((`a`.`nomina` = `b`.`numero`))) where (`a`.`status` like 'K%') union all select `nomi`.`numero` AS `numero`,`odirect`.`numero` AS `opago`,`nomi`.`fecha` AS `fecha`,`nomi`.`asig` AS `asig`,`nomi`.`rete` AS `rete`,`nomi`.`status` AS `status`,`nomi`.`descrip` AS `descrip` from (`nomi` left join `odirect` on((`nomi`.`opago` = `odirect`.`numero`))) where isnull(`odirect`.`numero`) order by `fecha` desc,`opago` desc,`numero` desc;


-- Dumping structure for view tortuga.v_pagos
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `v_pagos`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `v_pagos` AS select `b`.`fapagado` AS `fapagado`,`b`.`fanulado` AS `fanulado`,`b`.`tipoc` AS `tipoc`,`b`.`fpagado` AS `fpagado`,`b`.`total` AS `total`,`b`.`total2` AS `total2`,`e`.`compromiso` AS `compromiso`,`b`.`cod_prov` AS `cod_prov`,(1 * `a`.`numero`) AS `numeron`,`a`.`numero` AS `numero`,`b`.`fecha` AS `fecha`,`a`.`codigoadm` AS `codigoadm`,`a`.`fondo` AS `fondo`,`a`.`partida` AS `codigopres`,`a`.`importe` AS `importe`,`b`.`status` AS `status`,`b`.`observa` AS `observa`,`f`.`denominacion` AS `denominacion`,`s`.`nombre` AS `nombre`,concat_ws('.',`a`.`codigoadm`,`a`.`partida`) AS `codigo` from ((((`itodirect` `a` join `v_presaldo` `f` on(((`a`.`codigoadm` = `f`.`codigoadm`) and (`a`.`fondo` = `f`.`fondo`) and (`a`.`partida` = `f`.`codigo`)))) join `odirect` `b` on((`a`.`numero` = `b`.`numero`))) join `sprv` `s` on((`s`.`proveed` = if((length(`b`.`cod_prov2`) > 0),`b`.`cod_prov2`,`b`.`cod_prov`)))) join `nomi` `e` on((`e`.`opago` = `b`.`numero`))) where (substr(`b`.`status`,1,1) = 'K') union all select `d`.`fapagado` AS `fapagado`,`d`.`fanulado` AS `fanulado`,`d`.`tipoc` AS `tipoc`,`d`.`fpagado` AS `fpagado`,`b`.`total` AS `total`,`d`.`total2` AS `total2`,(select group_concat(`ocompra`.`compromiso` separator ',') AS `GROUP_CONCAT(ocompra.compromiso)` from (`ocompra` join `pacom` on((`ocompra`.`numero` = `pacom`.`compra`))) where (`pacom`.`pago` = `d`.`numero`)) AS `compromiso`,`b`.`cod_prov` AS `cod_prov`,(1 * `d`.`numero`) AS `numeron`,`d`.`numero` AS `numero`,`d`.`fecha` AS `fecha`,`a`.`codigoadm` AS `codigoadm`,`a`.`fondo` AS `fondo`,`a`.`partida` AS `codigopres`,`a`.`importe` AS `importe`,`d`.`status` AS `status`,`d`.`observa` AS `observa`,`f`.`denominacion` AS `denominacion`,`s`.`nombre` AS `nombre`,concat_ws('.',`a`.`codigoadm`,`a`.`partida`) AS `codigo` from (((((`itocompra` `a` join `v_presaldo` `f` on(((`a`.`codigoadm` = `f`.`codigoadm`) and (`a`.`fondo` = `f`.`fondo`) and (`a`.`partida` = `f`.`codigo`)))) join `ocompra` `b` on((`a`.`numero` = `b`.`numero`))) join `pacom` `c` on((`b`.`numero` = `c`.`compra`))) join `odirect` `d` on((`c`.`pago` = `d`.`numero`))) join `sprv` `s` on((`s`.`proveed` = if((length(`d`.`cod_prov2`) > 0),`d`.`cod_prov2`,`d`.`cod_prov`)))) where (substr(`d`.`status`,1,1) = 'F') union all select `b`.`fapagado` AS `fapagado`,`b`.`fanulado` AS `fanulado`,`b`.`tipoc` AS `tipoc`,`b`.`fpagado` AS `fpagado`,`b`.`total` AS `total`,`b`.`total2` AS `total2`,'' AS `compromiso`,`b`.`cod_prov` AS `cod_prov`,(1 * `a`.`numero`) AS `numeron`,`a`.`numero` AS `numero`,`b`.`fecha` AS `fecha`,`a`.`codigoadm` AS `codigoadm`,`a`.`fondo` AS `fondo`,`a`.`partida` AS `codigopres`,`a`.`importe` AS `importe`,`b`.`status` AS `status`,`b`.`observa` AS `observa`,`f`.`denominacion` AS `denominacion`,`s`.`nombre` AS `nombre`,concat_ws('.',`a`.`codigoadm`,`a`.`partida`) AS `codigo` from (((`itodirect` `a` join `v_presaldo` `f` on(((`a`.`codigoadm` = `f`.`codigoadm`) and (`a`.`fondo` = `f`.`fondo`) and (`a`.`partida` = `f`.`codigo`)))) join `odirect` `b` on((`a`.`numero` = `b`.`numero`))) join `sprv` `s` on((`s`.`proveed` = if((length(`b`.`cod_prov2`) > 0),`b`.`cod_prov2`,`b`.`cod_prov`)))) where (substr(`b`.`status`,1,1) = 'B') union all select `b`.`fapagado` AS `fapagado`,`b`.`fanulado` AS `fanulado`,`b`.`tipoc` AS `tipoc`,`b`.`fpagado` AS `fpagado`,`b`.`total` AS `total`,`b`.`total2` AS `total2`,'' AS `compromiso`,`b`.`cod_prov` AS `cod_prov`,(1 * `b`.`numero`) AS `numeron`,`b`.`numero` AS `numero`,`b`.`fecha` AS `fecha`,`a`.`codigoadm` AS `codigoadm`,`a`.`fondo` AS `fondo`,`a`.`codigopres` AS `codigopres`,`b`.`total2` AS `importe`,`b`.`status` AS `status`,`b`.`observa` AS `observa`,`f`.`denominacion` AS `denominacion`,`s`.`nombre` AS `nombre`,concat_ws('.',`a`.`codigoadm`,`a`.`codigopres`) AS `codigo` from (((`obra` `a` join `v_presaldo` `f` on(((`a`.`codigoadm` = `f`.`codigoadm`) and (`a`.`fondo` = `f`.`fondo`) and (`a`.`codigopres` = `f`.`codigo`)))) join `odirect` `b` on((`a`.`numero` = `b`.`obr`))) join `sprv` `s` on((`s`.`proveed` = if((length(`b`.`cod_prov2`) > 0),`b`.`cod_prov2`,`b`.`cod_prov`)))) where (substr(`b`.`status`,1,1) = 'O') union all select `b`.`fapagado` AS `fapagado`,`b`.`fanulado` AS `fanulado`,`b`.`tipoc` AS `tipoc`,`b`.`fpagado` AS `fpagado`,`b`.`total` AS `total`,`b`.`total2` AS `total2`,'' AS `compromiso`,`b`.`cod_prov` AS `cod_prov`,(1 * `b`.`numero`) AS `numeron`,`b`.`numero` AS `numero`,`b`.`fecha` AS `fecha`,'' AS `codigoadm`,'' AS `fondo`,'' AS `codigopres`,0 AS `importe`,`b`.`status` AS `status`,`b`.`observa` AS `observa`,'' AS `denominacion`,`s`.`nombre` AS `nombre`,'' AS `codigo` from (`odirect` `b` join `sprv` `s` on((`s`.`proveed` = if((length(`b`.`cod_prov2`) > 0),`b`.`cod_prov2`,`b`.`cod_prov`)))) where (substr(`b`.`status`,1,1) = 'G') union all select `b`.`fapagado` AS `fapagado`,`b`.`fanulado` AS `fanulado`,`b`.`tipoc` AS `tipoc`,`b`.`fpagado` AS `fpagado`,`b`.`total` AS `total`,`b`.`total2` AS `total2`,'' AS `compromiso`,`b`.`cod_prov` AS `cod_prov`,(1 * `b`.`numero`) AS `numeron`,`b`.`numero` AS `numero`,`b`.`fecha` AS `fecha`,'' AS `codigoadm`,'' AS `fondo`,'' AS `codigopres`,0 AS `importe`,`b`.`status` AS `status`,`b`.`observa` AS `observa`,'' AS `denominacion`,`s`.`nombre` AS `nombre`,'' AS `codigo` from (`odirect` `b` join `sprv` `s` on((`s`.`proveed` = if((length(`b`.`cod_prov2`) > 0),`b`.`cod_prov2`,`b`.`cod_prov`)))) where (substr(`b`.`status`,1,1) = 'M') union all select `b`.`fapagado` AS `fapagado`,`b`.`fanulado` AS `fanulado`,`b`.`tipoc` AS `tipoc`,`b`.`fpagado` AS `fpagado`,`b`.`total` AS `total`,`b`.`total2` AS `total2`,`e`.`compromiso` AS `compromiso`,`b`.`cod_prov` AS `cod_prov`,(1 * `a`.`numero`) AS `numeron`,`a`.`numero` AS `numero`,`b`.`fecha` AS `fecha`,`a`.`codigoadm` AS `codigoadm`,`a`.`fondo` AS `fondo`,`a`.`partida` AS `codigopres`,`a`.`importe` AS `importe`,`b`.`status` AS `status`,`b`.`observa` AS `observa`,`f`.`denominacion` AS `denominacion`,`s`.`nombre` AS `nombre`,concat_ws('.',`a`.`codigoadm`,`a`.`partida`) AS `codigo` from ((((`itodirect` `a` join `ocompra` `e` on((`a`.`ocompra` = `e`.`numero`))) join `v_presaldo` `f` on(((`a`.`codigoadm` = `f`.`codigoadm`) and (`a`.`fondo` = `f`.`fondo`) and (`a`.`partida` = `f`.`codigo`)))) join `odirect` `b` on((`a`.`numero` = `b`.`numero`))) join `sprv` `s` on((`s`.`proveed` = if((length(`b`.`cod_prov2`) > 0),`b`.`cod_prov2`,`b`.`cod_prov`)))) where (substr(`b`.`status`,1,1) = 'C');


-- Dumping structure for view tortuga.v_pagossolo
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `v_pagossolo`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `v_pagossolo` AS select `b`.`fapagado` AS `fapagado`,`b`.`fanulado` AS `fanulado`,`b`.`cod_prov2` AS `cod_prov2`,`b`.`tipoc` AS `tipoc`,`b`.`fpagado` AS `fpagado`,`b`.`total` AS `total`,`b`.`total2` AS `total2`,`e`.`compromiso` AS `compromiso`,`b`.`cod_prov` AS `cod_prov`,(1 * `a`.`numero`) AS `numeron`,`a`.`numero` AS `numero`,`b`.`fecha` AS `fecha`,`a`.`codigoadm` AS `codigoadm`,`a`.`fondo` AS `fondo`,`a`.`partida` AS `codigopres`,`a`.`importe` AS `importe`,`b`.`status` AS `status`,`b`.`observa` AS `observa`,concat_ws('.',`a`.`codigoadm`,`a`.`partida`) AS `codigo` from ((`itodirect` `a` join `odirect` `b` on((`a`.`numero` = `b`.`numero`))) join `nomi` `e` on((`e`.`opago` = `b`.`numero`))) where (substr(`b`.`status`,1,1) = 'K') union all select `d`.`fapagado` AS `fapagado`,`d`.`fanulado` AS `fanulado`,`d`.`cod_prov2` AS `cod_prov2`,`d`.`tipoc` AS `tipoc`,`d`.`fpagado` AS `fpagado`,`b`.`total` AS `total`,`d`.`total2` AS `total2`,(select group_concat(`ocompra`.`compromiso` separator ',') AS `GROUP_CONCAT(ocompra.compromiso)` from (`ocompra` join `pacom` on((`ocompra`.`numero` = `pacom`.`compra`))) where (`pacom`.`pago` = `d`.`numero`)) AS `compromiso`,`b`.`cod_prov` AS `cod_prov`,(1 * `d`.`numero`) AS `numeron`,`d`.`numero` AS `numero`,`d`.`fecha` AS `fecha`,`a`.`codigoadm` AS `codigoadm`,`a`.`fondo` AS `fondo`,`a`.`partida` AS `codigopres`,`a`.`importe` AS `importe`,`d`.`status` AS `status`,`b`.`observa` AS `observa`,concat_ws('.',`a`.`codigoadm`,`a`.`partida`) AS `codigo` from (((`itocompra` `a` join `ocompra` `b` on((`a`.`numero` = `b`.`numero`))) join `pacom` `c` on((`b`.`numero` = `c`.`compra`))) join `odirect` `d` on((`c`.`pago` = `d`.`numero`))) where (substr(`d`.`status`,1,1) = 'F') union all select `b`.`fapagado` AS `fapagado`,`b`.`fanulado` AS `fanulado`,`b`.`cod_prov2` AS `cod_prov2`,`b`.`tipoc` AS `tipoc`,`b`.`fpagado` AS `fpagado`,`b`.`total` AS `total`,`b`.`total2` AS `total2`,'' AS `compromiso`,`b`.`cod_prov` AS `cod_prov`,(1 * `a`.`numero`) AS `numeron`,`a`.`numero` AS `numero`,`b`.`fecha` AS `fecha`,`a`.`codigoadm` AS `codigoadm`,`a`.`fondo` AS `fondo`,`a`.`partida` AS `codigopres`,`a`.`importe` AS `importe`,`b`.`status` AS `status`,`b`.`observa` AS `observa`,concat_ws('.',`a`.`codigoadm`,`a`.`partida`) AS `codigo` from (`itodirect` `a` join `odirect` `b` on((`a`.`numero` = `b`.`numero`))) where (substr(`b`.`status`,1,1) = 'B') union all select `b`.`fapagado` AS `fapagado`,`b`.`fanulado` AS `fanulado`,`b`.`cod_prov2` AS `cod_prov2`,`b`.`tipoc` AS `tipoc`,`b`.`fpagado` AS `fpagado`,`b`.`total` AS `total`,`b`.`total2` AS `total2`,'' AS `compromiso`,`b`.`cod_prov` AS `cod_prov`,(1 * `b`.`numero`) AS `numeron`,`b`.`numero` AS `numero`,`b`.`fecha` AS `fecha`,`a`.`codigoadm` AS `codigoadm`,`a`.`fondo` AS `fondo`,`a`.`codigopres` AS `codigopres`,`b`.`total2` AS `importe`,`b`.`status` AS `status`,`b`.`observa` AS `observa`,concat_ws('.',`a`.`codigoadm`,`a`.`codigopres`) AS `codigo` from (`obra` `a` join `odirect` `b` on((`a`.`numero` = `b`.`obr`))) where (substr(`b`.`status`,1,1) = 'O') union all select `b`.`fapagado` AS `fapagado`,`b`.`fanulado` AS `fanulado`,`b`.`cod_prov2` AS `cod_prov2`,`b`.`tipoc` AS `tipoc`,`b`.`fpagado` AS `fpagado`,`b`.`total` AS `total`,`b`.`total2` AS `total2`,'' AS `compromiso`,`b`.`cod_prov` AS `cod_prov`,(1 * `b`.`numero`) AS `numeron`,`b`.`numero` AS `numero`,`b`.`fecha` AS `fecha`,'' AS `codigoadm`,'' AS `fondo`,'' AS `codigopres`,0 AS `importe`,`b`.`status` AS `status`,`b`.`observa` AS `observa`,'' AS `codigo` from `odirect` `b` where (substr(`b`.`status`,1,1) = 'G') union all select `b`.`fapagado` AS `fapagado`,`b`.`fanulado` AS `fanulado`,`b`.`cod_prov2` AS `cod_prov2`,`b`.`tipoc` AS `tipoc`,`b`.`fpagado` AS `fpagado`,`b`.`total` AS `total`,`b`.`total2` AS `total2`,'' AS `compromiso`,`b`.`cod_prov` AS `cod_prov`,(1 * `b`.`numero`) AS `numeron`,`b`.`numero` AS `numero`,`b`.`fecha` AS `fecha`,'' AS `codigoadm`,'' AS `fondo`,'' AS `codigopres`,0 AS `importe`,`b`.`status` AS `status`,`b`.`observa` AS `observa`,'' AS `codigo` from `odirect` `b` where (substr(`b`.`status`,1,1) = 'M') union all select `b`.`fapagado` AS `fapagado`,`b`.`fanulado` AS `fanulado`,`b`.`cod_prov2` AS `cod_prov2`,`b`.`tipoc` AS `tipoc`,`b`.`fpagado` AS `fpagado`,`b`.`total` AS `total`,`b`.`total2` AS `total2`,'' AS `compromiso`,`b`.`cod_prov` AS `cod_prov`,(1 * `a`.`numero`) AS `numeron`,`a`.`numero` AS `numero`,`b`.`fecha` AS `fecha`,`a`.`codigoadm` AS `codigoadm`,`a`.`fondo` AS `fondo`,`a`.`partida` AS `codigopres`,`a`.`importe` AS `importe`,`b`.`status` AS `status`,`b`.`observa` AS `observa`,concat_ws('.',`a`.`codigoadm`,`a`.`partida`) AS `codigo` from (`itodirect` `a` join `odirect` `b` on((`a`.`numero` = `b`.`numero`))) where (substr(`b`.`status`,1,1) = 'C');


-- Dumping structure for view tortuga.v_pagos_encab
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `v_pagos_encab`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `v_pagos_encab` AS select `b`.`numero` AS `numero`,`b`.`fecha` AS `fecha`,`b`.`compra` AS `compra`,`b`.`cod_prov` AS `cod_prov`,`b`.`subtotal` AS `subtotal`,`b`.`exento` AS `exento`,`b`.`ivag` AS `ivag`,`b`.`tivag` AS `tivag`,`b`.`mivag` AS `mivag`,`b`.`ivar` AS `ivar`,`b`.`tivar` AS `tivar`,`b`.`mivar` AS `mivar`,`b`.`ivaa` AS `ivaa`,`b`.`tivaa` AS `tivaa`,`b`.`mivaa` AS `mivaa`,`b`.`pago` AS `pago`,`b`.`creten` AS `creten`,`b`.`breten` AS `breten`,`b`.`reteiva` AS `reteiva`,`b`.`reten` AS `reten`,`b`.`total` AS `total`,`b`.`total2` AS `total2`,`b`.`iva` AS `iva`,`b`.`observa` AS `observa`,`b`.`anulado` AS `anulado`,`b`.`status` AS `status`,`b`.`reteiva_prov` AS `reteiva_prov`,`b`.`impmunicipal` AS `impmunicipal`,`b`.`crs` AS `crs`,`b`.`imptimbre` AS `imptimbre`,`b`.`pcrs` AS `pcrs`,`b`.`pimptimbre` AS `pimptimbre`,`b`.`pimpmunicipal` AS `pimpmunicipal`,`b`.`retenomina` AS `retenomina`,`b`.`amortiza` AS `amortiza`,`b`.`porcent` AS `porcent`,`b`.`anticipo` AS `anticipo`,`b`.`fpagado` AS `fpagado`,`b`.`fislr` AS `fislr`,`b`.`fcrs` AS `fcrs`,`b`.`fmunicipal` AS `fmunicipal`,`b`.`ftimbre` AS `ftimbre`,`b`.`mtimbre` AS `mtimbre`,`b`.`mislr` AS `mislr`,`b`.`mmuni` AS `mmuni`,`b`.`mcrs` AS `mcrs`,`b`.`cod_prov2` AS `cod_prov2`,`b`.`tipoc` AS `tipoc`,`b`.`otrasrete` AS `otrasrete`,`b`.`fanulado` AS `fanulado`,`b`.`observacaj` AS `observacaj`,`b`.`fapagado` AS `fapagado`,`a`.`fondo` AS `fondo`,`s`.`nombre` AS `nombre` from (((`itodirect` `a` join `odirect` `b` on((`a`.`numero` = `b`.`numero`))) join `sprv` `s` on((`s`.`proveed` = if((length(`b`.`cod_prov2`) > 0),`b`.`cod_prov2`,`b`.`cod_prov`)))) join `nomi` `e` on((`e`.`opago` = `b`.`numero`))) where (substr(`b`.`status`,1,1) = 'K') group by `b`.`numero` union all select `d`.`numero` AS `numero`,`d`.`fecha` AS `fecha`,`d`.`compra` AS `compra`,`d`.`cod_prov` AS `cod_prov`,`d`.`subtotal` AS `subtotal`,`d`.`exento` AS `exento`,`d`.`ivag` AS `ivag`,`d`.`tivag` AS `tivag`,`d`.`mivag` AS `mivag`,`d`.`ivar` AS `ivar`,`d`.`tivar` AS `tivar`,`d`.`mivar` AS `mivar`,`d`.`ivaa` AS `ivaa`,`d`.`tivaa` AS `tivaa`,`d`.`mivaa` AS `mivaa`,`d`.`pago` AS `pago`,`d`.`creten` AS `creten`,`d`.`breten` AS `breten`,`d`.`reteiva` AS `reteiva`,`d`.`reten` AS `reten`,`d`.`total` AS `total`,`d`.`total2` AS `total2`,`d`.`iva` AS `iva`,`d`.`observa` AS `observa`,`d`.`anulado` AS `anulado`,`d`.`status` AS `status`,`d`.`reteiva_prov` AS `reteiva_prov`,`d`.`impmunicipal` AS `impmunicipal`,`d`.`crs` AS `crs`,`d`.`imptimbre` AS `imptimbre`,`d`.`pcrs` AS `pcrs`,`d`.`pimptimbre` AS `pimptimbre`,`d`.`pimpmunicipal` AS `pimpmunicipal`,`d`.`retenomina` AS `retenomina`,`d`.`amortiza` AS `amortiza`,`d`.`porcent` AS `porcent`,`d`.`anticipo` AS `anticipo`,`d`.`fpagado` AS `fpagado`,`d`.`fislr` AS `fislr`,`d`.`fcrs` AS `fcrs`,`d`.`fmunicipal` AS `fmunicipal`,`d`.`ftimbre` AS `ftimbre`,`d`.`mtimbre` AS `mtimbre`,`d`.`mislr` AS `mislr`,`d`.`mmuni` AS `mmuni`,`d`.`mcrs` AS `mcrs`,`d`.`cod_prov2` AS `cod_prov2`,`d`.`tipoc` AS `tipoc`,`d`.`otrasrete` AS `otrasrete`,`d`.`fanulado` AS `fanulado`,`d`.`observacaj` AS `observacaj`,`d`.`fapagado` AS `fapagado`,`a`.`fondo` AS `fondo`,`s`.`nombre` AS `nombre` from ((((`itocompra` `a` join `ocompra` `b` on((`a`.`numero` = `b`.`numero`))) join `pacom` `c` on((`b`.`numero` = `c`.`compra`))) join `odirect` `d` on((`c`.`pago` = `d`.`numero`))) join `sprv` `s` on((`s`.`proveed` = if((length(`d`.`cod_prov2`) > 0),`d`.`cod_prov2`,`d`.`cod_prov`)))) where (substr(`d`.`status`,1,1) = 'F') group by `d`.`numero` union all select `b`.`numero` AS `numero`,`b`.`fecha` AS `fecha`,`b`.`compra` AS `compra`,`b`.`cod_prov` AS `cod_prov`,`b`.`subtotal` AS `subtotal`,`b`.`exento` AS `exento`,`b`.`ivag` AS `ivag`,`b`.`tivag` AS `tivag`,`b`.`mivag` AS `mivag`,`b`.`ivar` AS `ivar`,`b`.`tivar` AS `tivar`,`b`.`mivar` AS `mivar`,`b`.`ivaa` AS `ivaa`,`b`.`tivaa` AS `tivaa`,`b`.`mivaa` AS `mivaa`,`b`.`pago` AS `pago`,`b`.`creten` AS `creten`,`b`.`breten` AS `breten`,`b`.`reteiva` AS `reteiva`,`b`.`reten` AS `reten`,`b`.`total` AS `total`,`b`.`total2` AS `total2`,`b`.`iva` AS `iva`,`b`.`observa` AS `observa`,`b`.`anulado` AS `anulado`,`b`.`status` AS `status`,`b`.`reteiva_prov` AS `reteiva_prov`,`b`.`impmunicipal` AS `impmunicipal`,`b`.`crs` AS `crs`,`b`.`imptimbre` AS `imptimbre`,`b`.`pcrs` AS `pcrs`,`b`.`pimptimbre` AS `pimptimbre`,`b`.`pimpmunicipal` AS `pimpmunicipal`,`b`.`retenomina` AS `retenomina`,`b`.`amortiza` AS `amortiza`,`b`.`porcent` AS `porcent`,`b`.`anticipo` AS `anticipo`,`b`.`fpagado` AS `fpagado`,`b`.`fislr` AS `fislr`,`b`.`fcrs` AS `fcrs`,`b`.`fmunicipal` AS `fmunicipal`,`b`.`ftimbre` AS `ftimbre`,`b`.`mtimbre` AS `mtimbre`,`b`.`mislr` AS `mislr`,`b`.`mmuni` AS `mmuni`,`b`.`mcrs` AS `mcrs`,`b`.`cod_prov2` AS `cod_prov2`,`b`.`tipoc` AS `tipoc`,`b`.`otrasrete` AS `otrasrete`,`b`.`fanulado` AS `fanulado`,`b`.`observacaj` AS `observacaj`,`b`.`fapagado` AS `fapagado`,`a`.`fondo` AS `fondo`,`s`.`nombre` AS `nombre` from ((`itodirect` `a` join `odirect` `b` on((`a`.`numero` = `b`.`numero`))) join `sprv` `s` on((`s`.`proveed` = if((length(`b`.`cod_prov2`) > 0),`b`.`cod_prov2`,`b`.`cod_prov`)))) where (substr(`b`.`status`,1,1) = 'B') group by `b`.`numero` union all select `b`.`numero` AS `numero`,`b`.`fecha` AS `fecha`,`b`.`compra` AS `compra`,`b`.`cod_prov` AS `cod_prov`,`b`.`subtotal` AS `subtotal`,`b`.`exento` AS `exento`,`b`.`ivag` AS `ivag`,`b`.`tivag` AS `tivag`,`b`.`mivag` AS `mivag`,`b`.`ivar` AS `ivar`,`b`.`tivar` AS `tivar`,`b`.`mivar` AS `mivar`,`b`.`ivaa` AS `ivaa`,`b`.`tivaa` AS `tivaa`,`b`.`mivaa` AS `mivaa`,`b`.`pago` AS `pago`,`b`.`creten` AS `creten`,`b`.`breten` AS `breten`,`b`.`reteiva` AS `reteiva`,`b`.`reten` AS `reten`,`b`.`total` AS `total`,`b`.`total2` AS `total2`,`b`.`iva` AS `iva`,`b`.`observa` AS `observa`,`b`.`anulado` AS `anulado`,`b`.`status` AS `status`,`b`.`reteiva_prov` AS `reteiva_prov`,`b`.`impmunicipal` AS `impmunicipal`,`b`.`crs` AS `crs`,`b`.`imptimbre` AS `imptimbre`,`b`.`pcrs` AS `pcrs`,`b`.`pimptimbre` AS `pimptimbre`,`b`.`pimpmunicipal` AS `pimpmunicipal`,`b`.`retenomina` AS `retenomina`,`b`.`amortiza` AS `amortiza`,`b`.`porcent` AS `porcent`,`b`.`anticipo` AS `anticipo`,`b`.`fpagado` AS `fpagado`,`b`.`fislr` AS `fislr`,`b`.`fcrs` AS `fcrs`,`b`.`fmunicipal` AS `fmunicipal`,`b`.`ftimbre` AS `ftimbre`,`b`.`mtimbre` AS `mtimbre`,`b`.`mislr` AS `mislr`,`b`.`mmuni` AS `mmuni`,`b`.`mcrs` AS `mcrs`,`b`.`cod_prov2` AS `cod_prov2`,`b`.`tipoc` AS `tipoc`,`b`.`otrasrete` AS `otrasrete`,`b`.`fanulado` AS `fanulado`,`b`.`observacaj` AS `observacaj`,`b`.`fapagado` AS `fapagado`,`a`.`fondo` AS `fondo`,`s`.`nombre` AS `nombre` from ((`obra` `a` join `odirect` `b` on((`a`.`numero` = `b`.`obr`))) join `sprv` `s` on((`s`.`proveed` = if((length(`b`.`cod_prov2`) > 0),`b`.`cod_prov2`,`b`.`cod_prov`)))) where (substr(`b`.`status`,1,1) = 'O') group by `b`.`numero` union all select `b`.`numero` AS `numero`,`b`.`fecha` AS `fecha`,`b`.`compra` AS `compra`,`b`.`cod_prov` AS `cod_prov`,`b`.`subtotal` AS `subtotal`,`b`.`exento` AS `exento`,`b`.`ivag` AS `ivag`,`b`.`tivag` AS `tivag`,`b`.`mivag` AS `mivag`,`b`.`ivar` AS `ivar`,`b`.`tivar` AS `tivar`,`b`.`mivar` AS `mivar`,`b`.`ivaa` AS `ivaa`,`b`.`tivaa` AS `tivaa`,`b`.`mivaa` AS `mivaa`,`b`.`pago` AS `pago`,`b`.`creten` AS `creten`,`b`.`breten` AS `breten`,`b`.`reteiva` AS `reteiva`,`b`.`reten` AS `reten`,`b`.`total` AS `total`,`b`.`total2` AS `total2`,`b`.`iva` AS `iva`,`b`.`observa` AS `observa`,`b`.`anulado` AS `anulado`,`b`.`status` AS `status`,`b`.`reteiva_prov` AS `reteiva_prov`,`b`.`impmunicipal` AS `impmunicipal`,`b`.`crs` AS `crs`,`b`.`imptimbre` AS `imptimbre`,`b`.`pcrs` AS `pcrs`,`b`.`pimptimbre` AS `pimptimbre`,`b`.`pimpmunicipal` AS `pimpmunicipal`,`b`.`retenomina` AS `retenomina`,`b`.`amortiza` AS `amortiza`,`b`.`porcent` AS `porcent`,`b`.`anticipo` AS `anticipo`,`b`.`fpagado` AS `fpagado`,`b`.`fislr` AS `fislr`,`b`.`fcrs` AS `fcrs`,`b`.`fmunicipal` AS `fmunicipal`,`b`.`ftimbre` AS `ftimbre`,`b`.`mtimbre` AS `mtimbre`,`b`.`mislr` AS `mislr`,`b`.`mmuni` AS `mmuni`,`b`.`mcrs` AS `mcrs`,`b`.`cod_prov2` AS `cod_prov2`,`b`.`tipoc` AS `tipoc`,`b`.`otrasrete` AS `otrasrete`,`b`.`fanulado` AS `fanulado`,`b`.`observacaj` AS `observacaj`,`b`.`fapagado` AS `fapagado`,`b`.`fondo` AS `fondo`,`s`.`nombre` AS `nombre` from (`odirect` `b` join `sprv` `s` on((`s`.`proveed` = if((length(`b`.`cod_prov2`) > 0),`b`.`cod_prov2`,`b`.`cod_prov`)))) where (substr(`b`.`status`,1,1) = 'G') group by `b`.`numero` union all select `b`.`numero` AS `numero`,`b`.`fecha` AS `fecha`,`b`.`compra` AS `compra`,`b`.`cod_prov` AS `cod_prov`,`b`.`subtotal` AS `subtotal`,`b`.`exento` AS `exento`,`b`.`ivag` AS `ivag`,`b`.`tivag` AS `tivag`,`b`.`mivag` AS `mivag`,`b`.`ivar` AS `ivar`,`b`.`tivar` AS `tivar`,`b`.`mivar` AS `mivar`,`b`.`ivaa` AS `ivaa`,`b`.`tivaa` AS `tivaa`,`b`.`mivaa` AS `mivaa`,`b`.`pago` AS `pago`,`b`.`creten` AS `creten`,`b`.`breten` AS `breten`,`b`.`reteiva` AS `reteiva`,`b`.`reten` AS `reten`,`b`.`total` AS `total`,`b`.`total2` AS `total2`,`b`.`iva` AS `iva`,`b`.`observa` AS `observa`,`b`.`anulado` AS `anulado`,`b`.`status` AS `status`,`b`.`reteiva_prov` AS `reteiva_prov`,`b`.`impmunicipal` AS `impmunicipal`,`b`.`crs` AS `crs`,`b`.`imptimbre` AS `imptimbre`,`b`.`pcrs` AS `pcrs`,`b`.`pimptimbre` AS `pimptimbre`,`b`.`pimpmunicipal` AS `pimpmunicipal`,`b`.`retenomina` AS `retenomina`,`b`.`amortiza` AS `amortiza`,`b`.`porcent` AS `porcent`,`b`.`anticipo` AS `anticipo`,`b`.`fpagado` AS `fpagado`,`b`.`fislr` AS `fislr`,`b`.`fcrs` AS `fcrs`,`b`.`fmunicipal` AS `fmunicipal`,`b`.`ftimbre` AS `ftimbre`,`b`.`mtimbre` AS `mtimbre`,`b`.`mislr` AS `mislr`,`b`.`mmuni` AS `mmuni`,`b`.`mcrs` AS `mcrs`,`b`.`cod_prov2` AS `cod_prov2`,`b`.`tipoc` AS `tipoc`,`b`.`otrasrete` AS `otrasrete`,`b`.`fanulado` AS `fanulado`,`b`.`observacaj` AS `observacaj`,`b`.`fapagado` AS `fapagado`,`b`.`fondo` AS `fondo`,`s`.`nombre` AS `nombre` from (`odirect` `b` join `sprv` `s` on((`s`.`proveed` = if((length(`b`.`cod_prov2`) > 0),`b`.`cod_prov2`,`b`.`cod_prov`)))) where (substr(`b`.`status`,1,1) = 'M') group by `b`.`numero` union all select `b`.`numero` AS `numero`,`b`.`fecha` AS `fecha`,`b`.`compra` AS `compra`,`b`.`cod_prov` AS `cod_prov`,`b`.`subtotal` AS `subtotal`,`b`.`exento` AS `exento`,`b`.`ivag` AS `ivag`,`b`.`tivag` AS `tivag`,`b`.`mivag` AS `mivag`,`b`.`ivar` AS `ivar`,`b`.`tivar` AS `tivar`,`b`.`mivar` AS `mivar`,`b`.`ivaa` AS `ivaa`,`b`.`tivaa` AS `tivaa`,`b`.`mivaa` AS `mivaa`,`b`.`pago` AS `pago`,`b`.`creten` AS `creten`,`b`.`breten` AS `breten`,`b`.`reteiva` AS `reteiva`,`b`.`reten` AS `reten`,`b`.`total` AS `total`,`b`.`total2` AS `total2`,`b`.`iva` AS `iva`,`b`.`observa` AS `observa`,`b`.`anulado` AS `anulado`,`b`.`status` AS `status`,`b`.`reteiva_prov` AS `reteiva_prov`,`b`.`impmunicipal` AS `impmunicipal`,`b`.`crs` AS `crs`,`b`.`imptimbre` AS `imptimbre`,`b`.`pcrs` AS `pcrs`,`b`.`pimptimbre` AS `pimptimbre`,`b`.`pimpmunicipal` AS `pimpmunicipal`,`b`.`retenomina` AS `retenomina`,`b`.`amortiza` AS `amortiza`,`b`.`porcent` AS `porcent`,`b`.`anticipo` AS `anticipo`,`b`.`fpagado` AS `fpagado`,`b`.`fislr` AS `fislr`,`b`.`fcrs` AS `fcrs`,`b`.`fmunicipal` AS `fmunicipal`,`b`.`ftimbre` AS `ftimbre`,`b`.`mtimbre` AS `mtimbre`,`b`.`mislr` AS `mislr`,`b`.`mmuni` AS `mmuni`,`b`.`mcrs` AS `mcrs`,`b`.`cod_prov2` AS `cod_prov2`,`b`.`tipoc` AS `tipoc`,`b`.`otrasrete` AS `otrasrete`,`b`.`fanulado` AS `fanulado`,`b`.`observacaj` AS `observacaj`,`b`.`fapagado` AS `fapagado`,`a`.`fondo` AS `fondo`,`s`.`nombre` AS `nombre` from (((`itodirect` `a` join `ocompra` `e` on((`a`.`ocompra` = `e`.`numero`))) join `odirect` `b` on((`a`.`numero` = `b`.`numero`))) join `sprv` `s` on((`s`.`proveed` = if((length(`b`.`cod_prov2`) > 0),`b`.`cod_prov2`,`b`.`cod_prov`)))) where (substr(`b`.`status`,1,1) = 'C') group by `b`.`numero`;


-- Dumping structure for view tortuga.v_presaldo
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `v_presaldo`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `v_presaldo` AS select `presupuesto`.`codigoadm` AS `codigoadm`,`presupuesto`.`tipo` AS `fondo`,`presupuesto`.`codigopres` AS `codigo`,`presupuesto`.`denominacion` AS `denominacion`,`presupuesto`.`apartado` AS `apartado`,((((`presupuesto`.`asignacion` + `presupuesto`.`aumento`) - `presupuesto`.`disminucion`) + `presupuesto`.`traslados`) - `presupuesto`.`comprometido`) AS `saldo`,'S' AS `movimiento`,'' AS `ordinal` from `presupuesto`;


-- Dumping structure for view tortuga.v_presaldoante
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `v_presaldoante`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `v_presaldoante` AS select `a`.`codigoadm` AS `codigoadm`,`a`.`tipo` AS `fondo`,`b`.`codigo` AS `codigo`,if((`c`.`ordinal` <> ''),`c`.`denominacion`,`b`.`denominacion`) AS `denominacion`,if((`c`.`ordinal` <> ''),((((`c`.`asignacion` + `c`.`aumento`) + `c`.`traslados`) - `c`.`disminucion`) - `c`.`comprometido`),((((`a`.`asignacion` + `a`.`aumento`) + `a`.`traslados`) - `a`.`disminucion`) - `a`.`comprometido`)) AS `saldo`,`b`.`movimiento` AS `movimiento`,`c`.`ordinal` AS `ordinal` from ((`presupuestoante` `a` join `ppla` `b` on((`a`.`codigopres` = `b`.`codigo`))) left join `ordinalante` `c` on(((`c`.`codigoadm` = `a`.`codigoadm`) and (`a`.`tipo` = `c`.`fondo`) and (`a`.`codigopres` = `c`.`codigopres`)))) where (substr(`b`.`codigo`,1,1) = '4');


-- Dumping structure for view tortuga.v_retenciones
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `v_retenciones`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `v_retenciones` AS select `a`.`numero` AS `desembolso`,`c`.`numero` AS `ordpago`,'' AS `ordcompra`,'' AS `multiple`,`a`.`cod_prov` AS `cod_prov`,`c`.`factura` AS `factura`,`c`.`controlfac` AS `controlfac`,`c`.`fechafac` AS `fechafac`,`c`.`total2` AS `total2`,`c`.`reteiva` AS `reteiva`,`c`.`reten` AS `reten`,`c`.`impmunicipal` AS `impmunicipal`,`c`.`imptimbre` AS `imptimbre`,`c`.`total` AS `total`,`f`.`nrocomp` AS `nrocomp`,`c`.`breten` AS `breten`,`c`.`subtotal` AS `subtotal`,trim(`c`.`creten`) AS `creten`,`p`.`rif` AS `rif`,`p`.`nombre` AS `nombre`,concat_ws(' ',`p`.`direc1`,`p`.`direc2`,`p`.`direc3`) AS `direccion`,`a`.`fdesem` AS `fdesem`,`c`.`crs` AS `crs`,`c`.`otrasrete` AS `otrasrete`,((`c`.`ivaa` + `c`.`ivag`) + `c`.`ivar`) AS `iva`,`c`.`exento` AS `exento`,`c`.`preten` AS `preten`,(((0 + `f`.`general`) + `f`.`reducida`) + `f`.`adicional`) AS `basei` from ((((`desem` `a` join `pades` `b` on((`a`.`numero` = `b`.`desem`))) join `odirect` `c` on((`b`.`pago` = `c`.`numero`))) left join `riva` `f` on((`c`.`numero` = `f`.`odirect`))) left join `sprv` `p` on((`a`.`cod_prov` = `p`.`proveed`))) where (((`c`.`status` = 'B3') or (`c`.`status` = 'C3') or (`c`.`status` = 'N3')) and (`a`.`status` = 'D2') and (`c`.`multiple` = 'N')) union all select `a`.`numero` AS `desembolso`,`c`.`numero` AS `ordpago`,'' AS `ordcompra`,`g`.`id` AS `multiple`,`a`.`cod_prov` AS `cod_prov`,`g`.`factura` AS `factura`,`g`.`controlfac` AS `controlfac`,`g`.`fechafac` AS `fechafac`,`g`.`total2` AS `total2`,`g`.`reteiva` AS `reteiva`,`g`.`reten` AS `reten`,`g`.`impmunicipal` AS `impmunicipal`,`g`.`imptimbre` AS `imptimbre`,`g`.`total` AS `total`,`f`.`nrocomp` AS `nrocomp`,`g`.`breten` AS `breten`,`g`.`subtotal` AS `subtotal`,`c`.`creten` AS `creten`,`p`.`rif` AS `rif`,`p`.`nombre` AS `nombre`,concat_ws(' ',`p`.`direc1`,`p`.`direc2`,`p`.`direc3`) AS `direccion`,`a`.`fdesem` AS `fdesem`,0 AS `crs`,`g`.`otrasrete` AS `otrasrete`,((`g`.`ivaa` + `g`.`ivag`) + `g`.`ivar`) AS `iva`,`g`.`exento` AS `exento`,`c`.`preten` AS `preten`,(((0 + `f`.`general`) + `f`.`reducida`) + `f`.`adicional`) AS `basei` from (((((`desem` `a` join `pades` `b` on((`a`.`numero` = `b`.`desem`))) join `odirect` `c` on((`b`.`pago` = `c`.`numero`))) join `itfac` `g` on((`c`.`numero` = `g`.`numero`))) left join `riva` `f` on(((`g`.`id` = `f`.`itfac`) and (`f`.`status` <> 'AN')))) left join `sprv` `p` on((`a`.`cod_prov` = `p`.`proveed`))) where (((`c`.`status` = 'B3') or (`c`.`status` = 'N3') or (`c`.`status` = 'C3')) and (`a`.`status` = 'D2') and (`c`.`multiple` = 'S'));


-- Dumping structure for view tortuga.v_retencionesislr
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `v_retencionesislr`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `v_retencionesislr` AS select `a`.`numero` AS `desembolso`,`c`.`numero` AS `ordpago`,'' AS `ordcompra`,'' AS `multiple`,`a`.`cod_prov` AS `cod_prov`,`c`.`factura` AS `factura`,`c`.`controlfac` AS `controlfac`,`c`.`fechafac` AS `fechafac`,`c`.`total2` AS `total2`,`c`.`subtotal` AS `breten`,`c`.`subtotal` AS `subtotal`,trim(`c`.`creten`) AS `creten`,`p`.`rif` AS `rif`,`p`.`nombre` AS `nombre`,concat_ws(' ',`p`.`direc1`,`p`.`direc2`,`p`.`direc3`) AS `direccion`,`a`.`fdesem` AS `fdesem`,((`c`.`ivaa` + `c`.`ivag`) + `c`.`ivar`) AS `iva`,`c`.`exento` AS `exento`,`c`.`preten` AS `preten`,`c`.`reten` AS `reten` from ((((`desem` `a` join `pades` `b` on((`a`.`numero` = `b`.`desem`))) join `odirect` `c` on((`b`.`pago` = `c`.`numero`))) left join `riva` `f` on(((`c`.`numero` = `f`.`odirect`) and (`f`.`tipo_doc` <> 'FC')))) left join `sprv` `p` on((`a`.`cod_prov` = `p`.`proveed`))) where (((`c`.`status` = 'B3') or (`c`.`status` = 'C3') or (`c`.`status` = 'N3')) and (`a`.`status` = 'D2') and (`c`.`multiple` = 'N') and (`c`.`reten` > 0)) union all select `a`.`numero` AS `desembolso`,`c`.`numero` AS `ordpago`,'' AS `ordcompra`,`g`.`id` AS `multiple`,`a`.`cod_prov` AS `cod_prov`,`g`.`factura` AS `factura`,`g`.`controlfac` AS `controlfac`,`g`.`fechafac` AS `fechafac`,sum(`g`.`total2`) AS `total2`,sum(`g`.`breten`) AS `breten`,sum(`g`.`subtotal`) AS `subtotal`,`c`.`creten` AS `creten`,`p`.`rif` AS `rif`,`p`.`nombre` AS `nombre`,concat_ws(' ',`p`.`direc1`,`p`.`direc2`,`p`.`direc3`) AS `direccion`,`a`.`fdesem` AS `fdesem`,sum(((`g`.`ivaa` + `g`.`ivag`) + `g`.`ivar`)) AS `iva`,sum(`g`.`exento`) AS `exento`,`c`.`preten` AS `preten`,`c`.`reten` AS `reten` from (((((`desem` `a` join `pades` `b` on((`a`.`numero` = `b`.`desem`))) join `odirect` `c` on((`b`.`pago` = `c`.`numero`))) join `itfac` `g` on((`c`.`numero` = `g`.`numero`))) left join `riva` `f` on(((`g`.`id` = `f`.`itfac`) and (`f`.`tipo_doc` <> 'FC')))) left join `sprv` `p` on((`a`.`cod_prov` = `p`.`proveed`))) where (((`c`.`status` = 'B3') or (`c`.`status` = 'N3') or (`c`.`status` = 'C3')) and (`a`.`status` = 'D2') and (`c`.`multiple` = 'S') and (`c`.`reten` > 0)) group by `c`.`numero`;


-- Dumping structure for view tortuga.v_retencionesislrd
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `v_retencionesislrd`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `v_retencionesislrd` AS select `a`.`numero` AS `desembolso`,`c`.`numero` AS `ordpago`,'' AS `ordcompra`,'' AS `multiple`,`a`.`cod_prov` AS `cod_prov`,`c`.`factura` AS `factura`,`c`.`controlfac` AS `controlfac`,`c`.`fechafac` AS `fechafac`,`c`.`total2` AS `total2`,`c`.`subtotal` AS `breten`,`c`.`subtotal` AS `subtotal`,trim(`c`.`creten`) AS `creten`,`p`.`rif` AS `rif`,`p`.`nombre` AS `nombre`,concat_ws(' ',`p`.`direc1`,`p`.`direc2`,`p`.`direc3`) AS `direccion`,`a`.`fdesem` AS `fdesem`,((`c`.`ivaa` + `c`.`ivag`) + `c`.`ivar`) AS `iva`,`c`.`exento` AS `exento`,`c`.`preten` AS `preten`,`c`.`reten` AS `reten` from ((((`desem` `a` join `pades` `b` on((`a`.`numero` = `b`.`desem`))) join `odirect` `c` on((`b`.`pago` = `c`.`numero`))) left join `riva` `f` on(((`c`.`numero` = `f`.`odirect`) and (`f`.`tipo_doc` <> 'FC')))) left join `sprv` `p` on((`a`.`cod_prov` = `p`.`proveed`))) where (((`c`.`status` = 'B3') or (`c`.`status` = 'C3') or (`c`.`status` = 'N3')) and (`a`.`status` = 'D2') and (`c`.`multiple` = 'N') and (`c`.`reten` > 0)) union all select `a`.`numero` AS `desembolso`,`c`.`numero` AS `ordpago`,'' AS `ordcompra`,`g`.`id` AS `multiple`,`a`.`cod_prov` AS `cod_prov`,`g`.`factura` AS `factura`,`g`.`controlfac` AS `controlfac`,`g`.`fechafac` AS `fechafac`,`g`.`total2` AS `total2`,`g`.`breten` AS `breten`,`g`.`subtotal` AS `subtotal`,`c`.`creten` AS `creten`,`p`.`rif` AS `rif`,`p`.`nombre` AS `nombre`,concat_ws(' ',`p`.`direc1`,`p`.`direc2`,`p`.`direc3`) AS `direccion`,`a`.`fdesem` AS `fdesem`,((`g`.`ivaa` + `g`.`ivag`) + `g`.`ivar`) AS `iva`,`g`.`exento` AS `exento`,`c`.`preten` AS `preten`,`c`.`reten` AS `reten` from (((((`desem` `a` join `pades` `b` on((`a`.`numero` = `b`.`desem`))) join `odirect` `c` on((`b`.`pago` = `c`.`numero`))) join `itfac` `g` on((`c`.`numero` = `g`.`numero`))) left join `riva` `f` on(((`g`.`id` = `f`.`itfac`) and (`f`.`tipo_doc` <> 'FC')))) left join `sprv` `p` on((`a`.`cod_prov` = `p`.`proveed`))) where (((`c`.`status` = 'B3') or (`c`.`status` = 'N3') or (`c`.`status` = 'C3')) and (`a`.`status` = 'D2') and (`c`.`multiple` = 'S') and (`c`.`reten` > 0));
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
