<?php
include_once 'Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CreventoLogviewer/classes/class.ilCreventoBaseQuery.php';
class ilCreventoUsrsQuery extends ilCreventoBaseQuery
{
    protected $db_table;
    protected $columns = array();
    
    public function __construct()
    {
        parent::__construct();
        
        $this->db_table = 'crnhk_crevento_usrs';
        $this->columns = array('evento_id', 'usrname', 'last_import_data', 'last_import_date', 'update_info_code');
    }
    
    public function getDBTable()
    {
        return $this->db_table;
    }
    
    public static function fetchData($evento_id)
    {
        global $ilDB;

        $query = 'SELECT last_import_data FROM crnhk_crevento_usrs WHERE evento_id = ' . $ilDB->quote($evento_id, 'integer');
                        
        $res = $ilDB->query($query);
        while($row = $ilDB->fetchAssoc($res))
        {
            $data = $row['last_import_data'];
        }
        return $data;
    }
}