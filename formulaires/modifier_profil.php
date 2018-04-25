<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

include_spip("base/abstract_sql");
include_spip('inc/editer');

function formulaires_modifier_profil_saisies_dist($id_auteur, $retour = '') {
	// 
	// L'auteur est enregistré comme contact ou organisation ?
	// 
	if ($id_contact = sql_getfetsel('id_contact', 'spip_contacts', 'id_auteur='.intval($id_auteur))) {
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
						'saisie' => 'radio',
						'options' => array(
							'nom' => 'civilite',
							'label' => _T('vprofils:formulaire_civilite_label'),
							'obligatoire' => 'oui',
							'data' => array(
								'madame' => _T('vprofils:formulaire_civilite_madame_label'),
								'monsieur' => _T('vprofils:formulaire_civilite_monsieur_label')
							)
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
		
	} elseif ($id_organisation = sql_getfetsel('id_organisation', 'spip_organisations', 'id_auteur='.intval($id_auteur))) {
		$saisies_id = array(
			array(
				'saisie' => 'hidden',
				'options' => array(
					'nom' => 'type_client',
					'defaut' => 'organisation'
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
						'saisie' => 'input',
						'options' => array(
							'nom' => 'nom',
							'label' => _T('vprofils:formulaire_nom_organisation_label'),
							'obligatoire' => 'oui',
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


function formulaires_modifier_profil_charger_dist($id_auteur, $retour = '') {
	if (!$id_auteur OR !$auteur = sql_fetsel('*', 'spip_auteurs', 'id_auteur='.intval($id_auteur))) {
		return false;
	}
	
	$valeurs = array();
	
	if ($contact = sql_fetsel('*', 'spip_contacts', 'id_auteur='.intval($id_auteur))) {
		$valeurs['type_client'] = '';
		$valeurs['civilite'] = $contact['civilite'];
		$valeurs['nom'] = $contact['nom'];
		$valeurs['prenom'] = $contact['prenom'];
		$valeurs['email'] = $auteur['email'];
		
		if ($id_organisation = sql_getfetsel('id_organisation', 'spip_organisations_liens', 'id_objet='.intval($contact['id_contact']).' AND objet='.sql_quote('contact'))) {
			
			$organisation_liee = sql_fetsel('*', 'spip_organisations', 'id_organisation='.intval($id_organisation));
			$valeurs['organisation'] = $organisation_liee['nom'];
			
		}
		
	} elseif ($organisation = sql_fetsel('*', 'spip_organisations', 'id_auteur='.intval($id_auteur))) {
		
		$valeurs['type_client'] = '';
		$valeurs['nom'] = $organisation['nom'];
		$valeurs['email'] = $auteur['email'];
		
	}
	
	// 
	// Coordonnees
	// 
	if ($id_adresse = sql_getfetsel('id_adresse', 'spip_adresses_liens', 'id_objet='.intval($id_auteur).' AND objet='.sql_quote('auteur').' AND type='.sql_quote(_ADRESSE_TYPE_DEFAUT))) {
		
		$adresse = sql_fetsel('*', 'spip_adresses', 'id_adresse='.intval($id_adresse));
		
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

function formulaires_modifier_profil_verifier_dist($id_auteur, $retour = '') {
	$erreurs = array();
	$type_client = _request('type_client');
	$auteur = sql_fetsel('*', 'spip_auteurs', 'id_auteur='.intval($id_auteur));
	
	// 
	// Les champs obligatoires
	// 
	if ($type_client == 'contact') {
		
		$obligatoires = array('civilite', 'nom', 'prenom', 'email', 'voie', 'code_postal', 'ville', 'pays');
		
	} elseif ($type_client == 'organisation') {
		
		$obligatoires = array('nom', 'email', 'voie', 'code_postal', 'ville', 'pays');
		
	}
	
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
	
	return $erreurs;
}

function formulaires_modifier_profil_traiter_dist($id_auteur, $retour = '') {
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
	
	// 
	// Si c'est un contact, corriger le champ Nom
	// 
	if ($type_client == 'contact') {
		$nom_prenom = $nom.'*'.$prenom;
		set_request('nom', $nom_prenom);
	}
	
	// 
	// Modification des champs Auteur
	// 
	$res_auteur = formulaires_editer_objet_traiter('auteur', intval($id_auteur), 0, 0, $retour, '');
	$res = array_merge($res, $res_auteur);
	
	// 
	// Rétablir le champ Nom tel que validé par l'auteur
	// 
	set_request('nom', $nom);
	
	if ($type_client == 'contact') {
		// 
		// Modification des champs Contact
		// 
		$id_contact = sql_getfetsel('id_contact', 'spip_contacts', 'id_auteur='.intval($id_auteur));
		$res_contact = formulaires_editer_objet_traiter('contact', intval($id_contact), 0, 0, $retour, '');
		$res = array_merge($res, $res_contact);
		
		$organisation = _request('organisation');
		$id_organisation_actuelle = sql_getfetsel('id_organisation', 'spip_organisations_liens', 'id_objet='.$id_contact.' and objet='.sql_quote('contact'));
		
		//
		// Si le champ Organisation est maintenant vide
		// mais une organisation est liée au chargement du formulaire.
		// 
		if (!$organisation AND $id_organisation_actuelle) {
			// 
			// Dissocier l'organisation
			// 
			//include_spip('action/supprimer_lien');
			$dissocier = charger_fonction("supprimer_lien","action");
			$organisation_contact = "organisation-$id_organisation_actuelle-contact-$id_contact";
			$dissocier($organisation_contact);
		}
		
		
		// 
		// Si le champ Organisation est renseigné
		// 
		if ($organisation = _request('organisation')) {
			
			if ($id_organisation = sql_getfetsel('id_organisation', 'spip_organisations', 'nom='.sql_quote($organisation))) {
				
				if (!sql_countsel('spip_organisations_liens', 'id_objet='.$id_contact.' and objet='.sql_quote('contact').' and id_organisation='.$id_organisation)) {
					// 
					// lier l'organisation
					// 
					include_spip('action/editer_liens');
					objet_associer(array('organisation' => $id_organisation), array('contact' => $id_contact));
				}
				
				$res['id_organisation'] = $id_organisation;
				
			} else {
				//
				// Modifier le champs Nom
				// 
				set_request('nom', $organisation);
				
				// 
				// créer
				// 
				$res_organisation = formulaires_editer_objet_traiter('organisation', 'new', 0, 0, $retour, '');
				
				if ($res_organisation['id_organisation']) {
					include_spip('action/editer_liens');
					objet_associer(array('organisation' => $res_organisation['id_organisation']), array('contact' => $id_contact));
				}
				
				//
				// Rétablir le champs Nom de l'auteur
				// 
				set_request('nom', $nom);
				
				$res = array_merge($res, $res_organisation);
			}
		}
		
		//
		// Si le champ Organisation est vide, mais 

	} elseif ($type_client == 'organisation') {
		
		$id_organisation = sql_getfetsel('id_organisation', 'spip_organisations', 'id_auteur='.intval($id_auteur));
		
		// 
		// Modification des champs Organisation
		// 
		$res_organisation = formulaires_editer_objet_traiter('organisation', $id_organisation, 0, 0, $retour, '');
		
		$res = array_merge($res, $res_organisation);
		
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
	$res = array_merge($res, $res_adresse);

	if ($retour) {
		$res['redirect'] = $retour;
	}
	
	return $resultat;
}
