<?php
include_once ('./Services/Table/classes/class.ilTable2GUI.php');
include_once 'Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CreventoLogviewer/classes/class.ilCreventoBaseQuery.php';

abstract class ilCreventoLogviewerBaseTableGUI extends ilTable2GUI
{
    /** @var $pl ilCreventoLogviewerPlugin */
    protected $pl;
    
    /** @var $ctrl ilCtrl */
    protected $ctrl;
    
    protected $modal_id = 'import_data_modal';
    protected $modal_url;
    
    function __construct($a_parent_obj, $cmd)
    {        
        global $ilCtrl;
    
        parent::__construct($a_parent_obj, $cmd);
        
        $this->pl = ilCreventoLogviewerPlugin::getInstance();
        $this->ctrl = &$ilCtrl;
        
        $this->setShowRowsSelector(true);
        $this->setExternalSorting(true);
        $this->setExternalSegmentation(true);
        $this->setEnableHeader(true);
        
        $this->setEnableTitle(true);
        $this->initFilter();
        
        $this->setTopCommands(true);
        $this->setEnableAllCommand(true);
        $this->setNoEntriesText($this->pl->txt('msg_no_entries'));
        $this->setLimit(1000);
        $this->determineOffsetAndOrder();
        $this->setHeaderRow();
        
        $this->createAndPrepareModal();
        
        $table_data = $this->getTableItems();
        $this->setMaxCount($table_data['count']);
        $this->setData($table_data['set']);
    }
    
    abstract protected function setHeaderRow();

    abstract protected function getTableItems();
    
    protected function createAndPrepareModal()
    {
        include_once 'Services/UIComponent/Modal/classes/class.ilModalGUI.php';
        include_once 'Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CreventoLogviewer/classes/class.ilCreventoBtnOpenModal.php';
        $this->modal = ilModalGUI::getInstance();
        $this->modal->setHeading('Heading');
        $this->modal->setBody('Body');
        
        $this->modal->setId($this->modal_id);
    }
    
    protected function setAndGetInfocodeFilter()
    {
        include_once'Services/Form/classes/class.ilMultiSelectInputGUI.php';
        $multi_select = new ilMultiSelectInputGUI($this->pl->txt('col_import_infocode'), 'hello');
        foreach($this->getStatuscodeArray() as $statuscode){$options[$statuscode] = $this->pl->txt("statuscode_$statuscode");}
        $multi_select->setOptions($options);
        //$multi_select->setInfo();
        $multi_select->setWidth(300);
        $multi_select->setHeight(150);
        $this->addFilterItem($multi_select);
        $multi_select->readFromSession();
        $selected = $multi_select->getValue();
        if(count($selected) == 0)
        {
            $selected = $this->getStatuscodeArray();
        }
        else
        {
            $this->filter['statuscodes'] = $selected;
        }
        $multi_select->setValue($selected);
    }
    
    protected function getInfocodeColor($code)
    {
        switch($code)
        {
            case 101:
            case 102:
            case 103:
            case 104:
            case 205:
            case 206:
            case 231:
            case 232:
            case 301:
            case 302:
            case 303:
            case 304:
                return 'green';
            case 211:
            case 212:
            case 213:
            case 313:
                return 'orange';
            case 121:
            case 123:
            case 324:
                return 'red';
            default:
                return 'black';
        }
        
    }
    
    abstract protected function getStatuscodeArray();
    
    protected function getModalButton($db_id)
    {
        $button = ilCreventoBtnOpenModal::getInstance();
        $button->setCaption($this->pl->txt('btn_show_data'), false);
        $button->setDataTarget('#' . $this->modal_id);
        $button->setOnClick("setModalContent('$db_id')");
        return $button;
    }
    
    public function getHTML()
    {
        return parent::getHTML() . $this->modal->getHTML();
    }
}