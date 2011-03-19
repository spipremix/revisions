<?php


function label_champ($champ){
	$label = "";
	switch ($champ){
		case 'surtitre':
			$label = "texte_sur_titre";
			break;
		case 'soustitre':
			$label = "texte_sous_titre";
			break;
		case 'nom_site':
			$label = "lien_voir_en_ligne";
			break;
		case 'chapo':
			$champ = "chapeau";
		default:
			$label = "info_$champ";
			break;
	}
	return $label?_T($label):"";
}