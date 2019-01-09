<?php
class Instalador extends Controller {
	function Instalador(){
		parent::Controller();
	}
	function index(){


		//./suministros/suminr.php 
		$this->db->simple_query("ALTER TABLE `suminr` ADD `status` CHAR( 1 ) NOT NULL DEFAULT 'P'");
		$this->db->simple_query("ALTER TABLE `suminr`  ADD COLUMN `conc` INT NULL DEFAULT NULL");


		//./suministros/su_caub.php 
		


		//./suministros/su_trasla.php 
		$query="CREATE TABLE `su_ittrasla` (
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`numero` INT(11) UNSIGNED NULL DEFAULT NULL,
			`codigo` VARCHAR(4) NULL DEFAULT NULL,
			`cant` DECIMAL(19,2) NULL DEFAULT '0.00',
			PRIMARY KEY (`id`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM
		ROW_FORMAT=DEFAULT";
		$this->db->simple_query($query);
		
		$query="CREATE TABLE `su_trasla` (
			`numero` INT(11) NOT NULL AUTO_INCREMENT,
			`fecha` DATE NULL DEFAULT NULL,
			`concepto` VARCHAR(200) NULL DEFAULT NULL,
			`total` DECIMAL(19,2) NULL DEFAULT '0.00',
			`status` CHAR(1) NULL DEFAULT 'P',
			`de` VARCHAR(4) NULL DEFAULT NULL,
			`para` VARCHAR(4) NULL DEFAULT NULL,
			PRIMARY KEY (`numero`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM
		ROW_FORMAT=DEFAULT";
		
		$this->db->simple_query($query);


		//./suministros/sumine.php 
		$this->db->simple_query("ALTER TABLE `sumine` ADD `status` CHAR( 1 ) NOT NULL DEFAULT 'P'");
		$this->db->simple_query("ALTER TABLE `sumine`  ADD COLUMN `conc` INT NULL DEFAULT NULL");


		//./suministros/su_gralma.php 
		


		//./suministros/su_alma.php 
		$mSQL="
		CREATE TABLE `alma` (
		  `codigo` varchar(4) NOT NULL,
		  `descrip` varchar(200) DEFAULT NULL,
		  `uadministra` varchar(4) DEFAULT NULL,
		  `cuenta` varchar(45) DEFAULT NULL,
		  PRIMARY KEY (`codigo`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8
		";
		$this->db->simple_query($mSQL);


		//./suministros/su_conc.php 
		$mSQL="CREATE TABLE `su_conc` (
			`id` int(10) NOT NULL AUTO_INCREMENT,
			`descrip` text,
			`tipo` char(1) DEFAULT NULL,
			PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$this->db->simple_query($mSQL);


		//./presupuesto/rete.php 
			$this->db->simple_query("ALTER TABLE `rete` ADD COLUMN `auxi` CHAR(15) NULL DEFAULT NULL");


		//./presupuesto/itfac2.php 
		$query="ALTER TABLE `itfac` ADD COLUMN `uivaa`      CHAR(1)          NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `itfac` ADD COLUMN `uivag`      CHAR(1)          NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `itfac` ADD COLUMN `uivar`      CHAR(1)          NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `itfac` ADD COLUMN `ureten`     CHAR(1)          NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `itfac` ADD COLUMN `uimptimbre` CHAR(1)          NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `itfac` ADD COLUMN `preteiva_prov` DECIMAL(19,2) NULL DEFAULT NULL";
		$this->db->simple_query($query);


		//./presupuesto/fondo.php 
		$query="ALTER TABLE `fondo`  ADD COLUMN `partidaiva` VARCHAR(25) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `fondo`  CHANGE COLUMN `cuenta` `cuenta` VARCHAR(25) NULL DEFAULT NULL ";
		$this->db->simple_query($query);
		$query="ALTER TABLE `fondo` ADD COLUMN `cuentap` VARCHAR(25) NULL DEFAULT NULL ";
		$this->db->simple_query($query);
		$query="ALTER TABLE `fondo`  ADD COLUMN `fcuenta` VARCHAR(25) NULL DEFAULT NULL ,  ADD COLUMN `fcuentap` VARCHAR(25) NULL DEFAULT NULL ";
		$this->db->simple_query($query);
		


		//./presupuesto/presupuesto.php 
		$query="ALTER TABLE `presupuesto`  ADD COLUMN `uejecutora` VARCHAR(4) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		
		$query="ALTER TABLE `presupuesto` CHANGE COLUMN `uejecutora` `uejecutora` VARCHAR(8) NULL DEFAULT NULL AFTER `movimiento`";
		$this->db->simple_query($query);


		//./presupuesto/reinte.php 
		$this->db->simple_query("
		CREATE TABLE `reinte` (
			`numero` VARCHAR(12) NOT NULL DEFAULT '',
			`fecha` DATE NULL DEFAULT NULL,
			`uejecuta` CHAR(4) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`uadministra` CHAR(4) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`concepto` TINYTEXT NULL COLLATE 'utf8_general_ci',
			`status` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`usuario` VARCHAR(12) NULL DEFAULT NULL COMMENT 'aa' COLLATE 'utf8_general_ci',
			`total` VARCHAR(45) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`comping` VARCHAR(12) NULL,
			PRIMARY KEY (`numero`),
			INDEX `uejecuta` (`uejecuta`)
		)
		COMMENT='Reintegros presupuestarios'
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM
		ROW_FORMAT=DEFAULT");
		$this->db->simple_query("
		CREATE TABLE `itreinte` (
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`numero` VARCHAR(12) NULL DEFAULT NULL,
			`codigoadm` VARCHAR(12) NULL DEFAULT NULL,
			`fondo` VARCHAR(20) NULL DEFAULT NULL,
			`codigopres` VARCHAR(17) NULL DEFAULT NULL,
			`ordinal` CHAR(3) NULL DEFAULT NULL,
			`monto` DECIMAL(19,2) NULL DEFAULT NULL,
			PRIMARY KEY (`id`),
			INDEX `numero` (`numero`),
			INDEX `codigopres` (`codigopres`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM
		ROW_FORMAT=DEFAULT
		AUTO_INCREMENT=0");


		//./presupuesto/anoprox.php 
		$query="
		
		CREATE TABLE `anoprox` (
			`numero` VARCHAR(9) NOT NULL DEFAULT '',
			`fecha` DATE NULL DEFAULT NULL,
			`uejecuta` CHAR(4) NULL DEFAULT NULL,
			`uadministra` CHAR(4) NULL DEFAULT NULL,
			`concepto` TINYTEXT NULL,
			`responsable` VARCHAR(250) NULL DEFAULT NULL,
			`status` CHAR(2) NULL DEFAULT NULL ,
			`usuario` VARCHAR(12) NULL DEFAULT NULL,
			PRIMARY KEY (`numero`),
			INDEX `uejecuta` (`uejecuta`)
		)
		COMMENT='Preyeccion proximo ano'
		ENGINE=MyISAM
		ROW_FORMAT=DEFAULT
		";
		echo $this->db->simple_query($query);
		$query="
		CREATE TABLE `itanoprox` (
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`numero` VARCHAR(9) NULL DEFAULT NULL,
			`codigoadm` VARCHAR(12) NULL DEFAULT NULL,
			`fondo` VARCHAR(20) NULL DEFAULT NULL,
			`codigopres` VARCHAR(17) NULL DEFAULT NULL,
			`ordinal` CHAR(3) NULL DEFAULT NULL,
			`unidad` VARCHAR(20) NULL DEFAULT NULL,
			`denomi` TINYTEXT NULL,
			`descrip` TINYTEXT NULL,
			`descripd` TINYTEXT NULL,
			`cant` DECIMAL(19,2) NULL DEFAULT NULL,
			PRIMARY KEY (`id`),
			INDEX `numero` (`numero`)
		)
		ENGINE=MyISAM
		ROW_FORMAT=DEFAULT
		AUTO_INCREMENT=1
		";
		echo $this->db->simple_query($query);


		//./presupuesto/ordinal.php 
		$this->db->simple_query("ALTER TABLE `ordinal`  CHANGE COLUMN `asignacion` `asignacion` DECIMAL(19,2) UNSIGNED NULL DEFAULT '0' AFTER `denominacion`,  CHANGE COLUMN `aumento` `aumento` DECIMAL(19,2) UNSIGNED NULL DEFAULT '0' AFTER `asignacion`,  CHANGE COLUMN `disminucion` `disminucion` DECIMAL(19,2) UNSIGNED NULL DEFAULT '0' AFTER `aumento`,  CHANGE COLUMN `traslados` `traslados` DECIMAL(19,2) NULL DEFAULT '0' AFTER `disminucion`,  CHANGE COLUMN `comprometido` `comprometido` DECIMAL(19,2) UNSIGNED NULL DEFAULT '0' AFTER `traslados`,  CHANGE COLUMN `causado` `causado` DECIMAL(19,2) UNSIGNED NULL DEFAULT '0' AFTER `comprometido`,  CHANGE COLUMN `opago` `opago` DECIMAL(19,2) UNSIGNED NULL DEFAULT '0' AFTER `causado`,  CHANGE COLUMN `pagado` `pagado` DECIMAL(19,2) UNSIGNED NULL DEFAULT '0' AFTER `opago`");


		//./presupuesto/estruadm.php 
			$query="ALTER TABLE `estruadm`
			CHANGE COLUMN `uejecutora` `uejecutora` CHAR(8) NULL DEFAULT NULL COMMENT 'Unidad Ejecutora' AFTER `causado`;";
			$this->db->simple_query($query):


		//./presupuesto/itfac3.php 
		$query="ALTER TABLE `itfac` ADD COLUMN `uivaa`      CHAR(1)          NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `itfac` ADD COLUMN `uivag`      CHAR(1)          NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `itfac` ADD COLUMN `uivar`      CHAR(1)          NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `itfac` ADD COLUMN `ureten`     CHAR(1)          NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `itfac` ADD COLUMN `uimptimbre` CHAR(1)          NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `itfac` ADD COLUMN `preteiva_prov` DECIMAL(19,2) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `itfac`  ADD COLUMN `basei` DECIMAL(19,2) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `itfac`  ADD COLUMN `nocompra` VARCHAR(20) NULL DEFAULT NULL";
		$this->db->simple_query($query);


		//./presupuesto/otrabajo.php 
		$query="ALTER TABLE `ocompra` ADD COLUMN `nocompra` VARCHAR(12) NULL AFTER `condiciones";
		$this->db->simple_query($query);
		


		//./presupuesto/odirect.php 
			$this->db->simple_query("ALTER TABLE `itodirect` CHANGE COLUMN `partida` `partida` VARCHAR(25) NULL DEFAULT NULL COMMENT 'Partida Presupuestaria'");
			$this->db->simple_query("ALTER TABLE `odirect` CHANGE COLUMN `numero` `numero` VARCHAR(12) NOT NULL COMMENT 'Nro de La Orden Pago'  ");
			$this->db->simple_query("ALTER TABLE `itodirect` CHANGE COLUMN `numero` `numero` VARCHAR(12) NULL DEFAULT NULL COMMENT 'Numero de la Orden'  ");
			$this->db->simple_query("ALTER TABLE `nomi`  CHANGE COLUMN `opago` `opago` VARCHAR(12) NULL DEFAULT NULL AFTER `fcomprome`");
			$this->db->simple_query("ALTER TABLE `odirect`  CHANGE COLUMN `nomina` `nomina` VARCHAR(12) NULL DEFAULT NULL");
			$this->db->simple_query("ALTER TABLE `odirect`  ADD COLUMN `cod_prov2` VARCHAR(5) NULL DEFAULT NULL AFTER `mcrs`");
			$this->db->simple_query("ALTER TABLE `odirect`  ADD COLUMN `redondear` CHAR(2) NULL");
			$this->db->simple_query("ALTER TABLE `odirect`  ADD COLUMN `otrasrete` DECIMAL(19,2) NULL DEFAULT '0'			");
			$this->db->simple_query("ALTER TABLE `itodirect` CHANGE COLUMN `partida` `partida` VARCHAR(25) NULL DEFAULT NULL COMMENT 'Partida Presupuestaria'  ");


		//./presupuesto/trami.php 
		$query="CREATE TABLE `trami` (
		`numero` INT(11) NOT NULL AUTO_INCREMENT,
		`compromiso` CHAR(12) NOT NULL,
		`fecha` DATE NOT NULL,
		`cod_prov` CHAR(5) NOT NULL,
		`concepto` VARCHAR(50) NOT NULL,
		`monto` DECIMAL(19,2) NOT NULL,
		PRIMARY KEY (`numero`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM
		ROW_FORMAT=DEFAULT
		AUTO_INCREMENT=1
		";
		$this->db->simple_query($query);
		
		$query="CREATE TABLE `ittrami` (
		`numero` INT(11) NULL DEFAULT NULL,
		`id` INT(11) NOT NULL AUTO_INCREMENT,
		`codigoadm` VARCHAR(12) NULL DEFAULT NULL,
		`fondo` VARCHAR(20) NULL DEFAULT NULL,
		`codigopres` VARCHAR(17) NULL DEFAULT NULL,
		`ordinal` CHAR(3) NULL DEFAULT NULL,
		`descripcion` VARCHAR(80) NULL DEFAULT NULL,
		`importe` DECIMAL(19,2) NULL DEFAULT NULL,
		PRIMARY KEY (`id`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM
		ROW_FORMAT=DEFAULT
		AUTO_INCREMENT=1
		";
		$this->db->simple_query($query);
		
		$query="ALTER TABLE `trami`  ADD COLUMN `status` CHAR(2) NOT NULL DEFAULT 'P'";
		$this->db->simple_query($query);
		$query="ALTER TABLE `trami`  ADD COLUMN `fondo` VARCHAR(20) NOT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `trami` ADD COLUMN `fcomprome` DATE NOT NULL ";
		$this->db->simple_query($query);
		$query="ALTER TABLE `trami` ADD COLUMN `fpagado` DATE NOT NULL";
		$this->db->simple_query($query);


		//./presupuesto/ocompra.php 
		$this->db->simple_query("ALTER TABLE `itocompra` CHANGE COLUMN `partida` `partida` VARCHAR(25) NULL DEFAULT NULL COMMENT 'Partida Presupuestaria'  ;");
		$this->db->simple_query("ALTER TABLE `itfac`  ADD COLUMN `nocompra` VARCHAR(12) NULL DEFAULT NULL");
		$this->db->simple_query("ALTER TABLE `ocompra`  ADD COLUMN `proced` VARCHAR(100) NULL");
		$this->db->simple_query("ALTER TABLE `ocompra`  ADD COLUMN `aplica` VARCHAR(20) NULL DEFAULT NULL");
		$this->db->simple_query("ALTER TABLE `ocompra`  ADD COLUMN `otrasrete` DECIMAL(19,2) NULL DEFAULT NULL");
		$this->db->simple_query("ALTER TABLE `ocompra`  ADD COLUMN `redondear` VARCHAR(2) NULL");
		$this->db->simple_query("ALTER TABLE `ocompra` ADD COLUMN `modalidad` VARCHAR(50) NULL DEFAULT '' AFTER `redondear`");
		$this->db->simple_query("ALTER TABLE `ocompra` ADD COLUMN `formaentrega` VARCHAR(50) NULL DEFAULT '' AFTER `modalidad`");
		$this->db->simple_query("ALTER TABLE `ocompra` ADD COLUMN `condiciones` TEXT NULL DEFAULT '' AFTER `formaentrega`");


		//./presupuesto/opago.php 
		$query="ALTER TABLE `pacom` CHANGE COLUMN `pago` `pago` VARCHAR(12) NULL DEFAULT NULL";
		$this->db->simple_query($query);
        $query="ALTER TABLE `odirect`  ADD COLUMN `tipoc` VARCHAR(2) NULL DEFAULT NULL";
        $this->db->simple_query($query);
		$query="ALTER TABLE `odirect`  ADD INDEX `numero` (`numero`)  ";
		$this->db->simple_query($query);
		$query="ALTER TABLE `pacom`    ADD INDEX `pago`   (`pago`)    ";
		$this->db->simple_query($query);
		$query="ALTER TABLE `pacom`    ADD INDEX `compra`   (`compra`)";
		$this->db->simple_query($query);
		$query="ALTER TABLE `ocompra`  ADD INDEX `numero` (`numero`)  ";
		$this->db->simple_query($query);
		$query="ALTER TABLE `sprv`     ADD INDEX `proveed` (`proveed`)";
		$this->db->simple_query($query);
		$query="ALTER TABLE `odirect`  ADD COLUMN `otrasrete` DECIMAL(19,2) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `odirect`  ADD COLUMN `fanulado` DATE NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `odirect` ADD COLUMN `observacaj` TEXT NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `odirect` ADD COLUMN `fapagado` DATE NULL DEFAULT NULL AFTER `observacaj`";
		$this->db->simple_query($query);
		$query="ALTER TABLE `odirect` ADD COLUMN `facaduca` DATE NULL DEFAULT NULL AFTER `fapagado`";
		$this->db->simple_query($query);


		//./presupuesto/presupante.php 
		$query="ALTER TABLE `presupuesto`  ADD COLUMN `uejecutora` VARCHAR(4) NULL DEFAULT NULL";
		$this->db->simple_query($query);


		//./presupuesto/opagoc.php 
		$this->db->simple_query("ALTER TABLE `itodirect` CHANGE COLUMN `partida` `partida` VARCHAR(25) NULL DEFAULT NULL COMMENT 'Partida Presupuestaria'");
		$this->db->simple_query("ALTER TABLE `odirect` CHANGE COLUMN `numero` `numero` VARCHAR(12) NOT NULL COMMENT 'Nro de La Orden Pago'  ");
		$this->db->simple_query("ALTER TABLE `itodirect` CHANGE COLUMN `numero` `numero` VARCHAR(12) NULL DEFAULT NULL COMMENT 'Numero de la Orden'  ");
		$this->db->simple_query("ALTER TABLE `nomi`  CHANGE COLUMN `opago` `opago` VARCHAR(12) NULL DEFAULT NULL AFTER `fcomprome`");
		$this->db->simple_query("ALTER TABLE `odirect`  CHANGE COLUMN `nomina` `nomina` VARCHAR(12) NULL DEFAULT NULL");
		$this->db->simple_query("ALTER TABLE `odirect`  ADD COLUMN `cod_prov2` VARCHAR(5) NULL DEFAULT NULL AFTER `mcrs`");
		$this->db->simple_query("ALTER TABLE `odirect`  ADD COLUMN `redondear` CHAR(2) NULL");
		$this->db->simple_query("ALTER TABLE `odirect`  ADD COLUMN `otrasrete` DECIMAL(19,2) NULL DEFAULT '0'			");
		$this->db->simple_query("ALTER TABLE `itodirect` ADD COLUMN `ocompra` VARCHAR(20) NULL DEFAULT NULL");


		//./presupuesto/trasla.php 
		$query="ALTER TABLE `ittrasla`  CHANGE COLUMN `codigopres` `codigopres` VARCHAR(25) NULL DEFAULT NULL AFTER `fondo`";
		$this->db->simple_query($query);
		$query="ALTER TABLE `trasla`  ADD COLUMN `nrooficio` VARCHAR(50) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `trasla` ADD COLUMN `resolu` VARCHAR(50) NULL DEFAULT NULL ";
		$this->db->simple_query($query);
		$query="ALTER TABLE `trasla` ADD COLUMN `fresolu` DATE NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `ittrasla`  CHANGE COLUMN `codigopres` `codigopres` VARCHAR(25) NULL DEFAULT NULL";
		$this->db->simple_query($query);


		//./presupuesto/sprv.php 
		$mSQL='ALTER TABLE `sprv`  ADD COLUMN `objeto` TEXT NULL DEFAULT NULL ';
		$this->db->simple_query($mSQL);
		$mSQL='ALTER TABLE `sprv`  ADD COLUMN `ingreso` DATE NULL DEFAULT NULL  ';
		$this->db->simple_query($mSQL);
		$mSQL='ALTER TABLE `sprv` ADD COLUMN `vence` DATE NULL DEFAULT NULL AFTER ';
		$this->db->simple_query($mSQL);
		$mSQL='ALTER TABLE `sprv`  ADD COLUMN `contaci` VARCHAR(20) NULL DEFAULT NULL ';
		$this->db->simple_query($mSQL);
		$mSQL='ALTER TABLE `sprv`  CHANGE COLUMN `direc1` `direc1` TINYTEXT NULL DEFAULT NULL ';
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `sprv`  ADD COLUMN `cod_prov` VARCHAR(5) NULL DEFAULT NULL ";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `sprv` ADD COLUMN `concepto` TEXT NULL DEFAULT NULL";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `sprv` ADD COLUMN `conceptof` TEXT NULL DEFAULT NULL";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `sprv`  CHANGE COLUMN `contacto` `contacto` VARCHAR(100) NULL DEFAULT NULL";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `sprv`  ADD COLUMN `numcuent` VARCHAR(50) NULL";
		$this->db->simple_query($mSQL);


		//./presupuesto/audis.php 
		$query="ALTER TABLE `itaudis`  CHANGE COLUMN `codigopres` `codigopres` VARCHAR(25) NULL DEFAULT NULL AFTER `numero`;";
		$this->db->simple_query($query);
		$query="ALTER TABLE `audis`  ADD COLUMN `resolu` VARCHAR(20) NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `audis`  ADD COLUMN `fresolu` DATE NULL";
		$this->db->simple_query($query);


		//./presupuesto/causacion.php 
		$query="ALTER TABLE `ocompra`  ADD COLUMN `mexento` DECIMAL(19,2) NULL DEFAULT 0";
		$this->db->simple_query($query);


		//./presupuesto/uejecuta.php 
		$query="ALTER TABLE `uejecutora`  ADD COLUMN `cuenta` VARCHAR(25) NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `uejecutora` CHANGE COLUMN `codigo` `codigo` CHAR(8) NOT NULL DEFAULT '' FIRST";
		$this->db->simple_query($query);


		//./presupuesto/opagoante.php 
			echo $this->db->simple_query("ALTER TABLE `itodirect` CHANGE COLUMN `partida` `partida` VARCHAR(25) NULL DEFAULT NULL COMMENT 'Partida Presupuestaria'");
			echo $this->db->simple_query("ALTER TABLE `odirect` CHANGE COLUMN `numero` `numero` VARCHAR(12) NOT NULL COMMENT 'Nro de La Orden Pago'  ");
			echo $this->db->simple_query("ALTER TABLE `itodirect` CHANGE COLUMN `numero` `numero` VARCHAR(12) NULL DEFAULT NULL COMMENT 'Numero de la Orden'  ");
			echo $this->db->simple_query("ALTER TABLE `nomi`  CHANGE COLUMN `opago` `opago` VARCHAR(12) NULL DEFAULT NULL AFTER `fcomprome`");
			echo $this->db->simple_query("ALTER TABLE `odirect`  CHANGE COLUMN `nomina` `nomina` VARCHAR(12) NULL DEFAULT NULL");
			echo $this->db->simple_query("ALTER TABLE `odirect`  ADD COLUMN `cod_prov2` VARCHAR(5) NULL DEFAULT NULL AFTER `mcrs`");




		//./ejemplo.php 
		$mSQL="CREATE TABLE `ejemplo` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8_general_ci NOT NULL,
  `naci` date NOT NULL,
  `sexo` char(1) COLLATE utf8_general_ci NOT NULL,
  `civil` char(1) COLLATE utf8_general_ci NOT NULL,
  `usuario` varchar(12) COLLATE utf8_general_ci NOT NULL,
  `color` varchar(12) COLLATE utf8_general_ci NOT NULL,
  `piel` varchar(1) COLLATE utf8_general_ci NOT NULL,
  `trabaja` char(1) COLLATE utf8_general_ci NOT NULL,
  `sueldo` decimal(19,2) NOT NULL,
  `observa` tinytext COLLATE utf8_general_ci NOT NULL,
  `blog` longtext COLLATE utf8_general_ci NOT NULL,
  `modifi` date NOT NULL,
  `foto` varchar(200) COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci";
		$this->db->simple_query($mSQL);


		//./contabilidad/casi.php 
	 $query="CREATE TABLE `casi` (
			`numero` INT(11) NOT NULL AUTO_INCREMENT,
			`comprob` VARCHAR(45) NOT NULL,
			`fecha` DATE NOT NULL DEFAULT '0000-00-00',
			`descrip` VARCHAR(60) NULL DEFAULT NULL,
			`total` DECIMAL(19,2) NULL DEFAULT NULL,
			`debe` DECIMAL(19,2) NULL DEFAULT NULL,
			`haber` DECIMAL(19,2) NULL DEFAULT NULL,
			`status` CHAR(2) NULL DEFAULT 'C1',
			`tipo` VARCHAR(10) NULL DEFAULT '',
			`origen` VARCHAR(20) NULL DEFAULT NULL,
			`transac` VARCHAR(8) NULL DEFAULT NULL,
			`usuario` VARCHAR(4) NULL DEFAULT NULL,
			`estampa` DATE NULL DEFAULT NULL,
			`hora` VARCHAR(8) NULL DEFAULT NULL,
			PRIMARY KEY (`comprob`),
			INDEX `comprorigen` (`numero`, `origen`),
			INDEX `fecha` (`fecha`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM
		AUTO_INCREMENT=1;
		"; 
		$this->db->simple_query($query);
		
		$query="CREATE TABLE `itcasi` (
			`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
			`fecha` DATE NOT NULL DEFAULT '0000-00-00',
			`numero` INT(11) NOT NULL DEFAULT '0',
			`origen` CHAR(20) NULL DEFAULT NULL,
			`cuenta` CHAR(30) NULL DEFAULT NULL,
			`referen` TEXT NULL,
			`concepto` TEXT NULL,
			`debe` DECIMAL(19,2) NOT NULL DEFAULT '0.00',
			`haber` DECIMAL(19,2) NOT NULL DEFAULT '0.00',
			`ccosto` CHAR(12) NULL DEFAULT NULL,
			`sucursal` CHAR(12) NULL DEFAULT NULL,
			`comprob` VARCHAR(30) NULL DEFAULT NULL,
			`mbanc_id` TEXT NULL,
			PRIMARY KEY (`id`),
			INDEX `comprob` (`comprob`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM
		AUTO_INCREMENT=1;
		";
		
		$this->db->simple_query($query);
	  
    $query="ALTER TABLE `casi` CHANGE COLUMN `comprob` `comprob` VARCHAR(45) NOT NULL ";
    $this->db->simple_query($query);
    $query="ALTER TABLE `casi`  CHANGE COLUMN `status` `status` CHAR(2) NULL DEFAULT NULL";
    $this->db->simple_query($query);
    $query="ALTER TABLE `itcasi`  CHANGE COLUMN `referen` `referen` VARCHAR(100) NULL DEFAULT NULL";
    $this->db->simple_query($query);
    $query="ALTER TABLE `casi`  CHANGE COLUMN `status` `status` CHAR(2) NULL DEFAULT 'C1'";
    $this->db->simple_query($query);
    $query="ALTER TABLE `itcasi`  CHANGE COLUMN `referen` `referen` TEXT NULL DEFAULT NULL ,  CHANGE COLUMN `concepto` `concepto` TEXT NULL DEFAULT NULL AFTER";
    $this->db->simple_query($query);
    $query="ALTER TABLE `cpla`  ADD COLUMN `fcreacion` DATE NULL";
    $this->db->simple_query($query);
    $qeury="ALTER TABLE `reglascont`  CHANGE COLUMN `concepto` `concepto` TEXT NULL DEFAULT NULL";
    $this->db->simple_query($query);
    $query="ALTER TABLE `cpla`  ADD COLUMN `felimina` DATE NULL";
    $this->db->simple_query($query);
    $query="ALTER TABLE `itcasi`  CHANGE COLUMN `referen` `referen` TEXT NULL DEFAULT NULL";
    $this->db->simple_query($query);
    $query="ALTER TABLE `itcasi`  ADD COLUMN `mbanc_id` text NULL DEFAULT NULL";
    $this->db->simple_query($query);


		//./contabilidad/cuentas.php 
		$mSQL="CREATE TABLE `cuentas` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`tipo` varchar(20) DEFAULT NULL,
		`codigo1` varchar(20) DEFAULT NULL,
		`codigo2` varchar(20) DEFAULT NULL,
		`cuenta1` varchar(25) DEFAULT NULL,
		`cuenta2` varchar(25) DEFAULT NULL,
		 PRIMARY KEY (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$this->db->simple_query($mSQL);


		//./contabilidad/casise.php 
		$mSQL="CREATE TABLE `casise` (
		`id` INT(10) NULL AUTO_INCREMENT,
		`ano` CHAR(4) NULL,
		`mes` CHAR(2) NULL,
		PRIMARY KEY (`id`)
		) COLLATE='utf8_general_ci' ENGINE=MyISAM ROW_FORMAT=DEFAULT";
		$this->db->simple_query($mSQL);


		//./contabilidad/reglas.php 
    
  $query="ALTER TABLE `reglascont` CHANGE COLUMN `condicion` `condicion` TEXT NULL DEFAULT NULL";
  $this->db->simple_query($query);
  $query="ALTER TABLE `reglascont`  ADD COLUMN `mbanc_id` TEXT NULL DEFAULT NULL";
  $this->db->simple_query($query);  


		//./contabilidad/bcta.php 
	$query="ALTER TABLE `bcta`  ADD COLUMN `descrip` TEXT NULL DEFAULT NULL";
	$this->db->simple_query($query);
	$query="ALTER TABLE `bcta`  ADD COLUMN `tipo` CHAR(1) NULL DEFAULT 'O'";
	$this->db->simple_query($query);
	$query="ALTER TABLE `bcta`  ADD COLUMN `deuda` DECIMAL(19,2) NULL DEFAULT '0'";
	$this->db->simple_query($query);
	$query="ALTER TABLE `bcta`  ADD COLUMN `saldo` DECIMAL(19,2) NULL DEFAULT '0'";
	$this->db->simple_query($query);


		//./bienes/bi_bienes.php 
		$mSQL="CREATE TABLE IF NOT EXISTS `bi_bienes` (
				`nombre` CHAR(30) NOT NULL COLLATE 'utf8_general_ci',
				`color` CHAR(20) NOT NULL COLLATE 'utf8_general_ci',
				`modelo` CHAR(20) NOT NULL COLLATE 'utf8_general_ci',
				`descrip` CHAR(50) NOT NULL COLLATE 'utf8_general_ci',
				`id` CHAR(8) NOT NULL DEFAULT '' COLLATE 'utf8_general_ci',
				PRIMARY KEY (`id`)
			)
			COLLATE='utf8_general_ci'
			ENGINE=MyISAM
			ROW_FORMAT=DEFAULT";
		$this->db->simple_query($mSQL);


		//./bienes/bi_terreno.php 
		$mSQL="CREATE TABLE IF NOT EXISTS `bi_terreno` (
				`id` CHAR(10) NOT NULL DEFAULT '' COLLATE 'utf8_general_ci',
				`expediente` CHAR(10) NOT NULL DEFAULT '' COLLATE 'utf8_general_ci',
				`est_propietario` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`denominacion` CHAR(100) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`u_agri` CHAR(5) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`u_gana` CHAR(5) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`u_misto` CHAR(5) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`otro_uso` TEXT NULL COLLATE 'utf8_general_ci',
				`municipio` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`direccion` CHAR(200) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`hectarea` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`metros` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`area_const` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`t_plana` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`p_plana` CHAR(4) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`t_splana` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`p_splana` CHAR(4) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`t_pendi` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`p_pendi` CHAR(4) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`t_mpendi` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`p_mpendi` CHAR(4) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`topo_total` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`topo_ptotal` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`permanencia` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`p_permanencia` CHAR(4) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`a_defores` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`p_defores` CHAR(4) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`bosques` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`p_bosques` CHAR(4) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`incultas` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`p_incultas` CHAR(4) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`no_aprove` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`pno_aprove` CHAR(4) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`naturales` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`p_naturales` CHAR(4) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`cultivos` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`p_cultivos` CHAR(4) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`pot_total` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`pot_ptotal` CHAR(4) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`rios` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`manantial` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`canales` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`embalse` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`pozo` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`acued` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`otro_agua` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`c_long` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`c_estan` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`c_material` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`v_interiores` CHAR(200) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`otra_bien` TEXT NULL COLLATE 'utf8_general_ci',
				`linderos` TEXT NULL COLLATE 'utf8_general_ci',
				`estudio_legal` TEXT NULL COLLATE 'utf8_general_ci',
				`fecha_adq` DATE NULL DEFAULT NULL,
				`valor_adq` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`fecha_m` DATE NULL DEFAULT NULL,
				`valor_m` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`mejoras` TEXT NULL COLLATE 'utf8_general_ci',
				`valor_conta` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`avaluo` TEXT NULL COLLATE 'utf8_general_ci',
				`planos` TEXT NULL COLLATE 'utf8_general_ci',
				`preparado` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				PRIMARY KEY (`id`),
				INDEX `expediente` (`expediente`)
			)
			COLLATE='utf8_general_ci'
			ENGINE=MyISAM
			ROW_FORMAT=DEFAULT";
		$this->db->simple_query($mSQL);


		//./bienes/alma.php 
		$mSQL="
		CREATE TABLE `alma` (
		  `codigo` varchar(4) NOT NULL,
		  `descrip` varchar(200) DEFAULT NULL,
		  `uadministra` varchar(4) DEFAULT NULL,
		  `cuenta` varchar(45) DEFAULT NULL,
		  PRIMARY KEY (`codigo`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8
		";
		$this->db->simple_query($mSQL);


		//./bienes/bi_edificio.php 
		$mSQL="CREATE TABLE IF NOT EXISTS `bi_edificio` (
				`id` CHAR(10) NOT NULL DEFAULT '' COLLATE 'utf8_general_ci',
				`expediente` CHAR(10) NOT NULL DEFAULT '' COLLATE 'utf8_general_ci',
				`est_propietario` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`denominacion` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`uso` VARCHAR(150) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`estado` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`municipio` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`direccion` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`area_terre` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`area_ocup` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`num_pisos` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`area_tpisos` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`area_anexa` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`pared_carga` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`madera` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`metalica` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`concreto` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`otro_estruc` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`tierra` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`cemento` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`ladrillo` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`mosaico` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`granito` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`otro_pisos` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`bloques_arci` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`bloques_conc` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`ladrillos` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`p_madera` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`p_metalica` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`otro_pared` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`t_metalico` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`asbesto` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`teja_concreto` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`teja_cana_ar` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`platabanda` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`otro_techo` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`pu_madera` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`pu_metalico` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`sanitarios` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`cocinas` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`agua` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`electri` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`telefono` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`aire_acon` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`ascensores` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`otro_servicios` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`patios` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`jardines` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`estaciona` CHAR(3) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`otro_anexo` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`linderos` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`estudio_legal` VARCHAR(200) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`valor_contable` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`fecha_adqu` DATE NULL DEFAULT NULL,
				`fecha_cont` DATE NULL DEFAULT NULL,
				`valor_adqu` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`avaluo_pro` VARCHAR(100) NULL DEFAULT NULL COMMENT 'para construccion y area de terreno ocupada' COLLATE 'utf8_general_ci',
				`planos` VARCHAR(200) NULL DEFAULT NULL COMMENT 'esquemas y fotografias' COLLATE 'utf8_general_ci',
				`valor_mejoras` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`realizado` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`fecha` DATE NULL DEFAULT NULL,
				PRIMARY KEY (`id`),
				INDEX `expediente` (`expediente`)
			)
			COLLATE='utf8_general_ci'
			ENGINE=MyISAM
			ROW_FORMAT=DEFAULT";
		$this->db->simple_query($mSQL);
                     
		$this->db->simple_query("ALTER TABLE `bi_edificio` CHANGE COLUMN `est_propietario` `est_propietario` TINYTEXT CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NULL DEFAULT NULL  ;");
                     
		$this->db->simple_query("ALTER TABLE `bi_edificio` CHANGE COLUMN `denominacion` `denominacion` TINYTEXT CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NULL DEFAULT NULL  ");
		                 
		$this->db->simple_query("ALTER TABLE `tortuga`.`bi_edificio` CHANGE COLUMN `direccion` `direccion` TINYTEXT CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NULL DEFAULT NULL  ");


		//./bienes/bi_subgrupo.php 
		$mSQL="
		CREATE TABLE `bi_subgrupo` (
		  `grupo` char(2) NOT NULL,
		  `codigo` char(4) NOT NULL,
		  `descrip` varchar(45) DEFAULT NULL,
		  PRIMARY KEY (`codigo`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8
		
		";
		
		$this->db->simple_query($mSQL);


		//./bienes/bi_muebles.php 
		$mSQL="
			CREATE TABLE `bi_muebles` (
			  `id` varchar(8) NOT NULL,
			  `codigo` varchar(20) DEFAULT NULL,
			  `grupo` varchar(4) DEFAULT NULL,
			  `subgrupo` varchar(4) DEFAULT NULL,
			  `seccion` varchar(4) DEFAULT NULL,
			  `numero` varchar(8) DEFAULT NULL,
			  `descrip` tinytext,
			  `alma` varchar(4) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8
		";
		$this->db->simple_query($mSQL);


		//./bienes/bi_moto.php 
		$mSQL="CREATE TABLE IF NOT EXISTS `bi_moto` (
			`id` CHAR(8) NOT NULL DEFAULT '' COLLATE 'utf8_general_ci',
			`expediente` CHAR(8) NOT NULL DEFAULT '' COLLATE 'utf8_general_ci',
			`marca` CHAR(30) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`modelo` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`anio` CHAR(4) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`color` CHAR(30) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`placa` CHAR(12) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`tipo` CHAR(12) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`serial_car` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`serial_motor` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`ubica` CHAR(100) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`depende` CHAR(100) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`fecha` DATE NULL DEFAULT NULL,
			`sistema_e` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`bobina` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`bujias` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`carburador` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`filtro_aire` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`filtro_gaso` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`motor` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`regulador` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`frenos_d` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`frenos_t` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`embrague` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`pulmon` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`bateria` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`cambios` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`memoria` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`gua_c` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`gua_f` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`pedal` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`bomba` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`sistema_r` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`cadena` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`tacometro` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`delantero` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`trasero` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`rin_d` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`rin_t` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`pito` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`sirena` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`reloj_t` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`casco` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`alarma` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`levas` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`bases_l` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`protec_l` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`tapa_acei` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`tubo_esc` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`tapas_lat` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`latoneria` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`guarda_d` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`guarda_t` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`tanque` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`tapa_tan` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`bastones` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`pintura` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`cruce_dd` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`cruce_di` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`cruce_td` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`cruce_ti` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`silvin` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`stop_t` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`cojin` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`retrovisor_d` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`retrovisor_i` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`luces_da` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`luces_db` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`luces_stop` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`luces_cruce_dd` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`luces_cruce_di` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`luces_cruce_td` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`luces_cruce_ti` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`estrobert` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`luces_freno` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`kilo` CHAR(20) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`bat_marca` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`bat_serial` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`estado_moto` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`observa` TEXT NULL COLLATE 'utf8_general_ci',
			`inspector` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`conductor` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`jefe_uv` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			`jefe_depen` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
			PRIMARY KEY (`id`),
			INDEX `expediente` (`expediente`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM
		ROW_FORMAT=DEFAULT";
		$this->db->simple_query($mSQL);


		//./bienes/bi_vehi.php 
		$mSQL="CREATE TABLE IF NOT EXISTS `bi_vehi` (
				`id` CHAR(8) NOT NULL DEFAULT '',
				`expediente` CHAR(8) NOT NULL DEFAULT '' COLLATE 'utf8_general_ci',
				`marca` CHAR(30) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`modelo` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`anio` CHAR(4) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`color` CHAR(30) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`placa` CHAR(12) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`tipo` CHAR(12) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`serial_car` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`serial_motor` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`ubica` CHAR(100) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`depende` CHAR(100) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`fecha` DATE NULL DEFAULT NULL,
				`arranque` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`alternador` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`bobina` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`inyectores` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`cable_distri` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`distri` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`bujias` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`carburador` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`filtro_aire` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`filtro_gaso` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`motor` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`diferencial` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`caja_veloci` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`bomba_frenos` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`bomba_direc` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`bomba_agua` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`bomba_gaso` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`frenos_d` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`frenos_t` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`embrague` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`v_aceite_m` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`v_aceite_c` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`radiador` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`tapas_radia` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`compresor` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`bateria` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`correas` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`carter` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`tren_d` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`delantero_d` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`delantero_i` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`trasero_d` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`trasero_i` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`rin_dd` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`rin_di` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`rin_td` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`rin_ti` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`repuesto` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`rin_repu` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`trian` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`gato` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`llave` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`radio` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`repro` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`corneta` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`pito` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`sirena` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`antena` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`alfombra` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`cables_aux` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`reloj_t` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`alarma` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`techo` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`capo` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`maleta` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`pisos` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`parrilla` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`platinas` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`puerta_dd` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`puerta_di` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`puerta_td` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`puerta_ti` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`puerta_pos` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`guarda_dd` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`guarda_di` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`guarda_td` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`guarda_ti` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`para_del` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`para_tra` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`pintura` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`cruce_dd` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`cruce_di` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`cruce_td` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`cruce_ti` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`ter_stop` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`asiento_dd` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`asiento_di` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`asiento_tra` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`t_techo` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`t_puerta_dd` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`t_puerta_di` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`t_puerta_td` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`t_puerta_ti` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`t_puerta_pos` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`parabrisa` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`v_trasero` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`v_puerta_dd` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`v_puerta_di` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`v_puerta_td` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`v_puerta_ti` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`v_puerta_pos` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`lateral_dd` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`lateral_di` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`lateral_td` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`lateral_ti` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`retrovisor_d` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`retrovisor_i` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`retrovisor_c` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`l_parabrisa_d` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`l_parabrisa_i` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`l_parabrisa_t` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`luces_dda` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`luces_ddb` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`luces_dia` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`luces_dib` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`luces_stop_td` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`luces_stop_ti` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`luces_cruce_dd` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`luces_cruce_di` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`luces_cruce_td` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`luces_cruce_ti` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`luces_retro` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`luces_coc` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`interna` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`emergencia` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`luces_freno` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`bat_marca` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`bat_serial` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`estado_vehi` CHAR(2) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`observa` TEXT NULL COLLATE 'utf8_general_ci',
				`inspector` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`conductor` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`jefe_uv` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				`jefe_depen` CHAR(50) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
				PRIMARY KEY (`id`),
				INDEX `expediente` (`expediente`)
			)
			COLLATE='utf8_general_ci'
			ENGINE=MyISAM
			ROW_FORMAT=DEFAULT";
		$this->db->simple_query($mSQL);


		//./bienes/bi_seccion.php 
		$mSQL="
		CREATE TABLE `bi_seccion` (
		  `grupo` char(2) NOT NULL,
		  `subgrupo` char(4) NOT NULL,
		  `codigo` char(4) NOT NULL,
		  `descrip` varchar(45) DEFAULT NULL,
		  PRIMARY KEY (`codigo`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8
		
		";
		
		$this->db->simple_query($mSQL);


		//./bienes/bi_linea.php 
		$mSQL="
		CREATE TABLE `bi_linea` (
		  `grupo` char(2) NOT NULL,
		  `subgrupo` char(4) NOT NULL,
		  `seccion` char(4) NOT NULL,
		  `codigo` char(4) NOT NULL,
		  `descrip` varchar(45) DEFAULT NULL,
		  PRIMARY KEY (`codigo`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8
		
		";
		
		var_dump($this->db->simple_query($mSQL));


		//./inventario/fotos.php 
		$mSQL='CREATE TABLE IF NOT EXISTS `sinvfot` (
		  `id` int(10) unsigned NOT NULL auto_increment,
		  `codigo` varchar(15) default NULL,
		  `nombre` varchar(50) default NULL,
		  `alto_px` smallint(5) unsigned default NULL,
		  `ancho_px` smallint(6) default NULL,
		  `ruta` varchar(100) default NULL,
		  `comentario` text,
		  `estampa` timestamp NULL default NULL,
		  PRIMARY KEY  (`id`),
		  UNIQUE KEY `id` (`id`),
		  UNIQUE KEY `foto` (`codigo`,`nombre`),
		  KEY `id_2` (`id`,`codigo`)
		) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8';
		$this->db->simple_query($mSQL);
		$mSQL='ALTER TABLE `sinvfot` ADD `sinv_id` INT UNSIGNED NOT NULL AFTER `id`';
		$this->db->simple_query($mSQL);
		$mSQL='ALTER TABLE `sinvfot` ADD INDEX `sinv_id` (`sinv_id`)';
		$this->db->simple_query($mSQL);
		$mSQL='ALTER TABLE `sinvfot` CHANGE `estampa` `estampa` TIMESTAMP NOT NULL';
		$this->db->simple_query($mSQL);
		$mSQL='UPDATE sinvfot AS a JOIN sinv AS b ON a.codigo=b.codigo SET a.sinv_id=b.id';
		$this->db->simple_query($mSQL);
		$mSQL='ALTER TABLE `sinvfot` ADD `principal` VARCHAR(3) NULL';
		$this->db->simple_query($mSQL);


		//./inventario/catalogo.php 
		$mSQL='CREATE TABLE `catalogo` (
		  `id` int(10) unsigned NOT NULL auto_increment,
		  `codigo` varchar(200) default NULL,
		  `nombre` varchar(50) default NULL,
		  `contenido` text,
		  `estampa` timestamp NULL default NULL,
		  PRIMARY KEY  (`id`),
		  UNIQUE KEY `id` (`id`),
		  KEY `id_2` (`id`,`codigo`)
		) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8';
		$this->db->simple_query($mSQL);


		//./inventario/ubica.php 
		$mSQL='ALTER TABLE sinv ADD id INT UNSIGNED NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (id);';


		//./inventario/sinvsant.php 
		$mSQL='ALTER TABLE `sinv` DROP PRIMARY KEY';
		$this->db->simple_query($mSQL);
		$mSQL='ALTER TABLE `sinv` ADD UNIQUE `codigo` (`codigo`)';
		$this->db->simple_query($mSQL);
		$mSQL='ALTER TABLE sinv ADD id INT AUTO_INCREMENT PRIMARY KEY';
		$this->db->simple_query($mSQL);


		//./inventario/sinvactu.php 
		$mSQL='
			CREATE TABLE /*!32312 IF NOT EXISTS*/ `sinvactu` (
  `codigo` varchar(15) NOT NULL default "",
  `descrip` varchar(45) default NULL,
  `clave` varchar(8) default NULL,
  `descrip2` varchar(45) default NULL,
  `antdescrip2` varchar(45) default NULL,
  `grupo` varchar(4) default NULL,
  `costo` decimal(13,2) unsigned default NULL,
  `precio1` decimal(13,2) unsigned default NULL,
  `antcosto` decimal(13,2) unsigned default NULL,
  `antprecio1` decimal(13,2) unsigned default NULL,
  `iva` decimal(6,2) unsigned default NULL,
  `antiva` decimal(6,2) unsigned default NULL,
  `precio2` decimal(13,2) default NULL,
  `precio3` decimal(13,2) default NULL,
  `precio4` decimal(13,2) unsigned default NULL,
  `base1` decimal(13,2) unsigned default NULL,
  `base2` decimal(13,2) default NULL,
  `base3` decimal(13,2) unsigned default NULL,
  `base4` decimal(13,2) unsigned default NULL,
  `margen1` decimal(13,2) unsigned default NULL,
  `margen2` decimal(13,2) unsigned default NULL,
  `margen3` decimal(13,2) unsigned default NULL,
  `margen4` decimal(13,2) unsigned default NULL,
  `antdescrip` varchar(45) default NULL,
  `antclave` varchar(8) default NULL,
  `antgrupo` varchar(4) default NULL,
  `antprecio2` decimal(13,2) unsigned default NULL,
  `antprecio3` decimal(13,2) unsigned default NULL,
  `antprecio4` decimal(13,2) unsigned default NULL,
  `antbase1` decimal(13,2) unsigned default NULL,
  `antbase2` decimal(13,2) unsigned default NULL,
  `antbase3` decimal(13,2) unsigned default NULL,
  `antbase4` decimal(13,2) unsigned default NULL,
  `antmargen1` decimal(13,2) unsigned default NULL,
  `antmargen2` decimal(13,2) unsigned default NULL,
  `antmargen3` decimal(13,2) unsigned default NULL,
  `antmargen4` decimal(13,2) unsigned default NULL,
  PRIMARY KEY  (`codigo`)
)
	';
$this->db->simple_query($mSQL);	


		//./forma11.php 
		$this->db->simple_query("ALTER TABLE `formatos`  ADD COLUMN `forma1` TEXT NULL AFTER `forma`");


		//./reportes.php 
		$mSQL="ALTER TABLE `reportes` ADD `proteo` TEXT NULL";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `reportes` ADD `harbour` TEXT NULL";
		$this->db->simple_query($mSQL);


		//./ingresos/tipo.php 
		$mSQL="CREATE TABLE `tipo` (
		`tipo` char(30) NOT NULL DEFAULT '',
		PRIMARY KEY (`tipo`)
	  ) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$this->db->simple_query($mSQL);


		//./ingresos/contribu.php 
		$query="
		CREATE TABLE `contribu` (
			`codigo` CHAR(6) NULL DEFAULT NULL,
			`nombre` CHAR(100) NULL DEFAULT NULL,
			`rifci` CHAR(13) NULL DEFAULT NULL,
			`nacionali` CHAR(10) NULL DEFAULT NULL,
			`localidad` CHAR(2) NULL DEFAULT NULL,
			`direccion` TEXT NULL DEFAULT NULL,
			`telefono` CHAR(50) NULL DEFAULT NULL,
			PRIMARY KEY (`codigo`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM
		ROW_FORMAT=DEFAULT
		";
		$this->db->simple_query($query);


		//./ingresos/local.php 
		$mSQL="CREATE TABLE `local` (
		`codigo` char(2) NOT NULL DEFAULT '',
		`nombre` char(20) DEFAULT NULL,
		PRIMARY KEY (`codigo`)
	  ) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$this->db->simple_query($mSQL);


		//./ingresos/vehiculo.php 
		$mSQL="CREATE TABLE `vehiculo` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`contribu` char(6) DEFAULT NULL,
		`clase` char(1) DEFAULT NULL,
		`marca` char(10) DEFAULT NULL,
		`tipo` char(10) DEFAULT NULL,
		`modelo` char(10) DEFAULT NULL,
		`color` char(20) DEFAULT NULL,
		`capaci` int(11) DEFAULT NULL,
		`serial_m` char(15) DEFAULT NULL,
		`placa_ant` char(7) DEFAULT NULL,
		`placa_act` char(9) DEFAULT NULL,
		`ano` char(4) DEFAULT NULL,
		`peso` double DEFAULT NULL,
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
		PRIMARY KEY (`id`)
	  ) ENGINE=MyISAM AUTO_INCREMENT=5686 DEFAULT CHARSET=utf8";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `vehiculo`	ADD COLUMN `recibo` INT(11) NULL DEFAULT NULL";
		$this->db->simple_query($mSQL);


		//./ingresos/tipoin.php 
		$mSQL="CREATE TABLE `tipoin` (
		`tipoin` char(30) NOT NULL DEFAULT '',
		PRIMARY KEY (`tipoin`)
	  ) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$this->db->simple_query($mSQL);


		//./ingresos/abonos.php 
		$mSQL="CREATE TABLE `abonos` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`estampa` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (`id`)
		      ) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$this->db->simple_query($mSQL);
		
		$query="
		CREATE TABLE `itabonos` (
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`abono` INT(11) NOT NULL,
			`recibo` INT(11) NULL DEFAULT NULL,
			PRIMARY KEY (`id`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM
		AUTO_INCREMENT=1";
		$this->db->simple_query($mSQL);
		

		$query="
		CREATE TABLE `sfpa` (
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`abono` INT(11) NOT NULL,
			`codmbanc` INT(11) NOT NULL,
			`codbanc` VARCHAR(10) NOT NULL,
			`tipo_doc` CHAR(2) NOT NULL,
			`cheque` TEXT NOT NULL,
			`monto` DECIMAL(19,2) NOT NULL,
			`fecha` DATE NOT NULL,
			`observa` TEXT NOT NULL,
			PRIMARY KEY (`id`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM
		AUTO_INCREMENT=1";
		$this->db->simple_query($mSQL);
		
		



		//./ingresos/ingmbanc.php 
		$query="ALTER TABLE `mbanc`  ADD COLUMN `coding` INT(11) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `ingresos`  CHANGE COLUMN `total` `total` DOUBLE(19,2) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `ingresos` ADD COLUMN `totalch` DOUBLE(19,2) NULL ";
		$this->db->simple_query($query);
		$query="ALTER TABLE `ingresos`  ADD COLUMN `concepto` TEXT NULL";
		$this->db->simple_query($query);
		$query="CREATE TABLE `ingmbanc` (
			`ingreso` INT(11) NOT NULL,
			`codmbanc` INT(11) NOT NULL,
			`numero` INT(11) NOT NULL,
			`codbanc` VARCHAR(10) NOT NULL,
			`tipo_doc` CHAR(2) NOT NULL,
			`cheque` TEXT NOT NULL,
			`monto` DECIMAL(19,2) NOT NULL,
			`fecha` DATE NOT NULL,
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`destino` CHAR(1) NOT NULL,
			`benefi` TEXT NOT NULL,
			`observa` TEXT NOT NULL,
			PRIMARY KEY (`id`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM
		AUTO_INCREMENT=1;
";


		//./ingresos/recibo.php 
		$mSQL="
		CREATE TABLE `recibo` (
			`numero` VARCHAR(10) NOT NULL,
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`fecha` DATE NULL DEFAULT NULL,
			`contribu` CHAR(6) NULL DEFAULT NULL,
			`tipo` CHAR(3) NULL DEFAULT NULL,
			`monto` DECIMAL(19,2) NOT NULL DEFAULT '0.00',
			`observa` TEXT NOT NULL,
			`direccion` TEXT NULL,
			`nombre` VARCHAR(200) NULL DEFAULT NULL,
			`rifci` VARCHAR(13) NULL DEFAULT NULL,
			`nacionali` VARCHAR(10) NULL DEFAULT NULL,
			`telefono` VARCHAR(50) NULL DEFAULT NULL,
			`user` VARCHAR(50) NULL DEFAULT NULL,
			`estampa` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			`patente` INT(11) NULL DEFAULT NULL,
			`inmueble` INT(11) NULL DEFAULT NULL,
			`vehiculo` INT(11) NULL DEFAULT NULL,
			`declaracion` DECIMAL(19,2) NULL DEFAULT '0.00',
			`fexp` DATE NULL DEFAULT NULL,
			`fven` DATE NULL DEFAULT NULL,
			`abono` INT(11) NULL DEFAULT NULL,
			`oper` VARCHAR(50) NOT NULL DEFAULT '',
			`status` CHAR(1) NOT NULL DEFAULT 'P',
			`Column 24` CHAR(1) NOT NULL DEFAULT 'P',
			PRIMARY KEY (`id`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM
		AUTO_INCREMENT=1
		";
		$this->db->simple_query($mSQL);
		
		$query="
		CREATE TABLE `itrecibo` (
			`id_recibo` INT(11) NULL DEFAULT NULL,
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`ano` CHAR(4) NULL DEFAULT NULL,
			`tipo` VARCHAR(20) NULL DEFAULT NULL,
			`nro` INT(11) NULL DEFAULT NULL,
			`descrip` TEXT NULL,
			`monto` DECIMAL(19,2) NULL DEFAULT NULL,
			PRIMARY KEY (`id`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM
		AUTO_INCREMENT=1";
		
		$this->db->simple_query($mSQL);
		$quey="ALTER TABLE `recibo` ADD COLUMN `tasam` DECIMAL(19,2) NOT NULL DEFAULT '0' AFTER `status`";
		$this->db->simple_query($mSQL);
		$quey="ALTER TABLE `recibo` ADD COLUMN `rasonsocial` VARCHAR(50) NOT NULL AFTER `tasam`";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `patente` CHANGE COLUMN `observa` `observa` TEXT NULL DEFAULT NULL AFTER `total`";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `patente` ADD COLUMN `fexpedicion` DATE NULL DEFAULT NULL AFTER `nro`";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `patente` ADD COLUMN `fvencimiento` DATE NULL DEFAULT NULL AFTER `fexpedicion`";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `recibo` CHANGE COLUMN `razonsocial` `razonsocial` VARCHAR(50) NULL AFTER `tasam`";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `recibo` ADD COLUMN `rif` VARCHAR(20) NULL AFTER `razonsocial`";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `recibo` ADD COLUMN `nomfis` VARCHAR(100) NULL AFTER `rif`";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `recibo` ADD COLUMN `efectos` VARCHAR(200) NULL AFTER `nomfis`";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `recibo` ADD COLUMN `efectos2` VARCHAR(200) NULL AFTER `nomfis`";
		$this->db->simple_query($mSQL);	
		$query="ALTER TABLE `recibo` ADD COLUMN `recibo` VARCHAR(20) NULL DEFAULT NULL AFTER `efectos`";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `recibo` ADD COLUMN `solvencia` VARCHAR(20) NULL DEFAULT NULL AFTER `recibo`";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `recibo` ADD COLUMN `concsolv` TEXT NULL DEFAULT NULL AFTER `efectos2`";
		$this->db->simple_query($mSQL);
		$query="alter table recibo add column `patente` INT(11) NULL DEFAULT NULL ";
		$this->db->simple_query($mSQL);
		$query="alter table recibo add column `inmueble` INT(11) NULL DEFAULT NULL";
		$this->db->simple_query($mSQL);
		$query="alter table recibo add column `vehiculo` INT(11) NULL DEFAULT NULL";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `recibo` ADD COLUMN `id` INT NOT NULL AUTO_INCREMENT AFTER `numero`,DROP PRIMARY KEY,ADD PRIMARY KEY (`id`)";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `tingresos`  ADD COLUMN `descrip2` VARCHAR(250) NULL DEFAULT NULL";
		$this->db->simple_query($mSQL);		
		$query="ALTER TABLE `tingresos` ADD COLUMN `codigo2` VARCHAR(20) NULL DEFAULT NULL AFTER `descrip2`";
		$this->db->simple_query($mSQL);		


		//./ingresos/marca.php 
		$mSQL="CREATE TABLE `marca` (
		`marca` char(30) NOT NULL DEFAULT '',
		PRIMARY KEY (`marca`)
		  ) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$this->db->simple_query($mSQL);


		//./ingresos/inmueble.php 
		$mSQL="CREATE TABLE `inmueble` (
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
	  ) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$this->db->simple_query($mSQL);
		
		$query="alter table inmueble add column  `recibo` int(11) DEFAULT NULL";
		$this->db->simple_query($mSQL);
		$query="alter table inmueble add column  `codigo` char(6) DEFAULT NULL";
		$this->db->simple_query($mSQL);
		$query="alter table inmueble add column  `cedula` varchar(50) DEFAULT NULL";
		$this->db->simple_query($mSQL);
		$query="alter table inmueble add column `nombre` text";
		$this->db->simple_query($mSQL);


		//./ingresos/ingmbanc2.php 
		$query="ALTER TABLE `mbanc`  ADD COLUMN `coding` INT(11) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `ingresos`  CHANGE COLUMN `total` `total` DOUBLE(19,2) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `ingresos` ADD COLUMN `totalch` DOUBLE(19,2) NULL ";
		$this->db->simple_query($query);
		$query="ALTER TABLE `ingresos`  ADD COLUMN `concepto` TEXT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `itingresos`  ADD COLUMN `denomi` TEXT NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `itingresos`  ADD COLUMN `bruto` DECIMAL(19,2) NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `itingresos` ADD COLUMN `dcto` DECIMAL(19,2) NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `ingresos`  ADD COLUMN `tbruto` DECIMAL(19,2) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `ingresos`  ADD COLUMN `tdcto` DECIMAL(19,2) NULL DEFAULT NULL ";
		$this->db->simple_query($query);
		$query="ALTER TABLE `ingmbanc` ADD COLUMN `codbanc` VARCHAR(10) NOT NULL ";
		$this->db->simple_query($query);
		$query="ALTER TABLE `ingmbanc` ADD COLUMN `tipo_doc` CHAR(2) NOT NULL    ";
		$this->db->simple_query($query);
		$query="ALTER TABLE `ingmbanc` ADD COLUMN `cheque` TEXT NOT NULL         ";
		$this->db->simple_query($query);
		$query="ALTER TABLE `ingmbanc` ADD COLUMN `monto` DECIMAL(19,2) NOT NULL ";
		$this->db->simple_query($query);
		$query="ALTER TABLE `ingmbanc`  ADD COLUMN `fecha` DATE NOT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `ingresos`  ADD COLUMN `planillas` TEXT NULL DEFAULT NULL ,  ADD COLUMN `ano` VARCHAR(100) NULL DEFAULT NULL,  ADD COLUMN `mes` VARCHAR(100) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `ingresos`  ADD COLUMN `quincena` VARCHAR(100) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `ingresos`  ADD COLUMN `tipod` CHAR(2) NULL DEFAULT 'I'";
		$this->db->simple_query($query);
		$query="CREATE ALGORITHM = UNDEFINED VIEW `v_mbancm` AS SELECT a.multiple,a.codbanc,a.tipo_doc,GROUP_CONCAT(a.cheque) cheque,a.fecha,SUM(a.monto) monto,a.observa,b.numcuent,b.banco,a.benefi FROM mbanc a JOIN banc b ON a.codbanc=b.codbanc WHERE multiple >0 AND status IN ('J2','A2') GROUP BY multiple,tipo_doc,codbanc, fecha,observa ORDER BY multiple DESC ";
		$this->db->simple_query($query);
		$query="ALTER TABLE `ingmbanc`  ADD COLUMN `multiple` INT NOT NULL";
		$this->db->simple_query($query);
		$query="ALTER ALGORITHM = UNDEFINED DEFINER=`datasis`@`localhost` VIEW `v_mbancm` AS select `a`.`multiple` AS `multiple`,`a`.`codbanc` AS `codbanc`,`a`.`tipo_doc` AS `tipo_doc`,group_concat(`a`.`cheque` separator ',') AS `cheque`,date_format(a.`fecha`,'%d/%m/%Y') AS `fecha`,sum(`a`.`monto`) AS `monto`,`a`.`observa` AS `observa`,`b`.`numcuent` AS `numcuent`,`b`.`banco` AS `banco`,`a`.`benefi` AS `benefi` from (`mbanc` `a` join `banc` `b` on((`a`.`codbanc` = `b`.`codbanc`))) where ((`a`.`multiple` > 0) and (`a`.`status` in ('J2','A2'))) group by `a`.`multiple`,`a`.`tipo_doc`,`a`.`codbanc`,`a`.`fecha`,`a`.`observa` order by `a`.`multiple` desc";
		$this->db->simple_query($query);


		//./ingresos/clase.php 
		$mSQL="CREATE TABLE `clase` (
		`codigo` char(1) NOT NULL DEFAULT '',
		`nombre` char(20) DEFAULT NULL,
		`monto` double DEFAULT NULL,
		PRIMARY KEY (`codigo`)
	  ) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$this->db->simple_query($mSQL);


		//./ingresos/tingresos.php 
		$mSQL="CREATE TABLE `tingresos` (
			`codigo` CHAR(2) NOT NULL,
			`descrip` VARCHAR(100) NULL DEFAULT NULL,
			`grupo` CHAR(1) NULL DEFAULT NULL,
			`descripcion` TEXT NULL,
			`titu1` TEXT NULL,
			`titu2` TEXT NULL,
			`codigopres` TEXT NULL,
			`contador` TEXT NULL,
			`prefijo` TEXT NULL,
			`monto` DECIMAL(19,2) NULL DEFAULT '0.00',
			`activo` CHAR(1) NULL DEFAULT 'S',
			PRIMARY KEY (`codigo`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM
		AUTO_INCREMENT=1";
		$this->db->simple_query($mSQL);
		
		$query="ALTER TABLE `tingresos` ADD COLUMN `formato` VARCHAR(20) NULL AFTER `activo`";
		$this->db->simple_query($query);


		//./ingresos/ingresos.php 
		$query="ALTER TABLE `ingresos` ADD COLUMN `recibido` TEXT NULL    ";
		$this->db->simple_query($query);
		$query="ALTER TABLE `ingresos` ADD COLUMN `fpago` VARCHAR(50) NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `ingresos` ADD COLUMN `tipo` VARCHAR(50) NULL ";
		$this->db->simple_query($query);
		$query="ALTER TABLE `ingresos` ADD COLUMN `npago` TEXT NULL       ";
		$this->db->simple_query($query);
		$query="ALTER TABLE `ingresos`  ADD COLUMN `recibo` VARCHAR(50) NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `ingresos`  ADD COLUMN `segun` TEXT NULL";
		$this->db->simple_query($query);


		//./ingresos/negocio.php 
		$mSQL="CREATE TABLE `negocio` (
                    `codigo` char(5) NOT NULL DEFAULT '',
                    `nombre` char(20) DEFAULT NULL,
                    `monto` double DEFAULT NULL,
                    PRIMARY KEY (`codigo`)
                  ) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$this->db->simple_query($mSQL);


		//./ingresos/solvencia.php 
		$mSQL="CREATE TABLE `solvencia` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8_general_ci DEFAULT NULL,
  `cedula` varchar(100) COLLATE utf8_general_ci DEFAULT NULL,
  `rif` varchar(100) COLLATE utf8_general_ci DEFAULT NULL,
  `direccion` varchar(100) COLLATE utf8_general_ci DEFAULT NULL,
  `concepto` varchar(200) COLLATE utf8_general_ci DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `contribu` varchar(6) COLLATE utf8_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci";
		$this->db->simple_query($mSQL);


		//./ingresos/ingpresup.php 
		$query="
		CREATE TABLE `ingpresup` (
			`codigoadm` VARCHAR(12) NOT NULL DEFAULT '',
			`fondo` VARCHAR(20) NOT NULL DEFAULT '',
			`codigopres` VARCHAR(25) NOT NULL DEFAULT '',
			`estimado` DECIMAL(19,2) NULL DEFAULT '0.00',
			`recaudado` DECIMAL(19,2) NULL DEFAULT '0.00',
			`denominacion` TEXT NULL,
			PRIMARY KEY (`codigopres`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM
		ROW_FORMAT=DEFAULT
		";
		$this->db->simple_query($query);
		$query="ALTER TABLE `ingpresup`  ADD COLUMN `cuenta` VARCHAR(25) NULL";
		$this->db->simple_query($query);
		
		$query="ALTER TABLE `ingpresup` ADD COLUMN `tipo` CHAR(1) NULL AFTER `refe`";
		$this->db->simple_query($query);


		//./ingresos/claseo.php 
		$mSQL="CREATE TABLE `claseo` (
		`codigo` char(1) NOT NULL DEFAULT '',
		`nombre` char(20) DEFAULT NULL,
		PRIMARY KEY (`codigo`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$this->db->simple_query($mSQL);


		//./ingresos/patente.php 
		$mSQL="
		CREATE TABLE `patente` (
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`contribu` VARCHAR(6) NOT NULL,
			`tarjeta` CHAR(6) NULL DEFAULT NULL,
			`licencia` CHAR(6) NULL DEFAULT NULL,
			`nombre_pro` CHAR(25) NULL DEFAULT NULL,
			`cedula` CHAR(8) NULL DEFAULT NULL,
			`nacionali` CHAR(10) NULL DEFAULT NULL,
			`razon` CHAR(50) NULL DEFAULT NULL,
			`dir_neg` CHAR(50) NULL DEFAULT NULL,
			`dir_pro` CHAR(50) NULL DEFAULT NULL,
			`telefonos` CHAR(15) NULL DEFAULT NULL,
			`capital` DOUBLE NULL DEFAULT NULL,
			`monto` DOUBLE NULL DEFAULT NULL,
			`fecha_es` DATE NULL DEFAULT NULL,
			`oficio` CHAR(30) NULL DEFAULT NULL,
			`local` CHAR(2) NULL DEFAULT NULL,
			`negocio` CHAR(5) NULL DEFAULT NULL,
			`registrado` CHAR(1) NULL DEFAULT NULL,
			`deuda` DOUBLE NULL DEFAULT NULL,
			`enero` DOUBLE NULL DEFAULT NULL,
			`febrero` DOUBLE NULL DEFAULT NULL,
			`marzo` DOUBLE NULL DEFAULT NULL,
			`abril` DOUBLE NULL DEFAULT NULL,
			`mayo` DOUBLE NULL DEFAULT NULL,
			`junio` DOUBLE NULL DEFAULT NULL,
			`julio` DOUBLE NULL DEFAULT NULL,
			`agosto` DOUBLE NULL DEFAULT NULL,
			`septiembre` DOUBLE NULL DEFAULT NULL,
			`octubre` DOUBLE NULL DEFAULT NULL,
			`noviembre` DOUBLE NULL DEFAULT NULL,
			`diciembre` DOUBLE NULL DEFAULT NULL,
			`deudacan` DOUBLE NULL DEFAULT NULL,
			`total` DOUBLE NULL DEFAULT NULL,
			`observa` CHAR(20) NULL DEFAULT NULL,
			`clase` CHAR(1) NULL DEFAULT NULL,
			`tipo` CHAR(1) NULL DEFAULT NULL,
			`catastro` CHAR(10) NULL DEFAULT NULL,
			`publicidad` CHAR(30) NULL DEFAULT NULL,
			`recibo` INT(11) NULL DEFAULT NULL,
			`declaracion` DECIMAL(19,2) NULL DEFAULT NULL,
			`repre` TEXT NULL,
			`repreced` TEXT NULL,
			`expclasi` TEXT NULL,
			`exphor` TEXT NULL,
			`kardex` TEXT NULL,
			`nro` TEXT NULL,
			PRIMARY KEY (`id`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM
		AUTO_INCREMENT=1;
		";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `patente`  CHANGE COLUMN `repre` `repre` TEXT NULL DEFAULT ''";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `patente`  ADD COLUMN `repreced` TEXT NULL DEFAULT ''";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `patente`  ADD COLUMN `expclasi` TEXT NULL DEFAULT '' AFTER `repreced`,  ADD COLUMN `exphor` TEXT NULL DEFAULT '' AFTER `expclasi`";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `patente`  ADD COLUMN `kardex` TEXT NULL AFTER `exphor`";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `patente` 	ADD COLUMN `actual` DECIMAL(19,2) NULL DEFAULT '0'  ";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `patente` 	ADD COLUMN `ajustado` DECIMAL(19,2) NULL DEFAULT '0'";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `patente` 	ADD COLUMN `neto` DECIMAL(19,2) NULL DEFAULT '0'    ";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `patente` CHANGE COLUMN `observa` `observa` TEXT(20) NULL DEFAULT NULL";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `patente` 	ADD COLUMN `objeto` TEXT NULL AFTER `neto`";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `patente` ADD COLUMN `archivo` TEXT NULL";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `patente` ADD COLUMN `utribu` VARCHAR(50) NULL";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `patente` CHANGE COLUMN `cedula` `cedula` VARCHAR(20) NULL DEFAULT NULL";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `patente` ADD COLUMN `fexpedicion` DATE NULL AFTER `nro`";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `patente`	ADD COLUMN `fvencimiento` DATE NULL AFTER `fexpedicion`";
		$this->db->simple_query($mSQL);
		
		


		//./forma1.php 
		$this->db->simple_query("ALTER TABLE `formatos`  ADD COLUMN `forma1` TEXT NULL AFTER `forma`");


		//./buscar.php 
		$mSQL="CREATE TABLE IF NOT EXISTS `modbus` (
		  `id` int(10) unsigned NOT NULL auto_increment,
		  `uri` varchar(50) NOT NULL default '',
		  `idm` varchar(50) NOT NULL default '',
		  `parametros` text,
		  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM AUTO_INCREMENT=1745 DEFAULT CHARSET=utf8";

		$this->db->simple_query($mSQL);


		//./supervisor/publicidad.php 
		$mSQL="CREATE TABLE `publicidad` (
		  `id` bigint(20) unsigned NOT NULL auto_increment,
		  `archivo` varchar(100) default NULL,
		  `bgcolor` varchar(7) default NULL,
		  `prob` float unsigned default NULL,
		  `estampa` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
		  `descrip` varchar(200) default NULL,
		  PRIMARY KEY  (`id`),
		  UNIQUE KEY `id` (`id`,`archivo`),
		  KEY `id_2` (`id`)
		) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8";
		$this->db->simple_query($mSQL);


		//./supervisor/repomenu.php 
		$mSQL="CREATE TABLE IF NOT EXISTS `intrarepo` (
		  `nombre` varchar(71) NOT NULL default '',
		  `modulo` varchar(10) NOT NULL default '',
		  `titulo` varchar(20) default NULL,
		  `mensaje` varchar(60) default NULL,
		  `activo` char(1) default 'S',
		  PRIMARY KEY  (`nombre`,`modulo`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `intrarepo` CHANGE COLUMN `titulo` `titulo` VARCHAR(100) NULL DEFAULT NULL";
		echo $this->db->simple_query($mSQL);
		$query="ALTER TABLE `intrarepo` CHANGE COLUMN `mensaje` `mensaje` VARCHAR(200) NULL DEFAULT NULL";
		echo $this->db->simple_query($mSQL);


		//./supervisor/subexls.php 
		$mSQL='
			CREATE TABLE /*!32312 IF NOT EXISTS*/ `sinvactu` (
		  `codigo` varchar(15) NOT NULL default "",
		  `descrip` varchar(45) default NULL,
		  `clave` varchar(8) default NULL,
		  `descrip2` varchar(45) default NULL,
		  `antdescrip2` varchar(45) default NULL,
		  `grupo` varchar(4) default NULL,
		  `costo` decimal(13,2) unsigned default NULL,
		  `precio1` decimal(13,2) unsigned default NULL,
		  `antcosto` decimal(13,2) unsigned default NULL,
		  `antprecio1` decimal(13,2) unsigned default NULL,
		  `iva` decimal(6,2) unsigned default NULL,
		  `antiva` decimal(6,2) unsigned default NULL,
		  `precio2` decimal(13,2) default NULL,
		  `precio3` decimal(13,2) default NULL,
		  `precio4` decimal(13,2) unsigned default NULL,
		  `base1` decimal(13,2) unsigned default NULL,
		  `base2` decimal(13,2) default NULL,
		  `base3` decimal(13,2) unsigned default NULL,
		  `base4` decimal(13,2) unsigned default NULL,
		  `margen1` decimal(13,2) unsigned default NULL,
		  `margen2` decimal(13,2) unsigned default NULL,
		  `margen3` decimal(13,2) unsigned default NULL,
		  `margen4` decimal(13,2) unsigned default NULL,
		  `antdescrip` varchar(45) default NULL,
		  `antclave` varchar(8) default NULL,
		  `antgrupo` varchar(4) default NULL,
		  `antprecio2` decimal(13,2) unsigned default NULL,
		  `antprecio3` decimal(13,2) unsigned default NULL,
		  `antprecio4` decimal(13,2) unsigned default NULL,
		  `antbase1` decimal(13,2) unsigned default NULL,
		  `antbase2` decimal(13,2) unsigned default NULL,
		  `antbase3` decimal(13,2) unsigned default NULL,
		  `antbase4` decimal(13,2) unsigned default NULL,
		  `antmargen1` decimal(13,2) unsigned default NULL,
		  `antmargen2` decimal(13,2) unsigned default NULL,
		  `antmargen3` decimal(13,2) unsigned default NULL,
		  `antmargen4` decimal(13,2) unsigned default NULL,
		  PRIMARY KEY  (`codigo`)
		)';
		$this->db->simple_query($mSQL);


		//./supervisor/tiketc.php 
		$mSQL="CREATE TABLE `tiketc` (
		  `id` bigint(20) unsigned NOT NULL auto_increment,
		  `padre` char(1) default NULL,
		  `pertenece` bigint(20) unsigned default NULL,
		  `prioridad` smallint(5) unsigned default NULL,
		  `usuario` varchar(50) default NULL,
		  `contenido` text,
		  `estampa` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
		  `actualizado` timestamp NULL default NULL,
		  `estado` char(1) default 'N',
		  PRIMARY KEY  (`id`),
		  KEY `id` (`id`)
		) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8"; 
		$mSQL2="CREATE TABLE `tiempo` (`hora` INT, `minutos` INT, `id` INT AUTO_INCREMENT, PRIMARY KEY(`id`), INDEX(`id`))";
		$this->db->simple_query($mSQL);


		//./supervisor/menu.php 
		$mSQL="ALTER TABLE `intramenu` ADD COLUMN `orden` TINYINT(4) NULL DEFAULT NULL AFTER `pertenece`";
		echo (int) $this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `intramenu` ADD COLUMN `ancho` INT(10) UNSIGNED NULL DEFAULT '800' AFTER `orden`";
		echo (int) $this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `intramenu` ADD COLUMN `alto`  INT(10) UNSIGNED NULL DEFAULT '600' AFTER `ancho`";
		echo (int) $this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `intrasida`  ADD COLUMN `id` INT(12) UNSIGNED NOT NULL AFTER `usuario`";
		echo (int) $this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `intramenu`  DROP PRIMARY KEY";
		echo (int) $this->db->simple_query($mSQL);
		$mSQL='ALTER TABLE intramenu ADD id INT AUTO_INCREMENT PRIMARY KEY';
		echo (int) $this->db->simple_query($mSQL);
		$mSQL="UPDATE intrasida JOIN intramenu ON intramenu.modulo= intrasida.modulo SET intrasida.id = intramenu.id";
		echo (int) $this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `intrasida`  DROP PRIMARY KEY,  ADD PRIMARY KEY (`usuario`, `id`)";
		echo (int) $this->db->simple_query($mSQL);
		$mSQL="ADD UNIQUE INDEX `modulo` (`modulo`)";
		echo (int) $this->db->simple_query($mSQL);
		
		//$mSQL='ALTER TABLE `intramenu` DROP PRIMARY KEY';
		//$this->db->simple_query($mSQL);
		//$mSQL='ALTER TABLE `intramenu` ADD UNIQUE `modulo` (`modulo`)';
		//$this->db->simple_query($mSQL);



		//./supervisor/tiketp.php 
		$mSQL="CREATE TABLE IF NOT EXISTS `tiketp` (`id` TINYINT AUTO_INCREMENT, `estampa` TIMESTAMP, `fecha` DATE, `codigo` VARCHAR (20),`empresa` VARCHAR (100), `tiket` TEXT,`usuario` VARCHAR (20),`status` VARCHAR (20), `asignacion` VARCHAR (20),`nombre` VARCHAR (50),PRIMARY KEY(`id`)) TYPE = MyISAM";
		$this->db->simple_query($mSQL);


		//./supervisor/usuarios.php 
		$query="ALTER TABLE `usuario`  ADD COLUMN `internet` CHAR(1) NULL DEFAULT 'N'";
		$this->db->simple_query($query);


		//./supervisor/formatos.php 
		$mSQL="CREATE TABLE IF NOT EXISTS `intrarepo` (
		  `nombre` varchar(71) NOT NULL default '',
		  `modulo` varchar(10) NOT NULL default '',
		  `titulo` varchar(20) default NULL,
		  `mensaje` varchar(60) default NULL,
		  `activo` char(1) default 'S',
		  PRIMARY KEY  (`nombre`,`modulo`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$this->db->simple_query($mSQL);
		$this->db->simple_query("ALTER TABLE `formatos`  ADD COLUMN `forma` TEXT NULL ");
		$this->db->simple_query("ALTER TABLE `formatos`  ADD COLUMN `forma1` TEXT NULL ");
		$this->db->simple_query("ALTER TABLE `formatos` 	CHANGE COLUMN `nombre` `nombre` VARCHAR(100) NOT NULL DEFAULT '' FIRST");


		//./supervisor/tiket.php 
		$mSQL="CREATE TABLE `tiket` (
		  `id` bigint(20) unsigned NOT NULL auto_increment,
		  `padre` char(1) default NULL,
		  `pertenece` bigint(20) unsigned default NULL,
		  `prioridad` smallint(5) unsigned default NULL,
		  `usuario` varchar(50) default NULL,
		  `contenido` text,
		  `estampa` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
		  `actualizado` timestamp NULL default NULL,
		  `estado` char(1) default 'N',
		  PRIMARY KEY  (`id`),
		  KEY `id` (`id`)
		) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8";
		$this->db->simple_query($mSQL);


		//./supervisor/muro.php 
		$mSQL="CREATE TABLE IF NOT EXISTS `muro` (
		  `codigo` int(11) NOT NULL auto_increment,
		  `envia` varchar(15) default NULL,
		  `recibe` varchar(15) default NULL,
		  `mensaje` text,
		  `estampa` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
		  PRIMARY KEY  (`codigo`)
		) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8";
		$this->db->simple_query($mSQL);


		//./supervisor/internet.php 
		$mSQL="CREATE TABLE `internet` (
		  `nombre` varchar(20) NOT NULL default '',
		  `lista` text,
		  `descrip` varchar(100) default NULL,
		  PRIMARY KEY  (`nombre`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$this->db->simple_query($mSQL);
		$mSQL="INSERT INTO internet (nombre) VALUES ('IPACEPTADOS')";                        
		$this->db->simple_query($mSQL);
		$mSQL="INSERT INTO internet (nombre) VALUES ('PAGINASNEGADAS')";
		$this->db->simple_query($mSQL);


		//./supervisor/subetxt.php 
		$query="
		CREATE TABLE `concilia` (
			`codbanc` VARCHAR(10) NULL DEFAULT NULL,
			`tipo_doc` CHAR(2) NULL DEFAULT NULL,
			`cheque` VARCHAR(50) NULL DEFAULT NULL,
			`fecha` DATE NULL DEFAULT NULL,
			`observa` TEXT NULL,
			`debe` DECIMAL(19,2) NULL DEFAULT '0.00',
			`haber` DECIMAL(19,2) NULL DEFAULT '0.00',
			`saldo` DECIMAL(19,2) NULL DEFAULT '0.00',
		PRIMARY KEY (`codbanc`, `cheque`, `tipo_doc`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM
		ROW_FORMAT=DEFAULT";
		$this->db->simple_query($query);


		//./supervisor/valores.php 
			$query="ALTER TABLE `valores` CHANGE COLUMN `nombre` `nombre` VARCHAR(100) NOT NULL DEFAULT '' FIRST";
			$this->db->simple_query($query);


		//./supervisor/repotra.php 
		$mSQL="CREATE TABLE `matbar`.`repotra` (`id` TINYINT AUTO_INCREMENT, `estampa` TIMESTAMP, `fecha` DATE, `t1horae` VARCHAR (8),`t2horae` VARCHAR (8), `t1horas` VARCHAR (8),`t2horas` VARCHAR (8),`empresa` VARCHAR (50), `tecnico1` VARCHAR (50), `tecnico2` VARCHAR (50), `tecnico3` VARCHAR (50), `informe` TEXT,`observa` TEXT, `t1tipos` VARCHAR(10),`t2tipos` VARCHAR(10),`t1tipoe` VARCHAR(10),`t2tipoe` VARCHAR(10),`nombre` VARCHAR(60),PRIMARY KEY(`id`)) TYPE = MyISAM";
		$this->db->simple_query($mSQL);


		//./supervisor/bitacora.php 
		$mSQL="CREATE TABLE `bitacora` (
		  `id` bigint(20) unsigned NOT NULL auto_increment,
		  `usuario` varchar(50) default NULL,
		  `nombre` varchar(100) default NULL,
		  `fecha` date default NULL,
		  `hora` time default NULL,
		  `actividad` text,
		  `comentario` text,
		  `revisado` char(1) default 'P',
		  `evaluacion` text,
		  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM AUTO_INCREMENT=524 DEFAULT CHARSET=utf8";
		$this->db->simple_query($mSQL);


		//./supervisor/sucu.php 
		$mSQL="ALTER TABLE `sucu` ADD `url` VARCHAR(200) NULL
		ALTER TABLE `sucu` ADD `prefijo` VARCHAR(3) NULL
		ALTER TABLE `sucu` ADD `proteo` VARCHAR(50) NULL;";
    $this->db->simple_query($mSQL);		


		//./doc/tablas.php 
		$this->db->simple_query("
		CREATE TABLE `doc_campos` (
		  `tabla` varchar(64) NOT NULL,
		  `campo` varchar(64) NOT NULL,
		  `type` varchar(45) DEFAULT NULL,
		  `null` varchar(45) DEFAULT NULL,
		  `key` varchar(45) DEFAULT NULL,
		  `default` varchar(45) DEFAULT NULL,
		  `extra` varchar(45) DEFAULT NULL,
		  PRIMARY KEY (`tabla`,`campo`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8
		");
		
		$this->db->simple_query("
		CREATE TABLE `doc_tablas` (
		  `nombre` varchar(64) NOT NULL,
		  `referen` text,
		  PRIMARY KEY (`nombre`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8
		");
	


		//./formatos.php 
//		$mSQL="ALTER TABLE `formatos` ADD `proteo3` TEXT NULL AFTER `forma`";
//		$this->db->simple_query($mSQL);
//		$mSQL="ALTER TABLE `formatos` ADD `proteo2` TEXT NULL AFTER `forma`";
//		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `formatos` ADD `proteo` TEXT NULL AFTER `forma`";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `formatos` ADD `harbour` TEXT NULL AFTER `proteo`";
		$this->db->simple_query($mSQL);


		//./nomina/asig.php 
		$mSQL="ALTER TABLE asig ADD PRIMARY KEY (codigo);";
		$this->db->simple_query($mSQL);	


		//./nomina/conc.php 
		$mSQL="ALTER TABLE conc ADD PRIMARY KEY (concepto);";
		$this->db->simple_query($mSQL);	
		$mSQL="ALTER TABLE `conc` ADD COLUMN `codigopres` VARCHAR(25) NULL  AFTER `cod_prov`";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `conc` ADD COLUMN `codigoadm` VARCHAR(25) NULL  AFTER `codigopres` ";
		$this->db->simple_query($mSQL);	
		$mSQL="ALTER TABLE `conc` ADD COLUMN `fondo` VARCHAR(25) NULL  AFTER `codigoadm` ";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `conc`  ADD COLUMN `salarial` CHAR(1) NULL DEFAULT 'N'";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `conc`  CHANGE COLUMN `descrip` `descrip` VARCHAR(100) NULL DEFAULT NUL";
		$this->db->simple_query($mSQL);


		//./nomina/noco.php 
			$query="ALTER TABLE `noco` ADD COLUMN `modo` CHAR(1) NULL DEFAULT '1' COMMENT '1 es normal, 2 los montos y partidas los toma a partir de los cargos' AFTER `fondo`";
			$this->db->simple_query($query);


		//./nomina/carg.php 
		$mSQL="ALTER TABLE carg ADD PRIMARY KEY (cargo);";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `carg` ADD COLUMN `codigoadm` VARCHAR(25) NULL DEFAULT NULL AFTER `sueldo`";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `carg` ADD COLUMN `codigopres` VARCHAR(25) NULL DEFAULT NULL AFTER `codigoadm`";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `carg`ADD COLUMN `fondo` VARCHAR(25) NULL DEFAULT NULL AFTER `codigopres`";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `carg` ADD COLUMN `cantidad` INT NULL DEFAULT '0' AFTER `fondo`";
		$this->db->simple_query($mSQL);


		//./nomina/nomi.php 
		$query="ALTER TABLE `asignomi`  CHANGE COLUMN `codigopres` `codigopres` VARCHAR(25) NULL DEFAULT NULL  AFTER `fondo`";
		$this->db->simple_query($query);

		$query="
				CREATE TABLE `dpresu04` (
			`row_id` INT(11) NOT NULL AUTO_INCREMENT,
			`codpre` CHAR(29) NULL DEFAULT NULL,
			`secuen` INT(11) NULL DEFAULT NULL,
			`nromov` CHAR(8) NULL DEFAULT NULL,
			`codmov` CHAR(2) NULL DEFAULT NULL,
			`nordpag` CHAR(8) NULL DEFAULT NULL,
			`fecmov` DATE NULL DEFAULT NULL,
			`concep` CHAR(60) NULL DEFAULT NULL,
			`modpre` DOUBLE NULL DEFAULT NULL,
			`crdact` DOUBLE NULL DEFAULT NULL,
			`moncomp` DOUBLE NULL DEFAULT NULL,
			`saldisp` DOUBLE NULL DEFAULT NULL,
			`moncaus` DOUBLE NULL DEFAULT NULL,
			`monpago` DOUBLE NULL DEFAULT NULL,
			`seccomp` INT(11) NULL DEFAULT NULL,
			`seccaus` INT(11) NULL DEFAULT NULL,
			`codusu` CHAR(4) NULL DEFAULT NULL,
			`regfec` DATE NULL DEFAULT NULL,
			`reghor` CHAR(8) NULL DEFAULT NULL,
			PRIMARY KEY (`row_id`)
		)
		ENGINE=MyISAM
		ROW_FORMAT=DEFAULT
		AUTO_INCREMENT=1";

		$this->db->simple_query($query);

		$query="ALTER TABLE `nomi`  ADD COLUMN `compromiso` VARCHAR(12) NULL ";
		$this->db->simple_query($query);

		$query="ALTER TABLE `nomi` 	ADD COLUMN `otros` DECIMAL(19,2) NULL DEFAULT '";
		$this->db->simple_query($query);

		$query="
		CREATE TABLE `otrosnomi` (
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`numero` INT(11) NOT NULL DEFAULT '0',
			`nomina` INT(11) NULL DEFAULT NULL,
			`codigoadm` VARCHAR(15) NULL DEFAULT NULL,
			`fondo` VARCHAR(20) NULL DEFAULT NULL,
			`codigopres` VARCHAR(17) NULL DEFAULT NULL,
			`ordinal` CHAR(3) NULL DEFAULT NULL,
			`cod_prov` VARCHAR(5) NULL DEFAULT NULL,
			`monto` DECIMAL(19,2) NULL DEFAULT NULL,
			`opago` INT(1) NULL DEFAULT NULL,
			`status` CHAR(1) NULL DEFAULT 'P',
			`nombre` VARCHAR(100) NULL DEFAULT NULL,
			`mbanc` INT(11) NULL DEFAULT NULL,
			PRIMARY KEY (`id`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM;
		";
		$this->db->simple_query($query);


		//./nomina/pers.php 
	  $query="ALTER TABLE `pers` ADD COLUMN `rif` VARCHAR(45) NULL  AFTER `horario` ";
	  $this->db->simple_query($query);
	  $query="ALTER TABLE `pers`  CHANGE COLUMN `cuenta` `cuenta` VARCHAR(20) NULL DEFAULT NULL";
	  $this->db->simple_query($query);
	  $query="ALTER TABLE `pers` ADD COLUMN `nombre2` VARCHAR(30) NULL DEFAULT NULL AFTER `nombre`";
	  $this->db->simple_query($query);
	  $query="ALTER TABLE `pers` ADD COLUMN `apellido2` VARCHAR(30) NULL DEFAULT NULL AFTER `apellido`";
	  $this->db->simple_query($query);
	  $query="ALTER TABLE `pers` ADD COLUMN `codigoadm` VARCHAR(25) NULL DEFAULT NULL";
	  $this->db->simple_query($query);
	  $query="ALTER TABLE `pers` ADD COLUMN `fondo` VARCHAR(25) NULL DEFAULT NULL";
	  $this->db->simple_query($query);
	  $query="ALTER TABLE `pers`  ADD COLUMN `codigopres` VARCHAR(25) NULL DEFAULT NULL";
	  $this->db->simple_query($query);
	  $query="ALTER TABLE `pers`  ADD COLUMN `vari7` VARCHAR(50) NULL DEFAULT '0.00' AFTER `vari6`";
	   $this->db->simple_query($query);
	  $query="ALTER TABLE `pers`   ADD COLUMN `vari8` VARCHAR(50) NULL DEFAULT '0.00' AFTER `vari7`;";
	   $this->db->simple_query($query);


		//./nomina/prenom.php 
		$query="ALTER TABLE `prenom` ADD COLUMN `modo` CHAR(1) NULL DEFAULT NULL AFTER `pprome`";
		$this->db->simple_query($query);


		//./nomina/aumentosueldo.php 
		$mSQL="ALTER TABLE ausu ADD PRIMARY KEY (codigo,fecha);";
		$this->db->simple_query($mSQL);	


		//./nomina/notabu.php 
		$query="CREATE TABLE `notabu` (
			`contrato` CHAR(5) NOT NULL DEFAULT '',
			`ano` DECIMAL(2,0) NOT NULL DEFAULT '0',
			`mes` DECIMAL(2,0) NOT NULL DEFAULT '0',
			`dia` DECIMAL(2,0) NOT NULL DEFAULT '0',
			`preaviso` DECIMAL(5,2) NULL DEFAULT '0.00',
			`vacacion` DECIMAL(5,2) NULL DEFAULT '0.00',
			`bonovaca` DECIMAL(5,2) NULL DEFAULT '0.00',
			`antiguedad` DECIMAL(5,2) NULL DEFAULT '0.00',
			`utilidades` DECIMAL(5,2) NULL DEFAULT '0.00',
			`prima` DECIMAL(2,0) NULL DEFAULT '0',
			PRIMARY KEY (`contrato`, `ano`, `mes`, `dia`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM
		ROW_FORMAT=DEFAULT";
		$this->db->simple_query($query);


		//./tesoreria/banc.php 
		$query="ALTER TABLE `banc`  ADD COLUMN `fondo` VARCHAR(25) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `banc`  CHANGE COLUMN `banco` `banco` VARCHAR(200) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `mbanc`  CHANGE COLUMN `codbanc` `codbanc` VARCHAR(10) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `banc`  CHANGE COLUMN `cuenta` `cuenta` VARCHAR(25) NULL DEFAULT NUL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `banc` ADD COLUMN `cuentaac` VARCHAR(25) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `banc`  ADD COLUMN `refe` TEXT NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `banc`  ADD COLUMN `intervenido` TEXT NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `banc`  ADD COLUMN `fondo2` VARCHAR(25) NULL DEFAULT NULL AFTER `fondo`";
		$this->db->simple_query($query);


		//./tesoreria/desem.php 
                $query="ALTER TABLE `pades` CHANGE COLUMN `pago` `pago` VARCHAR(12) NOT NULL  ";
                $this->db->simple_query($query);
                $query="ALTER TABLE `riva`  CHANGE COLUMN `nrocomp` `nrocomp` VARCHAR(8) NOT NULL FIRST";
                $this->db->simple_query($query);
                $query="ALTER TABLE `riva`  DROP PRIMARY KEY";
                $this->db->simple_query($query);
                $query="ALTER TABLE `riva`  ADD COLUMN `id` INT(11) NOT NULL AUTO_INCREMENT AFTER `nrocomp`,  ADD PRIMARY KEY (`id`)";
                $this->db->simple_query($query);

                //repara tablas
                $query="ALTER TABLE `anoprox`  COLLATE='utf8_general_ci',  CHANGE COLUMN `numero` `numero` VARCHAR(9) NOT NULL DEFAULT '' FIRST,  CHANGE COLUMN `uejecuta` `uejecuta` CHAR(4) NULL DEFAULT NULL AFTER `fecha`,  CHANGE COLUMN `uadministra` `uadministra` CHAR(4) NULL DEFAULT NULL AFTER `uejecuta`,  CHANGE COLUMN `concepto` `concepto` TINYTEXT NULL AFTER `uadministra`,  CHANGE COLUMN `responsable` `responsable` VARCHAR(250) NULL DEFAULT NULL AFTER `concepto`,  CHANGE COLUMN `status` `status` CHAR(2) NULL DEFAULT NULL AFTER `responsable`,  CHANGE COLUMN `usuario` `usuario` VARCHAR(12) NULL DEFAULT NULL COMMENT 'aa' AFTER `status`";
                $this->db->simple_query($query);

                $query="ALTER TABLE `asignomi`  COLLATE='utf8_general_ci',  CHANGE COLUMN `codigoadm` `codigoadm` VARCHAR(15) NULL DEFAULT NULL AFTER `numero`,  CHANGE COLUMN `fondo` `fondo` VARCHAR(20) NULL DEFAULT NULL AFTER `codigoadm`,  CHANGE COLUMN `codigopres` `codigopres` VARCHAR(17) NULL DEFAULT NULL AFTER `fondo`,  CHANGE COLUMN `ordinal` `ordinal` CHAR(3) NULL DEFAULT NULL AFTER `codigopres`";
                $this->db->simple_query($query);
                $query="ALTER TABLE `bcta`  COLLATE='utf8_general_ci',  CHANGE COLUMN `denominacion` `denominacion` VARCHAR(200) NULL DEFAULT NULL AFTER `codigo`;";
                $this->db->simple_query($query);
                $query="ALTER TABLE `desem` ADD COLUMN `tcrs` DECIMAL(19,2) NULL AFTER `id`            ";
                $this->db->simple_query($query);
                $query="ALTER TABLE `desem` ADD COLUMN `ttimbre` DECIMAL(19,2) NULL AFTER `tcrs`       ";
                $this->db->simple_query($query);
                $query="ALTER TABLE `desem` ADD COLUMN `tmunicipal` DECIMAL(19,2) NULL AFTER `ttimbre` ";
                $this->db->simple_query($query);
                $query="ALTER TABLE `desem` ADD COLUMN `tislr` DECIMAL(19,2) NULL AFTER `tmunicipal`   ";
                $this->db->simple_query($query);
                $query="ALTER TABLE `desem` ADD COLUMN `triva` DECIMAL(19,2) NULL AFTER `tislr`         ";
                $this->db->simple_query($query);
                $query="ALTER TABLE `desem`  ADD COLUMN `total2` DECIMAL(19,2) NULL";
                $this->db->simple_query($query);
                $query="ALTER TABLE `mbanc`  ADD INDEX `desem` (`desem`)";
                $this->db->simple_query($query);
                $query="ALTER TABLE `desem`  ADD COLUMN `otrasrete` DECIMAL(19,2) NULL DEFAULT 0";
                $this->db->simple_query($query);
                $query="ALTER TABLE `mbanc`  ADD COLUMN `cheque2` VARCHAR(50) NULL DEFAULT NULL";
                $this->db->simple_query($query);


		//./tesoreria/entregach.php 
			$query="ALTER TABLE `mbanc` ADD COLUMN `estampaentrega` DATETIME NULL DEFAULT NULL AFTER `observacaj`";
			$this->db->simple_query($query);


		//./tesoreria/bancse.php 
		$mSQL="CREATE TABLE `bancse` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `codbanc` varchar(5) NOT NULL DEFAULT '',
                `mes` char(2) NOT NULL DEFAULT '',
                PRIMARY KEY (`id`)
              ) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$this->db->simple_query($mSQL);
		


		//./tesoreria/chequebus2.php 
		$query="ALTER TABLE `mbanc` ADD COLUMN `observacaj` TEXT NULL DEFAULT NULL	";
		$this->db->simple_query($query);


		//./tesoreria/mbancnoc.php 
		$this->db->simple_query("
		CREATE TABLE `mbancnoc` (
		`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		`codbanc` VARCHAR(10) NULL DEFAULT NULL,
		`tipo_doc` CHAR(2) NULL DEFAULT NULL,
		`cheque` TEXT NULL,
		`fecha` DATE NOT NULL DEFAULT '0000-00-00',
		`monto` DECIMAL(17,2) NULL DEFAULT NULL,
		`observa` TEXT NULL,
		`status` CHAR(2) NULL DEFAULT NULL,
		`usuario` VARCHAR(4) NULL DEFAULT NULL,
		`estampa` TIMESTAMP NULL DEFAULT NULL,
		`concilia` CHAR(1) NULL DEFAULT NULL,
		`fconcilia` DATE NULL DEFAULT NULL,
		PRIMARY KEY (`id`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM
		ROW_FORMAT=DEFAULT
		AUTO_INCREMENT=1");
		


		//./tesoreria/mbanc.php 
		$this->db->simple_query("ALTER TABLE `mbanc` CHANGE COLUMN `benefi` `benefi` VARCHAR(100) NULL ");
		$this->db->simple_query("ALTER TABLE `mbanc` ADD COLUMN `liable` CHAR(1) NULL DEFAULT 'S'");
		$this->db->simple_query("ALTER TABLE `mbanc` ADD COLUMN `fliable` DATE NULL");
		$this->db->simple_query("ALTER TABLE `mbanc` CHANGE COLUMN `cheque` `cheque` TEXT NULL DEFAULT NULL");
        $this->db->simple_query("ALTER TABLE `mbanc` ADD COLUMN `staing` CHAR(1) NULL DEFAULT NULL");
		$this->db->simple_query("ALTER TABLE `mbanc` ADD COLUMN `observa2` TEXT NULL");
		$this->db->simple_query("ALTER TABLE `mbanc` ADD COLUMN `fecha2` DATE NULL DEFAULT NULL");
		$this->db->simple_query("ALTER TABLE `mbanc` ADD COLUMN `multiple` INT NULL DEFAULT NULL");
		$this->db->simple_query("
		CREATE TABLE `mbancm` (
			`numero` INT(10) NOT NULL AUTO_INCREMENT,
			PRIMARY KEY (`numero`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM
		ROW_FORMAT=DEFAULT
		AUTO_INCREMENT=1");

		$this->db->simple_query("ALTER TABLE `mbancm`  ADD COLUMN `usuario` VARCHAR(50) NULL AFTER `numero`,  ADD COLUMN `fecha` TIMESTAMP NULL");
		$this->db->simple_query("ALTER TABLE `mbanc`  ADD COLUMN `caduco` CHAR(1) NULL DEFAULT 'N'");
		$this->db->simple_query("ALTER TABLE `mbanc`  CHANGE COLUMN `concilia` `concilia` CHAR(1) NULL DEFAULT 'N'");
		$this->db->simple_query("ALTER TABLE `mbanc`  ADD COLUMN `pcodbanc` VARCHAR(10) NULL DEFAULT NULL");
		$this->db->simple_query("ALTER TABLE `mbanc`  ADD COLUMN `deid` INT NULL DEFAULT NULL");
		$this->db->simple_query("ALTER TABLE `mbanc`  ADD COLUMN `paid` INT(11) NULL DEFAULT NULL");
		$this->db->simple_query("ALTER TABLE `mbanc`  ADD COLUMN `coding` INT(11) NULL DEFAULT NULL");


		//./tesoreria/chequebus.php 
		$query="ALTER TABLE `mbanc` ADD COLUMN `observacaj` TEXT NULL DEFAULT NULL	";
		$this->db->simple_query($query);


		//./tesoreria/relch.php 
		$query="ALTER TABLE `relch`  ADD COLUMN `fondo` VARCHAR(25) NULL DEFAULT NULL";
		$this->db->simple_query($query);
Archivo no existe
Archivo no existe
Archivo no existe


		//./tesoreria/tban.php 
		$query="ALTER TABLE `tban`  ADD COLUMN `formacheque` VARCHAR(25) NULL DEFAULT 'CHEQUE' AFTER `formaca`;";
		$this->db->simple_query($query);
		echo "Campo para formato agregado";


		//./tesoreria/sbanc.php 
		$query="CREATE TABLE `sbanc` (
			`codbanc` VARCHAR(5) NOT NULL DEFAULT '',
			`ano` CHAR(4) NULL DEFAULT NULL,
			`mes` CHAR(2) NULL DEFAULT NULL,
			`saldo` DECIMAL(19,2) NULL DEFAULT NULL,
			PRIMARY KEY (`codbanc`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM
		ROW_FORMAT=DEFAULT
		";
		$this->db->simple_query($query);

		$query="ALTER TABLE `sbanc`  DROP PRIMARY KEY,  ADD PRIMARY KEY (`codbanc`, `ano`, `mes`)";
		$this->db->simple_query($query);
		
		$query="ALTER TABLE `sbanc` ADD COLUMN `fecha` DATE NULL DEFAULT NULL ";
		$this->db->simple_query($query);
		
		$query="ALTER TABLE `sbanc` DROP PRIMARY KEY,	ADD PRIMARY KEY (`codbanc`, `fecha`)";
		$this->db->simple_query($query);


		//./desarrollo.php 
		$crud.="\t\t".'$mSQL="'.$row['Create Table'].'";'."\n";
        $crud.="\t\t".'$this->db->simple_query($mSQL);'."\n";


		//./vivienda/ccomunal.php 
		$mSQL="CREATE TABLE `ccomunal` (
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`nombre` VARCHAR(255) NULL DEFAULT NULL,
			PRIMARY KEY (`id`)
		)
		COLLATE='utf32_general_ci'
		ENGINE=MyISAM;";
		$this->db->simple_query($mSQL);


		//./vivienda/ocivh.php 
		$mSQL="CREATE TABLE `ocivh` (
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`nombre` VARCHAR(255) NULL DEFAULT NULL,
			PRIMARY KEY (`id`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM;
		";
		$this->db->simple_query($mSQL);
	}
}
