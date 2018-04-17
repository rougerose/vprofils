<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}


function formulaires_inscription_tiers_charger_dist($statut='6forum', $retour='') {
	$valeurs = array(
		'bio' => '',
		'civilite' => '',
		'nom_inscription' => '',
		'prenom' => '',
		'mail_inscription' => '',
		'type_organisation' => '',
		'organisation' => '',
		'service' => '',
		'voie' => '',
		'complement' => '',
		'boite_postale' => '',
		'code_postal' => '',
		'ville' => '',
		'region' => '',
		'pays' => '',
		'texte_message' => '',
		'date_message' => ''
	);
	
	$message_charger = charger_fonction("charger", "formulaires/ecrire_message");
	$message = $message_charger($retour, '');
	$valeurs['texte_message'] = $message['texte_message'];
	return $valeurs;
}


function formulaires_inscription_tiers_verifier_dist($statut='6forum', $retour='') {
	$erreurs = array();
	$inscription_verifier = charger_fonction("verifier","formulaires/inscription");
	$erreurs = $inscription_verifier($statut);
	
	$obligatoire = array();
	$obligatoire['civilite'] = _request('civilite');
	$obligatoire['prenom'] = _request('prenom');
	$obligatoire['voie'] = _request('voie');
	$obligatoire['code_postal'] = _request('code_postal');
	$obligatoire['ville'] = _request('ville');
	$obligatoire['pays'] = _request('pays');
	$obligatoire['message_date'] = _request('message_date');
	
	$organisation = _request('type_organisation');
	
	if ($type_organisation == 'on') {
		$obligatoire['organisation'] = _request('organisation');
	}
	
	foreach ($obligatoire as $champ => $valeur) {
		if (!$valeur) {
			$erreurs[$champ] = _T('info_obligatoire');
		}
	}
		
	return $erreurs;
}


function formulaires_inscription_tiers_traiter_dist($statut='6forum', $retour='') {
	$inscription_traiter = charger_fonction("traiter","formulaires/inscription");
	$res = $inscription_traiter($statut); // res = message_ok et id_auteur
	
	
	return $res;
}
