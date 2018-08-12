<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

include_spip('inc/cextras');
include_spip('base/vprofils');

function vprofils_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();
	// ajouter champs extras
	cextras_api_upgrade(vprofils_declarer_champs_extras(), $maj['create']);
	
	$maj['1.0.1'] = array(
		array('sql_alter',"TABLE spip_adresses CHANGE code_facteur code_facteur int(4) DEFAULT '0' NOT NULL")
	);
	
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


function vprofils_vider_tables($nom_meta_base_version) {
	cextras_api_vider_tables(vprofils_declarer_champs_extras());
	effacer_meta($nom_meta_base_version);
}
