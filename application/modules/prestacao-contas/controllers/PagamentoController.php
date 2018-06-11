<?php
class PrestacaoContas_PagamentoController extends MinC_Controller_Action_Abstract
{
    public function init()
    {
        $PermissoesGrupo = [
            Autenticacao_Model_Grupos::PROPONENTE,
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
        $idpronac = $this->_request->getParam('idpronac');

        $this->view->idpronac = $idpronac;

        $planilhaAprovacaoModel = new PlanilhaAprovacao();
        $resposta = $planilhaAprovacaoModel->planilhaAprovada($idpronac);

        foreach ($resposta as $item) {
            $vlComprovar = $item->vlAprovado - $item->vlComprovado;
            $vlTotalComprovar += $vlComprovar;
        }

        $this->view->vlTotalComprovar = $vlTotalComprovar;

    }

    public function planilhaPagamentoAction()
    {
        $idpronac = (int)$this->_request->getParam('idpronac');

        $planilhaAprovacaoModel = new PlanilhaAprovacao();
        $resposta = $planilhaAprovacaoModel->planilhaAprovada($idpronac);

        $planilhaJSON = null;

        foreach($resposta as $item) {
            $planilhaJSON
            [$item->cdProduto]
            ['etapa']
            [$item->cdEtapa]
            ['UF']
            [$item->cdUF]
            ['cidade']
            [$item->cdCidade]
            ['itens']
            [$item->idPlanilhaItens] = [
                'item' => utf8_encode($item->Item),
                'varlorAprovado' => $item->vlAprovado,
                'varlorComprovado' => $item->vlComprovado,
                'comprovacaoValidada' => $item->ComprovacaoValidada,
                'idPlanilhaAprovacao' => $item->idPlanilhaAprovacao,
                'idPlanilhaItens' => $item->idPlanilhaItens,
            ];

            $planilhaJSON[$item->cdProduto] += [
                'produto' => html_entity_decode(utf8_encode($item->Produto)),
                'cdProduto' => $item->cdProduto,
            ];

            $planilhaJSON[$item->cdProduto]['etapa'][$item->cdEtapa] += [
                'etapa' => utf8_encode($item->Etapa),
                'cdEtapa' =>  $item->cdEtapa
            ];

            $planilhaJSON[$item->cdProduto]['etapa'][$item->cdEtapa]['UF'][$item->cdUF] += [
                'Uf' => $item->Uf,
                'cdUF' => $item->cdUF
            ];

            $planilhaJSON[$item->cdProduto]['etapa'][$item->cdEtapa]['UF'][$item->cdUF]['cidade'][$item->cdCidade] += [
                'cidade' => utf8_encode($item->Cidade),
                'cdCidade' => $item->cdCidade
            ];
        }

        $this->_helper->json($planilhaJSON);
    }

    }