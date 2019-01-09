SELECT denopart,fondo,codigo,SUM(asignacion) asignacion,SUM(modifica) modifica,SUM(final) final,SUM(comprometido1)comprometido1 FROM (
SELECT denopart,fondo,MID(codigopres,1,4) codigo,SUM(asignacion) asignacion,0 modifica                        ,0 final                                      ,0 comprometido1   ,0 disponible                                                        FROM view_pres GROUP BY MID(codigopres,1,4)
UNION ALL
SELECT denopart,fondo,MID(codigopres,1,4) codigo,0                         ,SUM(aumento-disminucion+traslados),0                                            ,0                 ,0                                                                   FROM view_pres GROUP BY MID(codigopres,1,4)
UNION ALL
SELECT denopart,fondo,MID(codigopres,1,4) codigo,0                         ,0                                 ,SUM(asignacion+aumento-disminucion+traslados),0                 ,0                                                                   FROM view_pres WHERE () AND modo IN ('Asignacion','AUMENTO','Traslado','DISMINUCION') GROUP BY MID(codigopres,1,4)
UNION ALL
SELECT denopart,fondo,MID(codigopres,1,4) codigo,0                         ,0                                 ,0                                            ,SUM(comprometido) ,0                                                                   FROM view_pres WHERE MONTH(fcomprome) IN (1,2,3) GROUP BY MID(codigopres,1,4)
UNION ALL
SELECT denopart,fondo,MID(codigopres,1,4) codigo,0                         ,0                                 ,0                                            ,SUM(comprometido) ,SUM(asignacion+aumento-disminucion+traslados-comprometido)          FROM view_pres WHERE MONTH(fcomprome) IN (1,2,3) GROUP BY MID(codigopres,1,4)
)t
GROUP BY codigo