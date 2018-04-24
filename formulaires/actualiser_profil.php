<?php

if (!defined("_ECRIRE_INC_VERSION")) {
    return;
}

include_spip('inc/editer');

function formulaires_actualiser_profil_identifier_dist($id_contact = 'new') {
	return serialize(array(intval($id_contact)));
}


function formulaires_actualiser_profil_charger_dist($id_contact = 'new', $redirect = '') {
	$valeurs = formulaires_editer_objet_charger('contact', $id_contact, $id_organisation = 0, 0, '', '');
	$valeurs['email'] = '';
	
	return $valeurs;
}


function formulaires_actualiser_profil_verifier_dist($id_contact = 'new', $redirect = '') {
	$erreurs = array();
	$id_auteur = sql_getfetsel('id_auteur', 'spip_contacts', 'id_contact='.intval($id_contact));
	$erreurs_contact = formulaires_editer_objet_verifier('contact', intval($id_contact), array('civilite', 'nom', 'prenom'));
  	$auteur_verifier = charger_fonction("verifier","formulaires/editer_auteur");
  	$erreurs_auteur = $auteur_verifier($id_auteur);
	$erreurs = array_merge($erreurs_contact, $erreurs_auteur);
	return $erreurs;
}


function formulaires_actualiser_profil_traiter_dist($id_contact = 'new', $redirect = '') {
	$res = formulaires_editer_objet_traiter('contact', $id_contact);
	$nom = _request('nom');
	$prenom = _request('prenom');
	$email = _request('email');
	$id_auteur = sql_getfetsel('id_auteur', 'spip_contacts', 'id_contact='.intval($id_contact));
	$auteur = sql_fetsel('nom, email', 'spip_auteurs', 'id_auteur='.intval($id_auteur));
	
	if (isset($res['message_ok']) AND $nom != $auteur['nom'] OR $email != $auteur['email']) {
		include_spip('action/editer_auteur');
		$auteur_set = array();
		$auteur_set['nom'] = $nom.'*'.$prenom;
		$auteur_set['email'] = $email;
		$err = auteur_modifier(intval($id_auteur));
	
		if ($err) {
			$res['message_erreur'] = $err; 
		}
	}
	if ($res['id_contact'] AND $res['message_ok'] AND $redirect) {
		$res['redirect'] = $redirect;
	}
	return $res;
}
