<?php
/* This function will determine whether or not the frontend user has access to the edition
 * 
 * The accessible variables are :
 * 		$gCms (the global cms object, through which we will be able to call the FEU module)
 *		$what (the level of the item we are attempting to edit, as given by the "what" parameter)
 *		$alias (the alias of the item we are attempting to edit. If it is a new item, $alias should be false)
 * 
 * The $return variable, which starts out true, should be set to true to give permission and to false to deny it.
 * 
 * You should begin by checking whether the FEU module is present and working. The following code:
 * $FEU = $this->GetModuleInstance('FrontEndUsers');
 * will make $FEU the module object for the FEU module if it is present, and will set $FEU to false;
 * 
 * Important FEU functions (provided that $FEU is not false) are :
 *  $FEU->LoggedIn() will return true if the user is logged in, and false otherwise.
 *	$FEU->LoggedInId()		returns the id of the user
 *	$FEU->LoggedInName()		returns the username of the user
 *  $FEU->MemberOfGroup($userid,$groupid)		returns true if the user is a member of the group, false otherwise
 * 	$FEU->GetGroupID($groupname)	returns the group id associated to a group name
 * 
 */ 
/*$gCms;
$FEU = $this->GetModuleInstance('FrontEndUsers');
$FEU->LoggedIn()
*/
?>

