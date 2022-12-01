<?php
	$lang["friendlyname"] = "YouTubePlayer";
	$lang["moddescription"] = "Embed YouTube Player";
	$lang["admindescription"] = "Embed YouTube Player";

	$lang["pagemenudelimiter"] = "&nbsp;&#124;&nbsp;";
	$lang["pagemenuoverflow"] = "&nbsp;...&nbsp;";
	
// strings for category
	$lang["category"] = "Category";
	$lang["category_plural"] = "Categories";
	$lang["add_category"] = "Ajouter category";
	$lang["edit_category"] = "Modifier category";
	$lang["filterby_category"] = "Filtrer par category";
	$lang["category_description"] = "Description";
	$lang["promt_deletecategory"] = "Vous &ecirc;tes sur le point de supprimer ce category (%s)? Tous les &eacute;l&eacute;ments rattach&eacute;s seront perdus. Voulez-vous continuer?";
	
// strings for videos
	$lang["videos"] = "Videos";
	$lang["videos_plural"] = "Videoss";
	$lang["add_videos"] = "Ajouter videos";
	$lang["edit_videos"] = "Modifier videos";
	$lang["filterby_videos"] = "Filtrer par videos";
	$lang["videos_videoid"] = "Videoid";
	$lang["videos_description"] = "Description";
	$lang["promt_deletevideos"] = "Vous &ecirc;tes sur le point de supprimer ce videos (%s)? Voulez-vous continuer?";
	$lang["templatehelp"] = '<h3>SVariables Smarty pour le gabarit de liste de: category</h3><ul>
	<li>$leveltitle</li>
	<li>$parentobj (if parent is specified)</li>
	<li>$itemlist (array of items)</li>
	<li>$item-&gt;is_selected</li>
	<li>$item-&gt;name</li>
	<li>$item-&gt;alias</li>
	<li>$item-&gt;detaillink</li>
	<li>$item-&gt;detailurl</li>
	<li>$item-&gt;description</li>
	<li>$item-&gt;isdefault</li>
	</ul><br/><hr/><br/><h3>SVariables Smarty pour le gabarit de liste de: videos</h3><ul>
	<li>$leveltitle</li>
	<li>$parentobj (if parent is specified)</li>
	<li>$itemlist (array of items)</li>
	<li>$item-&gt;is_selected</li>
	<li>$item-&gt;name</li>
	<li>$item-&gt;alias</li>
	<li>$item-&gt;detaillink</li>
	<li>$item-&gt;detailurl</li>
	<li>$item-&gt;videoid</li>
	<li>$item-&gt;description</li>
	<li>$item-&gt;parent_id</li>
	<li>$item-&gt;parent_alias</li>
	<li>$item-&gt;parent_name</li>
	<li>$item-&gt;parentlink</li>
	<li>$tiem-&gt;parenturl</li>
	<li>$item-&gt;isdefault</li>
	</ul><br/><hr/><br/><h3>Variables Smarty pour le gabarit de d&eacute;tail</h3><ul>
	<li>$leveltitle</li>
	<li>$item-&gt;name</li>
	<li>$item-&gt;alias</li>
	<li>$item-&gt;videoid</li>
	<li>$item-&gt;description</li>
	<li>$item-&gt;parent_id</li>
	<li>$item-&gt;parent_alias</li>
	<li>$item-&gt;parent_name</li>
	<li>$item-&gt;parentlink</li>
	<li>$tiem-&gt;parenturl</li>
	<li>$item-&gt;isdefault</li>
	<li>$labels->...</li>
	</ul><br/><p>Dans le gabarit de d&eacute;tail du niveau final, utilisez l\'objet $labels pour afficher les &eacute;tiquettes de champs selon la langue ($labels->nom_du_champ).</p><p>Vous pouvez acc&eacute;der aux objets parents en utilisant $item->parent_object->parent_object->... (et ainsi de suite)</p><p><br/><h2>Breadcrumbs</h2>
				<p>Lorsque vous &ecirc;tes dans un gabarit de module, you pouvez appeler les breadcrumbs en utilisant la balise {youtubeplayer_breadcrumbs}. Vous pouvez utiliser les m&ecirc;mes param&egrave;tres que dans la balise breadcrumbs du cms (initial, delimiter, classid, currentclassid), de m&ecirc;me que le param&egrave;tre "startlevel".<br/>
				En dehors des gabarits de module, n\'importe o&ugrave; sur la page, vous pouvez appeler l\'action breadcrumbs {cms_module module="youtubeplayer" action="breadcrumbs"}, utilisant les m&ecirc;mes param&egrave;tres.</p></p>';

// For file fields
$lang["Remove"] = "Enlever";
$lang["browsefilestitle"] = "Choisissez un fichier ou uploadez un nouveau fichier plus bas.";
$lang["showingdir"] = "R&eacute;pertoire";
$lang["browsefilesresize"] = "L'image sera automatiquement redimensionn&eacute;e pour le module.";
$lang["browsefilecurrentpath"] = "R&eacute;pertoire : ";
$lang["parentdir"] = "R&eacute;pertoire parent";
$lang["addafile"] = "Ajouter un fichier";

