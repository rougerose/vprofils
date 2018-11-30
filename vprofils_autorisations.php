<?php 


if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

function vprofils_autoriser() {}


function autoriser_vprofils_configurer($faire, $type, $id, $qui, $opt) {
	return autoriser('webmestre');
}


/**
 * Autorisation instituer commandes
 *
 * Pour interdire de changer le statut d'une commande depuis l'espace privé. 
 * Le changement de statut doit passer par les actions programmées uniquement.
 *
 */
function autoriser_commande_instituer($faire, $type, $id, $qui, $opt){
	return false;
}


/**
 * Surcharge autorisation plugin BANK
 *
 * Autoriser les webmestres et une liste d'auteurs avec un statut administrateur
 * à faire des encaissements par virement.
 * 
 */
function autoriser_transaction_encaisservirement($faire, $type, $id_transaction, $qui, $opt) {
	if (($qui['webmestre'] == 'oui') or (in_array($qui['id_auteur'], explode(':', _ID_GESTIONNAIRES)) and $qui['statut'] == '0minirezo' and !$qui['restreint'])) {
		return true;
	}
}

/**
 * Surcharge autorisation plugin bank
 *
 * Autoriser les webmestres et une liste d'auteurs avec un statut administrateur
 * à faire des encaissements par chèque.
 *
 */
function autoriser_transaction_encaissercheque($faire, $type, $id_transaction, $qui, $opt) {
	if (($qui['webmestre'] == 'oui') or (in_array($qui['id_auteur'], explode(':', _ID_GESTIONNAIRES)) and $qui['statut'] == '0minirezo' and !$qui['restreint'])) {
		return true;
	}
}
