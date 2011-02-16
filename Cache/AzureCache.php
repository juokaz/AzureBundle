<?php

namespace Bundle\AzureBundle\Cache;

class AzureCache {

    protected $tableStorage = null;
    protected $tableName = '';
    protected $partition = '';

    private $opened = false;

    public function __construct(\Microsoft_WindowsAzure_Storage_Table $tableStorage, $table = 'cache', $partition = 'cache')
	{
        $this->tableStorage = $tableStorage;
        $this->tableName = $table;
        $this->partition = $partition;
    }

    public function get($id)
    {
        $this->open();

        try
        {
            $record = $this->tableStorage->retrieveEntityById(
                $this->tableName,
                $this->partition,
                $id
            );

            // maybe record has expired?
            if ($record->expires > 0 && time() > $record->expires + $record->updated) {
                $this->tableStorage->deleteEntity($this->tableName, $record);
                return false;
            }

            return base64_decode($record->serializedData);
        }
        catch (\Microsoft_WindowsAzure_Exception $ex)
        {
            return false;
        }
    }

    public function store($id, $data, $expires = 0)
    {
        $this->open();

        $record = new \Microsoft_WindowsAzure_Storage_DynamicTableEntity($this->tableName, $id);
        $record->serializedData = base64_encode($data);

        $record->expires = $expires;
        $record->setAzurePropertyType('expires', 'Edm.Int32');

        $record->updated = time();
        $record->setAzurePropertyType('updated', 'Edm.Int32');

        try
        {
            $this->tableStorage->updateEntity($this->tableName, $record);
        }
        catch (\Microsoft_WindowsAzure_Exception $unknownRecord)
        {
            $record->inserted = time();
            $record->setAzurePropertyType('inserted', 'Edm.Int32');

            $this->tableStorage->insertEntity($this->tableName, $record);
        }
    }

    public function delete($id)
    {
        $this->open();

        try
        {
            $record = $this->tableStorage->retrieveEntityById(
                $this->tableName,
                $this->partition,
                $id
            );
            $this->tableStorage->deleteEntity($this->tableName, $record);

            return true;
        }
        catch (\Microsoft_WindowsAzure_Exception $ex)
        {
            return false;
        }
    }

    public function search($pattern)
    {
        // @todo implement properly
        return array();
    }

    private function open()
    {
        if ($this->opened)
            return true;

    	// Make sure that table exists
    	$tableExists = $this->tableStorage->tableExists($this->tableName);

    	if (!$tableExists) {
		    $this->tableStorage->createTable($this->tableName);
		}

		return $this->opened = true;
    }
}