<?php

/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */

include_once("./Services/UIComponent/classes/class.ilUIHookPluginGUI.php");

/**
* ilCreventoLogviewerUIHookGUI class
*
* @author Simon Moor <simon.moor@hslu.ch>
* @version $Id$
* @ingroup ServicesUIComponent
*/
class ilCreventoLogviewerUIHookGUI extends ilUIHookPluginGUI {
	function modifyGUI($a_comp, $a_part, $a_par = array())
	{
		if ($a_part == "tabs")
		{
			// $a_par["tabs"] is ilTabsGUI object
			
			$ref_id=(int)$_GET['ref_id'];
            
			if(isset($_GET['admin_mode']) && $_GET['admin_mode'] == 'settings' &&
			   isset($_GET['plugin_id'])  && ($_GET['plugin_id'] == 'crevento' ||  $_GET['plugin_id'] == 'creventologviewer') &&
               isset($_GET['cmdClass'])   && $_GET['cmdClass'] == 'ilobjcomponentsettingsgui' &&
               isset($_GET['cmd'])        && $_GET['cmd'] == 'showPlugin')
			{
			    global $ilias,$tree,$lng, $ilCtrl, $tabs;
			    /** @var $ilCtrl ilCtrl
			     *  @var $a_par['tabs'] ilTabsGUI */
			    $pl = ilCreventoLogviewerPlugin::getInstance();
			    $ilCtrl->setParameterByClass('ilCreventoLogviewerGUI', 'ref_id', $_GET['ref_id']);
			    $link = $ilCtrl->getLinkTargetByClass(array('ilUIPluginRouterGUI','ilCreventoLogviewerGUI'), 'showUsrs');
			    $a_par['tabs']->addTab('infos', 'Information', $_SERVER['REQUEST_URI']);
			    $a_par['tabs']->addTab('crevlog', $pl->txt('tab_import_logs'), $link);
			    $a_par['tabs']->activateTab('infos');
			}
		}
	}
	
}
?>