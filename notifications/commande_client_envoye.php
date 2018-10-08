<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}


function notifications_commande_client_envoye_dist($quoi, $id_commande) {
	$id_auteur = sql_getfetsel('id_auteur', 'spip_commandes', 'id_commande='.intval($id_commande));
	if ($id_auteur) {
		$email = sql_getfetsel('email', 'spip_auteurs', 'id_auteur='.intval($id_auteur));
		
		if ($email) {
			$texte = recuperer_fond(
				'notifications/commande_client_envoye', 
				array(
					'id_commande' => $id_commande
				)
			);
			
			notifications_envoyer_mails($email, $texte);
		}
	}
}
