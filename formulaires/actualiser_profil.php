<?php

if (!defined("_ECRIRE_INC_VERSION")) {
    return;
}

include_spip('inc/editer');

function formulaires_actualiser_profil_identifier_dist($id_contact = 'new') {
	return serialize(array(intval($id_contact)));
}


function formulaires_actualiser_profil_charger_dist($id_contact = 'new', $redirect = '') {
	$contexte = formulaires_editer_objet_charger('contact', $id_contact, '', '', $redirect, '');
	return $contexte;
}


function formulaires_actualiser_profil_verifier_dist($id_contact = 'new', $redirect = '') {
	$erreurs = formulaires_editer_objet_verifier('contact', $id_contact, array('civilite', 'nom', 'prenom'));
	return $erreurs;
}


function formulaires_actualiser_profil_traiter_dist($id_contact = 'new', $redirect = '') {
	$res = formulaires_editer_objet_traiter('contact', $id_contact, '', '', $redirect, '');
	return $res;
}
