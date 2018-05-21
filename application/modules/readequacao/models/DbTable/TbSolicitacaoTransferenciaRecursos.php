<?php

class Readequacao_Model_DbTable_TbSolicitacaoTransferenciaRecursos extends MinC_Db_Table_Abstract
{
    protected $_schema  = "sac";
    protected $_name    = "tbSolicitacaoTransferenciaRecursos";
    
    /**
     * Método para buscar projetos recebedores de uma readequação
     * @access public
     * @param integer $idReadequacao
     * @return integer
     */
    public function obterProjetosRecebedores($idReadequacao)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => $this->_name),
            array(
                new Zend_Db_Expr("a.idReadequacao, a.tpTransferencia, a.idPronacRecebedor, a.vlRecebido, a.siAnaliseTecnica, a.siAnaliseComissao, a.stEstado")
            )
        );
        
        $select->joinInner(
            array('b' => 'projetos'),
            'a.idPronacRecebedor = b.IdPRONAC',
            array(
                new Zend_Db_Expr("
                    b.NomeProjeto,
                    b.AnoProjeto + b.Sequencial AS pronac"
                )
            ),
            'SAC.dbo'
        );
        
        $select->where('idReadequacao = ?', $idReadequacao);
        
        return $this->fetchAll($select);
    }
}