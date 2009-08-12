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

/* fonction pour le pipeline d'autorisation */
function revisions_autoriser(){}

// Voir les revisions ?
// = voir l'objet
function autoriser_voirrevisions_dist($faire, $type, $id, $qui, $opt) {
	return
		autoriser('voir', $type, $id, $qui, $opt);
}

// tout le monde peut voir le bouton de suivi des revisions
function autoriser_suivi_revisions_bouton_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return true;
}

?>
