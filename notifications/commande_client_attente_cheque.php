<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}


function notifications_commande_client_attente_cheque_dist($quoi, $id_commande, $options) {
	$email = sql_getfetsel('email', 'spip_auteurs', 'id_auteur='.intval($options['id_auteur']));
	
	if ($email) {
		$texte = recuperer_fond(
			'notifications/commande_client_attente_cheque', 
			array(
				'id_commande' => $id_commande,
				'id_auteur' => $options['id_auteur'],
				'config' => $options['config']
			)
		);
		
		notifications_envoyer_mails($email, $texte);
	}
}
