<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}


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
		$contact_set['prenom'] = _request('prenom');
		$contact_set['civilite'] = _request('civilite');
		
		contact_modifier($id_contact, $contact_set);
	}
	return $id_contact;
}


function vprofils_creer_organisation($id_contact) {
	// nom de l'organisation
	$organisation = _request('organisation');
	
	// organisation déjà enregistrée ? 
	$id_organisation = sql_getfetsel('id_organisation', 'spip_organisations', 'nom='.sql_quote($organisation));

	if (intval($id_organisation)) {
		if ($id_lien = sql_getfetsel('id_organisation', 'spip_organisations_liens', 'id_objet='.$id_contact.' and objet='.sql_quote('contact')) AND $id_lien == $id_organisation) {
			return $id_organisation;
		}
	} else {
		include_spip('action/editer_organisation');
		$id_organisation = organisation_inserer();
		$organisation_set = array();
		$organisation_set['nom'] = _request('organisation');
		organisation_modifier($id_organisation, $organisation_set);
	}
	
	// lier l'organisation au contact
	include_spip('action/editer_liens');
	objet_associer(array('organisation' => $id_organisation), array('contact' => $id_contact));
	
	return $id_organisation;
}
