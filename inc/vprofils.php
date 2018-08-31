<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

/**
 * Vérifier l'existence d'un auteur tiers (bénéficiaire abonnement offert)
 * sinon créer son profil.
 * 
 * @param  array $champs
 * @return int|bool
 */
function vprofils_verifier_ou_creer_auteur_tiers($champs) {
	$id_auteur = sql_getfetsel('id_auteur', 'spip_auteurs', 'email='.sql_quote($champs['mail_inscription']));
	
	if (!$id_auteur) {
		
		$nom = $champs['nom_inscription'].'*'.$champs['prenom'];
		$login = $champs['mail_inscription'];
		$email = $champs['mail_inscription'];
		$statut = '6forum';
		
		$inscrire_auteur = charger_fonction('inscrire_auteur', 'action');
		
		$auteur = $inscrire_auteur($statut, $email, $nom, array('login' => $login));
		
		if (!is_array($auteur)) {
			spip_log("Erreur lors de la création du profil auteur tiers ".var_export($champs, true), 'vprofils'._LOG_ERREUR);
			return false;
		}
		
		$id_auteur = $auteur['id_auteur'];
	}
	
	return $id_auteur;
}


/**
 * Vérifier l'existence d'un contact pour un auteur tiers (bénéficiaire 
 * d'un abonnement offert), ou bien le créer.
 * 
 * @param  int $id_auteur
 * @param  array $champs
 * @return int|bool
 */
function vprofils_verifier_ou_creer_contact_auteur_tiers($id_auteur, $champs) {
	$id_contact = sql_getfetsel('id_contact', 'spip_contacts', 'id_auteur='.intval($id_auteur));
	
	if(!$id_contact) {
		
		include_spip("action/editer_contact");
		$id_contact = contact_inserer();
		
		if ($id_contact) {
			$set = array(
				'id_auteur' => $id_auteur,
				'civilite' => $champs['civilite'],
				'nom' => $champs['nom_inscription'],
				'prenom' => $champs['prenom'],
			);
			autoriser_exception('modifier', 'contact', $id_contact);
			contact_modifier($id_contact, $set);
			autoriser_exception('modifier', 'contact', $id_contact, false);
			
			
			// Vérifier
			$row = sql_fetsel('*', 'spip_contacts', 'id_contact='.intval($id_contact));
			
			if (!$row['nom']) {
				spip_log("Erreur lors de la création du profil contact tiers $id_contact".var_export($champs, true), 'vprofils'._LOG_ERREUR);
				
				return false;
			}
			
			return $id_contact;
		}
		return false;
	}
	return $id_contact;
}


/**
 * Vérifier l'existence d'une adresse pour un auteur tiers 
 * (bénéficiaire d'un abonnement offert), ou la créer.
 * 
 * @param  int $id_auteur
 * @param  array $champs
 * @return int|bool
 */
function vprofils_verifier_ou_creer_coordonnees_tiers($id_auteur, $champs) {
	$type_defaut = _ADRESSE_TYPE_DEFAUT;
	
	// Chercher un id_adresse, mais la vérification ne va pas plus loin : 
	// ce sera au tiers (bénéficiaire de l'abonnement) de vérifier
	// son adresse lors de l'activation de son abonnement.
	$id_adresse = sql_getfetsel('id_adresse', 'spip_adresses_liens', 'objet='.sql_quote('auteur').' AND id_objet='.sql_quote(intval($id_auteur)).' AND type='.sql_quote($type_defaut));
	
	// Sinon on crée l'adresse avec les informations communiquées par le payeur
	// et elles seront vérifiées également par le bénéficiaire à l'activation.
	if (!$id_adresse) {
		include_spip("action/editer_adresse");
		
		$id_adresse = insert_adresse(array(
			'objet' => 'auteur',
			'id_objet' => intval($id_auteur),
			'type' => $type_defaut
		));
		
		if ($id_adresse) {
			$set = array(
				'voie' => $champs['voie'],
				'complement' => $champs['complement'],
				'boite_postale' => $champs['boite_postale'],
				'code_postal' => $champs['code_postal'],
				'ville' => $champs['ville'],
				'region' => $champs['region'],
				'pays' => $champs['pays'],
				'organisation' => $champs['organisation'],
				'service' => $champs['service'],
			);
			
			autoriser_exception('modifier', 'adresse', $id_adresse);
			revisions_adresses($id_adresse, $set);
			autoriser_exception('modifier', 'adresse', $id_adresse, false);
			
			return $id_adresse;
		}
		
		return false;
	}
	return $id_adresse;
}


/**
 * Créer un contact et lier à un auteur existant
 * 
 * @param  int $id_auteur
 * @return int
 */
/*function vprofils_creer_contact($id_auteur) {
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
		$contact_set['civilite'] = _request('civilite');
		$contact_set['prenom'] = _request('prenom');
		$contact_set['nom'] = _request('nom_inscription');
		$contact_set['organisation'] = _request('organisation');
		$contact_set['service'] = _request('service');
		
		contact_modifier($id_contact, $contact_set);
	}
	return $id_contact;
}
*/

/**
 * Créer une organisation et lier à un contact existant
 * 
 * @param  int $id_contact
 * @return int
 */
/*function vprofils_creer_organisation($id_contact) {
	// nom de l'organisation
	$organisation = _request('organisation');
	$service = (_request('service')) ? _request('service') : '';

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
	$organisation_set['nom'] = $organisation;
	$organisation_set['service'] = $service;
	organisation_modifier($id_organisation, $organisation_set);

	// lier l'organisation au contact
	include_spip('action/editer_liens');
	objet_associer(array('organisation' => $id_organisation), array('contact' => $id_contact));

	return $id_organisation;
}
*/

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
			spip_log("Contact #$id_contact -- ".$contact['nom']." ".$contact['prenom'].", auteur #".$contact['id_auteur']." -- est un doublon éventuel de l'auteur #".$auteur['id_auteur']." -- ".$auteur['nom']." -- ", "vprofils_auteurs_doublons"._LOG_INFO_IMPORTANTE);
		}
	}
}
