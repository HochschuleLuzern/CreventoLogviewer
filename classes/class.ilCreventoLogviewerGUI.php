<?php

/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
* ilCreventoLogviewerGUI class
*
* @author Raphael Heer <raphael.heer@hslu.ch>
* @version $Id$
* @ingroup ServicesUIComponent
* 
* @ilCtrl_isCalledBy ilCreventoLogviewerGUI: ilRouterGUI, ilUIPluginRouterGUI
*/
class ilCreventoLogviewerGUI 
{
    /** @var $object ilObjComponentSettings */
    protected $object;
	
    /** @var $pl ilCreventoLogViewerPlugin */
    protected $pl;
    
    function __construct()
    {
        /**
         * @var $ilCtrl ilCtrl
         * @var $ilUser ilObjUser
         * @var $ilTabs ilTabsGUI
         */
        global $tpl, $ilCtrl, $ilUser, $ilTabs;
        
        $this->tpl = &$tpl;
        $this->ctrl = &$ilCtrl;
        $this->user = &$ilUser;
        $this->tabs = &$ilTabs;
        $this->object = ilObjectFactory::getInstanceByRefId($_GET['ref_id']);
        $this->ref_id = $this->object->getRefId();
        $this->pl = ilCreventoLogviewerPlugin::getInstance();
        
        $this->ctrl->setParameter($this, 'ref_id', $this->ref_id);
    }
    
    function executeCommand()
    {
        // Check Access
        $this->checkAccess();
        
        // Fill header 
        $this->initHeader();
        
        // Fill content
        $cmd = $this->ctrl->getCmd();
        
        switch($cmd)
        {
            default:
                if(method_exists($this, $cmd))
                {
                    $this->$cmd();
                }
                else 
                {
                    $this->defaultcmd();
                }
                break;
        }
        
        $this->tpl->printToStdout();
    }
    
    private function checkAccess()
    {
        global $rbacreview;
        
        if(!$rbacreview->isAssigned($this->user->getId(), '2'))
        {
            ilUtil::sendFailure('You have no permissions for this! This is only for Administrators!', true);
            ilUtil::redirect('goto.php?target=root_1');
        }
    }
    
    private function initHeader()
    {
        global $ilLocator, $ilTabs, $lng, $ilDB;
        
        /* Add breadcrumbs */
        $ilLocator->addRepositoryItems($this->ref_id);
        $this->tpl->setLocator($ilLocator->getHTML());
        
        /* Add title, description and icon of the current repositoryobject */
        try {
            // Try to get the date from the last run
            $sql = "SELECT * FROM cron_job WHERE job_id = 'crevento_import'";
            $res = $ilDB->query($sql);
            $cron = $ilDB->fetchAssoc($res);
            $last_run = ilDatePresentation::formatDate(new ilDateTime($cron['job_result_ts'],IL_CAL_UNIX));
            $this->tpl->setTitle("Evento Import Logviewer (".$this->pl->txt('title_last_run')." $last_run)");
        }
        catch (Exception $e)
        {
            $this->tpl->setTitle("Evento Import Logviewer");
        }
        $this->tpl->setDescription($this->pl->txt('subtitle_eventoimport'));
        $this->tpl->setTitleIcon(ilObject::_getIcon("", "big", $this->object->getType()));
        
        $this->ctrl->setParameterByClass('ilAdministrationGUI', 'ref_id', $this->ref_id);
        $ilTabs->setBackTarget('Plugins', $this->ctrl->getLinkTargetByClass('ilAdministrationGUI', 'jump'));
        $ilTabs->addTab('infos', 'Information', $link);
        $ilTabs->addTab('crevlog', 'Show Logs', $link);
        $ilTabs->activateTab('crevlog');
       
        $link_usrs = $this->ctrl->getLinkTarget($this, 'showUsrs');
        $ilTabs->addSubTab('usrs', $this->pl->txt('tab_usrs'), $link_usrs);
        
        $link_mas = $this->ctrl->getLinkTarget($this, 'showMas');
        $ilTabs->addSubTab('mas', $this->pl->txt('tab_mas'), $link_mas);
        
        $link_subs = $this->ctrl->getLinkTarget($this, 'showSubs');
        $ilTabs->addSubTab('subs', $this->pl->txt('tab_subs'), $link_subs);
        
        /* Create and add backlink */
        //$ilTabs->setBackTarget($lng->txt("cmps_plugins"),
        //                $this->ctrl->getLinkTargetByClass(array('ilAdministrationGUI','ilObjComponentSettingsGUI'), "listPlugins"));
        /*$back_link = $this->ctrl->getLinkTargetByClass(array(
                        'ilRepositoryGUI',
                        'ilObj' . $this->obj_def->getClassName($this->obj->getType()) . 'GUI'
        ));
        $class_name = $this->obj_def->getClassName($this->obj->getType());
        $this->ctrl->setParameterByClass('ilObj' . $class_name . 'GUI', 'ref_id', $this->ref_id);
        $this->tabs->setBackTarget($this->plugin->txt('tab_back_link'), $back_link);*/
    }
    
    function showLogs()
    {
        $this->tpl->setContent('just a placeholder');
    }
    
    function defaultcmd()
    {
        $this->tpl->setContent(print_r($this->ctrl->getCmd(), true));        
    }
    
