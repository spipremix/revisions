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
include_spip('inc/presentation');

function exec_revisions_restaurer_dist()
{
	pipeline('exec_init',
		array('args'=>array('exec'=>'revisions_restaurer'),'data'=>''));

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('revisions:titre_restaurer_revision'), "accueil", "accueil");

	echo debut_gauche("",true);

	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'revisions_restaurer'),'data'=>''));
	echo creer_colonne_droite("",true);
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'revisions_restaurer'),'data'=>''));
	echo debut_droite("",true);

	exec_revisions_restaurer_args(
		_request('type'),
		intval(_request('id_objet')),
		intval(_request('id_version'))
	);

	echo fin_gauche(), fin_page();
}

function reconstituer_version($type, $id_objet, $id_version) {
	$prev  = array();
	foreach (sql_allfetsel('id_version', 'spip_versions', 'id_objet='.intval($id_objet).' AND objet='.sql_quote($type).' AND id_version <= '.sql_quote($id_version), 'id_version') as $id) {
		$prev = array_merge($prev, recuperer_version($id_objet,$type, $id['id_version']));
	}
	return $prev;
}

function exec_revisions_restaurer_args($type, $id_objet, $id_version) {
	if (!$type
	OR !$id_objet = intval($id_objet)
	OR !$id_version = intval($id_version)
	) {
		echo 'mauvais arguments';
		return;
	}

	if (!autoriser('modifier', $type, $id_objet)) {
		echo 'vous n avez pas acces a cette page';
		return;
	}

	// Restaurer : faire la liste des champs dispos dans cet objet
	// et voir s'ils ont une revision
	include_spip('inc/revisions');
	$old = reconstituer_version($type, $id_objet, $id_version);

	// les comparer aux champs actuels de cet objet
	$int = array(); // interessants
	$row = sql_fetsel('*', table_objet_sql($type), id_table_objet($type).'='.$id_objet);
	foreach($old as $champ=>$val) {
		if (isset($row[$champ])
		AND $row[$champ] != $val) {
			$int[$champ] = $val;
		}
	}

	if (!$int) {
		echo _L('La révision '.$id_version.' est identique à la version actuelle');
		return;
	}

	// proposer de restaurer ceux qui different
	// => formulaire renvoyant sur ?? (he oui pas de crayons)
	include_spip('inc/diff');
	include_spip('inc/suivi_versions');

	echo _L('Les champs suivants peuvent être restaurés depuis la révision '.$id_version.' :');
	foreach ($int as $champ=> $val) {
		echo "<h2>$champ</h2>";
		echo "différence par rapport au contenu actuel : ";

		$diff = new Diff(new DiffTexte);
		$n = preparer_diff($val);
		$o = preparer_diff($row[$champ]);
		$t = afficher_diff($diff->comparer($n,$o));

		echo propre_diff($t);

		echo "<div class='formulaire_spip'><p>Copier l'ancienne version :</p>";
		echo "<textarea>".entites_html($val)."</textarea></div>\n";
	}


}

?>
