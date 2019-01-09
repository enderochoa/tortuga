function nformat(num,n){
	var i=0;
	var fact=1;
	miles='<?php echo $miles; ?>';
	centimos='<?php echo $centimos; ?>';
	num = num.toString().replace(/$|\,/g,'');
	if(isNaN(num)) num = "0";
	for(i=0;i < n;i++){ fact=10*fact; }
	sign  = (num == (num = Math.abs(num)));
	num   = Math.floor(num*fact+0.50000000001);
	//alert(num);
	cents = num%fact;
	num   = Math.floor(num/fact).toString();
	if(cents<10) cents = "0" + cents;
		for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
			num = num.substring(0,num.length-(4*i+3))+miles+ num.substring(num.length-(4*i+3));
	return (((sign)?'':'-') + num + centimos + cents);
}

function des_nformat(num){
	miles='<?php echo $miles; ?>';
	centimos='<?php echo $centimos; ?>';
	num = num.split(miles).join('');
	num = parseFloat(num.replace(centimos,'.'));
	if(isNaN(num)) return(0);
	return(num);
}

function moneyformat(num){
	return nformat(num,2);
}
                                                                    
function des_moneyformat(num){
	return des_nformat(num);
}