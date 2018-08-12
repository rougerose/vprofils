<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}


function vprofils_declarer_champs_extras($champs = array()) {
	$champs['spip_adresses']['organisation'] = array(
		'saisie' => 'input',
		'options' => array(
			'nom' => 'organisation',
			'label' => _T('vprofils:formulaire_organisation_label'),
			'sql' => "tinytext NOT NULL DEFAULT ''"
		)
	);
	
	$champs['spip_adresses']['service'] = array(
		'saisie' => 'input',
		'options' => array(
			'nom' => 'service',
			'label' => _T('vprofils:formulaire_service_label'),
			'sql' => "tinytext NOT NULL DEFAULT ''"
		)
	);
	
	$champs['spip_adresses']['code_facteur'] = array(
		'saisie' => 'input',
		'options' => array(
			'nom' => 'code_facteur',
			'label' => _T('vprofils:formulaire_code_facteur_label'),
			'sql' => "int(4) NOT NULL DEFAULT 0"
		)
	);
	
	return $champs;
}
