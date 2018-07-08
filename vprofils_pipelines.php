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
		include_spip('inc/vprofils');
		$page = _request('page');
		$page .= (_request('etape')) ? _request('etape') : '';
		$formulaire_complet = vprofils_selectionner_formulaire_inscription($page);
		
		if ($formulaire_complet) {
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
		include_spip('inc/vprofils');
		$page = _request('page');
		$page .= (_request('etape')) ? _request('etape') : '';
		$formulaire_complet = vprofils_selectionner_formulaire_inscription($page);
		
		if ($formulaire_complet) {
			$flux['data']['type_organisation'] = '';
			$flux['data']['organisation'] = '';
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
		include_spip('inc/vprofils');
 		$page = _request('page');
 		$page .= (_request('etape')) ? _request('etape') : '';
 		$formulaire_complet = vprofils_selectionner_formulaire_inscription($page);
 		
 		if ($formulaire_complet) {
			$obligatoires_adresse = array('voie', 'code_postal', 'ville', 'pays');
			$obligatoires = array_merge($obligatoires, $obligatoires_adresse);
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
		
		$id_auteur = intval($flux['data']['id_auteur']);
		
		include_spip('inc/vprofils');
		
		// créer ou récupérer le contact
		$id_contact = vprofils_creer_contact($id_auteur);
		
		// rectifier des données de l'auteur : 
		// - login = e-mail
		// - nom = Nom*Prénom
		// - mot de passe : mot de passe saisi dans le formulaire.
		$prenom = _request('prenom');
		$nom = _request('nom_inscription');
		$nom_prenom = $nom.'*'.$prenom;
		$mail = _request('mail_inscription');
		$password = _request('password');
		
		// autoriser la modification de l'auteur
		include_spip('inc/autoriser');
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
		
		// Vérifier si l'auteur n'existe pas déjà comme auteur Vacarme
		// et le noter dans les logs pour un éventuel traitement ultérieur du doublon ?
		vprofils_verifier_doublons($id_contact);
		
		// Formulaire simple ou complet ?
 		$page = _request('page');
 		$page .= (_request('etape')) ? _request('etape') : '';
 		$formulaire_complet = vprofils_selectionner_formulaire_inscription($page);
		
		if ($formulaire_complet) {
			
			// Créer l'organisation et lier au contact,
			// si nécessaire
			if (_request('organisation')) {
				$id_organisation = vprofils_creer_organisation($id_contact);
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
		
		// TODO: vrai dans les tous cas ?
		$url = parametre_url($self, 'formulaire', 'identification');

		if (isset($flux['data']['message_ok'])) {
			$flux['data']['message_ok'] = _T('vprofils:message_ok_formulaire_inscription', array('url' => $url));
		}
	}
	return $flux;
}



function vprofils_trig_bank_reglement_en_attente($flux) {
	if (
		$flux['args']['statut'] == 'attente' 
		&& strpos($flux['args']['mode'], 'virement') !== false 
		|| strpos($flux['args']['mode'], 'cheque') !== false
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
		$notifications('commande_client_attente_'.$config['config'], $id_commande, $options);
		// pour Vacarme
		$notifications('commande_vendeur_attente_'.$config['config'], $id_commande, $options);
		
		
		// supprimer le panier si nécessaire
		if ($source && preg_match(",panier#(\d+)$,", $source, $m)) {
			$id_panier = intval($m[1]);
			
			$supprimer_panier = charger_fonction('supprimer_panier', 'action/');
			$supprimer_panier($id_panier);
			
			sql_updateq("spip_commandes", array('source' => ''), "source=" . sql_quote($source));
		}
	}
	
	return $flux;
}
