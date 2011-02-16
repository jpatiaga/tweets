<?php

class Application_Model_Users extends Zend_Db_Table
{
    protected $_name="users";
    function checkUnique($username)
    {
        $select = $this->_db->select()
                            ->from($this->_name,array('username'))
                            ->where('username=?',$username);
        $result = $this->getAdapter()->fetchOne($select);
        if($result){
            return true;
        }
        return false;
    }

}

