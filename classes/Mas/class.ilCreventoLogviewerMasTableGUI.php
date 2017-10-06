<?php
include_once 'Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CreventoLogviewer/classes/class.ilCreventoLogviewerBaseTableGUI.php';
require_once("./Services/Link/classes/class.ilLink.php");
class ilCreventoLogviewerMasTableGUI extends ilCreventoLogviewerBaseTableGUI
{
    function __construct($object_gui, $cmd = 'showMas')
    {
        /** @var $ilCtrl ilCtrl */
        global $ilCtrl;
        parent::__construct($object_gui, $cmd);
        $this->plugin = ilCreventoLogviewerPlugin::getInstance();
        $this->modal_url = $ilCtrl->getLinkTarget($object_gui, 'getMasData', '',true);;
        
        $this->setRowTemplate("tpl.mas_table_row.html", "Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CreventoLogviewer");
        $this->setTitle($this->pl->txt('title_mas'));
        
        $this->setFormAction($this->ctrl->getFormAction($object_gui, 'applyMasFilter'));
        $this->setFilterCommand('applyMasFilter');
        $this->setResetCommand('resetMasFilter');
        $this->setDefaultOrderField("evento_id");
        $this->setDefaultOrderDirection("asc");
    }
    
    public function initFilter()
    {
        global $lng, $ilCtrl;
        
        include_once("./Services/Form/classes/class.ilTextInputGUI.php");
        $ul = new ilTextInputGUI($this->pl->txt('col_evento_id'), "evento_id");
        $ul->setDataSource($ilCtrl->getLinkTarget($this->getParentObject(),
                        "addUserAutoComplete", "", true));
        $ul->setSize(20);
        $ul->setSubmitFormOnEnter(true);
        $this->addFilterItem($ul);
        $ul->readFromSession();
        $this->filter["evento_id"] = $ul->getValue();
        
        $this->setAndGetInfocodeFilter();
        
        /*foreach($unit_options as $unit_id => $txt)
        {
            if($this->hasResultUnit($result, $unit_id, $result_units))
            {
                $selectedvalues[] = $unit_id;
            }
        }
        $multi_select->setValue($selectedvalues);*/
    }
    
    protected function setHeaderRow()
    {
        $this->addColumn($this->pl->txt('col_evento_id'), 'evento_id');
        $this->addColumn($this->pl->txt('col_ref_id'), 'ref_id');
        $this->addColumn($this->pl->txt('col_role_id'), 'role_id');
        $this->addColumn($this->pl->txt('col_end_date'), 'end_date');
        $this->addColumn($this->pl->txt('col_number_of_subs'), 'number_of_subs');
        $this->addColumn($this->pl->txt('col_last_import_date'), 'last_import_date');
        $this->addColumn($this->pl->txt('col_import_infocode'), 'update_info_code');
        $this->addColumn($this->pl->txt('col_last_import_data'));//$this->pl->txt('col_last_import_data'));
    }
    
    protected function getTableItems()
    {
        include_once 'Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CreventoLogviewer/classes/Mas/class.ilCreventoMasQuery.php';
        $query = new ilCreventoMasQuery();//var_dump($this->filter);die;
        $query->setTextFilters('evento_id', $this->filter['evento_id']);
        $query->setStatuscodeFilter($this->filter['statuscodes']);
        $query->setLimit($this->getLimit());
        $query->setOffset($this->getOffset());
        return $query->query();
    }
    
    /**
     *
     * @param unknown $row
     */
    function fillRow($row)
    {   
        $obj_id = ilObject::_lookupObjectId($row['ref_id']);
        $crs_link = ilLink::_getLink($row['ref_id'], ilObject::_lookupType($obj_id));
        
        $this->tpl->setCurrentBlock('evento_id_td');
        $this->tpl->setVariable('COURSE_LINK', $crs_link);
        $this->tpl->setVariable('EVENTO_ID', $row['evento_id']);
        $this->tpl->parseCurrentBlock();
        
        $this->tpl->setCurrentBlock('ref_td');
        $this->tpl->setVariable('COURSE_LINK', $crs_link);
        $this->tpl->setVariable('REF_ID', $row['ref_id']);
        $this->tpl->parseCurrentBlock();
        
        $this->tpl->setCurrentBlock('role_td');
        $this->tpl->setVariable('ROLE', $row['role_id']);
        $this->tpl->parseCurrentBlock();
        
        $this->tpl->setCurrentBlock('end_date_td');
        $this->tpl->setVariable('END_DATE', $row['end_date']);
        $this->tpl->parseCurrentBlock();
        
        $this->tpl->setCurrentBlock('number_of_subs_td');
        $this->tpl->setVariable('NUMBER_OF_SUBS', $row['number_of_subs']);
        $this->tpl->parseCurrentBlock();
        
        $this->tpl->setCurrentBlock('last_import_td');
        $this->tpl->setVariable('LAST_IMPORT', $row['last_import_date']);
        $this->tpl->parseCurrentBlock();
              
        $this->tpl->setCurrentBlock('update_infocode_td');
        $this->tpl->setVariable('UPDATE_INFOCODE', $this->pl->txt('statuscode_' . $row['update_info_code']));
        $this->tpl->setVariable('INFOCODE_COLOR', $this->getInfocodeColor($row['update_info_code']));
        $this->tpl->parseCurrentBlock();
        
        $this->tpl->setCurrentBlock('last_import_data_td');
        $button = $this->getModalButton($row['evento_id']);
        $this->tpl->setVariable('DATA_BUTTON', $button->render());
        $this->tpl->parseCurrentBlock();

    }
    
    protected function getStatuscodeArray()
    {
        return array(205, 206, 231, 232, 211, 212, 213);
    }
}