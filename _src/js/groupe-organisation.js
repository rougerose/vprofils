$(function() {
	chargement=true;
	verifier_saisies_type_client = function(form){
		var $groupe = $(form).find(".js-groupe-organisation"),
			$organisation = $groupe.find("input[name='organisation']");
		
		if ($(form).find("[name='type_client']:checked").val() == "type_organisation") {
			$groupe.show(400);
			$organisation.attr("required",true);
		} else {
			$groupe.hide(400);
			$organisation.attr("required",false);
			if (chargement==true) {
				$groupe.hide(400).css("display","none");
			} else {
				$groupe.hide(400);
			};
		}
		$(form).trigger('saisies_afficher_si_js_ok');
	};
	
	$("#js-groupe-organisation").parents("form").each(function(){
		verifier_saisies_type_client(this);
	});
	$("#js-groupe-organisation").parents("form").change(function(){
		verifier_saisies_type_client(this);}
	);
	chargement=false;
});
