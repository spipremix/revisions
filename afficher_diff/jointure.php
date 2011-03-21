<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2011                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/diff');

/**
 * Afficher le diff d'un champ texte generique
 * @param string $champ
 * @param string $old
 * @param string $new
 * @param string $format
 *   apercu, diff ou complet
 * @return string
 */
function afficher_diff_jointure_dist($champ,$old,$new,$format='diff'){
	$join = substr($champ,9);
	$objet = objet_type($join);

	$old = explode(',',$old);
	$new = explode(',',$new);

	$liste = array();

	// les communs
	$intersection = array_intersect($new,$old);
	foreach($intersection as $id)
		if ($id=intval($id))
			$liste[$id] = generer_info_entite(intval(trim($id)),$objet,'titre');

	// les supprimes
	$old = array_diff($old,$intersection);
	foreach($old as $id)
		if ($id=intval($id))
			$liste[$id] = "<span class='diff-supprime'>".generer_info_entite(intval(trim($id)),$objet,'titre')."</span>";

	// les ajoutes
	$new = array_diff($new,$intersection);
	foreach($new as $id)
		if ($id=intval($id))
			$liste[$id] = "<span class='diff-ajoute'>".generer_info_entite(intval(trim($id)),$objet,'titre')."</span>";

	ksort($liste);
	$liste = implode(', ',$liste);
	return $liste;
}
