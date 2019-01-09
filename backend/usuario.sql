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

-- Dumping data for table tortuga.usuario: 89 rows
DELETE FROM `usuario`;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` (`us_codigo`, `us_nombre`, `us_clave`, `us_fechae`, `us_horae`, `us_fechas`, `us_horas`, `supervisor`, `vendedor`, `cajero`, `internet`, `caja`, `usachat`) VALUES
	('1', 'SUPERVISOR', '356a192b7913b04c54574d18c28d46e6395428ab', '2009-10-07', '16:08:57', '2009-09-02', '17:14:27', 'S', '', NULL, 'S', NULL, 'S'),
	('2', 'USUARIO', '356a192b7913b04c54574d18c28d46e6395428ab', NULL, NULL, NULL, NULL, 'N', '', NULL, 'S', 1, 'N');
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
