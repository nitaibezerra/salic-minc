<?php
class Foo_Model_Foo extends Zend_Db_Table_Abstract
{
    protected $_name = "Foo";
    protected $_primary = "idFoo";

    public function __construct($config = array())
    {
        parent::__construct($config);
    }

    public function listar()
    {
        return array('teste01', 'teste02');
    }
}
