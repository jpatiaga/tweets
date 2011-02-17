<?php

class Application_Model_UserMapper
{

    protected $_dbTable;

    public function setDbTable($dbTable)
    {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }
 
    public function getDbTable()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable('Application_Model_Users');
        }
        return $this->_dbTable;
    }
		
		public function getUser($userid) {
				$result = $this->getDbTable()->find($userid);
        if (0 == count($result)) {
            return;
        }
        return $result->current();
		}

		public function usernameById($userid)
		{
        $result = $this->getDbTable()->find($userid);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
				return $row->username;
		}
		

}

