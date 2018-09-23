<?php 

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}


/**
 * Formulaire d'inscription :
 * - ajouter le champs mot de passe.
 * - ajouter les champs adresse lorsque le contexte le demande : 
 *   souscription d'abonnement, achat d'exemplaire. 
 *   Sinon formulaire sans adresse (version simple).
 * 
 * @param  array $flux
 * @return array
 */
function vprofils_formulaire_fond($flux){
	
	if ($flux['args']['form'] == 'inscription') {
		
		//
		// Champs mot de passe
		// 
		$champs_password = recuperer_fond('formulaires/inc-inscription-motdepasse', $flux['args']['contexte']);
		$flux['data'] = preg_replace(
			'%<(li|div)[^>]*[saisie|editer]_mail_inscription[^>]*>.*?</\1>%is',
			"$0$champs_password",
			$flux['data']
		);
		
		// Formulaire simple ou complet ?
		// include_spip('inc/vprofils');
		$formulaire_complet = (_request('type')) ? _request('type') : '';
		
		//$page = _request('page');
		//$page .= (_request('etape')) ? _request('etape') : '';
		//$formulaire_complet = vprofils_selectionner_formulaire_inscription($page);
		
		if ($formulaire_complet == 'complet') {
			$coordonnees = recuperer_fond('formulaires/inc-inscription-coordonnees', $flux['args']['contexte']);
			$marque = '<!-- coordonnees -->';
			
			if (($html = strpos($flux['data'], $marque)) !== false) {
				$flux['data'] = substr_replace($flux['data'], $coordonnees, $html + strlen($marque), 0);
			}
		}
	}
	
	return $flux;
}


/**
 * Formulaire d'inscription
 *
 * chargement des saisies supplémentaires pour l'inscription d'un abonné.
 *
 * @param  array $flux
 * @return array
 */
function vprofils_formulaire_charger($flux) {
	if ($flux['args']['form'] == 'inscription' and $flux['data'] != false) {
		
		// les saisies supplémentaires
		$flux['data']['civilite'] = '';
		$flux['data']['prenom'] = '';
		$flux['data']['password'] = '';
		$flux['data']['password_confirmation'] = '';
		
		// Formulaire simple ou complet ?
		$formulaire_complet = (_request('type')) ? _request('type') : '';
		// include_spip('inc/vprofils');
		// $page = _request('page');
		// $page .= (_request('etape')) ? _request('etape') : '';
		// $formulaire_complet = vprofils_selectionner_formulaire_inscription($page);
		
		if ($formulaire_complet == 'complet') {
			$flux['data']['organisation'] = '';
			$flux['data']['service'] = '';
			$flux['data']['voie'] = '';
			$flux['data']['complement'] = '';
			$flux['data']['boite_postale'] = '';
			$flux['data']['code_postal'] = '';
			$flux['data']['ville'] = '';
			$flux['data']['region'] = '';
			$flux['data']['pays'] = '';
		}
	}
	
	return $flux;
}


/**
 * Formulaire d'inscription
 *
 * vérification des données
 * 
 * @param  array $flux
 * @return array
 */
function vprofils_formulaire_verifier($flux) {
	
	if ($flux['args']['form'] == 'inscription'){
		
		//
		// Pour le mot de passe
		// ************************
		// code repris du plugin inscriptionmotdepasse
		//  ************************
		
		// Si les deux champs de mot de passe sont différents, ce n'est pas bien confirmé
		if (_request('password') != _request('password_confirmation')){
			$flux['data']['password_confirmation'] = _T('info_passes_identiques');
		}
		
		if ( strlen(_request('password')) < _PASS_LONGUEUR_MINI ){
			$flux['data']['password'] = _T('info_passe_trop_court_car_pluriel', array('nb' => _PASS_LONGUEUR_MINI));
		}
		
		// Mais si l'un des deux champs n'est pas rempli, cette erreur prend le dessus
		if (!_request('password')){
			$flux['data']['password'] = _T('info_obligatoire');
		}
		if (!_request('password_confirmation')){
			$flux['data']['password_confirmation'] = _T('info_obligatoire');
		}
		
		// 
		// Pour les autres champs
		//  ************************
		
		$obligatoires = array();
		$obligatoires = array('civilite', 'nom_inscription', 'prenom', 'mail_inscription');
		
		// Formulaire simple ou complet ?
		$formulaire_complet = (_request('type')) ? _request('type') : '';
		// include_spip('inc/vprofils');
 		// $page = _request('page');
 		// $page .= (_request('etape')) ? _request('etape') : '';
 		// $formulaire_complet = vprofils_selectionner_formulaire_inscription($page);
 		
 		if ($formulaire_complet == 'complet') {
			$obligatoires_adresse = array('voie', 'code_postal', 'ville', 'pays');
			$obligatoires = array_merge($obligatoires, $obligatoires_adresse);
			
			if (_request('service') and !_request('organisation')) {
				$flux['data']['organisation'] = _T('vprofils:erreur_si_service_organisation_nonvide');
			}
 		}
		
		foreach ($obligatoires as $obligatoire) {
			if (!strlen(_request($obligatoire))) {
				$flux['data'][$obligatoire] = _T('vprofils:erreur_' . $obligatoire . '_obligatoire');
			}
		}
	}

	return $flux;
}


