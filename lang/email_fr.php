<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(
	
	// C
	'cher_chere_madame' => "Chère",
	'cher_chere_monsieur' => "Cher", 
	
	// I
	'il_elle_madame' => "elle",
	'il_elle_monsieur' => "il",
	'info_cheque_envoyer' => 'Veuillez libeller votre chèque : 
-* en euros ;
-* à l\'ordre de « @ordre@ » ;
-* d\'un montant de @montant@ ;
-* compensable dans une agence bancaire située en France ;
-* accompagné, au dos du chèque, de la référence "<b>Transaction numéro @transaction@</b>".',
	'info_cheque_envoyer_adresse' => 'Veuillez envoyer votre chèque à l\'adresse :',
	'info_virement_etablir' => 'Libellé de votre virement : <b>Transaction numéro @transaction@</b>
_ Montant : @montant@

Compte bancaire :
-* Bénéficiaire : « @ordre@ »
-* Banque : @banque@<br/>
@adressebanque@
-* IBAN : @iban@
-* BIC : @bic@',
	
	// P
	'politesse_signature' => "Cordialement, <br /> Le comité de rédaction de Vacarme",
);
