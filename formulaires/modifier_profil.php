<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

include_spip("base/abstract_sql");
include_spip('inc/editer');


/**
 * Saisies du formulaire modification de profil abonné/client
 * @param  int $id_auteur
 * @param  string $retour URL de redirection
 * @param  string $option Si password les champs sont ajoutés
 * @return array
 */
function formulaires_modifier_profil_saisies_dist($id_auteur, $retour = '', $option = '') {
	// saisies données personnelles
	$saisies_id = array(
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
	
	if ($option and $option == 'password') {
		// Ajouter les champs Mot de passe au fieldset des saisies_id
		$saisies_id[0]['saisies'][] = array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'password',
				'label' => _T('entree_mot_passe'),
				'obligatoire' => 'oui',
				'type' => 'password',
			)
		);
		$saisies_id[0]['saisies'][] = array(
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
						// 'defaut' => 'FR',
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


/**
 * Formulaire modifier profil, chargement
 * @param  int $id_auteur
 * @param  string $retour URL de redirection
 * @param  string $option Si password les champs sont ajoutés
 * @return array
 */
function formulaires_modifier_profil_charger_dist($id_auteur, $retour = '', $option = '') {
	$id_auteur = intval($id_auteur);
	$auteur = sql_fetsel('*', 'spip_auteurs', 'id_auteur='.$id_auteur);
	if (!$id_auteur or !$auteur) {
		return false;
	}
	
	$valeurs = array();
	
	if ($contact = sql_fetsel('*', 'spip_contacts', 'id_auteur='.intval($id_auteur))) {
		$valeurs['civilite'] = isset($contact['civilite']) ? $contact['civilite'] : '';
		$valeurs['nom'] = isset($contact['nom']) ? $contact['nom'] : '';
		$valeurs['prenom'] = isset($contact['prenom']) ? $contact['prenom'] : '';
		$valeurs['email'] = isset($auteur['email']) ? $auteur['email'] : '';
	}
	
	// 
	// Coordonnees
	// 
	$adresse = sql_fetsel('*', 'spip_adresses AS adresses INNER JOIN spip_adresses_liens AS L1 ON (L1.id_adresse = adresses.id_adresse)', 'L1.id_objet='.$id_auteur.' AND L1.objet='.sql_quote('auteur'));
	
	$valeurs['organisation'] = isset($adresse['organisation']) ? $adresse['organisation'] : '';
	$valeurs['service'] = isset($adresse['service']) ? $adresse['service'] : '';
	$valeurs['voie'] = isset($adresse['voie']) ? $adresse['voie'] : '';
	$valeurs['complement'] = isset($adresse['complement']) ? $adresse['complement'] : '';
	$valeurs['boite_postale'] = isset($adresse['boite_postale']) ? $adresse['boite_postale'] : '';
	$valeurs['code_postal'] = isset($adresse['code_postal']) ? $adresse['code_postal'] : '';
	$valeurs['ville'] = isset($adresse['ville']) ? $adresse['ville'] : '';
	$valeurs['region'] = isset($adresse['region']) ? $adresse['region'] : '';
	$valeurs['pays'] = isset($adresse['pays']) ? $adresse['pays'] : '';
	
	//
	// Mot de passe
	// 
	if ($option and $option == 'password') {
		$valeurs['password'] = '';
		$valeurs['password_confirmation'] = '';
	}
	
	return $valeurs;
}


/**
 * Formulaire modifier profil, vérification
 * @param  int $id_auteur
 * @param  string $retour URL de redirection
 * @param  string $option Si password les champs sont ajoutés
 * @return array
 */
function formulaires_modifier_profil_verifier_dist($id_auteur, $retour = '', $option = '') {
	$erreurs = array();
	
	$obligatoires = array('civilite', 'nom', 'prenom', 'email', 'voie', 'code_postal', 'ville', 'pays');
	
	foreach ($obligatoires as $obligatoire) {
		if (!strlen(_request($obligatoire))) {
			$erreurs[$obligatoire] = _T('vprofils:erreur_' . $obligatoire . '_obligatoire');
		}
	}
	
	// 
	// Vérifications supplémentaires
	// 
	
	// Organisation et service
	if (_request('service') and !_request('organisation')) {
		$erreurs['organisation'] = _T('vprofils:erreur_si_service_organisation_nonvide');
	}
	
	// Email
	if (!isset($erreurs['email'])) {
		$auteur = sql_fetsel('*', 'spip_auteurs', 'id_auteur='.intval($id_auteur));
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
	
	// Mot de passe
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


/**
 * Formulaire modifier profil, traitement
 * @param  int $id_auteur
 * @param  string $retour URL de redirection
 * @param  string $option Si password les champs sont ajoutés
 * @return array
 */
function formulaires_modifier_profil_traiter_dist($id_auteur, $retour = '', $option = '') {
	if ($retour) {
		refuser_traiter_formulaire_ajax();
	}
	
	$res = array();
	$id_auteur = intval($id_auteur);
	$auteur = sql_fetsel('*', 'spip_auteurs', 'id_auteur='.$id_auteur);
	
	$civilite = _request('civilite');
	$email = _request('email');
	$nom = _request('nom');
	$prenom = _request('prenom');
	
	//
	// Modification de l'email et éventuellement du nom
	// 
	if ($email !== $auteur['email']) {
		$email_nouveau = $email;
		
		// 
		// si le login est identique au mail, 
		// il faut alors modifier le premier.
		// 
		if ($auteur['email'] == $auteur['login']) {
			$login_nouveau = $email_nouveau;
			set_request('login', $email_nouveau);
		}
	}
	
	// Changement de Nom et/ou de Prénom ?
	if ($nom !== nom($auteur['nom']) or $prenom !== prenom($auteur['nom'])) {
		$nom_nouveau = $nom;
		$prenom_nouveau = $prenom;
		$nom_prenom = $nom.'*'.$prenom;
		
		// Auteur a besoin d'un champ nom
		set_request('nom', $nom_prenom);
	}
	
	// Saisie d'un mot de passe ?
	if ($option && $option == 'password') {
		$password = _request('password');
		$password_nouveau = $password;
		set_request('new_pass', $password);
	}
	
	// 
	// Modification des champs Auteur
	// 
	if ($nom_nouveau OR $prenom_nouveau OR $email_nouveau OR $login_nouveau) {
		$modifier_auteur = charger_fonction('editer_objet', 'action');
		$_auteur = $modifier_auteur($id_auteur, 'auteur', array(
			'nom' => $nom_prenom,
			'email' => $email_nouveau,
			'login' => $login_nouveau
		));
		if ($_auteur[1]) {
			return $res['message_erreur'] = _T('spip:erreur_technique_enregistrement_impossible');
		}
	}
	
	// 
	// Rétablir le champ Nom tel que validé par l'auteur
	// 
	set_request('nom', $nom);
	// 
	// Contact
	// 
	if (!$contact = sql_fetsel('*', 'spip_contacts', 'id_auteur='.$id_auteur)) {
		$definir_contact = charger_fonction('definir_contact', 'action');
		$id_contact = $definir_contact('contact/'.$id_auteur);
		if (!$id_contact) {
			return $res['message_erreur'] = _T('spip:erreur_technique_enregistrement_impossible');
		} else {
			include_spip('action/editer_contact');
			contact_modifier($id_contact, $set = array(
				'civilite' => $civilite,
				'prenom' => $prenom,
				'nom' => $nom)
			);
		}
	} else {
		if (
			$civilite !== $contact['civilite'] 
			or $prenom !== $contact['prenom'] 
			or $nom !== $contact['nom']
		) {
			include_spip('action/editer_contact');
			contact_modifier($contact['id_contact'], $set = array(
				'civilite' => $civilite,
				'prenom' => $prenom,
				'nom' => $nom)
			);
		}
	}
	
	// 
	// Modification des champs Adresse
	// 
	$set_adresse['organisation'] = _request('organisation');
	$set_adresse['service'] = _request('service');
	$set_adresse['voie'] = _request('voie');
	$set_adresse['complement'] = _request('complement');
	$set_adresse['boite_postale'] = _request('boite_postale');
	$set_adresse['code_postal'] = _request('code_postal');
	$set_adresse['ville'] = _request('ville');
	$set_adresse['region'] = _request('region');
	$set_adresse['pays'] = _request('pays');
	
	$type_adresse = _ADRESSE_TYPE_DEFAUT;
	
	$adresse = sql_fetsel('*', 'spip_adresses AS adresses INNER JOIN spip_adresses_liens AS L1 ON (L1.id_adresse = adresses.id_adresse)', 'L1.id_objet='.$id_auteur.' AND L1.objet='.sql_quote('auteur').' AND L1.type='.sql_quote($type_adresse));
	
	if (!$adresse) {
		$inserer_adresse = charger_fonction('editer_objet', 'action');
		$_adresse = $inserer_adresse('new', 'adresse', $set_adresse);
		if (!$id_adresse = intval($_adresse[0])) {
			return $res['message_erreur'] = _T('spip:erreur_technique_enregistrement_impossible');
		} else {
			include_spip('action/editer_liens');
			objet_associer(
				array('adresse' => $id_adresse),
				array('auteur' => $id_auteur),
				array('type' => $type_adresse),
			);
		}
	} else {
		if (
			$adresse['organisation'] != $set_adresse['organisation']
			or $adresse['service'] != $set_adresse['service']
			or $adresse['voie'] != $set_adresse['voie']
			or $adresse['boite_postale'] != $set_adresse['boite_postale']
			or $adresse['code_postal'] != $set_adresse['code_postal']
			or $adresse['ville'] != $set_adresse['ville']
			or $adresse['region'] != $set_adresse['region']
			or $adresse['pays'] != $set_adresse['pays']
		) {
			$modifier_adresse = charger_fonction('editer_objet', 'action');
			$_adresse = $modifier_adresse($adresse['id_adresse'], 'adresse', $set_adresse);
			if (!$id_adresse = intval($_adresse[0])) {
				return $res['message_erreur'] = _T('spip:erreur_technique_enregistrement_impossible');
			}
		}
	}

	if (!isset($res['message_erreur'])) {
		$res['message_ok'] = _T('info_modification_enregistree');
		$res['editable'] = true;
	}
	
	if ($retour) {
		$res['redirect'] = $retour;
		// $res['editable'] = true;
	}
	
	return $res;
}
