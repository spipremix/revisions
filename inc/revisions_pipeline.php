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


function revisions_boite_infos($flux){
	$type = $flux['args']['type'];
	if ($id = intval($flux['args']['id'])
	AND $type == 'article'
	AND autoriser('voirrevisions',$type,$id)
	// regarder le numero de revision le plus eleve, et afficher le bouton
	// si c'est interessant (id_version>1)
	AND sql_countsel('spip_versions', 'id_objet='.$id.' AND objet = '.sql_quote($type)) > 1
	) {
		include_spip('inc/presentation');
		$flux['data'] .= icone_horizontale(_T('info_historique_lien'), generer_url_ecrire('objets_versions',"id_objet=$id&objet=$type"), "revision-24.png");
	}

	return $flux;
}

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
	// qui correspondront aux objets versionnés
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
?>