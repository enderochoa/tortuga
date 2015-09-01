#INSERT INTO itstra
SELECT '00001935',b.codigo,b.descrip,0,NULL,NULL,NULL,NULL,NULL,b.existen,b.existen*b.ultimo
FROM itstra AS a
RIGHT JOIN sinv AS b ON a.codigo=b.codigo AND a.numero=1935
WHERE b.clave<>'MAYOR' AND a.numero IS NULL