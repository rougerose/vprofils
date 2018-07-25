<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(
	// B
	'bonjour' => "Bonjour",
	
	// C
	'cher_chere_madame' => "Chère",
	'cher_chere_monsieur' => "Cher",
	
	'commande_client_attente_objet' => "Votre commande sur @nom_site@",
	
	'commande_client_attente_cheque_intro' => "Nous vous confirmons avoir bien enregistré votre commande n<sup>o</sup>&nbsp;@reference@, détaillée ci-dessous. 
	
	Vous souhaitez régler par chèque, votre commande sera donc validée après réception de votre réglement.",
	
	'commande_client_attente_cheque_paiement' => "Veuillez libeller votre chèque : 
-* en euros ;
-* à l’ordre de « @ordre@ » ;
-* d’un montant de @montant@ ;
-* compensable dans une agence bancaire située en France ;
-* accompagné, au dos du chèque, de la référence «Transaction numéro @transaction@».

Veuillez envoyer votre chèque à l’adresse suivante : «@adresse@».",
	
	'commande_client_attente_virement_intro' => "Nous vous confirmons avoir bien enregistré votre commande n<sup>o</sup>&nbsp;@reference@, détaillée ci-dessous.
	
	Vous souhaitez régler par virement, votre commande sera donc validée après réception de votre réglement.",
	
	'commande_client_attente_virement_paiement' => "Afin de faciliter le traitement de votre paiement, veuillez indiquer la référence <b>Transaction numéro @transaction@</b> avec votre virement.
	
	Le montant attendu est de @montant@
	
	Nos coordonnées bancaires :
-* Bénéficiaire : « @ordre@ »
-* Banque : @banque@<br/>@adressebanque@
-* IBAN : @iban@
-* BIC : @bic@",
	
	'commande_vendeur_attente_cheque' => "La commande n<sup>o</sup>&nbsp;@reference@ au nom de @auteur@ vient d’être enregistrée. 
	
	Elle est en attente de réglement par <strong>chèque</strong>. 
	
	La transaction associée à cette commande est enregistrée sous le n<sup>o</sup>&nbsp;@transaction@.",
	
	'commande_vendeur_attente_virement' => "La commande n<sup>o</sup>&nbsp;@reference@ au nom de @auteur@ vient d’être enregistrée. 
	
	Elle est en attente de réglement par <strong>virement</strong>. 
	
	La transaction associée à cette commande est enregistrée sous le n<sup>o</sup>&nbsp;@transaction@.",
	
	'commande_confirmation' => 'Nous avons bien enregistré votre commande, dont vous trouverez le détail ci-dessous, et votre réglement.

Vous receverez un e-mail de notre part dès que votre commande sera expédiée.

Nous vous remercions de votre confiance.',
	
	'confirmation_activation_numero_actuel' => 'Votre abonnement est maintenant actif.

Vous recevrez votre exemplaire de <em>@titre_numero_encours@</em>, le numéro que vous avez choisi pour débuter votre abonnement, dans quelques jours.

Nous vous souhaitons par avance une bonne lecture.',

	'confirmation_activation_numero_prochain' => 'Votre abonnement est maintenant actif.

Vous recevrez votre exemplaire de <em>@titre_numero_prochain@</em>, le numéro que vous avez choisi pour débuter votre abonnement, dès qu’il sera disponible.

Nous vous souhaitons par avance une bonne lecture.',

	
	// I
	'il_elle_madame' => "elle",
	'il_elle_monsieur' => "il",
	
	// N
	'notification_commande_vacarme' => "Une commande au nom de @client@ est enregistrée.",
	'notification_commande_numero' => "Commande n<sup>o</sup> @reference@ du @date@",
	
	// O
	'offert_a' => "Offert à",
	
	// P
	'politesse_signature' => "Cordialement, <br /> Le comité de rédaction de Vacarme",
);
