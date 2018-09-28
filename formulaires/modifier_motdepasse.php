<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}


include_spip('inc/editer');

function formulaires_modifier_motdepasse_saisies_dist($id_auteur = 0) {
	$saisies = array();
	
	$saisies = array(
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'password',
				'label' => _T('vprofils:info_nouveau_passe'),
				'obligatoire' => 'oui',
				'type' => 'password',
			)
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'password_confirmation',
				'label' => _T('vprofils:info_confirmer_passe'),
				'obligatoire' => 'oui',
				'type' => 'password'
			)
		)
	);
	return $saisies;
}



function formulaires_modifier_motdepasse_charger_dist($id_auteur = 0) {
	$valeurs = array();
	
	$valeurs['password'] = '';
	$valeurs['password_confirmation'] = '';
	
	return $valeurs;
}



function formulaires_modifier_motdepasse_verifier_dist($id_auteur = 0) {
	$erreurs = array();
	
	if (_request('password') != _request('password_confirmation')){
		$erreurs['password_confirmation'] = _T('info_passes_identiques');
	}
	
	if ( strlen(_request('password')) < _PASS_LONGUEUR_MINI ){
		$erreurs['password'] = _T('info_passe_trop_court_car_pluriel', array('nb' => _PASS_LONGUEUR_MINI));
	}
	
	if (!_request('password')){
		$erreurs['password'] = _T('info_obligatoire');
	}
	
	if (!_request('password_confirmation')){
		$erreurs['password_confirmation'] = _T('info_obligatoire');
	}
	
	return $erreurs;
}


function formulaires_modifier_motdepasse_traiter_dist($id_auteur = 0) {
	$password = _request('password');
	set_request('new_pass', $password);
	
	$res = formulaires_editer_objet_traiter('auteur', intval($id_auteur), 0, 0, '', '');
	
	return $res;
}
