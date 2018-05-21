<?php

class Readequacao_Model_TbProjetoRecebedorRecursoMapper extends MinC_Db_Mapper
{
    public function __construct()
    {
        parent::setDbTable('Readequacao_Model_DbTable_TbProjetoRecebedorRecurso');
    }

    public function save($model)
    {
        return parent::save($model);
    }
}
