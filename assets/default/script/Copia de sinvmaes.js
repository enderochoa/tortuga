function roundNumber(num, dec) {
	var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
	return result;
}

//****************************************************
//dec: 0=decimal, 1 = unidades, 2 =decenas, 3=centenas
//****************************************************
function roundSup(num, dec) {
	num=num-0.01;
	num=roundNumber(num,2);
	
	if(dec==0)
		result = Math.ceil(num);
	else{
		result = num.toString();
		elemento=result.length-3-dec;
		previo="";
		for (i=elemento;i<result.length;i++)
		 previo=previo+result[i];
	
		factor=5*(Math.pow(10,dec)/10);
		previo=parseFloat(previo);
		
		if(previo<=factor)
			diferencia=factor-previo;
		else
			diferencia=(2*factor)-previo;
		
		result=num+diferencia
	}
	return result;
}

function cost(){
	var fcalc =$F("fcalc");
	var costo=parseFloat($F("costo"));
	var ultimo=parseFloat($F("ultimo"));
		
	if(fcalc=="P")
		ccosto=costo;
	else
		ccosto=ultimo;
	return ccosto;
}
		 
function calculos(){ 
	var iva   = parseFloat($F("iva"));
	var costo= cost();
	
	for(i=1;i<6;i++){
		margen=parseFloat($F("margen"+i.toString()));
		nmargen = roundNumber(margen,2);
		nbase   = roundNumber(costo*100/(100-margen),2);
		nprecio = roundNumber(nbase*((iva+100)/100),2);
		$("base"+i.toString()).value   = nbase;
		$("precio"+i.toString()).value = nprecio;
	}
}
function cambioprecio(){
//variables
	var i=0;
	var costo=cost();
	var iva=parseFloat($F("iva"));
	for(i=1;i<6;i++){
		precio=parseFloat($F("precio"+i.toString()));
		base=precio*100/(100+iva);
		nbase=roundNumber(base,2);
		document.getElementById("base"+i.toString()).value = nbase;
		margen=100-(costo*100)/nbase;
		nmargen=roundNumber(margen,2);
		document.getElementById("margen"+i.toString()).value = nmargen;
	}
}
function cambiobase(){
	var i=0;
	var costo=cost();
	var iva=parseFloat($F("iva"));
	for(i=1;i<6;i++){
		base=parseFloat($F("base"+i.toString()));
		precio=(base*(iva+100)/100);
		nprecio=roundNumber(precio,2);
		document.getElementById("precio"+i.toString()).value = nprecio;
		margen=100-(costo*100)/base;
		nmargen=roundNumber(margen,2);
		document.getElementById("margen"+i.toString()).value = nmargen;
	}
}
function redonde(){ 
	var redondeo =$F("redondeo");
	var i=0;
	var dec=parseInt(redondeo[1]);
	var costo=cost();
	var iva=parseFloat($F("iva"));
	
	if(redondeo!="NO"){
		if(redondeo[0]=="P"){
			for(i=1;i<6;i++){
				precio=parseFloat($F("precio"+i.toString()));
				nprecio=roundSup(precio, dec);
				nprecio=roundNumber(nprecio,2);
				document.getElementById("precio"+i.toString()).value = nprecio;
				base=nprecio*100/(100+iva);
				nbase=roundNumber(base,2);
				document.getElementById("base"+i.toString()).value = nbase;
				margen=100-(costo*100/nbase);
				nmargen=roundNumber(margen,2);
				document.getElementById("margen"+i.toString()).value = nmargen;
			}
		}else if(redondeo[0]=="B"){
			for(i=1;i<6;i++){
				base=parseFloat($F("base"+i.toString()));                                                                                         
				nbase=roundSup(base, dec);
				nbase=roundNumber(nbase,2);
				precio=(nbase*(iva+100)/100);
				nprecio=roundNumber(precio,2);
				margen=100-(costo*100/nbase);
				nmargen=roundNumber(margen,2);
				document.getElementById("margen"+i.toString()).value = nmargen;
				document.getElementById("precio"+i.toString()).value = nprecio;
				document.getElementById("base"+i.toString()).value = nbase;
			}
		}
	}			     
}