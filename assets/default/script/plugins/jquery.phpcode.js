/*
 * phpCode - AJAX based syntax highlighting for PHP code
 *
 * Pass ajax script URI optionally or use default "./phpcode.php";
 * Example: jQuery(".phpCode").phpCode();
 * Example: jQuery(".phpCode").phpCode("./another_script.php");
 *
 * Version: 1.0
 * Copyright (c) 2008 Ronald Vilbrandt
 * http://blog.rvi-media.de/
 * info@rvi-media.de
 */
(function() {

	jQuery.fn.phpCode = function(url) {

		if(!url) {
			url = "./phpcode.php";
		}

		return this.each(function() {

			jQuery(this).each(function() {

				var	elem = this;

				jQuery.post(url, {code : jQuery(this).text()}, function(data) {
					jQuery(elem).html(data);
				});
			});
		});
	}

})();