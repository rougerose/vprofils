<?php

if (!defined("_ECRIRE_INC_VERSION")) {
    return;
}


/**
 * Inscription : envoyer les identifiants par mail.
 *
 * Fonction redefinissable qui doit retourner un tableau
 * dont les elements seront les arguments de inc_envoyer_mail
 *
 * Code repris du plugin inscriptionmotdepasse
 * et adapté pour les besoins spécifique. 
 *
 * Souscription d'un abonnement, inscription de l'abonné : 
 * - la notification standard est courcircuitée. 
 * - le mot de passe saisi lors de l'inscription est enregistré à la place
 * du mot de passe automatique. 
 *
 * Offrir un abonnement, inscription du bénéficiaire :
 * 
 *
 * @param array $desc
 * @param string $nom
 * @param string $mode
 * @param array $options
 * @return array
 */
function envoyer_inscription($desc, $nom, $mode, $options) {
	include_spip('action/editer_auteur');
	$envoyer_mail = true;
	
	// Récupérer l'email pour retrouver l'identifiant de l'utilisateur
	$email = $desc['email'];
	
	$password = _request('password');
	
	// Récupérer le formulaire et le champ Bio
	$form = _request('formulaire_action');
	$bio = _request('bio');
	
	// Si tout s'est bien passé avant, SPIP a déjà créé l'auteur et lui a déjà donné un login et pass
	if ($user = sql_fetsel('*', 'spip_auteurs', 'email='.sql_quote($email))){
		if ($password) {
			// On modifie le mot de passe en utilisant les API de SPIP
			auteur_instituer($user['id_auteur'], array('pass' => $password));
			
			// On modifie l'information de mot de passe
			$desc['pass'] = $password;
		}

		// Si l'inscription est celle du bénéficiaire d'un abonnement offert
		if ($form == 'inscription_tiers' AND $bio == 'abonnement_offert') {
			auteur_instituer($user['id_auteur'], array('bio' => $bio));
			// la notification par mail de l'inscription ne doit pas lui
			// être envoyée
			$envoyer_mail = false;
		}
	}
	
	// On continue comme la fonction d'origine
	$contexte = array_merge($desc, $options);
	$contexte['nom'] = $nom;
	$contexte['mode'] = $mode;
	$contexte['url_confirm'] = generer_url_action('confirmer_inscription', '', true, true);
	$contexte['url_confirm'] = parametre_url($contexte['url_confirm'], 'email', $desc['email']);
	$contexte['url_confirm'] = parametre_url($contexte['url_confirm'], 'jeton', $desc['jeton']);
	
	$modele_mail = 'modeles/mail_inscription';
	if (isset($options['modele_mail']) and $options['modele_mail']){
		$modele_mail = $options['modele_mail'];
	}
	
	if ($envoyer_mail) {
		$message = recuperer_fond($modele_mail, $contexte);
	} else {
		// aucun message, ce qui annulera l'envoi du mail au nouvel inscrit.
		$message = '';
	}
	
	$from = (isset($options['from']) ? $options['from'] : null);
	$head = null;

	return array("", $message, $from, $head);
}
