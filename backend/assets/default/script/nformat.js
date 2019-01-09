function number_format(num,miles,centimos){
  num = num.toString().replace(/$|\,/g,'');
  if(isNaN(num)) num = "0";
  	sign = (num == (num = Math.abs(num)));
  num = Math.floor(num*100+0.50000000001);
  cents = num%100;
  num = Math.floor(num/100).toString();
  if(cents<10) cents = "0" + cents;
  	for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
  		num = num.substring(0,num.length-(4*i+3))+miles+ num.substring(num.length-(4*i+3));
	return (((sign)?'':'-') + num + centimos + cents);
}

function des_number_format(num,miles,centimos){
	num = num.split(miles).join('');
	num = parseFloat(num.replace(centimos,'.'));
	if(isNaN(num)) return(0);
	return(num);
}

function fn(form,field){
	var next=0, found=false;
	var f=form;
	if(event.keyCode!=13) return;
		for(var i=0;i<f.length;i++) {
			if(field.name==f.item(i).name){
				next=i+1;
				found=true;
				break;
			}
		}
	while(found){
		if(f.item(next).disabled==false && f.item(next).type!='hidden'){
			f.item(next).focus();
			break;
		}else{
			if(next<f.length-1)
				next=next+1;
			else
				break;
		}
	}
}