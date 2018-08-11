<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

include_spip("base/abstract_sql");
include_spip('inc/editer');



function formulaires_modifier_profil_saisies_dist($id_auteur, $retour = '', $option = '') {
	$id_contact = sql_getfetsel('id_contact', 'spip_contacts', 'id_auteur='.intval($id_auteur));
	
	$saisies_id = array(
		array(
			'saisie' => 'hidden',
			'options' => array(
				'nom' => 'type_client',
				'defaut' => 'contact'
			)
		),
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'groupe_identite',
				'label' => _T('vprofils:formulaire_compte_titre'),
				'conteneur_class' => 'formulaire__groupe formulaire__groupe--identite'
			),
			'saisies' => array(
				array(
					'saisie' => 'civilite',
					'options' => array(
						'nom' => 'civilite',
						'label' => _T('vprofils:formulaire_civilite_label'),
						'obligatoire' => 'oui'
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'nom',
						'label' => _T('vprofils:formulaire_nom_label'),
						'obligatoire' => 'oui',
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'prenom',
						'label' => _T('vprofils:formulaire_prenom_label'),
						'obligatoire' => 'oui'
					)
				),
				array(
					'saisie' => 'email',
					'options' => array(
						'nom' => 'email',
						'label' => _T('vprofils:formulaire_mail_label'),
						'obligatoire' => 'oui'
					)
				)
			)
		)
	);
	
	if ($option && $option == 'password') {
		// Ajouter les champs Mot de passe au fieldset des saisies_id
		$saisies_id[1]['saisies'][] = array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'password',
				'label' => _T('entree_mot_passe'),
				'obligatoire' => 'oui',
				'type' => 'password',
			)
		);
		$saisies_id[1]['saisies'][] = array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'password_confirmation',
				'label' => _T('vprofils:info_confirmer_passe'),
				'obligatoire' => 'oui',
				'type' => 'password'
			)
		);
	}
	
	// 
	// Les saisies d'adresse
	// 
	$saisies_ad = array(
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'groupe_adresse',
				'label' => _T('vprofils:formulaire_coordonnees_titre'),
				'conteneur_class' => 'formulaire__groupe formulaire__groupe--adresse'
			),
			'saisies' => array(
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'organisation',
						'label' => _T('vprofils:formulaire_organisation_label')
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'service',
						'label' => _T('vprofils:formulaire_service_label')
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'voie',
						'label' => _T('vprofils:formulaire_voie_label'),
						'obligatoire' => 'oui'
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'complement',
						'label' => _T('vprofils:formulaire_complement_label')
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'boite_postale',
						'label' => _T('vprofils:formulaire_boite_postale_label'),
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'code_postal',
						'label' => _T('vprofils:formulaire_code_postal_label'),
						'obligatoire' => 'oui'
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'ville',
						'label' => _T('vprofils:formulaire_ville_label'),
						'obligatoire' => 'oui'
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'region',
						'label' => _T('vprofils:formulaire_region_label'),
					)
				),
				array(
					'saisie' => 'pays',
					'options' => array(
						'nom' => 'pays',
						'label' => _T('vprofils:formulaire_pays_label'),
						'defaut' => 'FR',
						'code_pays' => 'oui',
						'obligatoire' => 'oui'
					)
				)
			)
		)
	);
	
	$saisies = array_merge($saisies_id, $saisies_ad);
	
	return $saisies;
}


function formulaires_modifier_profil_charger_dist($id_auteur, $retour = '', $option = '') {
	if (!$id_auteur OR !$auteur = sql_fetsel('*', 'spip_auteurs', 'id_auteur='.intval($id_auteur))) {
		return false;
	}
	
	$valeurs = array();
	
	if ($contact = sql_fetsel('*', 'spip_contacts', 'id_auteur='.intval($id_auteur))) {
		$valeurs['type_client'] = 'contact';
		$valeurs['civilite'] = $contact['civilite'];
		$valeurs['nom'] = $contact['nom'];
		$valeurs['prenom'] = $contact['prenom'];
		$valeurs['email'] = $auteur['email'];
		$valeurs['organisation'] = $contact['organisation'];
		$valeurs['service'] = $contact['service'];
	}
	
	// 
	// Coordonnees
	// 
	if ($id_adresse = sql_getfetsel('id_adresse', 'spip_adresses_liens', 'id_objet='.intval($id_auteur).' AND objet='.sql_quote('auteur').' AND type='.sql_quote(_ADRESSE_TYPE_DEFAUT))) {
		
		$adresse = sql_fetsel('*', 'spip_adresses', 'id_adresse='.intval($id_adresse));
		
	}
	
	//
	// Mot de passe
	// 
	if ($option && $option == 'password') {
		$valeurs['password'] = '';
		$valeurs['password_confirmation'] = '';
	}
	
	$valeurs['voie'] = isset($adresse['voie']) ? $adresse['voie'] : '';
	$valeurs['complement'] = isset($adresse['complement']) ? $adresse['complement'] : '';
	$valeurs['boite_postale'] = isset($adresse['boite_postale']) ? $adresse['boite_postale'] : '';
	$valeurs['code_postal'] = isset($adresse['code_postal']) ? $adresse['code_postal'] : '';
	$valeurs['ville'] = isset($adresse['ville']) ? $adresse['ville'] : '';
	$valeurs['region'] = isset($adresse['region']) ? $adresse['region'] : '';
	$valeurs['pays'] = isset($adresse['pays']) ? $adresse['pays'] : '';
	
	return $valeurs;
}



