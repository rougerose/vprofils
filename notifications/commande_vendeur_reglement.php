<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

function notifications_commande_vendeur_reglement_dist($quoi, $id_commande, $options) {
	include_spip('inc/config');
	$config = lire_config('vprofils');
	$emails = $config['vendeur'];
	$destinataires = explode(',', $emails);
	$destinataires = array_map('trim', $destinataires);
	$mode = $options['config']['presta'];
	
	if ($destinataires) {
		$texte = recuperer_fond(
			'notifications/commande_vendeur_reglement', 
			array(
				'id_commande' => $id_commande,
				'mode' => $mode
			)
		);
		
		notifications_envoyer_mails($destinataires, $texte);
	}
}
