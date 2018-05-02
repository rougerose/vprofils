<?php

if (!defined("_ECRIRE_INC_VERSION")) {
    return;
}


if (!defined('_ADRESSE_TYPE_DEFAUT')) {
	define('_ADRESSE_TYPE_DEFAUT', 'pref'); // principale
}


// 
// Formulaires et nospam
// 
$GLOBALS['formulaires_no_spam'][] = 'inscription_tiers';



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
	include_spip('action/editer_auteur');
	$envoyer_mail = true;
	
	// 
	// Récupérer l'email pour retrouver l'identifiant de l'utilisateur
	$email = $desc['email'];
	
	// 
	// Récupérer le nom du formulaire et le champ pgp d'identification du bénéficiaire
	$form = _request('formulaire_action');
	// $options_abo = _request('options_abonnement');
	
	// 
	// Si tout s'est bien passé avant, SPIP a déjà créé l'auteur.
	if ($user = sql_fetsel('*', 'spip_auteurs', 'email='.sql_quote($email))){

		// 
		// Si l'inscription est celle du bénéficiaire d'un abonnement offert :
		// 1- on enregistre la date d'envoi du message demandée par le payeur
		// dans le formulaire. Cette date sera reportée dans la date_debut
		// de l'abonnement à sa création. 
		// 
		// 2- on bloque l'envoi du mail automatique à cette étape de l'inscription.
		// 
		// NOTE: [2 mai 2018] - vérification uniquement sur le type de formulaire
		// utilisé. Le champ $options_abo est supprimé car, pour le moment, 
		// l'abonnement offert est le seul cas qui nécessite l'utilisation
		// de ce formulaire. 
		// 
		if ($form == 'inscription_tiers') {
			// Date d'envoi de la notification et date potentielle de départ
			// de l'abonnement.
			$date_envoi = _request('message_date');
			
			// Le motif (abonnement_offert) et la date de départ d'abonnement
			// sont enregistrés provisoirement dans la bio de l'auteur
			// bénéficiaire.
			// TODO: effacer ces infos lors du traitement du paiement
			// de l'abonnement, puisque la date de départ sera reportée 
			// sur le champ date_debut de l'abonnement. 
			// Lors du traitement de la confirmation d'abonnement par le
			// bénéficiaire, il faudra également s'assurer que cette date_debut
			// est correcte à ce qu'il souhaite réellement. 

			$options_abo = serialize(array('abonnement_offert_date' => $date_envoi));
			auteur_modifier($user['id_auteur'], array('pgp' => $options_abo));
			
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