    protected function applyUsrsFilter()
    {
        include_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CreventoLogviewer/classes/Usrs/class.ilCreventoLogviewerUsrsTableGUI.php';
        $table = new ilCreventoLogviewerUsrsTableGUI($this);
        $table->resetOffset();
        $table->writeFilterToSession();
        $this->showUsrs();
    }
    
    protected function resetUsrsFilter()
    {
        include_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CreventoLogviewer/classes/Usrs/class.ilCreventoLogviewerUsrsTableGUI.php';
        $table = new ilCreventoLogviewerUsrsTableGUI($this);
        $table->resetOffset();
        $table->resetFilter();
        $this->showUsrs();
    }
    
    protected function showUsrs()
    {
        $this->tabs->activateSubTab('usrs');
        
        include_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CreventoLogviewer/classes/Usrs/class.ilCreventoLogviewerUsrsTableGUI.php';
        $table = new ilCreventoLogviewerUsrsTableGUI($this);
        $this->tpl->setContent($table->getHTML());
        $this->setModalJavaScript('getUsrsData');
    }
    
    protected function getUsrsData()
    {
        global $ilDB;
        $evento_id = $_GET['evento_id'];
        include_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CreventoLogviewer/classes/Usrs/class.ilCreventoUsrsQuery.php';
        $data = ilCreventoUsrsQuery::fetchData($evento_id);
        echo '<pre>'.print_r(unserialize($data), true) . '</pre>';
        exit;
    }
    
    protected function applyMasFilter()
    {
        include_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CreventoLogviewer/classes/Mas/class.ilCreventoLogviewerMasTableGUI.php';
        $table = new ilCreventoLogviewerMasTableGUI($this);
        $table->resetOffset();
        $table->writeFilterToSession();
        $this->showMas();
    }
    
    protected function resetMasFilter()
    {
        include_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CreventoLogviewer/classes/Mas/class.ilCreventoLogviewerMasTableGUI.php';
        $table = new ilCreventoLogviewerMasTableGUI($this);
        $table->resetOffset();
        $table->resetFilter();
        $this->showMas();
    }
    
    
    protected function showMas()
    {
        $this->tabs->activateSubTab('mas');
        
        include_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CreventoLogviewer/classes/Mas/class.ilCreventoLogviewerMasTableGUI.php';
        $table = new ilCreventoLogviewerMasTableGUI($this);
        $this->tpl->setContent($table->getHTML());
        $this->setModalJavaScript('getMasData');
    }
    
    protected function getMasData()
    {
        global $ilDB;
        $evento_id = $_GET['evento_id'];
        include_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CreventoLogviewer/classes/Mas/class.ilCreventoMasQuery.php';
        $data = ilCreventoMasQuery::fetchData($evento_id);
        echo '<pre>'.print_r(unserialize($data), true) . '</pre>';
        exit;
    }
    
    protected function applySubsFilter()
    {
        include_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CreventoLogviewer/classes/Subs/class.ilCreventoLogviewerSubsTableGUI.php';
        $table = new ilCreventoLogviewerSubsTableGUI($this);
        $table->resetOffset();
        $table->writeFilterToSession();
        $this->showSubs();
    }
    
    protected function resetSubsFilter()
    {
        include_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CreventoLogviewer/classes/Subs/class.ilCreventoLogviewerSubsTableGUI.php';
        $table = new ilCreventoLogviewerSubsTableGUI($this);
        $table->resetOffset();
        $table->resetFilter();
        $this->showSubs();
    }
    
    
    protected function showSubs()
    {
        $this->tabs->activateSubTab('subs');
        
        include_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CreventoLogviewer/classes/Subs/class.ilCreventoLogviewerSubsTableGUI.php';
        $table = new ilCreventoLogviewerSubsTableGUI($this);
        $this->tpl->setContent($table->getHTML());
        $this->setModalJavaScript('getSubsData');
    }
    
    protected function getSubsData()
    {
        global $ilDB;
        $evento_id = $_GET['evento_id'];
        include_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CreventoLogviewer/classes/Subs/class.ilCreventoSubsQuery.php';
        $data = ilCreventoSubsQuery::fetchData(explode('_', $evento_id));
        echo '<pre>'.print_r(unserialize($data), true) . '</pre>';
        exit;
    }
    
    protected function setModalJavaScript($cmd)
    {
        $this->tpl->addJavaScript('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CreventoLogviewer/js/ImportDataModal.js');
        $link = str_replace('&amp;', '&', ILIAS_HTTP_PATH."/".$this->ctrl->getLinkTarget($this, $cmd));
        $this->tpl->addOnLoadCode("crevento_url='$link';");
    }
    
    /**
     * Show auto complete results
     */
    protected function addUserAutoComplete()
    {
        include_once './Services/User/classes/class.ilUserAutoComplete.php';
        $auto = new ilUserAutoComplete();
        $auto->setSearchFields(array('login','firstname','lastname','email'));
        $auto->enableFieldSearchableCheck(false);
        $auto->setMoreLinkAvailable(true);
        
        if(($_REQUEST['fetchall']))
        {
            $auto->setLimit(ilUserAutoComplete::MAX_ENTRIES);
        }

        echo $auto->getList($_REQUEST['term']);
        exit();
    }
}
?>
