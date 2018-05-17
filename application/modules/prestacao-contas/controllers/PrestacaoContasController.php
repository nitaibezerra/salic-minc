<?php

class PrestacaoContas_PrestacaoContasController extends MinC_Controller_Action_Abstract
{
    public function init()
    {
        $PermissoesGrupo = [
            Autenticacao_Model_Grupos::TECNICO_PRESTACAO_DE_CONTAS,
            Autenticacao_Model_Grupos::COORDENADOR_PRESTACAO_DE_CONTAS
        ];

        $auth = Zend_Auth::getInstance();

        isset($auth->getIdentity()->usu_codigo) ? parent::perfil(1, $PermissoesGrupo) : parent::perfil(4, $PermissoesGrupo);

        isset($auth->getIdentity()->usu_codigo) ? $this->idUsuario = $auth->getIdentity()->usu_codigo : $this->idUsuario = $auth->getIdentity()->IdUsuario;

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo');

        if (isset($auth->getIdentity()->usu_codigo)) {
            $this->codGrupo = $GrupoAtivo->codGrupo;
            $this->codOrgao = $GrupoAtivo->codOrgao;
            $this->codOrgaoSuperior = (!empty($auth->getIdentity()->usu_org_max_superior)) ? $auth->getIdentity()->usu_org_max_superior : null;
        }

        parent::init();
    }

    public function indexAction()
    {
    }

    public function tipoAvaliacaoAction()
    {
        $idPronac = $this->_request->getParam("idPronac");
        if (!$idPronac) {
           throw new Exception('Não existe idPronac');
        }
        $informacoes = new PrestacaoContas_Model_vwInformacoesConsolidadasParaAvaliacaoFinanceira();
        $informacoes = $informacoes->informacoes($idPronac);
        $this->view->idPronac = $this->_request->getParam("idPronac");
        $this->view->informacoes = $informacoes->current();
    }

    /* @todo: adicionar função de salvar o tipo de amostragem*/
    public function tipoAvaliacaoSalvarAction()
    {
        $idPronac = $this->_request->getParam("idPronac");
        $avaliacao = $this->_request->getParam("avaliacao");
        if (!$idPronac) {
           throw new Exception('Não existe idPronac');
        }
        if (!$avaliacao) {
           throw new Exception('Não existe avaliacao');
        }
        if ($avaliacao == "todos") {
            $this->redirect('/realizarprestacaodecontas/planilhaorcamentaria/idPronac/' . $idPronac );
        }
        $this->redirect('/prestacao-contas/prestacao-contas/amostragem/idPronac/' . $idPronac . '/tipoAvaliacao/' . $avaliacao);
    }

    public function amostragemAction()
    {
        $idPronac = $this->_request->getParam("idPronac");
        $tipoAvaliacao = $this->_request->getParam("tipoAvaliacao");
        if (!$idPronac) {
           throw new Exception('Não existe idPronac');
        }
        if (!$tipoAvaliacao) {
           throw new Exception('Não existe tipoAvaliacao');
        }
        $comprovantes = new PrestacaoContas_Model_spComprovantes();
        $comprovantes = $comprovantes->exec($idPronac, $tipoAvaliacao);
        $this->view->idPronac = $idPronac;
        $this->view->comprovantes = $comprovantes;


        $projeto = new Projetos();
        $projeto = $projeto->buscarTodosDadosProjeto($idPronac);
        $projeto = $projeto->current();

        $this->view->nomeProjeto = $projeto->NomeProjeto;
        $this->view->pronac = $projeto->pronac;
        $this->view->idPronac = $projeto->IdPRONAC;

        $diligencia = new Diligencia();
        $this->view->existeDiligenciaAberta = $diligencia->existeDiligenciaAberta($idPronac, null);
    }

    public function salvarAnaliseAction()
    {
        $idPronac = $this->_request->getParam("idPronac");
        $idComprovantePagamento = $this->_request->getParam("idComprovantePagamento");
        $situacao = $this->_request->getParam("situacao");
        $justificativa = $this->_request->getParam("justificativa");
        if (!$idComprovantePagamento || !$situacao) {
           throw new Exception('Faltando dados');
        }
        $tblComprovantePag = new ComprovantePagamentoxPlanilhaAprovacao();
        $rsComprovantePag = $tblComprovantePag
            ->buscar(['idComprovantePagamento = ?' => $idComprovantePagamento])
            ->current();
        $rsComprovantePag->dtValidacao = date('Y/m/d H:i:s');
        $rsComprovantePag->dsJustificativa = isset($justificativa) ? $justificativa : null;
        $rsComprovantePag->stItemAvaliado = $situacao;
        $rsComprovantePag->save();
        $this->_helper->json(['idComprovantePagamento' => $idComprovantePagamento]);
    }

    public function planilhaAction()
    {
        $idpronac = (int)$this->_request->getParam('idpronac');

        if ($idpronac == 0) {
            throw new Exception('idpronac não informado!');
        }

        $planilhaAprovacaoModel = new PlanilhaAprovacao();
        $planilha = $planilhaAprovacaoModel->vwComprovacaoFinanceiraProjeto($idpronac); 

        $planilhaJSON = null;

        foreach($planilha as $item) {
            $planilhaJSON
                [$item->cdProduto]
                ['etapa']
                [$item->cdEtapa]
                ['UF']
                [$item->cdUF]
                ['cidade']
                [$item->cdCidade]
                ['item']
                [$item->idPlanilhaItens] = [
                    'item' => utf8_encode($item->Item),
                    'varlorAprovado' => $item->vlAprovado,
                    'varlorComprovado' => $item->vlComprovado,
                    'comprovacaoValidada' => $item->ComprovacaoValidada,
            ];

            $planilhaJSON[$item->cdProduto] += [
                    'produto' => utf8_encode($item->Produto),
                    'cdProduto' => $item->cdProduto, 
            ];

            $planilhaJSON[$item->cdProduto]['etapa'][$item->cdEtapa] += [
                utf8_encode($item->Etapa),
                $item->cdEtapa
            ];

            $planilhaJSON[$item->cdProduto]['etapa'][$item->cdEtapa]['UF'][$item->cdUF] += [
                $item->cdUF,
                $item->Uf
            ];

            $planilhaJSON[$item->cdProduto]['etapa'][$item->cdEtapa]['UF'][$item->cdUF]['cidade'][$item->cdCidade] += [
                $item->cdCidade,
                utf8_encode($item->Cidade),
            ];
        }

        $this->_helper->json($planilhaJSON);
    }
}
