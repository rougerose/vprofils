<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}


function notifications_commande_vendeur_attente_cheque_dist($quoi, $id_commande, $options) {
	include_spip('inc/config');
	$config = lire_config('vprofils');
	$emails = $config['vendeur'];
	$destinataires = explode(',', $emails);
	$destinataires = array_map('trim', $destinataires);
	
	if (count($destinataires)) {
		$texte = recuperer_fond(
			'notifications/commande_vendeur_attente_virement', 
			array('id_commande' => $id_commande));
		notifications_envoyer_mails($destinataires, $texte);
	}
}
