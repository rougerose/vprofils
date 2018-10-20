<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}


function formulaires_inscription_tiers_charger_dist($statut='6forum', $retour='') {
	$valeurs = array(
		'_id_abonnement' => '',
		'id_payeur' => '',
		'civilite' => '',
		'nom_inscription' => '',
		'prenom' => '',
		'mail_inscription' => '',
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
	return $valeurs;
}


function formulaires_inscription_tiers_verifier_dist($statut='6forum', $retour='') {
	$erreurs = array();
	
	include_spip('inc/editer');
	include_spip('inc/filtres');
	
	if (!$nom = _request('nom_inscription')) {
		$erreurs['nom_inscription'] = _T('info_obligatoire');
	} elseif (!nom_acceptable(_request('nom_inscription'))) {
		$erreurs['nom_inscription'] = _T('ecrire:info_nom_pas_conforme');
	}
	
	if (!$mail = strval(_request('mail_inscription'))) {
		$erreurs['mail_inscription'] = _T('info_obligatoire');
	}
	
	if (!_request('organisation') and _request('service')) {
		$erreurs['organisation'] = _T('vprofils:erreur_si_service_organisation_nonvide');
	}
	
	$id_abonnements_offre = _request('_id_abonnements_offre');
	$id_payeur = _request('id_payeur');
	
	if (is_null($id_payeur)) {
		$url_offrir = generer_url_public('offrir');
		$erreurs['message_erreur'] = _T('vprofils:message_erreur_formulaire_inscription_tiers_id_payeur_manquant', array('url' => $url_offrir));
		return $erreurs;
	}
	
	if (isset($erreurs['message_erreur']) AND $erreurs['message_erreur'] == _T('form_forum_email_deja_enregistre')) {
		$url_contact = generer_url_public('contact');
		$erreurs['message_erreur'] = _T('vprofils:message_erreur_formulaire_inscription_tiers_email_deja', array('url' => $url_contact));
		
		// $mail_beneficiaire = _request('mail_inscription');
		// spip_log("Enregistrement du bénéficiaire d'un abonnement offert par $id_payeur a échoué lors du traitement du formulaire d'inscription standard, l'adresse email $mail_beneficiaire est déjà enregistrée.", 'vprofils'._LOG_ERREUR);
	}
	
	$obligatoires = array('civilite', '_id_abonnement', 'prenom', 'voie', 'code_postal', 'ville', 'pays', 'message_date');
	
	foreach ($obligatoires as $obligatoire) {
		if (!strlen(_request($obligatoire))) {
			$erreurs[$obligatoire] = _T('vprofils:erreur_' . $obligatoire . '_obligatoire');
		}
	}
	
	return $erreurs;
}


function formulaires_inscription_tiers_traiter_dist($statut='6forum', $retour='') {
	$res = array();
	include_spip('inc/vprofils');
	
	$mail = _request('mail_inscription');
	
	// 
	// le bénéficiaire est déjà enregistré ?
	$auteur = sql_fetsel('*', 'spip_auteurs', 'email = '.sql_quote($mail));
	$id_auteur = intval($auteur['id_auteur']);
	$id_payeur = _request('id_payeur');
	
	// 
	// Si l'auteur existe déjà, on ne tient pas compte des données identité et adresse
	// indiquées par le payeur. Ce sera vérifié avec le bénéficiaire lorsqu'il
	// validera le code cadeau.
	// 
	// Si l'auteur n'existe pas, on traite normalement tout le formulaire. 
	// 
	if (!$id_auteur) {
		$inscription_traiter = charger_fonction("traiter", "formulaires/inscription");
		$res = $inscription_traiter($statut);
		$id_auteur = $res['id_auteur'];
		
		if (!$id_auteur) {
			$erreur = $res['message_erreur'];
			$id_payeur = _request('id_payeur');
			spip_log("Enregistrement du bénéficiaire d'un abonnement offert par $id_payeur a échoué lors du traitement du formulaire d'inscription standard. Message d'erreur : $erreur", 'vprofils'._LOG_ERREUR);
		
			$url_contact = generer_url_public('contact');
			$res['message_erreur'] = _T('vprofils:message_erreur_formulaire_inscription_tiers', array('url' => $url_contact));
			
			return $res;
		}
		
		// rectifier des données de l'auteur : 
		// - login = e-mail
		// - nom = Nom*Prénom
		$prenom = _request('prenom');
		$nom = _request('nom_inscription');
		$nom_prenom = $nom.'*'.$prenom;
		
		// autoriser la modification de l'auteur
		include_spip('inc/autoriser');
		autoriser_exception('modifier', 'auteur', $id_auteur);
		
		// modifier les données de l'auteur
		include_spip('action/editer_auteur');
		$err = auteur_modifier($id_auteur, array(
			'nom' => $nom_prenom,
			'login' => $mail
		));
		// retirer l'autorisation exceptionnelle
		autoriser_exception('modifier', 'auteur', $id_auteur, false);
		
		// adresse et liaison avec l'auteur
		include_spip('inc/editer');
		
		$adresse = formulaires_editer_objet_traiter('adresse', 'new');
		
		if (isset($adresse['id_adresse'])) {
			include_spip('action/editer_liens');
			objet_associer(array('adresse' => $adresse['id_adresse']), array('auteur' => $id_auteur), array('type' => _ADRESSE_TYPE_DEFAUT));
			
			$res['id_adresse'] = $adresse['id_adresse'];
		}
	}
	
	// 
	// Récupérer ou créer le contact
	// 
	if (!$contact = sql_fetsel('*', 'spip_contacts', 'id_auteur='.$id_auteur)) {
		$definir_contact = charger_fonction('definir_contact', 'action');
		$id_contact = $definir_contact('contact/'.$id_auteur);
		if (!$id_contact) {
			spip_log("Erreur lors de l'inscription de $nom $prenom (login : $mail) après l'étape de création du contact. L'identifiant auteur utilisé est #$id_auteur", 'vprofils_erreurs_inscription'._LOG_ERREUR);
			return $res['message_erreur'] = _T('vprofils:message_erreur_inscription');
		} 
	}
	
	if ($id_contact or $id_contact = $contact['id_contact']) {
		include_spip('action/editer_contact');
		contact_modifier($id_contact, $set = array(
			'civilite' => $civilite,
			'prenom' => $prenom,
			'nom' => $nom)
		);
	}
	
	// 
	// Message et date d'envoi
	// 
	$texte_message = _request('texte_message');
	
	if (!empty($texte_message)) {
		$objet = " ";
		$date_envoi = date('Y-m-d H:i:s', _request('message_date'));
		$type = "kdo";
		
		$id_message = sql_insertq('spip_messages', array(
			'titre' => safehtml($objet),
			'texte' => safehtml($texte_message),
			'type' => $type,
			'date_heure' => $date_envoi,
			'date_fin' => $date_envoi,
			'rv' => 'non',
			'statut' => 'prepa',
			'id_auteur' => $id_payeur,
			'destinataires' => $id_auteur)
		);
	}
	
	// 
	// Modifier le panier.
	// Au niveau de l'item contenant l'abonnement offert,
	// la valeur de l'option coupon (oui) est modifiée pour contenir l'id_auteur
	// du bénéficiaire.
	// 
	include_spip('inc/paniers');
	$id_panier = paniers_id_panier_encours();

	// 
	// id de l'offre abonnement concernée par le cadeau
	// 
	$ids = _request('_id_abonnement');
	@list($id_objet, $cle) = explode('-', $ids);
	
	$panier = sql_fetsel('id_objet, options', 'spip_paniers_liens', 'id_panier='.$id_panier.' and objet='.sql_quote('abonnements_offre').' and id_objet='.$id_objet);

	$options = unserialize($panier['options']);

	if ($options[$cle][0] == 'oui') {
		
		$options[$cle][0] = $id_auteur;
		
		sql_updateq('spip_paniers_liens', array('options' => serialize($options)), 'id_panier='.$id_panier.' and objet='.sql_quote('abonnements_offre').' and id_objet='.$id_objet);
	}
	
	$res['message_ok'] = _T('vprofils:message_ok_formulaire_inscription_tiers');
	
	// 
	// Redirection éventuelle
	// 
	if ($retour) {
		$res['redirect'] = $retour;
	}
	
	return $res;
}
