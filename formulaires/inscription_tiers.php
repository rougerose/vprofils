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
	
	$inscription_verifier = charger_fonction("verifier","formulaires/inscription");
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
	
	$obligatoire = array();
	$obligatoire['civilite'] = _request('civilite');
	$obligatoire['prenom'] = _request('prenom');
	$obligatoire['voie'] = _request('voie');
	$obligatoire['code_postal'] = _request('code_postal');
	$obligatoire['ville'] = _request('ville');
	$obligatoire['pays'] = _request('pays');
	$obligatoire['message_date'] = _request('message_date');
	
	foreach ($obligatoire as $champ => $valeur) {
		if (!$valeur) {
			$erreurs[$champ] = _T('info_obligatoire');
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
	if (isset($id_auteur)) {
		include_spip('inc/vprofils');
		
		// récupérer ou créer le contact
		$id_contact = vprofils_creer_contact($id_auteur);
		
		// créer l'organisation
		if (_request('organisation')) {
			vprofils_creer_organisation($id_contact);
		}
		
		// adresse et liaison avec l'auteur
		include_spip('inc/editer');
		
		$adresse = formulaires_editer_objet_traiter('adresse', 'new');
		
		if (isset($adresse['id_adresse'])) {
			include_spip('action/editer_liens');
			objet_associer(array('adresse' => $adresse['id_adresse']), array('auteur' => $id_auteur), array('type' => 'livraison'));
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
				'destinataires' => $id_payeur)
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