function formulaires_modifier_profil_verifier_dist($id_auteur, $retour = '', $option = '') {
	$erreurs = array();

	$auteur = sql_fetsel('*', 'spip_auteurs', 'id_auteur='.intval($id_auteur));
	
	// 
	// Les champs obligatoires
	// 
	$obligatoires = array('civilite', 'nom', 'prenom', 'email', 'voie', 'code_postal', 'ville', 'pays');
	
	foreach ($obligatoires as $obligatoire) {
		if (!strlen(_request($obligatoire))) {
			$erreurs[$obligatoire] = _T('vprofils:erreur_' . $obligatoire . '_obligatoire');
		}
	}
	
	// 
	// Vérifier l'email
	// 
	if (!isset($erreurs['email'])) {
		
		$email = trim(_request('email'));
		
		if (!email_valide($email)) {
			
			$erreurs['email'] = _T('vprofils:erreur_email_invalide');
			
		} elseif ($auteur['email'] == $auteur['login']) {
			
			// 
			// si email=login verifier l'unicité
			// 
			if (sql_countsel("spip_auteurs", "(email=".sql_quote($email)." OR login=".sql_quote($email).") AND id_auteur!=".intval($id_auteur))){
				$erreurs['email'] = _T('vprofils:erreur_email_doublon');
				
			}
		}
	}
	
	if (_request('service') and !_request('organisation')) {
		$erreurs['organisation'] = _T('vprofils:erreur_si_service_organisation_nonvide');
	}
	
	//
	// Mot de passe
	// 
	if ($option && $option == 'password') {
		if (_request('password') != _request('password_confirmation')){
			$erreurs['password_confirmation'] = _T('info_passes_identiques');
		}
		
		if ( strlen(_request('password')) < _PASS_LONGUEUR_MINI ){
			$erreurs['password'] = _T('info_passe_trop_court_car_pluriel', array('nb' => _PASS_LONGUEUR_MINI));
		}
		
		if (!_request('password')){
			$erreurs['password'] = _T('info_obligatoire');
		}
		
		if (!_request('password_confirmation')){
			$erreurs['password_confirmation'] = _T('info_obligatoire');
		}
	}
	
	
	return $erreurs;
}



function formulaires_modifier_profil_traiter_dist($id_auteur, $retour = '', $option = '') {
	if ($retour) {
		refuser_traiter_formulaire_ajax();
	}
	
	$res = array();
	
	$type_client = _request('type_client');
	$auteur = sql_fetsel('*', 'spip_auteurs', 'id_auteur='.intval($id_auteur));
	
	$email = _request('email');
	$nom = _request('nom');
	$prenom = _request('prenom');
	
	//
	// Modification de l'email et éventuellement du nom
	// 
	if ($email !== $auteur['email']) {
		$email_nouveau = $email;
		
		// 
		// si l'email est aussi le login, alors il faut modifier
		// 
		if ($auteur['email'] == $auteur['login']) {
			set_request('login', $email_nouveau);
		}
	}
	
	$nom_prenom = $nom.'*'.$prenom;
	set_request('nom', $nom_prenom);
	
	// Mot de passe
	if ($option && $option == 'password') {
		$password = _request('password');
		set_request('new_pass', $password);
	}
	
	// 
	// Modification des champs Auteur
	// 
	$res_auteur = formulaires_editer_objet_traiter('auteur', intval($id_auteur), 0, 0, $retour, '');
	
	if (isset($res_auteur['message_erreur'])) {
		return $res['message_erreur'] = $res_auteur['message_erreur'];
	} else {
		$res['message_ok'] = $res_auteur['message_ok'];
	}
	
	// 
	// Rétablir le champ Nom tel que validé par l'auteur
	// 
	set_request('nom', $nom);
	
	if ($type_client == 'contact') {
		// 
		// Modification des champs Contact
		// 
		$id_contact = sql_getfetsel('id_contact', 'spip_contacts', 'id_auteur='.intval($id_auteur));
		$organisation = _request('organisation');
		$service = _request('service');
		$res_contact = formulaires_editer_objet_traiter('contact', intval($id_contact), 0, 0, $retour, '');
		
		if (isset($res_contact['message_erreur'])) {
			return $res['message_erreur'] = $res_contact['message_erreur'];
		} else {
			$res['message_ok'] = $res_auteur['message_ok'];
		}
	} 
	
	// 
	// Modification des champs Adresse
	// 
	$id_adresse = sql_getfetsel('id_adresse', 'spip_adresses_liens', 'id_objet='.intval($id_auteur).' AND objet='.sql_quote('auteur').' AND type='.sql_quote(_ADRESSE_TYPE_DEFAUT));
	
	if (!$id_adresse) {
		$id_adresse = 'new';
	}
	
	$res_adresse = formulaires_editer_objet_traiter('adresse', $id_adresse);
	
	if ($id_adresse == 'new' AND $res_adresse['id_adresse']) {
		include_spip('action/editer_liens');
		objet_associer(array('adresse' => $res_adresse['id_adresse']), array('auteur' => intval($id_auteur)), array('type' => _ADRESSE_TYPE_DEFAUT));
	}
	
	if (isset($res_adresse['message_erreur'])) {
		return $res['message_erreur'] = $res_adresse['message_erreur'];
	} else {
		$res['message_ok'] = $res_adresse['message_adresse'];
	}

	if (!isset($res['message_erreur']) and !isset($res['message_ok'])) {
		$res['message_ok'] = _T('info_modification_enregistree');
		$res['editable'] = true;
	}
	
	if ($retour) {
		$res['redirect'] = $retour;
		$res['editable'] = true;
	}
	
	return $res;
}
