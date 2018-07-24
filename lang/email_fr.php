<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(
	// B
	'bonjour' => "Bonjour",
	
	// C
	'cher_chere_madame' => "Chère",
	'cher_chere_monsieur' => "Cher",
	
	'commande_confirmation' => "Nous avons bien enregistré votre commande, dont vous trouverez le détail ci-dessous, et le réglement qui l'accompagne. 

Vous receverez un e-mail de notre part dès que les articles de votre commande seront expédiés. 

Nous vous remercions de votre confiance.",
	
	'confirmation_activation_numero_actuel' => "Votre abonnement est maintenant actif.

Vous recevrez votre exemplaire de <em>@titre_numero_encours@</em>, le numéro que vous avez choisi pour débuter votre abonnement, dans quelques jours.

Nous vous souhaitons par avance une bonne lecture.",

	'confirmation_activation_numero_prochain' => "Votre abonnement est maintenant actif.

Vous recevrez votre exemplaire de <em>@titre_numero_prochain@</em>, le numéro que vous avez choisi pour débuter votre abonnement, dès qu'il sera disponible.

Nous vous souhaitons par avance une bonne lecture.",

	
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
	
	
	'intro_cheque' => "Nous vous confirmons que nous avons bien enregistré votre commande numéro <b>@reference@</b>, détaillée ci-dessous. Vous souhaitez régler par chèque, votre commande sera donc validée après réception de votre réglement.",
	
	'intro_virement' => "Nous vous confirmons que nous avons bien enregistré votre commande numéro <b>@reference@</b>, détaillée ci-dessous. Vous souhaitez régler par virement, votre commande sera donc validée après réception de votre réglement.",
	
	// N
	'notification_commande_vacarme' => "Une commande au nom de @client@ est enregistrée.",
	'notification_commande_numero' => "Commande n<sup>o</sup> @reference@ du @date@",
	
	// O
	'objet_equivalent_abonnements_offre' => "Abonnement",
	'objet_equivalent_produit' => "Cadeau d’abonnement",
	'objet_equivalent_rubrique' => "Numéro",
	'offert_a' => "Offert à",
	
	// P
	'politesse_signature' => "Cordialement, <br /> Le comité de rédaction de Vacarme",
);