// strings for general fields
$lang["id"] = "id";
$lang["name"] = "Nom";
$lang["alias"] = "Alias";
$lang["isdefault"] = "Par d&eacute;faut?";
$lang["active"] = "Actif";
$lang["parent"] = "Parent";
$lang["nbchildren"] = "Nb d'&eacute;l&eacute;ments";
	
// GENERAL
$lang["activate"] = "Activer";
$lang["unactivate"] = "D&eacute;sactiver";
$lang["Yes"] = "Oui";
$lang["No"] = "Non";
$lang["Actions"] = "Actions";
$lang["reorder"] = "R&eacute;ordonner";
$lang["listtemplate"] = "Gabarit de liste pour";
$lang["defaulttemplates"] = "Gabarits par d&eacute;faut";
$lang["templates"] = "Gabarits";
$lang["template"] = "Gabarit";
$lang["edittemplate"] = "Modifier le gabarit";
$lang["templatevars"] = "Variables de gabarit";
$lang["deftemplatefor"] = "Gabarit de liste par d&eacute;faut pour le niveau ";
$lang["defdetailtemplate"] = "Gabarit de d&eacute;tail par d&eacute;faut";
$lang["addtemplate"] = "Ajouter un gabarit";
$lang["filterby"] = "Filtrer par";
$lang["showall"] = "Tout voir (aucun filtre)";
$lang["fieldoptions"] = "Options de champs";
$lang["addoption"] = "Ajouter une option";
$lang["modifyanoption"] = "Modifier une option";
$lang["message_deleted"] = "&Eacute;l&eacute;ment supprim&eacute;.";
$lang["message_modified"] = "Modification sauvegard&eacute;e.";
$lang["warning_tab"] = "Attention: Sauvegardez les changements dans les autres onglets avant de travailler dans celui-ci...";
$lang["error_missginvalue"] = "Une ou plusieurs valeur obligatoires n'ont pas &eacute;t&eacute; entr&eacute;es.";
$lang["error_alreadyexists"] = "Il y a d&eacute;j&agrave; un &eacute;l&eacute;ment portant ce nom.";
$lang["error_date"] = "La date que vous avez entr&eacute;e est invalide.";
$lang["error_noparent"] = "Aucun parent d&eacute;fini!";
$lang["error_notfound"] = "L'&eacute;l&eacute;ment n'a pas pu &ecirc;tre trouv&eacute;.";
$lang["error_noitemfound"] = "Aucun &eacute;l&eacute;ment trouv&eacute;.";
$lang["finaltemplate"] = "Gabarit pour le niveau final (videos)";
$lang["prompt_deleteoption"] = "Voulez-vous vraiment supprimer cette option?";
$lang["frontend_submit"] = "Envoyer";
$lang["frontend_cancel"] = "Annuler";

// BREADCRUMBS :
$lang["youarehere"] = "Vous &ecirc;tes ici: ";
$lang["breadcrumbs_delimiter"] = " &gt; ";

// SEARCH :
$lang["searchtitle"] = "Recherche";
$lang["searchagain"] = "Recommencer la recherche";
$lang["searchbtn"] = "Chercher!";
$lang["contains"] = "Contiens";
$lang["isexactly"] = "Est egal &agrave;";

