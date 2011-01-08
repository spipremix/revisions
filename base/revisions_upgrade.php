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

/**
 * Installation/maj des tables revision
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function revisions_upgrade($nom_meta_base_version,$version_cible){

	spip_log($GLOBALS['meta'][$nom_meta_base_version],'revisions');
	$current_version = 1.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){

		if ($current_version==1.0){
			include_spip('base/revisions');
			include_spip('base/create');
			include_spip('inc/meta');
			// creer les tables
			creer_base();
			// mettre les metas par defaut
			$config = charger_fonction('config','inc');
			$config();
			if($GLOBALS['meta']['articles_versions'] == 'oui'){
				ecrire_meta('objets_versions',serialize(array('articles')));
			}
			effacer_meta('articles_versions');
			ecrire_meta($nom_meta_base_version,$version_cible);
		}
		if ($current_version<1.1){
			include_spip('inc/meta');
			revisions_objet_upgrade_11();
			// Si dans une installation antérieure ou un upgrade, les articles étaient versionnés
			// On crée la meta correspondante
			$config = charger_fonction('config','inc');
			$config();
			if($GLOBALS['meta']['articles_versions'] == 'oui'){
				ecrire_meta('objets_versions',serialize(array('articles')));
			}
			effacer_meta('articles_versions');
			echo _T('revisions:plugin_update',array('version'=>1.1));
			ecrire_meta($nom_meta_base_version,$current_version=1.1);
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

	effacer_meta($nom_meta_base_version);
}

function revisions_objet_upgrade_11() {
	// Ajout du champs objet et modification du champs id_article en id_objet
	// sur les 2 tables spip_versions et spip_versions_fragments
	sql_alter("TABLE spip_versions CHANGE `id_article` `id_objet` bigint(21) DEFAULT 0 NOT NULL");
	sql_alter("TABLE spip_versions ADD `objet` VARCHAR (25) DEFAULT '' NOT NULL AFTER `id_objet`");
	sql_alter("TABLE spip_versions_fragments CHANGE `id_article` `id_objet` bigint(21) DEFAULT 0 NOT NULL");
	sql_alter("TABLE spip_versions_fragments ADD `objet` VARCHAR (25) DEFAULT '' NOT NULL AFTER `id_objet`");

	// Changement des clefs primaires également
	sql_alter("TABLE `spip_versions` DROP PRIMARY KEY");
	sql_alter("TABLE `spip_versions` ADD PRIMARY KEY ( `id_version`, `id_objet`, `objet` )");
	sql_alter("TABLE `spip_versions_fragments` DROP PRIMARY KEY");
	sql_alter("TABLE `spip_versions_fragments` ADD PRIMARY KEY ( `id_objet` , `objet` , `id_fragment` , `version_min` )");

	// Ajouter l'objet "article" aux révisions existantes dans les 2 tables
	// Les id_objet restent les id_articles puisque les révisions n'étaient possibles que sur les articles
	sql_updateq("spip_versions",array('objet'=>'article'),"objet=''");
	sql_updateq("spip_versions_fragments",array('objet'=>'article'),"objet=''");
}
?>