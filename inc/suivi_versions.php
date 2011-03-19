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

include_spip('inc/revisions');
include_spip('inc/diff');

/**
 * http://doc.spip.org/@afficher_para_modifies
 *
 * @param string $texte
 * @param bool $court
 * @return string
 */
function afficher_para_modifies ($texte, $court = false) {
	// Limiter la taille de l'affichage
	if ($court) $max = 200;
	else $max = 2000;

	$paras = explode ("\n",$texte);
	for ($i = 0; $i < count($paras) AND strlen($texte_ret) < $max; $i++) {
		if (strpos($paras[$i], '"diff-')) $texte_ret .= $paras[$i]."\n\n";
#		if (strlen($texte_ret) > $max) $texte_ret .= '(...)';
	}
	$texte = $texte_ret;
	return $texte;
}


/**
 * Retourne le titre de la rubrique demandee, pour affichage de la chaine
 * "deplace de XX vers YY"
 * http://doc.spip.org/@titre_rubrique
 *
 * @param  $id_rubrique
 * @return mixed|string
 */
function titre_rubrique($id_rubrique) {
	if (!$id_rubrique = intval($id_rubrique))
		return _T('info_sans_titre');

	return generer_info_entite($id_rubrique,'rubrique','titre');
}

/**
 * Afficher un diff correspondant a une revision d'un objet
 * 
 * @param int $id_objet
 * @param string $objet
 * @param int $id_version
 * @param bool $court
 * @return string
 */
function revisions_diff ($id_objet,$objet, $id_version, $court=false){
	$textes = revision_comparee($id_objet,$objet, $id_version, 'diff');
	if (!is_array($textes)) return $textes;
	$rev = '';
	$nb = 0;
	foreach ($textes as $var => $t) {
		if ($n=strlen($t)) {
			if ($court)
				$nb += $n;
			else {
				$aff = propre_diff($t);
				if ($GLOBALS['les_notes']) {
					$aff .= '<p>'.$GLOBALS['les_notes'].'</p>';
					$GLOBALS['les_notes'] = '';
				}
				$rev .= "<blockquote>$aff</blockquote>";
			}
		}
	}
	return $court ? _T('taille_octets', array('taille' => $nb)) : $rev;
}

/**
 * retourne un array() des champs modifies a la version id_version
 * le format =
 *    - diff => seulement les modifs (suivi_revisions)
 *    - apercu => idem, mais en plus tres cout s'il y en a bcp
 *    - complet => tout, avec surlignage des modifications (objets_versions)
 *
 * http://doc.spip.org/@revision_comparee
 *
 * @param int $id_objet
 * @param string $objet
 * @param int $id_version
 * @param string $format
 * @param null $id_diff
 * @return array
 */
function revision_comparee($id_objet, $objet, $id_version, $format='diff', $id_diff=NULL) {
	include_spip('inc/diff');

	// chercher le numero de la version precedente
	if (!$id_diff) {
		$id_diff = sql_getfetsel("id_version", "spip_versions", "id_objet=" . intval($id_objet) . " AND id_version < " . intval($id_version)." AND objet=".sql_quote($objet), "", "id_version DESC", "1");
	}

	if ($id_version && $id_diff) {

		// si l'ordre est inverse, on remet a l'endroit
		if ($id_diff > $id_version) {
			$t = $id_version;
			$id_version = $id_diff;
			$id_diff = $t;
		}

		$old = recuperer_version($id_objet,$objet, $id_diff);
		$new = recuperer_version($id_objet,$objet, $id_version);

		$textes = array();

		// Mode "diff": on ne s'interesse qu'aux champs presents dans $new
		// Mode "complet": on veut afficher tous les champs
		switch ($format) {
			case 'complet':
				$champs = liste_champs_versionnes('spip_articles','articles');
				break;
			case 'diff':
			case 'apercu':
			default:
				$champs = array_keys($new);
				break;
		}

		foreach ($champs as $champ) {
			// si la version precedente est partielle,
			// il faut remonter dans le temps
			$id_ref = $id_diff-1;
			while (!isset($old[$champ])
			AND $id_ref>0) {
				$prev = recuperer_version($id_objet,$objet, $id_ref--);
				if (isset($prev[$champ]))
					$old[$champ] = $prev[$champ];
			}
			if (!strlen($new[$champ]) && !strlen($old[$champ])) continue;

			// si on n'a que le vieux, ou que le nouveau, on ne
			// l'affiche qu'en mode "complet"
			if ($format == 'complet')
				$textes[$champ] = strlen($new[$champ])
					? $new[$champ] : $old[$champ];

			// si on a les deux, le diff nous interesse, plus ou moins court
			if (isset($new[$champ])
			AND isset($old[$champ])) {
				// cas particulier : id_rubrique
				if (in_array($champ, array('id_rubrique'))) {
					$textes[$champ] = _T('version_deplace_rubrique',
										 array('from'=> titre_rubrique($old[$champ])
											   ,'to'=>titre_rubrique($new[$champ]))
										 );
				}

				// champs textuels
				else {
					$diff = new Diff(new DiffTexte);
					$n = preparer_diff($new[$champ]);
					$o = preparer_diff($old[$champ]);
					$textes[$champ] = afficher_diff($diff->comparer($n,$o));
					if ($format == 'diff' OR $format == 'apercu')
						$textes[$champ] = afficher_para_modifies($textes[$champ], ($format == 'apercu'));
				}
			}
		}
	}

	// que donner par defaut ? (par exemple si id_version=1)
	if (!$textes)
		$textes = recuperer_version($id_objet,$objet, $id_version);

	return $textes;
}

?>
