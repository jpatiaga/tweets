<?php

class Application_Model_HiddentweetsMapper
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
            $this->setDbTable('Application_Model_DbTable_Guestbook');
        }
        return $this->_dbTable;
    }

	public function save(Application_Model_Guestbook $hiddentweets)
    {
        $data = array(
            'id'   => $hiddentweets->getId(),
        );
		// only insert is needed because we are only storing the id, we do not care if the insert fails when the record already exists
        $this->getDbTable()->insert($data);
    }

	public function find($id, Application_Model_Hiddentweets $hiddentweets)
	{
		$result = $this->getDbTable()->find($id);
		if (0 == count($result)) {
			die('Debug: no results found for [' . $id . ']');
            return;
        }
		$row = $result->current();
		$hiddentweets->setId($row->id);
		return $hiddentweets;
	}

}

