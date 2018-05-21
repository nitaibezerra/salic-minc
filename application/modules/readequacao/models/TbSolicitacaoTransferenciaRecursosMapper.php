<?php

class Readequacao_Model_TbSolicitacaoTransferenciaRecursosMapper extends MinC_Db_Mapper
{
    public function __construct()
    {
        parent::setDbTable('Readequacao_Model_DbTable_TbSolicitacaoTransferenciaRecursos');
    }

    public function save($model)
    {
        return parent::save($model);
    }
}
