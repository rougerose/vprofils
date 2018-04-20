<?php 

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}


/**
 * Formulaire d'inscription : ajouter les champs supplémentaires.
 *
 * La page d'inscription affiche les coordonnées si le contexte
 * n'est pas lié au processus pour offrir un abonnement (page offrir). 
 * Car cette dernière ne requiert qu'un formulaire simple 
 * (nom, prénom, email, mot de passe) lors de l'inscription du payeur.
 *
 * Dans tous les cas, ajout au formulaire d'inscription les champs
 * de saisie relatifs au mot de passe.
 * 
 * @param  array $flux
 * @return array
 */
function vprofils_formulaire_fond($flux){
	if ($flux['args']['form'] == 'inscription') {
		
		$champs_password = recuperer_fond('formulaires/inc-inscriptionmotdepasse', $flux['args']['contexte']);
		$flux['data'] = preg_replace(
			'%<(li|div)[^>]*[saisie|editer]_mail_inscription[^>]*>.*?</\1>%is',
			"$0$champs_password",
			$flux['data']
		);

		$page = _request('page');
		$etape  = _request('etape');
		
		// Toutes les pages d'inscription nécessitent le formulaire
		// des coordonnées, exceptée la page "offrir" avec étape 3.
		if (isset($etape) AND $etape != 3 AND $page == 'offrir') {
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
 * chargemennt des saisies supplémentaires pour l'inscription
 * d'un abonné.
 *
 * @param  array $flux
 * @return array
 */
function vprofils_formulaire_charger($flux) {
	if ($flux['args']['form'] == 'inscription' and $flux['data'] != false) {
		$page = _request('page');
		$etape = _request('etape');
		
		// les saisies supplémentaires
		$flux['data']['civilite'] = '';
		$flux['data']['prenom'] = '';
		$flux['data']['password'] = '';
		$flux['data']['password_confirmation'] = '';
		
		// Toutes les pages d'inscription nécessitent le formulaire
		// des coordonnées, exceptée la page "offrir" avec étape 3.
		if (isset($etape) AND $etape != 3 AND $page == 'offrir') {
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
		// Pour les coordonnées
		//  ************************

	  	$page = _request('page');
		$etape = _request('etape');
		$obligatoire = array();
		
		$obligatoire['civilite'] = _request('civilite');
		$obligatoire['prenom'] = _request('prenom');
		
		// Toutes les pages d'inscription nécessitent le formulaire
		// des coordonnées, exceptée la page "offrir" avec étape 3.
		if (isset($etape) AND $etape != 3 AND $page == 'offrir') {
			$obligatoire['voie'] = _request('voie');
			$obligatoire['code_postal'] = _request('code_postal');
			$obligatoire['ville'] = _request('ville');
			$obligatoire['pays'] = _request('pays');
		}
		
		foreach ($obligatoire as $champ => $valeur) {
			if (!$valeur) {
				$flux['data'][$champ] = _T('info_obligatoire');
			}
		}
		
	}
	
	// if ($flux['args']['form'] == 'login'){
	// 	$statut = sql_getfetsel('statut', 'spip_auteurs', 'login='.sql_quote(_request('var_login')).' OR email=' .sql_quote(_request('var_login')) );
	// 
	// 	// if ($statut == 'nouveau'){
	// 	// 	$flux['data']['message_erreur'] = _T('inscriptionmotdepasse:erreur_email_non_confirme');
	// 	// }
	// }
	
	return $flux;
}


/**
 * Formulaire d'inscription et d'identification
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

		$id_auteur = $GLOBALS['auteur_session']['id_auteur'];

		if ($id_auteur AND intval($id_auteur) > 0) {
			
			$page = _request('page');
			$etape = _request('etape');
			$cible = $flux['args']['args'][0];
			
			$id_contact = sql_getfetsel('id_contact', 'spip_contacts', 'id_auteur='.$id_auteur);
			
			if (!$id_contact) {
				$nom_auteur = $GLOBALS['auteur_session']['nom'];
				$nom = nom($nom_auteur);
				$prenom = prenom($nom_auteur);
				
				// On créé un contact uniquement si on peut extraire 
				// un nom du champ NOM de l'auteur.
				if ($nom) {
					$definir_contact = charger_fonction('definir_contact', 'action');
					
					$id_contact = $definir_contact('contact/'.$id_auteur);
					
					if (intval($id_contact)) {
						include_spip('action/editer_contact');
						$contact_set = array();
						$contact_set['nom'] = $nom;
						$contact_set['prenom'] = $prenom;
					
						contact_modifier($id_contact, $contact_set);
					}
				} else {
					spip_log("Connexion de l'auteur #$id_auteur : impossible de creer sa fiche contact lors de son identification. La conversion automatique de son nom à partir du champ NOM de son profil Auteur n'a pas fonctionné. Le nom enregistré sur son profil Auteur : $nom_auteur", "vprofils" . _LOG_ERREUR);
				}
			} else {
				// 
				// Pour les pages d'inscription et d'abonnement, 
				// le formulaire de login boucle sur la page courante.
				// 
				// Si tout est ok au niveau du contact,
				// et si page publique et si une étape est dans l'environnement,
				// on peut alors rediriger sur l'étape suivante.
				// 
				if (is_url_prive($cible) == false AND $etape AND intval($etape)) {
					$etape_suivante = $etape + 1;
					$redirection = generer_url_public($page, "etape=$etape_suivante", true);
					include_spip('inc/headers');
					redirige_par_entete($redirection);
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
		include_spip('inc/vprofils');
		
		$id_auteur = $flux['data']['id_auteur'];
		
		// récupérer ou créer le contact
		$id_contact = vprofils_creer_contact($id_auteur);
		
		$page = _request('page');
		$etape = _request('etape');
		
		// Toutes les pages d'inscription nécessitent le formulaire
		// des coordonnées, exceptée la page "offrir" avec étape 3.
		if (isset($etape) AND $etape != 3 AND $page == 'offrir') {
			
			// créer l'organisation
			if (_request('organisation')) {
				vprofils_creer_organisation($id_contact);
			}
			
			// adresse et liaison avec l'auteur
			include_spip('inc/actions');
			include_spip('inc/editer');
			$res = formulaires_editer_objet_traiter('adresse', 'new', $id_parent = '', $lier_trad = '', $retour = '', $config_fonc = '', $row = array(), $hidden = '');
			
			if ($res['id_adresse']) {
				objet_associer(array('adresse' => $res['id_adresse']), array('auteur' => $id_auteur), array('type' => 'livraison'));
			}
		}
		
		// lien vers page d'identification
		$self = _request('self');
		
		// TODO: vrai dans les tous cas ?
		$url = parametre_url($self, 'form', 'identification');

		if (isset($flux['data']['message_ok'])) {
			$flux['data']['message_ok'] = _T('vprofils:message_ok_formulaire_inscription', array('url' => $url));
		}
	}
	return $flux;
}
