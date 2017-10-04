<?php

abstract class ilCreventoBaseQuery
{
    // DB
    protected $db;  /** @var $db ilDB  */
    protected $db_table;
    protected $columns = array();
    
    // Join
    protected $join = array();
    
    // Where
    protected $text_filters = array();
    protected $number_filters = array();
    protected $datetime_filters = array();
    
    // Order
    protected $order_column = '';
    protected $order_dir = 'asc';
    
    // Limit / Offset
    protected $limit = 0;
    protected $offset = 0;
    
    // After SQL Filter
    protected $after_sql_filter = array();
    
    public function __construct()
    {
        global $ilDB;
        
        $this->db = $ilDB;
    }
    
    public abstract function getDBTable();
    
    public function getCountQuery()
    {
        return 'SELECT COUNT('.$this->columns[0].') as cnt FROM ' . $this->getDBTable();
    }
    
    public function setTextFilters($column, $filter)
    {
        if(in_array($column, $this->columns))
        {
            if($filter != '')
            {
                $this->text_filters[$column] = " $column LIKE " . $this->db->quote("%$filter%", 'text');
            }
        }
        else 
        {
            throw new ilException("Column ' . $column . ' does not exist in table " . $this->getDBTable());
        }
    }
    
    public function setStatuscodeFilter($statuscodes)
    {
        if(is_array($statuscodes))
        {
            foreach($statuscodes as $code)
            {
                $query .= ' update_info_code = ' . $this->db->quote($code, 'integer') . ' ||'; 
            }
            
            $this->number_filters['update_info_code'] = '(' . trim($query, '|') . ')';
        }
    }
    
    public function setOrderColumn($order_column)
    {
        if(in_array($order_column, $this->columns))
        {
            $this->order_column = $order_column;
        }
        else 
        {
            throw new ilException("Column '.  $order_column . ' does not exist in table " . $this->getDBTable());
        }
    }
    
    public function setOrderDir($order_dir)
    {
        $this->order_dir = $order_dir;
    }
    
    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    public function setLimit($limit)
    {
        $this->limit = $limit;
    }
    
    /**
     * Set order field (column in usr_data)
     * Default order is 'login'
     * @param string
     */
    public function setOrderField($a_order)
    {
        $this->order_field = $a_order;
    }
    
    /**
     * Set order direction
     * 'asc' or 'desc'
     * Default is 'asc'
     * @param string $a_dir
     */
    public function setOrderDirection($a_dir)
    {
        $this->order_dir = $a_dir;
    }
    
    public function setAfterSQLFilter($col, $filter_params)
    {
        if($filter_params != null && $filter_params != '')
        {
            $this->after_sql_filter[$col] = $filter_params;
        }
    }
    
    public function query()
    {
        // Set select and count query
        $select_query = 'SELECT * FROM ' . $this->getDBTable();
        $count_query = $this->getCountQuery();
        
        $join = $this->createJoinQuery();
        $select_query .= $join;
        $count_query .= $join;
        
        $where .= $this->createWhereQuery();
        $select_query .= $where;
        $count_query .= $where;
        
        $select_query .= $this->createOrderQuery();
        //var_dump($select_query);die;
        $set = $this->db->query($count_query);
        $count = ($rec = $this->db->fetchAssoc($set)) ? $rec['cnt'] : 0;

        $offset = (int) $this->offset;
        $limit = (int) $this->limit;
        if($offset >= $count)
        {
            $offset = 0;
        }
        $this->db->setLimit($limit, $offset);
        //$query .= $this->createLimitOffsetQuery();

        $res = $this->db->query($select_query);
        $data = $this->filterAndGetResult($res);

        return array('count' => $count, 'set' => $data);
    }

    protected function createJoinQuery()
    {
        return '';
    }
    
    protected function createWhereQuery()
    {
        if(count($this->text_filters) > 0 || count($this->number_filters))
        {
            $where_query = ' WHERE';
            foreach($this->text_filters as $filter)
            {
                $where_query .= $filter . ' &&';
            }
            foreach($this->number_filters as $filter)
            {
                $where_query .= $filter . ' &&';
            }
            return trim($where_query, '&');
        }
        return '';
    }
    
    protected function createOrderQuery()
    {
        if($this->order_column != '')
        {
            $dir = strtolower($this->dir) == 'desc' ? 'DESC' : 'ASC';
            return " ORDER BY $this->order_column $dir";
        }
    }
    
    protected function createLimitOffsetQuery()
    {
        $limit_offset_query = $this->limit > 0 ? ' LIMIT ' . $this->db->quote($this->limit, 'integer') : '';
        $limit_offset_query .= $this->offset > 0 ? ' OFFSET ' . $this->db->quote($this->offset, 'integer') : '';

        return $limit_offset_query;
    }
    
    protected function filterAndGetResult($res)
    {
        while($row = $this->db->fetchAssoc($res))
        {
            $data[] = $row;
        }
        
        return $data;
    }
    
    abstract public static function fetchData($evento_id);
}