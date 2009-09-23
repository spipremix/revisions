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
	AND sql_countsel('spip_versions', 'id_objet='.$id.' AND objet = '.sql_quote($type)) > 1
	)
		$flux['data'] .= icone_horizontale(_T('info_historique_lien'), generer_url_ecrire('objets_versions',"id_objet=$id&objet=$type"), "", "revision-24.png", false);

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
	$array['breves'] = 'revisions:breves';
	$array['rubriques'] = 'revisions:rubriques';
	$array['mots'] = 'revisions:mots';
	$array['groupes_mots'] = 'revisions:groupes_mots';

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
	$array['spip_breves'] = array(
								'table_objet' => 'breves',
								'type' => 'breve',
								'champs' => array('id_rubrique', 'titre', 'lien_titre', 'lien_url', 'texte'),
								'url_voir' => 'breves_voir',
								'texte_retour' => 'revisions:icone_retour_breve',
								'url_edit' => 'breves_edit',
								'texte_modifier' => 'icone_modifier_breve',
								'icone_objet' => 'breve-24.png'
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
	$array['spip_mots'] = array(
								'table_objet' => 'mots',
								'type' => 'mot',
								'champs' => array('titre', 'descriptif', 'texte','id_groupe'),
								'url_voir' => 'mots_edit',
								'texte_retour' => 'revisions:icone_retour_mot',
								'url_edit' => 'mots_edit',
								'url_edit_param'=>'&edit=oui',
								'texte_modifier' => 'icone_modifier_mot',
								'icone_objet' => 'mot-24.png'
							);
	$array['spip_groupe_mots'] = array(
								'table_objet' => 'groupes_mots',
								'type' => 'groupe_mot',
								'champs' => array('titre', 'descriptif', 'texte','un_seul','obligatoire','tables_liees','minirezo','forum','comite'),
								'url_voir' => 'mots_tous',
								'texte_retour' => 'revisions:icone_retour_groupe_mot',
								'url_edit' => 'mots_type',
								'texte_modifier' => 'icone_modifier_mot',
								'icone_objet' => 'groupe-mot-edit-24.png'
							);
	return $array;
}
?>