<?php


function label_champ($champ){
	$label = "";
	// si jointure: renvoyer le nom des objets joints
	if (strncmp($champ,'jointure_',9)==0)
		return _T(objet_info(objet_type(substr($champ,9)),'texte_objets'));
	
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