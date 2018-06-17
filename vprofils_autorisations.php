<?php 


if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

function vprofils_autoriser() {
	
}


function autoriser_vprofils_configurer($faire, $type, $id, $qui, $opt) {
	return autoriser('webmestre');
}
