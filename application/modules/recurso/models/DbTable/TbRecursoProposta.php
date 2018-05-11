<?php

class Recurso_Model_DbTable_TbRecursoProposta extends MinC_Db_Table_Abstract
{
    protected $_schema = 'sac';
    protected $_name = 'tbRecursoProposta';
    protected $_primary = 'idRecursoProposta';

    /**
     * @var Recurso_Model_TbRecursoProposta
     */
    public $tbRecursoProposta;

    public function __construct(array $config = array())
    {
        $this->tbRecursoProposta = new Recurso_Model_TbRecursoProposta();
        parent::__construct($config);
    }

    public function inativarRecursos($id_preprojeto)
    {
        if (!is_null($id_preprojeto) && !empty($id_preprojeto)) {
            $this->alterar(
                array('stAtivo' => Recurso_Model_TbRecursoProposta::SITUACAO_RECURSO_INATIVO),
                ['idPreProjeto = ?' => $id_preprojeto]
            );
        }
    }

    public function cadastrarRecurso(
        $idPreProjeto,
        $tpRecurso = Recurso_Model_TbRecursoProposta::TIPO_RECURSO_PEDIDO_DE_RECONSIDERACAO
    )
    {
        if (!$idPreProjeto) {
            throw new Exception("Identificador do projeto n&atilde;o informado.");
        }

        $preprojetoDbTable = new Proposta_Model_DbTable_PreProjeto();
        $arrPreprojeto = $preprojetoDbTable->findBy(['idPreProjeto' => $idPreProjeto]);
        $dados = [
            'idPreProjeto' => $idPreProjeto,
            'idProponente' => $arrPreprojeto['idAgente'],
            'dtRecursoProponente' => $this->getExpressionDate(),
            'stAtendimento' => Recurso_Model_TbRecursoProposta::SITUACAO_ATENDIMENTO_SEM_AVALIACAO,
            'stAtivo' => Recurso_Model_TbRecursoProposta::SITUACAO_RECURSO_ATIVO,
            'tpRecurso' => $tpRecurso,
            'stRascunho' => Recurso_Model_TbRecursoProposta::SITUACAO_RASCUNHO_SALVO,
        ];

        $this->inativarRecursos($idPreProjeto);
        $this->inserir($dados);
        $this->enviarEmailAberturaDePrazoRecursal($idPreProjeto);
    }

    private function enviarEmailAberturaDePrazoRecursal($id_preprojeto)
    {
        $mensagemEmail = <<<MENSAGEM_EMAIL
Foi aberto o prazo para entrada com Recurso ou Desist&ecirc;ncia do Prazo Recursal.
Ao acessar a Proposta {$id_preprojeto} a op&ccedil;&atilde;o "Enquadramento" no menu lateral estar&aacute; dispon&iacute;vel.
MENSAGEM_EMAIL;

        $preprojetoDbTable = new Proposta_Model_DbTable_PreProjeto();
        $preprojetoDbTable->enviarEmailProponente(
            $id_preprojeto,
            'Recurso',
            $mensagemEmail
        );
    }

    public function obterRecursoAtualVisaoProponente($id_preprojeto)
    {
        $preprojetoDbTable = new Proposta_Model_DbTable_PreProjeto();
        $arrPreprojeto = $preprojetoDbTable->findBy(['idPreProjeto' => $id_preprojeto]);
        return $this->findBy([
            'idPreProjeto' => $id_preprojeto,
            'idProponente' => $arrPreprojeto['idAgente'],
            'stAtivo' => new Zend_Db_Expr((string)Recurso_Model_TbRecursoProposta::SITUACAO_RECURSO_ATIVO),
//            'stAtendimento in (?)' => [
//                Recurso_Model_TbRecursoProposta::SITUACAO_ATENDIMENTO_SEM_AVALIACAO,
//                Recurso_Model_TbRecursoProposta::SITUACAO_ATENDIMENTO_INDEFERIDO
//            ]
        ]);
    }

    public function obterRecursoAtualVisaoAvaliador($id_preprojeto)
    {
        $preprojetoDbTable = new Proposta_Model_DbTable_PreProjeto();
        $arrPreprojeto = $preprojetoDbTable->findBy(['idPreProjeto' => $id_preprojeto]);

        return $this->findBy([
            'idPreProjeto' => $id_preprojeto,
            'idProponente' => $arrPreprojeto['idAgente'],
            'stAtivo' => new Zend_Db_Expr((string)Recurso_Model_TbRecursoProposta::SITUACAO_RECURSO_ATIVO),
            'stAtendimento <> ?' => Recurso_Model_TbRecursoProposta::SITUACAO_ATENDIMENTO_SEM_AVALIACAO
        ]);
    }

    public function obterRecursoAtual($id_preprojeto)
    {
        $preprojetoDbTable = new Proposta_Model_DbTable_PreProjeto();
        $arrPreprojeto = $preprojetoDbTable->findBy(['idPreProjeto' => $id_preprojeto]);

        return $this->findBy([
            'idPreProjeto' => $id_preprojeto,
            'idProponente' => $arrPreprojeto['idAgente'],
            'stAtivo' => new Zend_Db_Expr((string)Recurso_Model_TbRecursoProposta::SITUACAO_RECURSO_ATIVO)
        ]);
    }

}
