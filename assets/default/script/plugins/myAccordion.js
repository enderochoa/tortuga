/**
 * myAccordion ( http://supercanard.phpnet.org/jquery-test/myAccordion/ )
 * plugin jQuery pour afficher des bôites d'onglet.
 * 
 * Version 1.0
 *
 * Auteur : Jonathan Coulet ( j.coulet@gmail.com )
 * 
 **/
(function($){
	$.fn.myAccordion = function(option){
		// Param plugin
		var param = jQuery.extend({
			speed: "fast", // @param : low, medium, fast
			defautContent: 0 // @param : number
		}, option);
		$(this).each(function() {
			// var
			var $this = this;
			var $thisId = this.id;
			// Attribut un id à chaque déclencheur
			$("#"+$thisId+" .myAccordion-declencheur").attr("id", function(arr){
				return $thisId+"-elem"+arr;
			})
			// Masque tous les content
			$("#"+$thisId+" .myAccordion-declencheur").next(".myAccordion-content").hide();
			// Ouvre le content par défaut
			$("#"+$thisId+" #myAccordion-elem"+option.defautContent).next(".myAccordion-content").show();
			$("#"+$thisId+" #myAccordion-elem"+option.defautContent).addClass("myAccordion-declencheur-actif");
			$("#"+$thisId+" #myAccordion-elem"+option.defautContent).next(".myAccordion-content").addClass("myAccordion-content-actif");
			// Action sur déclencheur
			$("#"+$thisId+" .myAccordion-declencheur").click(function(){
				$("#"+$thisId+" .myAccordion-content-actif").hide(option.speed);
				$("#"+$thisId+" .myAccordion-content-actif").removeClass("myAccordion-content-actif");
				$("#"+$thisId+" .myAccordion-declencheur-actif").removeClass("myAccordion-declencheur-actif");
				var contentCourant = $(this).attr("id");
				$("#"+$thisId+" #"+contentCourant).next(".myAccordion-content").show(option.speed);
				$("#"+$thisId+" #"+contentCourant).addClass("myAccordion-declencheur-actif");
				$("#"+$thisId+" #"+contentCourant).next(".myAccordion-content").addClass("myAccordion-content-actif");
			});
		});
	}
})(jQuery);
