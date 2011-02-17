<?php

class Application_Model_PostMapper
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
            $this->setDbTable('Application_Model_DbTable_Post');
        }
        return $this->_dbTable;
    }
 
    public function save(Application_Model_Post $post)
    {
        $data = array(
            'post'   => $post->getPost(),
            'userid' => $post->getUserid(),
            'created' => date('Y-m-d H:i:s'),
        );
 
        if (null === ($id = $post->getId())) {
            unset($data['id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }
 
    public function find($id, Application_Model_Post $post)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        $post->setId($row->id)
                  ->setPost($row->post)
                  ->setUserid($row->userid)
                  ->setCreated($row->created);
    }
		
		public function searchByUser($userid)
		{
				$select = $this->getDbTable()->select()->where('userid = ?', $userid)->order('created DESC');
				$resultSet = $this->getDbTable()->fetchAll($select);
				return $this->_get_entries($resultSet);
		}
 
    public function fetchAll()
    {
				$select = $this->getDbTable()->select()->order('created DESC');
        $resultSet = $this->getDbTable()->fetchAll($select);
        return $this->_get_entries($resultSet);
    }

		private function _get_entries($resultSet)
		{
				$entries = array();
				foreach ($resultSet as $row) {
            $entry = new Application_Model_Post();
            $entry->setId($row->id)
                  ->setPost($row->post)
                  ->setUserid($row->userid)
                  ->setCreated($row->created);
						$selecteduser = new Application_Model_UserMapper();
					  $entry->setUsername($selecteduser->usernameById($row->id));
            $entries[] = $entry;
        }
				return $entries;
		}

}

