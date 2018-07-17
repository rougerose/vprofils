<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

/**
 * Créer un contact et lier à un auteur existant
 * 
 * @param  int $id_auteur
 * @return int
 */
function vprofils_creer_contact($id_auteur) {
	// L'auteur a déjà un contact lié ?
	$id_contact = sql_getfetsel('id_contact', 'spip_contacts', 'id_auteur='.$id_auteur);
	
	if (!$id_contact) {
		$definir_contact = charger_fonction('definir_contact', 'action');
		$id_contact = $definir_contact('contact/'.$id_auteur);
	}
	
	// Mettre à jour le contact avec les données du formulaire
	if (intval($id_contact)) {
		include_spip('action/editer_contact');
		$contact_set = array();
		
		// Données : prénom, civilite,
		// ainsi que le nom car la fonction definir_contact a récupéré le nom
		// de l'auteur qui a été enregistré sous la forme Nom*Prénom
		$contact_set['prenom'] = _request('prenom');
		$contact_set['civilite'] = _request('civilite');
		$contact_set['nom'] = _request('nom_inscription');
		
		contact_modifier($id_contact, $contact_set);
	}
	return $id_contact;
}


/**
 * Créer une organisation et lier à un contact existant
 * 
 * @param  int $id_contact
 * @return int
 */
function vprofils_creer_organisation($id_contact) {
	// nom de l'organisation
	$organisation = _request('organisation');
	
	// organisation déjà enregistrée et déjà liée au contact ?
	if (
		$id_organisation = sql_getfetsel('id_organisation', 'spip_organisations', 'nom='.sql_quote($organisation))
		AND sql_countsel('spip_organisations_liens', 'id_objet='.$id_contact.' and objet='.sql_quote('contact').' and id_organisation='.$id_organisation)
	) {
		return $id_organisation;
	}
	
	// sinon créer l'organisation
	include_spip('action/editer_organisation');
	$id_organisation = organisation_inserer();
	$organisation_set = array();
	$organisation_set['nom'] = _request('organisation');
	organisation_modifier($id_organisation, $organisation_set);
	
	// lier l'organisation au contact
	include_spip('action/editer_liens');
	objet_associer(array('organisation' => $id_organisation), array('contact' => $id_contact));
	
	return $id_organisation;
}


/**
 * À partir du nom et prénom du contact, 
 * chercher si un auteur vacarme existe déjà. 
 * 
 * Si c'est le cas, noter les infos dans les logs pour éventuel traitement
 * ultérieur.
 * @param  int $id_contact
 */
function vprofils_verifier_doublons($id_contact) {
	$contact = sql_fetsel('id_auteur, prenom, nom', 'spip_contacts', 'id_contact='.intval($id_contact));
	$nom_prenom = mb_convert_case($contact['nom'].'*'.$contact['prenom'], MB_CASE_LOWER, "UTF-8");
	if ($auteurs = sql_allfetsel('id_auteur, nom', 'spip_auteurs', 'nom='.sql_quote($nom_prenom).' AND id_auteur!='.sql_quote(intval($contact['id_auteur'])))) {
		foreach ($auteurs as $auteur) {
			spip_log("Contact #$id_contact -- ".$contact['nom']." ".$contact['prenom']." -- est un doublon éventuel de l'auteur #".$auteur['id_auteur']." -- ".$auteur['nom']." -- ", "vprofils_auteurs_doublons"._LOG_INFO_IMPORTANTE);
		}
	}
}
