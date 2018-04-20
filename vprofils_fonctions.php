<?php

if (!defined("_ECRIRE_INC_VERSION")) {
    return;
}

/**
 * Filtre |prenom_nom
 *
 * Transformer la chaîne Nom*Prénom en Prénom Nom
 * 
 * @param  string $texte Nom*Prénom
 * @return string Prénom Nom
 */
function filtre_prenom_nom($texte) {
  return prenom_nom($texte);
}


/**
 * Transformer la chaîne Nom*Prénom en Prénom Nom
 *
 * @param  string $texte Nom*Prénom
 * @return string Prénom Nom
 */
function prenom_nom($texte) {
    if (strstr($texte, "*")) {
        $prenom = prenom($texte);
        $nom = nom($texte);
        if ($prenom && $nom) {
            return $prenom.'&nbsp;'.$nom;
        } else {
            $prenom.$nom;
        }
    } else {
        return $texte;
    }
}


/**
 * Extraire le prénom de la chaîne Nom*Prénom
 * 
 * @param  string $texte Nom*Prénom
 * @return string Prénom
 */
function prenom($texte) {
    if(strstr($texte,"*")) {
        if ($prenom = trim(preg_replace('#^(.*)\*(.*)#', '$2', $texte))) {
            return $prenom;
        } else {
            return "";
        }
    } else {
        return "";
    }
}


/**
 * Extraire le nom de la chaîne Nom*Prénom
 * 
 * @param  string $texte Nom
 * @return string Nom
 */
function nom($texte) {
    if(strstr($texte,"*")) {
        if ($nom = trim(preg_replace('#^(.*)\*(.*)#', '$1', $texte))) {
            return $nom;
        } else {
            return "";
        }

    } else {
        return "";
    }
}


/**
 * Liste de dates depuis aujourd'hui jusque dans 6 mois.
 *
 * Ce filtre est utilisé dans la saisie message_date_envoi
 * du formulaire inscription_tiers
 * 
 * @param  array $tableau 
 * @return array
 */
function liste_dates($tableau) {
	if (is_array($tableau)) {
		$begin = new DateTime('now +0day');
		$end   = new DateTime('now +6months');

		for($i = $begin; $i <= $end; $i->modify('+1 day')){
			$tableau[$i->format("U")] = affdate($i->format("Y-m-d H:i:s"));
		}
	}
	return $tableau;
}
