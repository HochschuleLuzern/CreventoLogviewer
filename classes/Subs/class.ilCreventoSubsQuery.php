<?php
include_once 'Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CreventoLogviewer/classes/class.ilCreventoBaseQuery.php';
class ilCreventoSubsQuery extends ilCreventoBaseQuery
{
    protected $db_table;
    protected $columns = array();
    
    public function __construct()
    {
        parent::__construct();
        
        $this->db_table = 'crnhk_crevento_subs';
        $this->columns = array('usr_id', 'role_id', 'last_import_date', 'update_info_code');
    }
    
    public function getDBTable()
    {
        return $this->db_table;
    }
    
    protected function filterAndGetResult($res)
    {
        global $rbacreview;
        
        $use_after_sql_filter = isset($this->after_sql_filter['crs_grp_name']);
        while($row = $this->db->fetchAssoc($res))
        {
            $row['crs_grp_obj_id'] = $rbacreview->getObjectOfRole($row['role_id']);
            $row['crs_grp_title'] = ilObject::_lookupTitle($row['crs_grp_obj_id']);
            
            if($use_after_sql_filter && stristr($this->after_sql_filter['crs_grp_name'], $title))
            {
                $data[] = $row;
            }
            else 
            {
                $data[] = $row;
            }
        }
        
        return $data;
    }
    
    public static function fetchData($evento_id)
    {
        global $ilDB;
        
        $usr_id = $evento_id[0];
        $role_id = $evento_id[1];

        $query = 'SELECT last_import_data FROM crnhk_crevento_subs WHERE usr_id = ' .  
                        $ilDB->quote($usr_id, 'integer') . ' AND role_id = ' . $ilDB->quote($role_id, 'integer');
        
        $res = $ilDB->query($query);
        while($row = $ilDB->fetchAssoc($res))
        {
            $data = $row['last_import_data'];
        }
        return $data;
    }
}