/**
 * Formulaires login et inscription
 *
 * Traitement des données spécifiques selon que la nature du formulaire
 * (inscription ou login).
 * 
 * @param  array $flux
 * @return array
 */
function vprofils_formulaire_traiter($flux) {
	// 
	// Formulaire login
	// *************************
	// 
	// Si, à la connexion d'un auteur, il n'existe pas de contact
	// alors on le créé car il s'agit d'un auteur pré-existant 
	// et, en principe, on a déjà le nom et le prénom puisque 
	// les auteurs Vacarme sont enregistrés ainsi : Nom*Prénom.
	// 
	if ($flux['args']['form'] == 'login') {
		
		include_spip('inc/vprofils');
		include_spip('inc/session');

		$id_auteur = $GLOBALS['visiteur_session']['id_auteur'];
		
		if ($id_auteur) {
			$id_contact = sql_getfetsel('id_contact', 'spip_contacts', 'id_auteur='.intval($id_auteur));
			
			if (!$id_contact) {
				$nom_auteur = $GLOBALS['auteur_session']['nom'];
				$nom = nom($nom_auteur);
				$prenom = prenom($nom_auteur);
				
				if ($nom) {
					$definir_contact = charger_fonction('definir_contact', 'action');
					$id_contact = $definir_contact('contact/'.$id_auteur);
					
					if ($id_contact) {
						include_spip('action/editer_contact');
						$contact_set = array();
						$contact_set['nom'] = $nom;
						$contact_set['prenom'] = $prenom;
						contact_modifier($id_contact, $contact_set);
					}
				} else {
					spip_log("Connexion de l'auteur #$id_auteur : impossible de creer sa fiche contact lors de son identification. La conversion automatique de son nom à partir du champ NOM de son profil Auteur n'a pas fonctionné. Le nom enregistré sur son profil Auteur : $nom_auteur", "vprofils" . _LOG_ERREUR);
				}
			}
		}
	}
	
	//
	// Formulaire inscription
	// *************************
	// 
	// A l'inscription d'un visiteur, ajouter le contact, 
	// éventuellement l'organisation, et les coordonnées postales.
	// 
	if ($flux['args']['form'] == 'inscription') {
		$res = array();
		$id_auteur = intval($flux['data']['id_auteur']);
		$prenom = _request('prenom');
		$nom = _request('nom_inscription');
		$civilite = _request('civilite');
		$mail = _request('mail_inscription');
		include_spip('inc/autoriser');
		
		// créer ou récupérer le contact
		if (!$contact = sql_fetsel('*', 'spip_contacts', 'id_auteur='.$id_auteur)) {
			$definir_contact = charger_fonction('definir_contact', 'action');
			$id_contact = $definir_contact('contact/'.$id_auteur);
			if (!$id_contact) {
				spip_log("Erreur lors de l'inscription de $nom $prenom (login : $mail) après l'étape de création du contact. L'identifiant auteur utilisé est #$id_auteur", 'vprofils_erreurs_inscription'._LOG_ERREUR);
				return $flux['data']['message_erreur'] = _T('vprofils:message_erreur_inscription');
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
		
		// rectifier des données de l'auteur : 
		// - login = e-mail
		// - nom = Nom*Prénom
		// - mot de passe : mot de passe saisi dans le formulaire.
		$nom_prenom = $nom.'*'.$prenom;
		$password = _request('password');
		
		// autoriser la modification de l'auteur
		autoriser_exception('modifier', 'auteur', $id_auteur);
		
		// modifier les données de l'auteur
		include_spip('action/editer_auteur');
		$err = auteur_modifier($id_auteur, array(
			'nom' => $nom_prenom,
			'login' => $mail,
			'pass' => $password
		));
		// retirer l'autorisation exceptionnelle
		autoriser_exception('modifier', 'auteur', $id_auteur, false);
		
		if ($err) {
			spip_log("Erreur lors de l'inscription de $nom_prenom (login : $mail) après l'étape de modification des données d'auteur. L'identifiant auteur utilisé est le $id_auteur", 'vprofils_erreurs_inscription'._LOG_ERREUR);
			return $flux['data']['message_erreur'] = _T('vprofils:message_erreur_inscription');
		}
		// Vérifier si l'auteur n'existe pas déjà comme auteur Vacarme
		// et le noter dans les logs pour un éventuel traitement ultérieur du doublon ?
		include_spip('inc/vprofils');
		vprofils_verifier_doublons($id_contact);
		
		// Formulaire simple ou complet ?
		$formulaire_complet = (_request('type')) ? _request('type') : '';
 		// $page = _request('page');
 		// $page .= (_request('etape')) ? _request('etape') : '';
 		// $formulaire_complet = vprofils_selectionner_formulaire_inscription($page);
		
		if ($formulaire_complet == 'complet') {
			$set_adresse['organisation'] = _request('organisation');
			$set_adresse['service'] = _request('service');
			$set_adresse['voie'] = _request('voie');
			$set_adresse['complement'] = _request('complement');
			$set_adresse['boite_postale'] = _request('boite_postale');
			$set_adresse['code_postal'] = _request('code_postal');
			$set_adresse['ville'] = _request('ville');
			$set_adresse['region'] = _request('region');
			$set_adresse['pays'] = _request('pays');
			
			$adresse = sql_fetsel('*', 'spip_adresses AS adresses INNER JOIN spip_adresses_liens AS L1 ON (L1.id_adresse = adresses.id_adresse)', 'L1.id_objet='.$id_auteur.' AND L1.objet='.sql_quote('auteur'));
			
			if (!$adresse) {
				$inserer_adresse = charger_fonction('editer_objet', 'action');
				$_adresse = $inserer_adresse('new', 'adresse', $set_adresse);
			}
			
			include_spip('inc/editer');
			$res = formulaires_editer_objet_traiter('adresse', 'new');
			
			if ($res['id_adresse']) {
				include_spip('action/editer_liens');
				objet_associer(array('adresse' => $res['id_adresse']), array('auteur' => $id_auteur), array('type' => _ADRESSE_TYPE_DEFAUT));
			}
		}
		
		// lien vers page d'identification
		$self = _request('self');
		
		$url = parametre_url($self, 'formulaire', 'identification');

		if (isset($flux['data']['message_ok'])) {
			$flux['data']['message_ok'] = _T('vprofils:message_ok_formulaire_inscription', array('url' => $url));
		}
	}
	return $flux;
}


/**
 * Notifications des commandes et paiements en attente
 * @param  array $flux
 * @return array
 */
function vprofils_trig_bank_reglement_en_attente($flux) {
	if (
		$flux['args']['statut'] == 'attente' 
		AND strpos($flux['args']['mode'], 'virement') !== false 
		OR strpos($flux['args']['mode'], 'cheque') !== false
	) {
		
		$id_auteur = $flux['args']['row']['id_auteur'];
		$id_commande = $flux['args']['row']['id_commande'];
		$source = sql_getfetsel('source', 'spip_commandes', 'id_commande='.intval($id_commande));
		
		include_spip('inc/bank');
		$config = bank_config($flux['args']['mode']);
		
		// envoyer la notification
		$notifications = charger_fonction('notifications', 'inc');
		$options = array(
			'id_auteur' => intval($id_auteur),
			'config' => $config
		);
		// pour le client
		$notifications('commande_client_attente', $id_commande, $options);
		// pour Vacarme
		$notifications('commande_vendeur_attente', $id_commande);
		
		
		// supprimer le panier si nécessaire
		if ($source AND preg_match(",panier#(\d+)$,", $source, $m)) {
			$id_panier = intval($m[1]);
			
			$supprimer_panier = charger_fonction('supprimer_panier', 'action/');
			$supprimer_panier($id_panier);
			
			sql_updateq("spip_commandes", array('source' => ''), "source=" . sql_quote($source));
		}
	}
	
	return $flux;
}

/**
 * Notification des paiements reçus
 * @param  array $flux 
 * @return array
 */
function vprofils_bank_traiter_reglement($flux) {
	if ($id_transaction = $flux['args']['id_transaction']
		AND $flux['args']['notifier'] == true
		AND $transaction = sql_fetsel('id_auteur, id_commande, statut, mode', 'spip_transactions', 'id_transaction='.intval($id_transaction))
		AND $id_commande = intval($transaction['id_commande'])
		AND $commande_statut = sql_getfetsel('statut', 'spip_commandes', 'id_commande='.$id_commande)
		AND $transaction['statut'] == 'ok'
		AND $commande_statut == 'paye'
	) {
		include_spip('inc/bank');
		$config = bank_config($transaction['mode']);
		$id_auteur = $transaction['id_auteur'];
		
		
		// Les offres d'abonnement obligatoire ou permanent ne font pas l'objet
		// d'une notification
		include_spip('inc/vabonnements');
		$offres_obligatoires = vabonnements_offres_obligatoires();
		
		// Envoyer les notifications
		$notifications = charger_fonction('notifications', 'inc');
		$options = array(
			'id_auteur' => intval($id_auteur),
			'config' => $config
		);
		
		// Notifier Vacarme, dans tous les cas.
		$notifications('commande_vendeur_reglement', $id_commande, $options);
		
		// Si la commande ne contient un abonnement de type permanent ou obligatoire,
		// notifier le client
		if (!sql_countsel('spip_commandes_details', sql_in('id_objet', $offres_obligatoires).' AND id_commande='.$id_commande.' AND objet='.sql_quote('abonnements_offre'))) {
			$notifications('commande_client_reglement', $id_commande, $options);
		}
	}
	return $flux;
}

/**
 * Types de coordonnées, pipeline du plugin Coordonnées.
 *
 * Limitation volontaire du choix des coordonnées à une seule possible (pref = principale)
 * pour simplifier les traitements et tant que l'on ne propose pas à l'abonné 
 * d'enregistrer plusieurs adresses, téléphones, etc. 
 * Cette limitation s'applique également dans l'espace privé.
 *
 * @pipeline
 * @param  $flux
 * @return $flux
 */
function vprofils_types_coordonnees($flux) {
	foreach($flux as $coordonnees => $types) {
		$flux[$coordonnees] = array_filter($types, function($k) {return $k == 'pref';}, ARRAY_FILTER_USE_KEY);
	}
	
	return $flux;
}


/**
 * Formulaires editer_contact et editer_adresse
 *
 * - editer_adresse : rendre le type obligatoire et choix "pref" par défaut.
 * - editer_contact, champ civilité : remplacer le champ input par des boutons
 * radio (madame|monsieur), ce qui permet de "normaliser" la saisie
 * (oups ! c'est pas gender fluid !).
 *
 * @pipeline
 * @param  array $flux
 * @return array
 */
function vprofils_formulaire_saisies($flux) {
	
	if (test_espace_prive()) {
		if ($flux['args']['form'] == 'editer_adresse') {
			$cle_ta = array_search('type_adresse', array_column($flux['data'], 'saisie'));
			
			if ($cle_ta !== false) {
				$flux['data'][$cle_ta]['options']['obligatoire'] = 'oui';
				$flux['data'][$cle_ta]['options']['defaut'] = 'pref';
			}
			
		} elseif ($flux['args']['form'] == 'editer_contact') {
			foreach ($flux['data'] as $cle => $saisie) {
				if ($saisie['options']['nom'] == 'civilite') {
					$flux['data'][$cle] = array(
						'saisie' => 'civilite',
						'options' => array(
							'nom' => 'civilite',
							'label' => _T('vprofils:formulaire_civilite_label'),
							'obligatoire' => 'oui'
						)
					);
				}
			}
		}
	}
	
	return $flux;
}
