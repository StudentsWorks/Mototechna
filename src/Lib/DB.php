<?php
namespace App\Lib;

use Nette;

class DB
{

    private $__context;
    private $__database;
    /**
     * DB constructor.
     */
    public function __construct($dsn, $username, $password)
    {
        $storage = new Nette\Caching\Storages\FileStorage(__DIR__ . '/../../temp');
        try {
            $this->__database = new Nette\Database\Connection($dsn, $username, $password);
        } catch (Nette\Database\ConnectionException $e) {
            die('Database connection failed.');
        }
        $structure = new Nette\Database\Structure($this->__database, $storage);
        $conventions = new Nette\Database\Conventions\DiscoveredConventions($structure);
        $this->__context = new Nette\Database\Context($this->__database, $structure, $conventions, $storage);
    }

    public function getContext()
    {
        return $this->__context;
    }

    public function begin()
    {
        $this->__database->beginTransaction();
    }

    public function commit()
    {
        $this->__database->commit();
    }

    public function rollback()
    {
        $this->__database->rollBack();
    }
}