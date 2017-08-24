<?php
require_once("./Services/Link/classes/class.ilLink.php");
include_once 'Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CreventoLogviewer/classes/class.ilCreventoLogviewerBaseTableGUI.php';

class ilCreventoLogviewerSubsTableGUI extends ilCreventoLogviewerBaseTableGUI
{
    /** @var $tree ilTree */
    protected $tree;
    
    /** @var $rbacreview ilRbacReview  */
    protected $rbacreview;
    
    function __construct($object_gui, $cmd = 'showSubs')
    {
        global $tree, $rbacreview;
        
        $this->tree = &$tree;
        $this->rbacreview = &$rbacreview;
        
        parent::__construct($object_gui, $cmd);
        $this->plugin = ilStructureImportPlugin::getInstance();
        
        $this->setRowTemplate("tpl.subs_table_row.html", "Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CreventoLogviewer");
        $this->setTitle($this->pl->txt('title_subs'));
        
        $this->setFormAction($this->ctrl->getFormAction($object_gui, 'applySubsFilter'));
        $this->setFilterCommand('applySubsFilter');
        $this->setResetCommand('resetSubsFilter');
        $this->setDefaultOrderField("login");
        $this->setDefaultOrderDirection("asc");
    }
    
    public function initFilter()
    {
        include_once("./Services/Form/classes/class.ilTextInputGUI.php");
        $ul = new ilTextInputGUI($this->pl->txt('col_crs_grp'), 'crs_grp_title');
        $ul->setSize(20);
        $ul->setSubmitFormOnEnter(true);
        $this->addFilterItem($ul);
        $ul->readFromSession();
        $this->filter["crs_grp_title"] = $ul->getValue();
        
        
        $this->setAndGetInfocodeFilter();
    }
    
    protected function setHeaderRow()
    {
        $this->addColumn($this->pl->txt('col_user'), 'evento_id');
        $this->addColumn($this->pl->txt('col_crs_grp'), 'crs_grp');
        $this->addColumn($this->pl->txt('col_role'), 'role_id');
        $this->addColumn($this->pl->txt('col_last_import_date'), 'last_import_date');
        $this->addColumn($this->pl->txt('col_import_infocode'), 'update_info_code');
    }
    
    protected function getTableItems()
    {
        include_once 'Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CreventoLogviewer/classes/Subs/class.ilCreventoSubsQuery.php';
        $query = new ilCreventoSubsQuery();
        $query->setStatuscodeFilter($this->filter['statuscodes']);
        $query->setAfterSQLFilter('crs_grp', $this->filter['crs_grp_title']);
        return $query->query();
        //$this->setData(ilCreventoQuery::_getUsrs(10, 0));
    }
    
    /**
     *
     * @param unknown $row
     */
    function fillRow($row)
    {
        $this->tpl->setCurrentBlock('login_td');
        $this->tpl->setVariable('USER_LINK', $this->createUserLink($row['usr_id']));
        $this->tpl->setVariable('LOGIN', ilObject::_lookupTitle($row['usr_id']) . ' (' . ilObjUser::_lookupLogin($row['usr_id']). ')'); // User login...
        $this->tpl->parseCurrentBlock();
        
        $this->tpl->setCurrentBlock('course_td');
        $obj_id = $this->rbacreview->getObjectOfRole($row['role_id']);
        $this->tpl->setVariable('COURSE_LINK', $this->createCrsGrpLink($row['crs_grp_obj_id']));
        $this->tpl->setVariable('COURSE', $row['crs_grp_title']);
        $this->tpl->parseCurrentBlock();
        
        $this->tpl->setCurrentBlock('role_td');
        //$this->tpl->setVariable('ROLE_LINK', $role_link);
        $this->tpl->setVariable('ROLE_NAME', $row['role_id']); //Role name...
        $this->tpl->parseCurrentBlock();
        
        $this->tpl->setCurrentBlock('last_import_td');
        $this->tpl->setVariable('LAST_IMPORT', $row['last_import_date']);
        $this->tpl->parseCurrentBlock();
        
        $this->tpl->setCurrentBlock('update_infocode_td');
        $this->tpl->setVariable('UPDATE_INFOCODE', $this->pl->txt('statuscode_' . $row['update_info_code']));
        $this->tpl->parseCurrentBlock();
    }
    
    private function createUserLink($usr_id)
    {
        $this->ctrl->setParameterByClass("ilobjusergui", "obj_id", $usr_id);
        $link = $this->ctrl->getLinkTargetByClass(array("ilAdministrationGUI","ilobjusergui"), "view");
        $this->ctrl->setParameterByClass("ilobjusergui", "obj_id", '');
        return $link;
    }
    
    private function createCrsGrpLink($obj_id)
    {
        $ref_ids = ilObject::_getAllReferences($obj_id);
        foreach ($ref_ids as $ref_id) {$obj_ref_id = $ref_id;}
        return ilLink::_getLink($obj_ref_id, ilObject::_lookupType($obj_id));
    }
    
    protected function getStatuscodeArray()
    {
        return array(102, 103, 104, 121, 123);
    }
}