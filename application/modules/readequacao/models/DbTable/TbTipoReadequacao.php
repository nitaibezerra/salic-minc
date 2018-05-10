<?php
/**
 * DAO tbTipoReadequacao
 * @since 28/02/2014
 */

class Readequacao_Model_DbTable_TbTipoReadequacao extends MinC_Db_Table_Abstract
{
    protected $_banco  = "SAC";
    protected $_schema = "SAC";
    protected $_name   = "tbTipoReadequacao";

    public function buscarTiposReadequacoesPermitidos($idPronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => $this->_name),
            array(
                new Zend_Db_Expr("idTipoReadequacao, dsReadequacao"),
            )
        );

        $select->where('idTipoReadequacao not in (?)', [1, 2, 22, 23]);
        $select->where(new Zend_Db_Expr("idTipoReadequacao not in (
            select idTipoReadequacao from SAC.dbo.tbReadequacao where idPronac = $idPronac AND stEstado = 0
        )"));

        $select->order('2');

        return $this->fetchAll($select);
    }
}
