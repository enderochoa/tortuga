/*
 *
 * Copyright (c) 2007 Tulio Faria (http://www.tuliofaria.net - http://www.iwtech.com.br)
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Version 1.0
 * Demo: http://www.tuliofaria.net/jquery-floatnumber/
 *
 * $LastChangedDate$
 * $Rev$
 */
(function($) {
		
	//Main Method
	$.fn.floatnumber = function(separator,precision) {	
		
		return this.each(function(){		
			var input=$(this);
			var valid=false;   
						
			function blur(){
        var re = new RegExp(",", "g");
        s = input.val();
        s = s.replace(re, ".");

        if (s=="")
            s = "0";

        if (!isNaN(s)){
          n = parseFloat(s);

          s = n.toFixed(precision);

          re2 = new RegExp("\\.", "g");
          s = s.replace(re2, separator);

          input.val(s);

        }

      }
			input.bind("blur", blur);
		});
		
		
	};
})(jQuery);
