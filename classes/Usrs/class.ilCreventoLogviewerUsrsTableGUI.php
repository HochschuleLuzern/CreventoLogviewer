<?php
include_once 'Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CreventoLogviewer/classes/class.ilCreventoLogviewerBaseTableGUI.php';

class ilCreventoLogviewerUsrsTableGUI extends ilCreventoLogviewerBaseTableGUI
{
    function __construct($object_gui, $cmd = 'showUsrs')
    {
        parent::__construct($object_gui, $cmd);
        $this->plugin = ilCreventoLogviewerPlugin::getInstance();
        
        $this->setRowTemplate("tpl.usrs_table_row.html", "Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CreventoLogviewer");
        $this->setTitle($this->pl->txt('title_usrs'));
        
        $this->setFormAction($this->ctrl->getFormAction($object_gui, 'applyUsrsFilter'));
        $this->setFilterCommand('applyUsrsFilter');
        $this->setResetCommand('resetUsrsFilter');
        $this->setDefaultOrderField("login");
        $this->setDefaultOrderDirection("asc");
    }

    public function initFilter()
    {
        global $lng, $ilCtrl;
        
        include_once("./Services/Form/classes/class.ilTextInputGUI.php");
        $ul = new ilTextInputGUI($lng->txt("login"), "login");
        $ul->setDataSource($ilCtrl->getLinkTarget($this->getParentObject(),
                        "addUserAutoComplete", "", true));
        $ul->setSize(20);
        $ul->setSubmitFormOnEnter(true);
        $this->addFilterItem($ul);
        $ul->readFromSession();
        $this->filter["login"] = $ul->getValue();
        
        $this->setAndGetInfocodeFilter();
    }
    
    protected function setHeaderRow()
    {
        global $lng;
        $this->addColumn($this->pl->txt('col_evento_id'), 'evento_id');
        $this->addColumn($lng->txt('login'), 'usrname');
        $this->addColumn($lng->txt('gender'), 'gender');
        $this->addColumn($lng->txt('firstname'), 'firstname');
        $this->addColumn($lng->txt('lastname'), 'lastname');
        $this->addColumn($lng->txt('mail'), 'mail');
        $this->addColumn($this->pl->txt('col_last_import_date'), 'last_import_date');
        $this->addColumn($this->pl->txt('col_import_infocode'), 'update_info_code');
        $this->addColumn($this->pl->txt('col_last_import_data'));
    }
    
    protected function getTableItems()
    {
        include_once 'Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CreventoLogviewer/classes/Usrs/class.ilCreventoUsrsQuery.php';
        $query = new ilCreventoUsrsQuery();
        $query->setStatuscodeFilter($this->filter['statuscodes']);
        $query->setTextFilters('usrname', $this->filter['login']);
        $query->setLimit($this->limit);
        $query->setOffset($this->offset);
        return $query->query();
        //$this->setData(ilCreventoQuery::_getUsrs(10, 0));
    }
    
    /**
     *
     * @param unknown $row
     */
    function fillRow($row)
    {
        if($row['usrname'] == '' && !$this->show_empty_usrs)
            return;
        
        $this->tpl->setCurrentBlock('evento_id_td');
        $this->tpl->setVariable('EVENTO_ID', $row['evento_id']);
        $this->tpl->parseCurrentBlock();
        
        $this->tpl->setCurrentBlock('login_td');
        $this->tpl->setVariable('LOGIN', $row['usrname']);
        $this->tpl->parseCurrentBlock();
        
        $import_data = unserialize($row['last_import_data']);

        $this->tpl->setCurrentBlock('gender_td');
        $this->tpl->setVariable('GENDER', $import_data['Gender'] == NULL ? ' ' : $import_data['Gender']);
        $this->tpl->parseCurrentBlock();
        
        $this->tpl->setCurrentBlock('first_name_td');
        $this->tpl->setVariable('FIRST_NAME', $import_data['FirstName'] == NULL ? ' ' : $import_data['FirstName']);
        $this->tpl->parseCurrentBlock();
        
        $this->tpl->setCurrentBlock('last_name_td');
        $this->tpl->setVariable('LAST_NAME', $import_data['LastName'] == NULL ? ' ' : $import_data['LastName']);
        $this->tpl->parseCurrentBlock();
        
        $this->tpl->setCurrentBlock('mail_td');
        $this->tpl->setVariable('MAIL', $import_data['Email'] == NULL ? ' ' : $import_data['Email']);
        $this->tpl->parseCurrentBlock();
        
        $this->tpl->setCurrentBlock('last_import_td');
        $this->tpl->setVariable('LAST_IMPORT', $row['last_import_date']);
        $this->tpl->parseCurrentBlock();
        
        $this->tpl->setCurrentBlock('update_infocode_td');
        $this->tpl->setVariable('UPDATE_INFOCODE', $this->pl->txt('statuscode_' . $row['update_info_code']));
        $this->tpl->parseCurrentBlock();
        
        $this->tpl->setCurrentBlock('last_import_data_td');
        $button = $this->getModalButton($row['evento_id']);
        $this->tpl->setVariable('DATA_BUTTON', $button->render());
        $this->tpl->parseCurrentBlock();
    }	
    
    protected function getStatuscodeArray()
    {
        return array(301, 302, 303, 304, 313, 324);
    }
}