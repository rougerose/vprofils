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


function vprofils_creer_organisation($id_auteur, $id_contact) {
	// nom de l'organisation
	$organisation = _request('organisation');
	$service = _request('service');
	
	// organisation déjà enregistrée ? 
	$id_organisation = sql_getfetsel('id_organisation', 'spip_organisations', 'nom='.$organisation);
	
	if (!$organisation) {
		include_spip('action/editer_liens');
		$id_organisation = $definir_contact('organisation/'.$id_auteur);
		
		include_spip('action/editer_organisation');
		$organisation_set = array();
		
		// nom
		$organisation_set['nom'] = _request('organisation');
		
		// service dans le descriptif
		// TODO: ajouter un champ extra #SERVICE via vextras ?
		$organisation_set['descriptif'] = _request('service');
		
		organisation_modifier($id_organisation, $organisation_set);
		
		// lier l'organisation au contact précédemment créé
		objet_associer(array('organisation' => $id_organisation), array('contact' => $id_contact));
	}
	// 
	// API SPIP de liaison des objets organisation et adresse.
	
	
	// mettre à jour
	
	return $id_organisation;
}
