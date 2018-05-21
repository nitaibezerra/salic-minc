<?php

class Readequacao_TransferenciaRecursosController extends Readequacao_GenericController 
{
    public function init()
    {
        parent::init();

        $idPronac = $this->_request->getParam('idPronac');
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }
        $this->idPronac = $idPronac;
        $this->view->idTipoReadequacao = Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_TRANSFERENCIA_RECURSOS;
        $this->view->idPronac = $idPronac;
    }

    public function indexAction()
    {
        $this->view->projeto = $this->projeto;
    }

    public function dadosProjetoTransferidorAction()
    {
        $this->_helper->layout->disableLayout();

        $mensagem = '';

        try {
            $projetos = new Projetos();
            
            $projeto = $projetos->buscarProjetoTransferidor($this->idPronac);
            
            if (count($projeto) > 0) {
                $projetoArr = [
                    'pronac' => $projeto->pronac,
                    'idPronac' => $projeto->idPronac,
                    'nome' => utf8_encode($projeto->nomeProjeto),
                    'valorComprovar' => $projeto->valorAComprovar,
                    'saldoDisponivel' => $projeto->valorAComprovar,
                    'area' => utf8_encode($projeto->area),
                    'idArea' => utf8_encode($projeto->codArea)
                    
                ];
            } else {
                $projetoArr = [];
                $mensagem = "Nao existe nenhum projeto com o idPronac fornecido!";
            }
            
            $this->_helper->json(
                [
                    'projeto' => $projetoArr,
                    'msg' => $mensagem
                ]
            );
        } catch (Exception $objException) {
            $this->_helper->json([
                'msg' => 'Houve um erro na consulta do projeto transferidor.',
                'error' => $objException->getMessage()
            ]);
        }
    }
    
    public function salvarReadequacaoAction()
    {
        $this->_helper->layout->disableLayout();

        $mensagem = '';
        $dados = [];
        
        $idReadequacao = $this->_request->getParam('idReadequacao');
        
        $Readequacao_Model_DbTable_TbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
        
        if (!$idReadequacao || $idReadequacao == '') {
            $auth = Zend_Auth::getInstance();        
            $tblAgente = new Agente_Model_DbTable_Agentes();
            $rsAgente = $tblAgente->buscar(array('CNPJCPF=?'=>$auth->getIdentity()->Cpf))->current();
            
            $dados['idPronac'] = $this->_request->getParam('idPronac');
            $dados['idTipoReadequacao'] = Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_TRANSFERENCIA_RECURSOS;
            $dados['dtSolicitacao'] = new Zend_Db_Expr('GETDATE()');
            $dados['idSolicitante'] = $rsAgente->idAgente;
            $dados['dsJustificativa'] = $this->_request->getParam('justificativa');
            $dados['dsSolicitacao'] = $this->_request->getParam('dsSolicitacao');
            $dados['stAtendimento'] = 'D';
            $dados['idDocumento'] = null;
            $dados['siEncaminhamento'] = Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_CADASTRADA_PROPONENTE;
            $dados['stEstado'] = Readequacao_Model_DbTable_TbReadequacao::ST_ESTADO_EM_ANDAMENTO;
        
            try {
                $idReadequacao = $Readequacao_Model_DbTable_TbReadequacao->inserir($dados);
                
                $this->_helper->json(
                    [
                        'readequacao' => [
                            'idReadequacao' => $idReadequacao,
                            'justificativa' => $this->_request->getParam('justificativa'),
                            'dsSolicitacao' => $this->_request->getParam('dsSolicitacao'),
                        ],
                        'msg' => 'Readequação inserida com sucesso.'
                    ]
                );
                
            } catch (Exception $objException) {            
                $this->_helper->json([
                    'msg' => 'Houve um erro na criação do registro de tbReadequacao',
                    'error' => $objException->getMessage()
                ]);
                $this->_helper->viewRenderer->setNoRender(true);
            }
            
        } else if ($idReadequacao) {
            $dados['dsJustificativa'] = $this->_request->getParam('justificativa');
            $dados['dsSolicitacao'] = $this->_request->getParam('dsSolicitacao');
            
            try {
                $update = $Readequacao_Model_DbTable_TbReadequacao->update(
                    $dados,
                    [
                        'idReadequacao = ?' => $idReadequacao
                    ]
                );
                
                $this->_helper->json(
                    [
                        'readequacao' => [
                            'idReadequacao' => $idReadequacao,
                            'justificativa' => $this->_request->getParam('justificativa'),
                            'dsSolicitacao' => $this->_request->getParam('dsSolicitacao'),
                        ],
                        'msg' => 'Readequação atualizada com sucesso.'
                    ]
                );
                
            } catch (Exception $objException) {            
                $this->_helper->json([
                    'msg' => 'Houve um erro na atualização do registro de tbReadequacao',
                    'error' => $objException->getMessage()
                ]);
                $this->_helper->viewRenderer->setNoRender(true);
            }            
        }        
    }
    
    public function listarProjetosRecebedoresDisponiveisAction()
    {
        $this->_helper->layout->disableLayout();
        $idPronac = $this->_request->getParam('idPronac');
        
        try {
            $projeto = new Projetos();
            $projetosDisponiveis = $projeto->buscarProjetosRecebedoresDisponiveis($idPronac);
            $projetosDisponiveisMontado = [];

            foreach ($projetosDisponiveis as $projeto) {
                $projetosDisponiveisMontado[] = [
                    'idPronac' => $projeto->IdPRONAC,
                    'pronac' => $projeto->AnoProjeto . $projeto->Sequencial,
                    'nome' => utf8_encode($projeto->NomeProjeto)
                ];
            }
            
            $this->_helper->json([
                'projetos' => $projetosDisponiveisMontado
            ]);
        } catch (Exception $objException) {
            $this->_helper->json([
                'msg' => 'Não há projetos disponíveis para o pronac fornecido.',
                'error' => $objException->getMessage()
            ]);
        }
    }
    
    public function listarProjetosRecebedoresAction()
    {
        $this->_helper->layout->disableLayout();
        $this->view->arrResult = [];
        
        $projetoArr = [];
        $idReadequacao = $this->_request->getParam('idReadequacao');
        $TbSolicitacaoTransferenciaRecursos = new Readequacao_Model_DbTable_TbSolicitacaoTransferenciaRecursos();
        
        try {
            $projetosRecebedores = $TbSolicitacaoTransferenciaRecursos->obterProjetosRecebedores($idReadequacao);
            
            if (count($projetosRecebedores) > 0) {
                foreach($projetosRecebedores as $projeto) {
                    $projetoArr[] = [
                        'idPronacRecebedor' => $projeto->idPronacRecebedor,
                        'nome' => utf8_encode($projeto->NomeProjeto),
                        'pronac' => $projeto->pronac,
                        'valorComprovar' => 10000,
                        'saldoDisponivel' => 10000
                    ];
                }
                $mensagem = "Projetos retornados: " . count($projetosRecebedores);
            } else {
                $projetoArr = [];
                $mensagem = "Não existe nenhum projeto com o idPronac forncedido!";
            }
            
            $this->_helper->json(
                [
                    'projetos' => $projetoArr,
                    'msg' => $mensagem
                ]
            );
        } catch (Exception $objException) {
            $this->_helper->json([
                'msg' => 'Houve um erro na consulta do projeto transferidor.',
                'error' => $objException->getMessage()
            ]);
        }
    }

    public function incluirProjetoRecebedorAction()
    {
        $this->_helper->layout->disableLayout();

        $dados = [];        
        $Readequacao_Model_DbTable_TbSolicitacaoTransferenciaRecursos = new Readequacao_Model_DbTable_TbSolicitacaoTransferenciaRecursos();
        
        try {
            $dados['idReadequacao'] = $this->_request->getParam('idReadequacao');
            $dados['tpTransferencia'] = $this->_request->getParam('dsSolicitacao');
            $dados['idPronacRecebedor'] = $this->_request->getParam('idPronac');
            $dados['vlRecebido'] = (float) str_replace(',', '.', $this->_request->getParam('valorRecebido'));
            $dados['siAnaliseTecnica'] = '';
            $dados['siAnaliseComissao'] = '';
            $dados['stEstado'] = 0;
            
            $idSolicitacaoTransferenciaRecursos = $Readequacao_Model_DbTable_TbSolicitacaoTransferenciaRecursos->inserir($dados);
            
            $this->_helper->json(
                [
                    'resposta' => true
                ]
            );
        } catch (Exception $objException) {
            $this->_helper->json(
                [
                    'error ' => $objException->getMessage(),
                    'resposta' => false
                ]
            );
        }        
    }
    
    public function excluirProjetoRecebedorAction()
    {
        
    }
    
    public function finalizarSolicitacaoTransferenciaRecursosAction()
    {
        try {
            $tbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();

            // inclui
            
            $this->_helper->json(
                [
                    'readequacao' => $readequacao,
                    'resposta' => true
                ]
            );
        } catch (Exception $objException) {
            $this->_helper->json(
                [
                    'error ' => $objException->getMessage(),
                    'resposta' => false
                ]
            );
        }
    }
}