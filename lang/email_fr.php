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



	'commande_client_reglement_simu' => "Votre réglement pour la commande n<sup>o</sup>&nbsp;@reference@ a bien été enregistré.
	
	Nous traitons votre commande dans les meilleurs délais et vous recevrez un email lorsqu’elle sera expédiée par courrier postal.
	
	Nous vous remercions de votre soutien.",



	'commande_client_reglement_gratuit' => "Nous avons le plaisir de vous offrir un abonnement à Vacarme. 
	
	Vous trouverez ci-dessous les informations à propos de cet abonnement.",



	'commande_client_reglement_gratuit_objet' => "Vacarme vous offre un abonnement",



	'commande_client_reglement_objet' => "Votre commande sur @nom_site@",



	'commande_vendeur_attente_cheque' => "La commande n<sup>o</sup>&nbsp;@reference@ au nom de @auteur@ vient d’être enregistrée. 
	
	Elle est en attente de réglement par <strong>chèque</strong>. 
	
	La transaction associée à cette commande est enregistrée sous le n<sup>o</sup>&nbsp;@transaction@.",



	'commande_vendeur_attente_virement' => "La commande n<sup>o</sup>&nbsp;@reference@ au nom de @auteur@ vient d’être enregistrée. 
	
	Elle est en attente de réglement par <strong>virement</strong>. 
	
	La transaction associée à cette commande est enregistrée sous le n<sup>o</sup>&nbsp;@transaction@.",



	'commande_vendeur_reglement_gratuit' => "La commande n<sup>o</sup>&nbsp;@reference@ au nom de @auteur@ vient d’être enregistrée. 
	
	La transaction n<sup>o</sup>&nbsp;@transaction@ est gratuite.",



	'commande_vendeur_reglement_simu' => "La commande n<sup>o</sup>&nbsp;@reference@ au nom de @auteur@ vient d’être enregistrée. 
	
	La transaction n<sup>o</sup>&nbsp;@transaction@ est réglée.",



	'confirmation_activation_numero_actuel' => 'Votre abonnement est maintenant actif.

	Vous recevrez votre exemplaire de <em>@titre_numero_encours@</em>, le premier numéro de votre abonnement, dans quelques jours.

	Nous vous remercions de votre soutien.',



	'confirmation_activation_numero_prochain' => 'Votre abonnement est maintenant actif.



Vous recevrez votre exemplaire de <em>@titre_numero_prochain@</em>, le premier numéro de votre abonnement, dès qu’il sera disponible.

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