// MODULE INTERACTION
$lang["postinstall"] = "Module ajout&eacute;.";
$lang["postuninstall"] = "Module d&eacute;sinstall&eacute;.";
$lang["really_uninstall"] = "Tout le contenu du module sera perdu. Poursuivre?";
$lang["uninstalled"] = "Module d&eacute;sinstall&eacute;.";
$lang["installed"] = "Module version %s install&eacute;.";
$lang["help"] = "<h2>Aide g&eacute;n&eacute;rale</h2><br/>
				<p>Pour appeler le module, utilisez la balise suivante :<br/>
				{cms_module module=\"youtubeplayer\"}</p>
				<p>Dans ce cas une liste des &eacute;l&eacute;ments du dernier niveau (videos) sera affich&eacute;e. Pour sp&eacute;cifier un niveau, utilisez le param&egrave;tre \"what\" :<br/>
				{cms_module module=\"youtubeplayer\" what=\"videos\"}<br/>
				<i>Les valeurs possibles pour \"what\" sont : category, videos</i></p>
				<p>Vous pouvez aussi demander les &eacute;l&eacute;ments qui appartiennent &agrave; un parent sp&eacute;cifique:<br/>
				{cms_module module=\"youtubeplayer\" parent=\"alias_of_parent\"}</p>
				<p>Vous pouvez finalement appeler un eacute;leacute;ment en particulier:<br>
				{cms_module module=\"youtubeplayer\" alias=\"alias_of_item\"}</p>
				<br/><h2>S&eacute;paration en pages</h2>
				<p>Vous pouvez limiter le nombre d'&eacute;l&eacute;ments devant &ecirc;tre affich&eacute;s sur une m&ecirc;me page:<br/>
				{cms_module module=\"youtubeplayer\" nbperpage=\"5\"}<br/>
				Le menu des pages est ensuite affich&eacute; avec la balise {".'$'."pagemenu}</p>
				<p>Des classes ont &eacute;t&eacute; assign&eacute;es aux &eacute;l&eacute;ments du menu de page pour que vous puissez le personnaliser.</p>
				<br/><h2>L'action de recherche</h2>
				<p>Vous pouvez utiliser l'action \"search\" pour afficher un formulaire de recherche:<br/>
				{cms_module module=\"youtubeplayer\" action=\"search\" what=\"videos\"}<br/>
				Utilisez le param&egrave;tre \"searchmode\" pour choisir la recherche simple ou avanc&eacute;e (par d&eacute;faut). Notez que vous ne pouvez pas faire une recherche en mode avanc&eacute; dans tous les niveaux en m&ecirc;me temps.<br/>
				Les param&egrave;tres suivants peuvent &ecirc;tre utilis&eacute;s avec l'action \"search\": what, limit, orderby, detailpage, listtemplate, inline, searchmode.</p><br/><br/>
				<p>Pour davantage d'aide, vous pouvez consulter le <a href=\"../modules/youtubeplayer/doc/faq.html\" target=\"_blank\">FAQ</a>.</p><br/><br/>";


//EVENTS
$lang["eventdesc_modified"] = "Appel&eacute; apr&egrave;s qu'un &eacute;l&eacute;ment soit modifi&eacute;.";
$lang["eventdesc_deleted"] = "Appel&eacute; apr&egrave;s qu'un &eacute;l&eacute;ment soit supprim&eacute;.";
$lang["eventdesc_added"] = "Appel&eacute; apr&egrave;s qu'un &eacute;l&eacute;ment soit ajout&eacute;.";
$lang["eventhelp_modified"] = "Param&egrave;tres: \"what\"=>niveau de l'&eacute;l&eacute;ment, \"itemid\"=>id de  l'&eacute;l&eacute;ment, \"alias\"=>alias de l'&eacute;l&eacute;ment.";
$lang["eventhelp_deleted"] = "Param&egrave;tres: \"what\"=>niveau de l'&eacute;l&eacute;ment.";
$lang["eventhelp_added"] = "Param&egrave;tres: \"what\"=>niveau de l'&eacute;l&eacute;ment, \"itemid\"=>id de  l'&eacute;l&eacute;ment, \"alias\"=>alias de l'&eacute;l&eacute;ment.";

//PARAMETERS
$lang["phelp_action"] = "Soit \"link\", \"search\", \"breadcrumbs\" ou \"default\".";
$lang["phelp_what"] = "Permet de sp&eacute;cifier le niveau. Les valeurs possibles sont : <i>category, videos</i>";
$lang["phelp_alias"] = "Alias de l'&eacute;l&eacute;ment que vous voulez afficher.";
$lang["phelp_parent"] = "Si vous voulez limiter les &eacute;l&eacute;ments affich&eacute;s &agrave; ceux appartenant &agrave; un parent particulier, entrez ici l'alias du parent.";
$lang["phelp_limit"] = "Limite le nombre d'item retourn&eacute;s par la requ&ecirc;te (0 = pas de limite)";
$lang["phelp_nbperpage"] = "Limite le nombre d'&eacute;l&eacute;ments affich&eacute;s par page.";
$lang["phelp_orderby"] = "Valeurs possibles : \"modified\", \"created\" et \"name\". Toute autre valeur ordonnera en fonction de l'ordre normal des items.";
$lang["phelp_detailpage"] = "Sp&eacute;cifie l'alias de la page vers laquelle pointeront les liens (si rien n'est sp&eacute;cifi&eacute;, la page actuelle est utilis&eacute;e)";
$lang["phelp_showdefault"] = "Mettre \"true\" si vous voulez afficher l'&eacute;l&eacute;ment par d&eacute;faut.";
$lang["phelp_random"] = "Entrez un nombre pour afficher ce nombre d'&eacute;l&eacute;ments al&eacute;atoires.";
$lang["phelp_finaltemplate"] = "Permet de sp&eacute;cifier le gabarit &agrave; utiliser pour l'affichage .";
$lang["phelp_listtemplate"] = "Permet de sp&eacute;cifier le gabarit &agrave; utiliser pour l'affichage de liste d&eacute;taill&eacute; du niveau final.";
$lang["phelp_forcelist"] = "Mettre &agrave; 1 si vous voulez afficher en format list m&ecirc;me lorsqu'il n'y a qu'un &eacute;l&eacute;ment (affecte le niveau final seulement).";
$lang["phelp_internal"] = "Pour usage interne; sp&eacute;cifie la page (lorsque nbperpage est utilis&eacute;.";
$lang["phelp_searchmode"] = "Utiliser avec l'action \"search\". Mettre \"simple\" pour chercher dans tous les champs, et \"advanced\" pour chercher dans des champs sp&eacute;cifiques.";
$lang["phelp_inline"] = "Met les liens \"inline\".";
$lang["phelp_decodeentities"] = "Utiliser avec l'action \"frontendadd\". Si actif, d&eacute;code les entit&eacute;s html.";

?>