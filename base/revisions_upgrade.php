<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2009                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

/**
 * Installation/maj des tables revision
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function revisions_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 1.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		
		if ($current_version==1.0){
			include_spip('base/revisions');
			include_spip('base/create');
			// creer les tables
			creer_base();
			// mettre les metas par defaut
			$config = charger_fonction('config','inc');
			$config();
			ecrire_meta($nom_meta_base_version,$current_version=$version_cible);
		}
	}
}

/**
 * Desinstallation/suppression des tables revisions
 *
 * @param string $nom_meta_base_version
 */
function revisions_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_versions");
	sql_drop_table("spip_versions_fragments");
	
//	effacer_meta("mots_cles_forums");

	effacer_meta($nom_meta_base_version);
}

?>