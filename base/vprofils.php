<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}


function vprofils_declarer_champs_extras($champs = array()) {
	$champs['spip_contacts']['organisation'] = array(
		'saisie' => 'input',
		'options' => array(
			'nom' => 'organisation',
			'label' => _T('vprofils:formulaire_organisation_label'),
			'sql' => "tinytext NOT NULL DEFAULT ''"
		)
	);
	
	$champs['spip_contacts']['service'] = array(
		'saisie' => 'input',
		'options' => array(
			'nom' => 'service',
			'label' => _T('vprofils:formulaire_service_label'),
			'sql' => "tinytext NOT NULL DEFAULT ''"
		)
	);
	
	return $champs;
}
