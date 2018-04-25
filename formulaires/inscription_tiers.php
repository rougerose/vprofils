<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}


function formulaires_inscription_tiers_charger_dist($statut='6forum', $retour='') {
	$valeurs = array(
		'id_payeur' => '',
		'bio' => '',
		'civilite' => '',
		'nom_inscription' => '',
		'prenom' => '',
		'mail_inscription' => '',
		'organisation' => '',
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
	
	$inscription_verifier = charger_fonction("verifier", "formulaires/inscription");
	$erreurs = $inscription_verifier($statut);
	
	$id_payeur = _request('id_payeur');
	
	if (is_null($id_payeur)) {
		$url_offrir = generer_url_public('offrir');
		$erreurs['message_erreur'] = _T('vprofils:message_erreur_formulaire_inscription_tiers_id_payeur_manquant', array('url' => $url_offrir));
		return $erreurs;
	}
	
	if (isset($erreurs['message_erreur']) AND $erreurs['message_erreur'] == _T('form_forum_email_deja_enregistre')) {
		$url_contact = generer_url_public('contact');
		$erreurs['message_erreur'] = _T('vprofils:message_erreur_formulaire_inscription_tiers_email_deja', array('url' => $url_contact));
		
		$mail_beneficiaire = _request('mail_inscription');
		spip_log("Enregistrement du bénéficiaire d'un abonnement offert par $id_payeur a échoué lors du traitement du formulaire d'inscription standard, l'adresse email $mail_beneficiaire est déjà enregistrée.", 'vprofils'._LOG_ERREUR);
	}
	
	$obligatoires = array('civilite', 'prenom', 'voie', 'code_postal', 'ville', 'pays', 'message_date');
	
	foreach ($obligatoires as $obligatoire) {
		if (!strlen(_request($obligatoire))) {
			$erreurs[$obligatoire] = _T('vprofils:erreur_' . $obligatoire . '_obligatoire');
		}
	}
	
	return $erreurs;
}


function formulaires_inscription_tiers_traiter_dist($statut='6forum', $retour='') {
	// enregistrer l'auteur
	$inscription_traiter = charger_fonction("traiter", "formulaires/inscription");
	$res = $inscription_traiter($statut);
	$id_auteur = $res['id_auteur'];
	
	$id_payeur = _request('id_payeur');
	
	// enregistrer contact et coordonnées
	if ($id_auteur) {
		
		include_spip('inc/vprofils');
		
		// récupérer ou créer le contact
		$id_contact = vprofils_creer_contact($id_auteur);
		$res['id_contact'] = $id_contact;
		
		// rectifier des données de l'auteur : 
		// - login = e-mail
		// - nom = Nom*Prénom
		$prenom = _request('prenom');
		$nom = _request('nom_inscription');
		$nom_prenom = $nom.'*'.$prenom;
		$mail = _request('mail_inscription');
		
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
		
		// Vérifier si l'auteur n'existe pas déjà comme auteur Vacarme
		// et le noter dans les logs pour un éventuel traitement ultérieur du doublon ?
		vprofils_verifier_doublons($id_contact);
		
		// créer l'organisation
		if (_request('organisation')) {
			$id_organisation = vprofils_creer_organisation($id_contact);
		}
		
		$res['id_organisation'] = $id_organisation;
		
		// adresse et liaison avec l'auteur
		include_spip('inc/editer');
		
		$adresse = formulaires_editer_objet_traiter('adresse', 'new');
		
		if (isset($adresse['id_adresse'])) {
			include_spip('action/editer_liens');
			objet_associer(array('adresse' => $adresse['id_adresse']), array('auteur' => $id_auteur), array('type' => 'livraison'));
			
			$res['id_adresse'] = $adresse['id_adresse'];
		}
		
		// message et date d'envoi. 
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
		
		$res['message_ok'] = _T('vprofils:message_ok_formulaire_inscription_tiers');
		
		// redirection
		if ($retour) {
			$res['redirect'] = $retour;
		}
		
	} else {
		$erreur = $res['message_erreur'];
		$id_payeur = _request('id_payeur');
		spip_log("Enregistrement du bénéficiaire d'un abonnement offert par $id_payeur a échoué lors du traitement du formulaire d'inscription standard. Message d'erreur : $erreur", 'vprofils'._LOG_ERREUR);
		
		$url_contact = generer_url_public('contact');
		$res['message_erreur'] = _T('vprofils:message_erreur_formulaire_inscription_tiers', array('url' => $url_contact));
	}
	
	return $res;
}
