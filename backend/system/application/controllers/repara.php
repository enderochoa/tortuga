<?php
class Repara extends Controller{


function Repara(){
		parent::Controller();
	}

	function index(){
	  
    $query="DROP PROCEDURE IF EXISTS `sp_view_pres`";
    if($this->db->query($query))echo "drop sp_view_pres</br>";
	  
    $query="CREATE PROCEDURE `sp_view_pres`()  LANGUAGE SQL  NOT DETERMINISTIC  CONTAINS SQL  SQL SECURITY DEFINER  COMMENT '' BEGIN
DROP TABLE IF EXISTS view_pres;
CREATE TABLE view_pres
SELECT asig.*,e.denominacion denoadm,f.descrip denofondo,'' nombre,asig.asignacion ccomprometido,asig.asignacion ccausado,asig.asignacion copago,asig.asignacion cpagado FROM ( 
    SELECT '2010-01-01' fecha,'' des,'' observa,'' cod_prov,0 numero,'' status,'Asignacion' modo,codigoadm, fondo, codigopres,ordinal,NULL faudis,NULL ftrasla, NULL fcomprome,NULL fcausado,NULL fopago, NULL fpagado, NULL frendi,0 comprometido, 0 causado, 0 opago, 0 pagado, 0 aumento, 0 disminucion, 0 traslados,asignacion,denominacion denopart 
    FROM ordinal 
    UNION ALL 
    SELECT '2010-01-01' fecha,'' des,'' observa,'' cod_prov,0 numero,'' status,'Asignacion' modo,codigoadm, tipo , codigopres,'' ordinal,NULL faudis,NULL ftrasla, NULL fcomprome,NULL fcausado,NULL fopago, NULL fpagado, NULL frendi,0 comprometido, 0 causado, 0 opago, 0 pagado, 0 aumento, 0 disminucion, 0 traslados,asignacion,denominacion denopart 
    FROM presupuesto a JOIN ppla ON codigo=codigopres WHERE ppla.movimiento='S' AND (SELECT COUNT(*) FROM ordinal c WHERE a.codigoadm=c.codigoadm AND a.tipo=c.fondo AND a.codigopres=c.codigopres)=0
  )asig JOIN estruadm e ON e.codigo=asig.codigoadm JOIN fondo f ON f.fondo= asig.fondo 
UNION ALL
SELECT todo.fecha,todo.des,todo.observa,todo.cod_prov,todo.numero,todo.status,todo.modo,todo.codigoadm,todo.fondo,todo.codigopres,todo.ordinal,todo.faudis,todo.ftrasla,todo.fcomprome,todo.fcausado,todo.fopago,todo.fpagado,todo.frendi,round(todo.comprometido,2) comprometido,round(todo.causado,2)causado,round(todo.opago,2) opago,round(todo.pagado,2) pagado,round(todo.aumento,2)aumento,round(todo.disminucion,2)disminucion,round(todo.traslados,2) traslados,round(todo.asignacion,2)asignacion,todo.denopart,e.denominacion denoadm,f.descrip denofondo,g.nombre,round((todo.aumento-todo.disminucion+todo.traslados-todo.comprometido),2) ccomprometido,round((todo.aumento-todo.disminucion+todo.traslados-todo.causado),2) ccausado,round((todo.aumento-todo.disminucion+todo.traslados-todo.opago),2)copago,round((todo.aumento-todo.disminucion+todo.traslados-todo.pagado),2) cpagado FROM (
  SELECT con.*,0 asignacion,c.denominacion denopart FROM
  (
    SELECT fecha,'' des,observa,cod_prov,a.numero,status,'Obra' modo,a.codigoadm, a.fondo,a.codigopres,a.ordinal,NULL faudis,NULL ftrasla,fcomprome,NULL fcausado,NULL fopago,NULL fpagado,NULL frendi,
    (monto-pagoviejo) as comprometido, 0 causado, 0 opago, 0 pagado, 0 aumento, 0 disminucion, 0 traslados 
    FROM obra a 
    WHERE a.status  IN ('O2','O4')  AND ordinal<>''
    
    UNION ALL
  
    SELECT b.fecha,'',b.observa,b.cod_prov,b.numero,b.status,'OP. Obra',a.codigoadm, a.fondo,a.codigopres,a.ordinal,NULL faudis,NULL ftrasla,NULL fcomprome, fopago fcausado,fopago,NULL fpagado,NULL frendi,
    0 as comprometido, (IF(b.status='OX',-1*(b.total2-b.amortiza),b.total2-b.amortiza)) causado, (IF(b.status='OX',-1*(b.total2-b.amortiza),b.total2-b.amortiza)) opago, ((b.total2-b.amortiza)*(b.status='O3')) pagado, 0 aumento, 0 disminucion, 0 traslados  
    FROM obra a JOIN odirect b ON a.numero=b.obr
    WHERE b.status  IN ('O2','OX','OY','O3')  AND ordinal<>''  
    

    UNION ALL
    
    SELECT b.fecha,'',b.descrip,'',a.numero,b.status,'Nomina',a.codigoadm, a.fondo,a.codigopres,a.ordinal,NULL faudis,NULL ftrasla,fcomprome, NULL fcausado,NULL fopago, NULL fpagado,NULL frendi,
    (monto) as comprometido, 0 causado, 0 opago, 0 pagado, 0 aumento, 0 disminucion, 0 traslados  
    FROM asignomi a 
    JOIN nomi b ON a.numero = b.numero
    WHERE b.status  IN ('C','D','O','E') AND ordinal<>''
    
    
    UNION ALL
        
    SELECT b.fecha,a.descripcion,b.observa,b.cod_prov,a.numero,status,'OP. Directo',a.codigoadm, a.fondo,a.partida,a.ordinal, NULL faudis,NULL ftrasla,NULL fcomprome,fopago fcausado,fopago,fpagado, NULL frendi,
    0 as comprometido, (IF(status='KX',-1*a.importe,a.importe)) causado, (IF(status='KX',-1*a.importe,a.importe)) opago, (IF(status='K3',a.importe,0)) pagado, 0 aumento, 0 disminucion, 0 traslados  
    FROM itodirect a JOIN odirect b ON a.numero=b.numero 
    WHERE b.status IN ('K2','KX','KY','K3') AND ordinal<>'' 
    
    
    UNION ALL

    SELECT b.fecha,a.descripcion,b.observa,cod_prov,a.numero,status,'Compra',b.estadmin, b.fondo,a.partida,a.ordinal, NULL faudis,NULL ftrasla,fcomprome,IF(status NOT IN ('C'),fcausado,NULL) fcausado,NULL fopago, NULL fpagado, NULL frendi,
    (IF(status='X',-1*a.importe,a.importe)) as comprometido, (IF(status='X',-1*a.importe,IF(status IN ('T','O','E'),a.importe,0))) causado, 0 opago, 0 pagado, 0 aumento, 0 disminucion, 0 traslados  
    FROM itocompra a JOIN ocompra b ON a.numero=b.numero 
    WHERE b.status IN ('C','T','O','E','X','Y') AND ordinal<>'' 
    
    
    UNION ALL
    
    SELECT b.fecha,a.descripcion,b.observa,cod_prov,a.numero,status,'Compra',b.estadmin, b.fondo,a.partida,a.ordinal, NULL faudis,NULL ftrasla,fcomprome,IF(status NOT IN ('C'),fcausado,NULL) fcausado,NULL fopago, NULL fpagado, NULL frendi,
    SUM((IF(status='X',-1*a.importe*a.iva/100,a.importe*a.iva/100))) as comprometido, SUM((IF(status='X',-1*a.importe*a.iva/100,IF(status IN ('T','O','E'),a.importe*a.iva/100,0)))) causado, 0 opago, 0 pagado, 0 aumento, 0 disminucion, 0 traslados  
    FROM itocompra a JOIN ocompra b ON a.numero=b.numero 
    WHERE b.status IN ('C','T','O','E','X','Y') AND (SELECT asignacion FROM presupuesto d WHERE d.codigoadm=b.estadmin AND d.tipo=b.fondo AND d.codigopres=(SELECT valor FROM valores WHERE nombre='PARTIDAIVA') )=0 AND ordinal<>'' 
    GROUP BY a.numero
    

    UNION ALL
    
    SELECT b.fecha,a.descripcion,b.observa,cod_prov,a.numero,status,'Compra',b.estadmin, b.fondo, (SELECT valor FROM valores WHERE nombre='PARTIDAIVA') partida,a.ordinal, NULL faudis,NULL ftrasla,fcomprome,IF(status NOT IN ('C'),fcausado,NULL) fcausado,NULL fopago, NULL fpagado, NULL frendi,
    SUM((IF(status='X',-1*a.importe*a.iva/100,a.importe*a.iva/100))) as comprometido, SUM((IF(status='X',-1*a.importe*a.iva/100,IF(status IN ('T','O','E'),a.importe*a.iva/100,0)))) causado, 0 opago, 0 pagado, 0 aumento, 0 disminucion, 0 traslados  
    FROM itocompra a JOIN ocompra b ON a.numero=b.numero 
    WHERE b.status IN ('C','T','O','E','X','Y') AND (SELECT asignacion FROM presupuesto d WHERE d.codigoadm=b.estadmin AND d.tipo=b.fondo AND d.codigopres=(SELECT valor FROM valores WHERE nombre='PARTIDAIVA') )>0 AND ordinal<>'' 
    GROUP BY a.numero
    
    UNION ALL
    
    SELECT d.fecha,a.descripcion,d.observa,d.cod_prov,d.numero,d.status,'OP. Compra',b.estadmin, b.fondo, (SELECT valor FROM valores WHERE nombre='PARTIDAIVA') partida,a.ordinal, NULL faudis,NULL ftrasla,NULL fcomprome,NULL fcausado,fopago, IF(d.status IN ('F3'),fpagado,NULL) fpagado, NULL frendi,
    0 as comprometido,0 causado,  SUM(a.importe*a.iva/100) opago, SUM(IF(d.status='F3',(a.importe*a.iva/100),0)) pagado, 0 aumento, 0 disminucion, 0 traslados  
    FROM itocompra a JOIN ocompra b ON a.numero=b.numero JOIN pacom c ON b.numero = c.compra JOIN odirect d ON c.pago = d.numero
    WHERE d.status IN ('F2','F3') AND (SELECT asignacion FROM presupuesto d WHERE d.codigoadm=b.estadmin AND d.tipo=b.fondo AND d.codigopres=(SELECT valor FROM valores WHERE nombre='PARTIDAIVA') )>0 AND ordinal<>'' 
    GROUP BY a.numero
    
    UNION ALL
    
    SELECT d.fecha,a.descripcion,d.observa,d.cod_prov,d.numero,d.status,'OP. Compra',b.estadmin, b.fondo, partida,a.ordinal, NULL faudis,NULL ftrasla,NULL fcomprome,NULL fcausado,fopago, IF(d.status IN ('F3'),fpagado,NULL) fpagado, NULL frendi,
    0 as comprometido,0 causado,  SUM(a.importe*a.iva/100) opago, SUM(IF(d.status='F3',(a.importe*a.iva/100),0)) pagado, 0 aumento, 0 disminucion, 0 traslados  
    FROM itocompra a JOIN ocompra b ON a.numero=b.numero JOIN pacom c ON b.numero = c.compra JOIN odirect d ON c.pago = d.numero
    WHERE d.status IN ('F2','F3') AND (SELECT asignacion FROM presupuesto d WHERE d.codigoadm=b.estadmin AND d.tipo=b.fondo AND d.codigopres=(SELECT valor FROM valores WHERE nombre='PARTIDAIVA'))=0 AND ordinal<>'' 
    GROUP BY a.numero
    
    UNION ALL
    
    SELECT d.fecha,a.descripcion,d.observa,d.cod_prov,d.numero,d.status,'OP. Compra',b.estadmin, b.fondo, a.partida,a.ordinal, NULL faudis,NULL ftrasla,NULL fcomprome,NULL fcausado,fopago, IF(d.status IN ('F3'),fpagado,NULL) fpagado, NULL frendi,
    0 as comprometido,0 causado,  (a.importe) opago, IF(d.status='F3',(a.importe),0) pagado, 0 aumento, 0 disminucion, 0 traslados  
    FROM itocompra a JOIN ocompra b ON a.numero=b.numero JOIN pacom c ON b.numero = c.compra JOIN odirect d ON c.pago = d.numero
    WHERE d.status IN ('F2','F3') AND  ordinal<>'' 
    
    
    UNION ALL
    
    SELECT b.fecha,a.descripcion,b.observa,b.cod_prov,a.numero,status,'OP. Directo',b.estadmin, b.fondo,a.partida,a.ordinal, NULL faudis,NULL ftrasla,fopago fcomprome,fopago fcausado,fopago,IF(status IN ('B3'),fpagado,NULL) fpagado, NULL frendi,
    SUM(IF(status='BX',-1*a.importe,a.importe)) as comprometido, SUM(IF(status='BX',-1*a.importe,a.importe)) causado, SUM(IF(status='BX',-1*a.importe,a.importe)) opago, SUM(IF(status IN ('B3'),a.importe,0)) pagado, 0 aumento, 0 disminucion, 0 traslados  
    FROM itodirect a JOIN odirect b ON a.numero=b.numero 
    WHERE b.status IN ('B2','BX','BY','B3') AND ordinal<>'' 
    GROUP BY a.numero
        
    UNION ALL
    
    SELECT b.fecha,a.descripcion,b.observa,b.cod_prov,a.numero,status,'OP. Directo',b.estadmin, b.fondo,a.partida,a.ordinal, NULL faudis,NULL ftrasla,fopago fcomprome,fopago fcausado,fopago,IF(status IN ('B3'),fpagado,NULL) fpagado, NULL frendi,
    SUM(IF(status='BX',-1*a.importe*a.iva/100,a.importe*a.iva/100)) as comprometido, SUM(IF(status='BX',-1*a.importe*a.iva/100,a.importe*a.iva/100)) causado, SUM(IF(status='BX',-1*a.importe*a.iva/100,a.importe*a.iva/100)) opago, SUM(IF(status IN ('B3'),a.importe*a.iva/100,0)) pagado, 0 aumento, 0 disminucion, 0 traslados  
    FROM itodirect a JOIN odirect b ON a.numero=b.numero 
    WHERE b.status  IN ('B2','BX','BY','B3') AND (SELECT asignacion FROM presupuesto d WHERE d.codigoadm=b.estadmin AND d.tipo=b.fondo AND d.codigopres=(SELECT valor FROM valores WHERE nombre='PARTIDAIVA') )=0 AND ordinal<>''  
    GROUP BY a.numero
        
    UNION ALL
    
    SELECT b.fecha,a.descripcion,b.observa,b.cod_prov,a.numero,status,'OP. Directo',b.estadmin, b.fondo, (SELECT valor FROM valores WHERE nombre='PARTIDAIVA') partida,a.ordinal, NULL faudis,NULL ftrasla,fopago fcomprome,fopago fcausado,fopago,IF(status IN ('B3'),fpagado,NULL) fpagado, NULL frendi,
    (IF(status='BX',-1*a.importe*a.iva/100,a.importe*a.iva/100)) as comprometido, (IF(status='BX',-1*a.importe*a.iva/100,a.importe*a.iva/100)) causado, (IF(status='BX',-1*a.importe*a.iva/100,a.importe*a.iva/100)) opago, IF(status IN ('B3'),a.importe*a.iva/100,0) pagado, 0 aumento, 0 disminucion, 0 traslados 
    FROM itodirect a JOIN odirect b ON a.numero=b.numero 
    WHERE b.status IN ('B2','BX','BY','B3') AND (SELECT asignacion FROM presupuesto d WHERE d.codigoadm=b.estadmin AND d.tipo=b.fondo AND d.codigopres=(SELECT valor FROM valores WHERE nombre='PARTIDAIVA') )>0 AND ordinal<>''  
    
    
    UNION ALL

    SELECT b.fecha,a.descripcion,b.observa,b.cod_prov,a.numero,CONCAT('R',status),'Rendicion',a.codigoadm, a.fondo,a.codigopres,a.ordinal, NULL faudis,NULL ftrasla, frendi fcomprome,frendi fcausado,frendi fopago, frendi fpagado, frendi,
    (a.subtotal) as comprometido, (a.subtotal) causado, (a.subtotal) opago, (a.subtotal) pagado, 0 aumento, 0 disminucion, 0 traslados  
    FROM itrendi a JOIN rendi b ON a.numero=b.numero 
    WHERE b.status IN ('C') AND ordinal<>'' 
    
    
    UNION ALL
            
    SELECT b.fecha,a.descripcion,b.observa,b.cod_prov,a.numero,CONCAT('R',status),'Rendicion',a.codigoadm, a.fondo,a.codigopres,a.ordinal, NULL faudis,NULL ftrasla, frendi fcomprome,frendi fcausado,frendi fopago, frendi fpagado, frendi,
    (a.subtotal*a.iva/100) as comprometido, (a.subtotal*a.iva/100) causado, (a.subtotal*a.iva/100) opago, (a.subtotal*a.iva/100) pagado, 0 aumento, 0 disminucion, 0 traslados  
    FROM itrendi a JOIN rendi b ON a.numero=b.numero 
    WHERE b.status IN ('C') AND ordinal<>''  AND (SELECT asignacion FROM presupuesto d WHERE d.codigoadm=a.codigoadm AND d.tipo=a.fondo AND d.codigopres=(SELECT valor FROM valores WHERE nombre='PARTIDAIVA') )=0
    
    
    UNION ALL
    
    SELECT b.fecha,a.descripcion,b.observa,b.cod_prov,a.numero,CONCAT('R',status),'Rendicion',a.codigoadm, a.fondo,(SELECT valor FROM valores WHERE nombre='PARTIDAIVA') codigopres,a.ordinal, NULL faudis,NULL ftrasla, frendi fcomprome,frendi fcausado,frendi fopago, frendi fpagado, frendi,
    (a.subtotal*a.iva/100) as comprometido, (a.subtotal*a.iva/100) causado, (a.subtotal*a.iva/100) opago, (a.subtotal*a.iva/100) pagado, 0 aumento, 0 disminucion, 0 traslados  
    FROM itrendi a JOIN rendi b ON a.numero=b.numero 
    WHERE b.status IN ('C') AND ordinal<>''  AND (SELECT asignacion FROM presupuesto d WHERE d.codigoadm=a.codigoadm AND d.tipo=a.fondo AND d.codigopres=(SELECT valor FROM valores WHERE nombre='PARTIDAIVA') )>0
    
      
    UNION ALL
    
    SELECT b.fecha,a.denomi,b.motivo,'',a.numero,status,b.tipo,a.codigoadm, a.fondo,a.codigopres,ordinal,  faudis,NULL ftrasla, NULL fcomprome,NULL fcausado,NULL fopago, NULL fpagado, NULL frendi,
    0  comprometido, 0 causado, 0 opago, 0 pagado, (a.monto*(MID(b.tipo,1,1)='A')) aumento, (a.monto*(MID(b.tipo,1,1)='D')) disminucion, 0 traslados 
    FROM itaudis a JOIN audis b ON a.numero=b.numero 
    WHERE b.status = 'C' AND ordinal<>''  
    
      
    UNION ALL
    
    SELECT b.fecha,a.denomi,b.motivo,'',a.numero,status,'Traslado',a.codigoadm, a.fondo, a.codigopres,ordinal,NULL faudis,ftrasla, NULL fcomprome,NULL fcausado,NULL fopago, NULL fpagado, NULL frendi,
    0  comprometido, 0 causado, 0 opago, 0 pagado, 0 aumento, 0 disminucion, (a.aumento-a.disminucion) traslados 
    FROM ittrasla a JOIN trasla b ON a.numero=b.numero 
    WHERE b.status = 'C' AND ordinal<>''  
    

  ) con
  JOIN ordinal c ON con.codigoadm=c.codigoadm AND con.codigopres = c.codigopres AND con.fondo = c.fondo AND con.ordinal=c.ordinal

  UNION ALL
    
  SELECT sin.*,0 asignacion,c.denominacion  FROM
  (  
    SELECT a.fecha,'',observa,cod_prov,a.numero,status,'Obra',a.codigoadm, a.fondo,a.codigopres,'' ordinal,NULL faudis,NULL ftrasla,fcomprome,NULL fcausado,NULL fopago,NULL fpagado,NULL frendi,
    (monto-pagoviejo) as comprometido, 0 causado, 0 opago, 0 pagado, 0 aumento, 0 disminucion, 0 traslados 
    FROM obra a 
    WHERE a.status  IN ('O2','O4')  AND (ordinal='' OR ordinal IS NULL)
    
    
    UNION ALL

    SELECT b.fecha,'',b.observa,b.cod_prov,a.numero,b.status,'OP. Obra',a.codigoadm, a.fondo,a.codigopres,'' ordinal,NULL faudis,NULL ftrasla,NULL fcomprome, fopago fcausado,fopago,NULL fpagado,NULL frendi,
    0 as comprometido, (IF(b.status='OX',-1*(b.total2-b.amortiza),b.total2-b.amortiza)) causado, (IF(b.status='OX',-1*(b.total2-b.amortiza),b.total2-b.amortiza)) opago, ((b.total2-b.amortiza)*(b.status='O3')) pagado, 0 aumento, 0 disminucion, 0 traslados  
    FROM obra a JOIN odirect b ON a.numero=b.obr
    WHERE b.status  IN ('O2','OX','OY','O3')  AND (ordinal='' OR ordinal IS NULL)  
    
    
    UNION ALL
    
    SELECT b.fecha,'',b.descrip,'',a.numero,b.status,'Nomina',a.codigoadm, a.fondo,a.codigopres,'' ordinal,NULL faudis,NULL ftrasla,fcomprome, NULL fcausado,NULL fopago, NULL fpagado,NULL frendi,
    (monto) as comprometido, 0 causado, 0 opago, 0 pagado, 0 aumento, 0 disminucion, 0 traslados  
    FROM asignomi a 
    JOIN nomi b ON a.numero = b.numero
    WHERE b.status  IN ('C','D','O','E') AND (ordinal='' OR ordinal IS NULL)
    
    
    UNION ALL
        
    SELECT b.fecha,a.descripcion,b.observa,b.cod_prov,a.numero,status,'OP. Nomina',a.codigoadm, a.fondo,a.partida,'' ordinal, NULL faudis,NULL ftrasla,NULL fcomprome,fopago fcausado,fopago,fpagado, NULL frendi,
    0 as comprometido, (IF(status='KX',-1*a.importe,a.importe)) causado, (IF(status='KX',-1*a.importe,a.importe)) opago, (IF(status='K3',a.importe,a.importe)) pagado, 0 aumento, 0 disminucion, 0 traslados  
    FROM itodirect a JOIN odirect b ON a.numero=b.numero 
    WHERE b.status IN ('K2','KX','KY','K3') AND (ordinal='' OR ordinal IS NULL) 
    
    
    UNION ALL

    SELECT b.fecha,a.descripcion,b.observa,cod_prov,a.numero,status,'Compra',b.estadmin, b.fondo,a.partida,'' ordinal, NULL faudis,NULL ftrasla,fcomprome,IF(status NOT IN ('C'),fcausado,NULL) fcausado,NULL fopago, NULL fpagado, NULL frendi,
    (IF(status='X',-1*a.importe,a.importe)) as comprometido, (IF(status='X',-1*a.importe,IF(status IN ('T','O','E'),a.importe,0))) causado, 0 opago, 0 pagado, 0 aumento, 0 disminucion, 0 traslados  
    FROM itocompra a JOIN ocompra b ON a.numero=b.numero 
    WHERE b.status IN ('C','T','O','E','X','Y') AND (ordinal='' OR ordinal IS NULL)
    

    UNION ALL
    
    SELECT b.fecha,a.descripcion,b.observa,cod_prov,a.numero,status,'Compra',b.estadmin, b.fondo,a.partida,'' ordinal, NULL faudis,NULL ftrasla,fcomprome,IF(status NOT IN ('C'),fcausado,NULL) fcausado,NULL fopago, NULL fpagado, NULL frendi,
    SUM(IF(status='X',-1*a.importe*a.iva/100,a.importe*a.iva/100)) as comprometido, SUM(IF(status='X',-1*a.importe*a.iva/100,IF(status IN ('T','O','E'),a.importe*a.iva/100,0))) causado, 0 opago, 0 pagado, 0 aumento, 0 disminucion, 0 traslados  
    FROM itocompra a JOIN ocompra b ON a.numero=b.numero 
    WHERE b.status IN ('C','T','O','E','X','Y') AND (SELECT asignacion FROM presupuesto d WHERE d.codigoadm=b.estadmin AND d.tipo=b.fondo AND d.codigopres=(SELECT valor FROM valores WHERE nombre='PARTIDAIVA') )=0 AND (ordinal='' OR ordinal IS NULL) 
    GROUP BY a.numero

    UNION ALL
    
    SELECT b.fecha,a.descripcion,b.observa,cod_prov,a.numero,status,'Compra',b.estadmin, b.fondo, (SELECT valor FROM valores WHERE nombre='PARTIDAIVA') partida,'' ordinal, NULL faudis,NULL ftrasla,fcomprome,IF(status NOT IN ('C'),fcausado,NULL) fcausado,NULL fopago, NULL fpagado, NULL frendi,
    SUM(IF(status='X',-1*a.importe*a.iva/100,a.importe*a.iva/100)) as comprometido, SUM(IF(status='X',-1*a.importe*a.iva/100,IF(status IN ('T','O','E'),a.importe*a.iva/100,0))) causado, 0 opago, 0 pagado, 0 aumento, 0 disminucion, 0 traslados  
    FROM itocompra a JOIN ocompra b ON a.numero=b.numero 
    WHERE b.status IN ('C','T','O','E','X','Y') AND (SELECT asignacion FROM presupuesto d WHERE d.codigoadm=b.estadmin AND d.tipo=b.fondo AND d.codigopres=(SELECT valor FROM valores WHERE nombre='PARTIDAIVA') )>0 AND (ordinal='' OR ordinal IS NULL) 
    GROUP BY a.numero
    
    UNION ALL
    
    SELECT d.fecha,a.descripcion,d.observa,d.cod_prov,d.numero,d.status,'OP. Compra',b.estadmin, b.fondo, (SELECT valor FROM valores WHERE nombre='PARTIDAIVA') partida,'' ordinal, NULL faudis,NULL ftrasla,NULL fcomprome,NULL fcausado,fopago, IF(d.status IN ('F3'),fpagado,NULL) fpagado, NULL frendi,
    0 as comprometido,0 causado,  SUM(a.importe*a.iva/100) opago, SUM(IF(d.status='F3',(a.importe*a.iva/100),0)) pagado, 0 aumento, 0 disminucion, 0 traslados  
    FROM itocompra a JOIN ocompra b ON a.numero=b.numero JOIN pacom c ON b.numero = c.compra JOIN odirect d ON c.pago = d.numero
    WHERE d.status IN ('F2','F3') AND (SELECT asignacion FROM presupuesto d WHERE d.codigoadm=b.estadmin AND d.tipo=b.fondo AND d.codigopres=(SELECT valor FROM valores WHERE nombre='PARTIDAIVA') )>0 AND (ordinal='' OR ordinal IS NULL) 
    GROUP BY a.numero
    
    UNION ALL
    
    SELECT d.fecha,a.descripcion,d.observa,d.cod_prov,d.numero,d.status,'OP. Compra',b.estadmin, b.fondo, partida,'' ordinal, NULL faudis,NULL ftrasla,NULL fcomprome,NULL fcausado,fopago, IF(d.status IN ('F3'),fpagado,NULL) fpagado, NULL frendi,
    0 as comprometido,0 causado,  SUM(a.importe*a.iva/100) opago, SUM(IF(d.status='F3',(a.importe*a.iva/100),0)) pagado, 0 aumento, 0 disminucion, 0 traslados  
    FROM itocompra a JOIN ocompra b ON a.numero=b.numero JOIN pacom c ON b.numero = c.compra JOIN odirect d ON c.pago = d.numero
    WHERE d.status IN ('F2','F3') AND (SELECT asignacion FROM presupuesto d WHERE d.codigoadm=b.estadmin AND d.tipo=b.fondo AND d.codigopres=(SELECT valor FROM valores WHERE nombre='PARTIDAIVA') )=0 AND (ordinal='' OR ordinal IS NULL) 
    GROUP BY a.numero
    
    UNION ALL
    
    SELECT d.fecha,a.descripcion,d.observa,d.cod_prov,d.numero,d.status,'OP. Compra',b.estadmin, b.fondo, a.partida,'' ordinal, NULL faudis,NULL ftrasla,NULL fcomprome,NULL fcausado,fopago, IF(d.status IN ('F3'),fpagado,NULL) fpagado, NULL frendi,
    0 as comprometido,0 causado,  (a.importe) opago, IF(d.status='F3',(a.importe),0) pagado, 0 aumento, 0 disminucion, 0 traslados  
    FROM itocompra a JOIN ocompra b ON a.numero=b.numero JOIN pacom c ON b.numero = c.compra JOIN odirect d ON c.pago = d.numero
    WHERE d.status IN ('F2','F3') AND  (ordinal='' OR ordinal IS NULL)
    
    
    UNION ALL
    
    SELECT b.fecha,a.descripcion,b.observa,b.cod_prov,a.numero,status,'OP. Directo',b.estadmin, b.fondo,a.partida,'' ordinal, NULL faudis,NULL ftrasla,fopago fcomprome,fopago fcausado,fopago,IF(status IN ('B3'),fpagado,NULL) fpagado, NULL frendi,
    (IF(status='BX',-1*a.importe,a.importe)) as comprometido, (IF(status='BX',-1*a.importe,a.importe)) causado, (IF(status='BX',-1*a.importe,a.importe)) opago, (IF(status='B3',a.importe,0)) pagado, 0 aumento, 0 disminucion, 0 traslados  
    FROM itodirect a JOIN odirect b ON a.numero=b.numero 
    WHERE b.status IN ('B2','BX','BY','B3') AND (ordinal='' OR ordinal IS NULL)
    
            
    UNION ALL
    
    SELECT b.fecha,a.descripcion,b.observa,b.cod_prov,a.numero,status,'OP. Directo',b.estadmin, b.fondo,a.partida,'' ordinal, NULL faudis,NULL ftrasla,fopago fcomprome,fopago fcausado,fopago,IF(status IN ('B3'),fpagado,NULL) fpagado, NULL frendi,
    SUM(IF(status='BX',-1*a.importe*a.iva/100,a.importe*a.iva/100)) as comprometido, SUM(IF(status='BX',-1*a.importe*a.iva/100,a.importe*a.iva/100)) causado, SUM(IF(status='BX',-1*a.importe*a.iva/100,a.importe*a.iva/100)) opago, SUM(IF(status='B3',a.importe*a.iva/100,0)) pagado, 0 aumento, 0 disminucion, 0 traslados  
    FROM itodirect a JOIN odirect b ON a.numero=b.numero 
    WHERE b.status  IN ('B2','BX','BY','B3') AND (SELECT asignacion FROM presupuesto d WHERE d.codigoadm=b.estadmin AND d.tipo=b.fondo AND d.codigopres=(SELECT valor FROM valores WHERE nombre='PARTIDAIVA') )=0 AND (ordinal='' OR ordinal IS NULL)  
    GROUP BY a.numero
            
    UNION ALL
    
    SELECT b.fecha,a.descripcion,b.observa,b.cod_prov,a.numero,status,'OP. Directo',b.estadmin, b.fondo, (SELECT valor FROM valores WHERE nombre='PARTIDAIVA') partida,'' ordinal, NULL faudis,NULL ftrasla,fopago fcomprome,fopago fcausado,fopago,IF(status IN ('B3'),fpagado,NULL) fpagado, NULL frendi,
    SUM(IF(status='BX',-1*a.importe*a.iva/100,a.importe*a.iva/100)) as comprometido, SUM(IF(status='BX',-1*a.importe*a.iva/100,a.importe*a.iva/100)) causado, SUM(IF(status='BX',-1*a.importe*a.iva/100,a.importe*a.iva/100)) opago, SUM(IF(status='B3',a.importe*a.iva/100,0)) pagado, 0 aumento, 0 disminucion, 0 traslados 
    FROM itodirect a JOIN odirect b ON a.numero=b.numero 
    WHERE b.status IN ('B2','BX','BY','B3') AND (SELECT asignacion FROM presupuesto d WHERE d.codigoadm=b.estadmin AND d.tipo=b.fondo AND d.codigopres=(SELECT valor FROM valores WHERE nombre='PARTIDAIVA') )>0 AND (ordinal='' OR ordinal IS NULL)  
    GROUP BY a.numero
    
    UNION ALL

    SELECT b.fecha,a.descripcion,b.observa,b.cod_prov,a.numero,CONCAT('R',status),'Rendicion',a.codigoadm, a.fondo,a.codigopres,'' ordinal, NULL faudis,NULL ftrasla, frendi fcomprome,frendi fcausado,frendi fopago, frendi fpagado, frendi,
    (a.subtotal) as comprometido, (a.subtotal) causado, (a.subtotal) opago, (a.subtotal) pagado, 0 aumento, 0 disminucion, 0 traslados  
    FROM itrendi a JOIN rendi b ON a.numero=b.numero 
    WHERE b.status IN ('C') AND (ordinal='' OR ordinal IS NULL)
    
    
    UNION ALL
            
    SELECT b.fecha,a.descripcion,b.observa,b.cod_prov,a.numero,CONCAT('R',status),'Rendicion',a.codigoadm, a.fondo,a.codigopres,'' ordinal, NULL faudis,NULL ftrasla, frendi fcomprome,frendi fcausado,frendi fopago, frendi fpagado, frendi,
    (a.subtotal*a.iva/100) as comprometido, (a.subtotal*a.iva/100) causado, (a.subtotal*a.iva/100) opago, (a.subtotal*a.iva/100) pagado, 0 aumento, 0 disminucion, 0 traslados  
    FROM itrendi a JOIN rendi b ON a.numero=b.numero 
    WHERE b.status IN ('C') AND (ordinal='' OR ordinal IS NULL)  AND (SELECT asignacion FROM presupuesto d WHERE d.codigoadm=a.codigoadm AND d.tipo=a.fondo AND d.codigopres=(SELECT valor FROM valores WHERE nombre='PARTIDAIVA') )=0
    
    
    UNION ALL
    
    SELECT b.fecha,a.descripcion,b.observa,b.cod_prov,a.numero,CONCAT('R',status),'Rendicion',a.codigoadm, a.fondo,(SELECT valor FROM valores WHERE nombre='PARTIDAIVA') codigopres,'' ordinal, NULL faudis,NULL ftrasla, frendi fcomprome,frendi fcausado,frendi fopago, frendi fpagado, frendi,
    (a.subtotal*a.iva/100) as comprometido, (a.subtotal*a.iva/100) causado, (a.subtotal*a.iva/100) opago, (a.subtotal*a.iva/100) pagado, 0 aumento, 0 disminucion, 0 traslados  
    FROM itrendi a JOIN rendi b ON a.numero=b.numero 
    WHERE b.status IN ('C') AND (ordinal='' OR ordinal IS NULL)  AND (SELECT asignacion FROM presupuesto d WHERE d.codigoadm=a.codigoadm AND d.tipo=a.fondo AND d.codigopres=(SELECT valor FROM valores WHERE nombre='PARTIDAIVA') )>0
    
      
    UNION ALL
    
    SELECT b.fecha,a.denomi,b.motivo,'',a.numero,status,b.tipo,a.codigoadm, a.fondo,a.codigopres,'' ordinal,  faudis,NULL ftrasla, NULL fcomprome,NULL fcausado,NULL fopago, NULL fpagado, NULL frendi,
    0  comprometido, 0 causado, 0 opago, 0 pagado, (a.monto*(MID(b.tipo,1,1)='A')) aumento, (a.monto*(MID(b.tipo,1,1)='D')) disminucion, 0 traslados 
    FROM itaudis a JOIN audis b ON a.numero=b.numero 
    WHERE b.status = 'C' AND ordinal='' OR ordinal IS NULL 
    
     
    UNION ALL
    
    SELECT b.fecha,a.denomi,b.motivo,'',a.numero,status,'Traslado',a.codigoadm, a.fondo, a.codigopres,'' ordinal,NULL faudis,ftrasla, NULL fcomprome,NULL fcausado,NULL fopago, NULL fpagado, NULL frendi,
    0  comprometido, 0 causado, 0 opago, 0 pagado, 0 aumento, 0 disminucion, (a.aumento-a.disminucion) traslados 
    FROM ittrasla a JOIN trasla b ON a.numero=b.numero 
    WHERE b.status = 'C' AND (ordinal='' OR ordinal IS NULL) 
    
  ) sin
  JOIN ppla c ON c.codigo=sin.codigopres
  )todo
  JOIN estruadm e ON e.codigo=todo.codigoadm
  JOIN fondo f ON f.fondo= todo.fondo
  LEFT JOIN sprv g ON todo.cod_prov=g.proveed;
END";

    if($this->db->query($query))echo "create sp_view_pres</br>";
    
$this->db->query("UPDATE odirect SET fopago='2010-02-05' ,fpagado='2010-02-05'  WHERE numero=44"  );
$this->db->query("UPDATE odirect SET fopago='2010-02-10' ,fpagado='2010-02-19'  WHERE numero=149"  );
$this->db->query("UPDATE odirect SET fopago='2010-02-11' ,fpagado='2010-02-11'  WHERE numero=182"  );
$this->db->query("UPDATE odirect SET fopago='2010-02-11' ,fpagado='2010-02-11'  WHERE numero=187"  );
$this->db->query("UPDATE odirect SET fopago='2010-02-11' ,fpagado='2010-02-11'  WHERE numero=189"  );
$this->db->query("UPDATE odirect SET fopago='2010-02-26' ,fpagado='2010-02-26'  WHERE numero=373"  );
$this->db->query("UPDATE odirect SET fopago='2010-02-26' ,fpagado='2010-02-26'  WHERE numero=390"  );
$this->db->query("UPDATE odirect SET fopago='2010-03-19' ,fpagado='2010-03-19'  WHERE numero=671"  );
$this->db->query("UPDATE odirect SET fopago='2010-03-22' ,fpagado='2010-03-22'  WHERE numero=705"  );
$this->db->query("UPDATE odirect SET fopago='2010-03-22' ,fpagado='2010-03-22'  WHERE numero=713"  );
$this->db->query("UPDATE odirect SET fopago='2010-04-28' ,fpagado='2010-04-28'  WHERE numero=1066" );
$this->db->query("UPDATE odirect SET fopago='2010-05-17' ,fpagado='2010-05-17'  WHERE numero=1314" );
$this->db->query("UPDATE odirect SET fopago='2010-05-19' ,fpagado='2010-05-26'  WHERE numero=1342" );
$this->db->query("UPDATE odirect SET fopago='2010-06-04' ,fpagado='2010-06-07'  WHERE numero=1562" );
$this->db->query("UPDATE odirect SET fopago='2010-06-04' ,fpagado='2010-06-07'  WHERE numero=1563" );
$this->db->query("UPDATE odirect SET fopago='2010-06-09' ,fpagado='2010-02-11'  WHERE numero=1595" );
$this->db->query("UPDATE odirect SET fopago='2010-06-09' ,fpagado='2010-02-11'  WHERE numero=1596" );
$this->db->query("UPDATE odirect SET fopago='2010-06-09' ,fpagado='2010-01-28'  WHERE numero=1597" );
$this->db->query("UPDATE odirect SET fopago='2010-06-10' ,fpagado='2010-02-11'  WHERE numero=1602" );
$this->db->query("UPDATE odirect SET fopago='2010-06-10' ,fpagado='2010-02-19'  WHERE numero=1603" );
$this->db->query("UPDATE odirect SET fopago='2010-06-10' ,fpagado='2010-02-05'  WHERE numero=1604" );
$this->db->query("UPDATE odirect SET fopago='2010-06-15' ,fpagado='2010-03-22'  WHERE numero=1636" );
$this->db->query("UPDATE odirect SET fopago='2010-06-15' ,fpagado=NULL          WHERE numero=1661" );
$this->db->query("UPDATE odirect SET fopago='2010-06-15' ,fpagado=NULL          WHERE numero=1666" );
$this->db->query("UPDATE odirect SET fopago='2010-06-18' ,fpagado=NULL          WHERE numero=1680" );
$this->db->query("UPDATE odirect SET fopago='2010-06-23' ,fpagado='2010-05-26'  WHERE numero=1740" );
$this->db->query("UPDATE odirect SET fopago='2010-07-01' ,fpagado='2010-05-17'  WHERE numero=1829" );
$this->db->query("UPDATE odirect SET fopago='2010-07-01' ,fpagado='2010-03-19'  WHERE numero=1833" );
$this->db->query("UPDATE odirect SET fopago=NULL         ,fpagado=NULL          WHERE numero=1871" );
$this->db->query("UPDATE odirect SET fopago='2010-07-12' ,fpagado='2010-04-28'  WHERE numero=1931" );
$this->db->query("UPDATE odirect SET fopago='2010-07-12' ,fpagado='2010-02-26'  WHERE numero=1932" );
$this->db->query("UPDATE odirect SET fopago='2010-07-13' ,fpagado='2010-02-26'  WHERE numero=1973" );
$this->db->query("UPDATE odirect SET fopago='2010-07-14' ,fpagado=NULL          WHERE numero=1985" );
$this->db->query("UPDATE odirect SET fopago='2010-07-16' ,fpagado='2010-07-19'  WHERE numero=2052" );
$this->db->query("UPDATE odirect SET fopago='2010-07-20' ,fpagado='2010-06-07'  WHERE numero=2063" );
$this->db->query("UPDATE odirect SET fopago='2010-07-20' ,fpagado='2010-06-07'  WHERE numero=2064" );
$this->db->query("UPDATE odirect SET fopago='2010-07-20' ,fpagado='2010-03-22'  WHERE numero=2065" );
$this->db->query("UPDATE odirect SET fopago='2010-07-22' ,fpagado=NULL          WHERE numero=2080" );
$this->db->query("UPDATE odirect SET fopago='2010-07-22' ,fpagado=NULL          WHERE numero=2081" );
$this->db->query("UPDATE odirect SET fopago='2010-07-22' ,fpagado='2010-07-22'  WHERE numero=2117" );
$this->db->query("UPDATE odirect SET fopago='2010-08-16' ,fpagado=NULL          WHERE numero=2320" );
$this->db->query("UPDATE odirect SET fopago='2010-08-26' ,fpagado=NULL          WHERE numero=2487" );
$this->db->query("UPDATE odirect SET fopago='2010-08-31' ,fpagado='2010-08-31'  WHERE numero=2544" );
$this->db->query("UPDATE odirect SET fopago='2010-09-02' ,fpagado='2010-08-31'  WHERE numero=2577" );
$this->db->query("UPDATE odirect SET fopago='2010-09-06' ,fpagado='2010-07-19'  WHERE numero=2652" );
$this->db->query("UPDATE odirect SET fopago='2010-09-15' ,fpagado='2010-07-22'  WHERE numero=2750");

$this->db->query("update trasla set ftrasla=fecha");    
$this->db->query("update audis set faudis=fecha");
$this->db->query("update ocompra set fcomprome=fecha,fcausado=fecha  where status not like '%Y%' AND status not like '%X%'");		
$this->db->query("update odirect set fopago=fecha  where status not like '%Y%' AND status not like '%X%'");
$this->db->query("update rendi set frendi=fecha");
echo "Ya esta Listo Nina. Corre el recalculo y se debe solucionar lo del -0.01";

}
}
?>