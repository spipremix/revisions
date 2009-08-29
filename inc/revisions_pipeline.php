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

// Afficher les dernieres revisions en bas de la page d'accueil de ecrire/
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

?>
