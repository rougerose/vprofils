<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}


if (!defined('_ADRESSE_TYPE_DEFAUT')) {
	define('_ADRESSE_TYPE_DEFAUT', 'pref'); // principale
}

/**
 * La liste des auteurs autorisés à faire des encaissements (chèque ou virement)
 * 394 -> LV
 * 168 -> VC
 * 507 -> ML
 */
define('_ID_GESTIONNAIRES', '394:168:507');

/**
 * Permettre un Nom d'inscription de 3 lettres
 */
define('_LOGIN_TROP_COURT', 3);

/**
 * Inscription : envoyer les identifiants par mail.
 *
 * Fonction redefinissable qui doit retourner un tableau
 * dont les elements seront les arguments de inc_envoyer_mail
 *
 * Code repris du plugin inscriptionmotdepasse
 * et adapté pour les besoins spécifiques de ce plugin, à savoir :
 * Souscription d'un abonnement, inscription de l'abonné :
 *     - la notification standard est courcircuitée.
 *     - le mot de passe saisi lors de l'inscription est enregistré à la place
 *       du mot de passe automatique.
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
	//include_spip('action/editer_auteur');
	$envoyer_mail = true;

	//
	// Récupérer l'email pour retrouver l'identifiant de l'utilisateur
	$email = $desc['email'];

	//
	// Récupérer le nom du formulaire et le champ pgp d'identification du bénéficiaire
	$form = _request('action');
	// $options_abo = _request('options_abonnement');

	//
	// Si tout s'est bien passé avant, SPIP a déjà créé l'auteur.
	if ($user = sql_fetsel('*', 'spip_auteurs', 'email='.sql_quote($email))){

		//
		// Si l'inscription est celle du bénéficiaire d'un abonnement offert,
		// on bloque l'envoi du mail automatique à cette étape de l'inscription.
		//
		if ($form == 'api_bank') {
			//
			// La notification par mail de l'inscription ne doit pas lui
			// être envoyée tout de suite.
			//
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
		// aucun message ce qui annulera l'envoi
		// de l'e-mail de confirmation d'inscription automatique au nouvel inscrit.
		$message = '';
	}

	$from = (isset($options['from']) ? $options['from'] : null);
	$head = null;

	return array("", $message, $from, $head);
}
