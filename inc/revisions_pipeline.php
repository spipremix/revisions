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


function revisions_boite_infos($flux){
	$type = $flux['args']['type'];
	if ($id = intval($flux['args']['id'])
	AND $type == 'article'
	AND autoriser('voirrevisions',$type,$id)
	// regarder le numero de revision le plus eleve, et afficher le bouton
	// si c'est interessant (id_version>1)
	AND sql_countsel('spip_versions', 'id_article='.$id) > 1
	)
		$flux['data'] .= icone_horizontale(_T('info_historique_lien'), generer_url_ecrire('articles_versions',"id_article=$id"), "", "revision-24.png", false);

	return $flux;
}

/*

TODO: restaurer une ancienne revision

// si une ancienne revision est demandee, la charger
// en lieu et place de l'actuelle ; attention les champs
// qui etaient vides ne sont pas vide's. Ca permet de conserver
// des complements ajoutes "orthogonalement", et ca fait un code
// plus generique.
function revisions_article_select($flux) {
	if ($id_version) {
		include_spip('inc/revisions');
		if ($textes = recuperer_version($id_article, $id_version)) {
			foreach ($textes as $champ => $contenu)
				$row[$champ] = $contenu;
		}
	}

	return $flux;
}

*/

/**
 * Afficher les dernieres revisions en bas de la page d'accueil de ecrire/
 */
function revisions_affiche_milieu($flux) {
	if ($flux['args']['exec'] == 'accueil') {
		include_spip('inc/suivi_versions');
		$flux['data'] .= afficher_suivi_versions (0, 0, false, "", true);
	}
	if ($flux['args']['exec'] == 'suivi_edito') {
		include_spip('inc/suivi_versions');
		$flux['data'] .= afficher_suivi_versions (0, 0, false, "", true);
	}
	return $flux;
}

/**
 * Definir les metas de configuration liee aux revisions
 * Utilisé par inc/config
 *
 * @param array $metas
 * @return array
 */
function revisions_configurer_liste_metas($metas){
	// Dorénavant dans les metas on utilisera un array serialisé de types d'objets
	// qui corrspondront aux objets versionnés
	$metas['objets_versions'] =  '';

	return $metas;
}

/**
 * Definir la liste des tables possibles
 * @param object $array
 * @return
 */
function revisions_revisions_liste_objets($array){
	$array['articles'] = 'revisions:articles';
	$array['rubriques'] = 'revisions:rubriques';

	return $array;
}

function revisions_revisions_infos_tables_versions($array){
	$array['spip_articles'] = array(
								'table_objet' => 'articles',
								'type' => 'article',
								'champs' => array('id_rubrique', 'surtitre', 'titre', 'soustitre', 'j_mots', 'descriptif', 'nom_site', 'url_site', 'chapo', 'texte', 'ps'),
								'url_voir' => 'articles',
								'texte_retour' => 'icone_retour_article',
								'url_edit' => 'articles_edit',
								'texte_modifier' => 'icone_modifier_article',
								'icone_objet' => 'article-24.png'
							);
	$array['spip_rubriques'] = array(
								'table_objet' => 'rubriques',
								'type' => 'rubrique',
								'champs' => array('titre', 'descriptif', 'texte'),
								'url_voir' => 'naviguer',
								'texte_retour' => 'revisions:icone_retour_rubrique',
								'url_edit' => 'rubriques_edit',
								'texte_modifier' => 'icone_modifier_rubrique',
								'icone_objet' => 'rubrique-24.png'
							);
	return $array;
}
?>