<?php
if( !defined('CMS_VERSION') ) exit;

// Typical Database Initialization
$db = $this->GetDb();
$dict = NewDataDictionary($db);

	$sqlarray = $dict->DropTableSQL(CMS_DB_PREFIX."module_youtubeplayer_category");
	$dict->ExecuteSQLArray($sqlarray);
	$db->DropSequence(CMS_DB_PREFIX."module_youtubeplayer_category_seq");
	

	$sqlarray = $dict->DropTableSQL(CMS_DB_PREFIX."module_youtubeplayer_videos");
	$dict->ExecuteSQLArray($sqlarray);
	$db->DropSequence(CMS_DB_PREFIX."module_youtubeplayer_videos_seq");
	
	$this->RemovePreference();
	$this->DeleteTemplate();
	
	$sqlarray = $dict->DropTableSQL(CMS_DB_PREFIX."module_youtubeplayer_saved_queries");
	$dict->ExecuteSQLArray($sqlarray);
	$db->DropSequence(CMS_DB_PREFIX."module_youtubeplayer_saved_queries_seq");

// permissions

	$this->RemovePermission("youtubeplayer_normaluser");
	$this->RemovePermission("youtubeplayer_advanced");
	$this->RemovePermission("youtubeplayer_manage_category");
	$this->RemovePermission("youtubeplayer_manage_videos");

	// template stuff
	try {
		$types = \CmsLayoutTemplateType::load_all_by_originator($this->GetName());
		if ( is_array($types) && count($types) ) {
			foreach( $types as $type ) {
				$templates = $type->get_template_list();
				if ( is_array($templates) && count($templates) ) {
					foreach( $templates as $template ) {
						$template->delete();
					}
				}
				$type->delete();
			}
		}
	} catch( \Exception $e ) {
		\xt_utils::log_exception( $e );
		audit('',$this->GetName(),'Uninstall error: '.$e->GetMessage());
	}
?>
