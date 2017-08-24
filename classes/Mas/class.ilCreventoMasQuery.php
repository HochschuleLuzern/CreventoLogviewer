<?php
include_once 'Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CreventoLogviewer/classes/class.ilCreventoBaseQuery.php';
class ilCreventoMasQuery extends ilCreventoBaseQuery
{
    protected $db_table;
    protected $columns = array();
    
    public function __construct()
    {
        parent::__construct();
        
        $this->db_table = 'crnhk_crevento_mas';
        $this->columns = array('evento_id', 'ref_id', 'role_id', 'end_date', 'number_of_subs', 'last_import_data', 'last_import_date', 'update_info_code');
    }
    
    public function getDBTable()
    {
        return $this->db_table;
    }
    
    public function setEnddateFilter($date, $operator)
    {
        if($date != null)
        {
            switch($operator)
            {
             case '>':
                $this->datetime_filters['end_date'] = 'end_date > ' . $this->db->quote($date, 'datetime') .' ';
                break;
             case '>=':
                 $this->datetime_filters['end_date'] = 'end_date >= ' . $this->db->quote($date, 'datetime') .' ';
                 break;
             case '<':
                 $this->datetime_filters['end_date'] = 'end_date < ' . $this->db->quote($date, 'datetime') .' ';
                 break;
             case '<=':
                 $this->datetime_filters['end_date'] = 'end_date <= ' . $this->db->quote($date, 'datetime') .' ';
                 break;
             case '=':
                 $this->datetime_filters['end_date'] = 'end_date = ' . $this->db->quote($date, 'datetime') .' ';
                 break;
            }
        }
    }
    
    protected function filterAndGetResult($res)
    {
        while($row = $this->db->fetchAssoc($res))
        {
            $data[] = $row;
        }
        
        return $data;
    }
}