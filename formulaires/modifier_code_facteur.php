<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

include_spip('inc/editer');


function formulaires_modifier_code_facteur_charger_dist($id_adresse = 0, $code_facteur = 0, $retour = '') {
	$valeurs = array(
		'code_facteur' => $code_facteur,
		'id_adresse' => $id_adresse,
	);
	
	return $valeurs;
}


function formulaires_modifier_code_facteur_verifier_dist($id_adresse = 0, $code_facteur = 0, $retour = '') {
	$erreurs = array();
	$code_facteur = _request('code_facteur');
	
	if (!is_numeric($code_facteur)) {
		$erreurs['message_erreur'] = 'Saisir 4 chiffres uniquement';
	}
	
	return $erreurs;
}


function formulaires_modifier_code_facteur_traiter_dist($id_adresse = 0, $code_facteur = 0, $retour = '') {
	
	$code_facteur = _request('code_facteur');
	$id_adresse = _request('id_adresse');
	
	include_spip('action/editer_adresse');
	revisions_adresses($id_adresse, array('code_facteur' => $code_facteur));
	
	if ($retour) {
		$res['redirect'] = $retour;
	}
	
	$res['message_ok'] = 'Code enregistré';
	//$res = formulaires_editer_objet_traiter('adresse', $id_adresse, '', '', $retour = '', '');
	// $code_facteur = _request('code_facteur');
	// $id_adresse = _request('id_adresse');
	
	// $res = formulaires_editer_objet_traiter('adresse', intval($id_adresse), 0, 0, '', '', '', '');
	
	return $res;
}