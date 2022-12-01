<?php
class YouTubeItem
{
    private $_data = array('id'=>null,'
            name'=>null,'description'=>null,
            'published'=>null,'the_date'=>null);

    public function __get($key)
    {
        switch( $key ) {
        case 'id':
        case 'name':
        case 'description':
        case 'published':
        case 'the_date':
            return $this->_data[$key];
        }
    
    }  
    public function __set($key,$val)
    {
        switch( $key ) {
        case 'name':
        case 'description':
            $this->_data[$key] = trim($val);
            break;
        case 'published':
            $this->_data[$key] = (bool) $val;
            break;
        case 'the_date':
            $this->_data[$key] = (int) $val;
        break; }
    }

    public function save()
    {
        if( !$this->is_valid() ) return FALSE;
        if( $this->id > 0 ) {
            $this->update();
        } else {
            $this->insert();
        }
    }

    public function is_valid()
    {
        if( !$this->name ) return false;
        if( !$this->the_date ) return false;
        return TRUE;
    }

    protected function insert()
    {
        $db = \cms_utils::get_db();
        $sql = 'INSERT INTO '.CMS_DB_PREFIX.'mod_holidays
                (name,description,published,the_date)
                VALUES (?,?,?,?)';
        $dbr = $db->Execute($sql,array($this->name,$this->description,$this->published,
                                        $this->the_date));
        if( !$dbr ) return FALSE;
        $this->_data['id'] = $db->Insert_ID();
        return TRUE;
    }

    protected function update()
    {
        $db = \cms_utils::get_db();
        $sql = 'UPDATE '.CMS_DB_PREFIX.'mod_holidays SET name = ?, description = ?,
                    published = ?, the_date = ? WHERE id = ?';
        $dbr = $db->Execute($sql,array($this->name,$this->description,$this->published,$this->the_date,$this->id));

        if( !$dbr ) return FALSE;
        return TRUE;
    }

    public function delete()
    {
        if( !$this->id ) return FALSE;
        $db = \cms_utils::get_db();
        $sql = 'DELETE FROM '.CMS_DB_PREFIX.'mod_holidays WHERE id = ?';
        $dbr = $db->Execute($sql,array($this->id));
        if( !$dbr ) return FALSE;
        $this->_data['id'] = null;
        return TRUE;
    }

    /** internal */
    public function fill_from_array($row)
    {
        foreach( $row as $key => $val ) {
            if( array_key_exists($key,$this->_data) ) {
                $this->_data[$key] = $val;
            }
        } 
    }

    public static function &load_by_id($id)
    {
        $id = (int) $id;
        $db = \cms_utils::get_db();
        $sql = 'SELECT * FROM '.CMS_DB_PREFIX.'mod_holidays WHERE id = ?';
        $row = $db->GetRow($sql,array($id));
        if( is_array($row) ) {
            $obj = new self();
            $obj->fill_from_array($row);
            return $obj;
        }
    }
} 
?>
