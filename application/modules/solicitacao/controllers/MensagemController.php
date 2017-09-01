<?php

class Solicitacao_MensagemController extends Solicitacao_GenericController
{

    public function init()
    {
        parent::init();

        if (!empty($this->idPreProjeto) || !empty($this->idPronac)) {
            parent::verificarPermissaoAcesso(!empty($this->idPreProjeto), !empty($this->idPronac), false);
        }
    }

    public function indexAction()
    {

    }

    /**
     * Metodo responsavel por preparar o formulario conforme cada acao.
     *
     * @name prepareForm
     * @param array $arrConfig
     *
     */
    public function prepareForm($dataForm = [], $arrConfig = [], $strUrlAction = '', $strActionBack = 'index')
    {

        $intId = $this->getRequest()->getParam('id', null);

        if (!empty($dataForm['idDocumento'])) {
            $tbl = new Arquivo_Model_DbTable_TbDocumento();
            $dataForm['arquivo'] = $tbl->buscarDocumento($dataForm['idDocumento'])->toArray();
        }
        if ($this->proposta) {
            $dataForm['idProjeto'] = $this->idPreProjeto;
            $dataForm['NomeProjeto'] = isset($this->proposta->NomeProjeto) ? $this->proposta->NomeProjeto : '';
//            $dataForm['idAgente'] = '';
        }

        if ($this->projeto) {
            $dataForm['Pronac'] = $this->projeto->AnoProjeto . $this->projeto->Sequencial;
            $dataForm['NomeProjeto'] = isset($this->projeto->NomeProjeto) ? $this->projeto->NomeProjeto : '';
            $dataForm['idPronac'] = $this->idPronac;
        }

        $this->view->arrPartial = array(
            'dataForm' => $dataForm,
            'arrConfig' => $arrConfig,
            'id' => $intId,
            'urlAction' => $strUrlAction,
            'strActionBack' => $strActionBack,
            'currentUrl' => Zend_Controller_Front::getInstance()->getRequest()->getRequestUri()
        );
    }

    public function listarAction()
    {
        $this->_helper->layout->disableLayout();
        $idPronac = $this->getRequest()->getParam('idPronac', null);
        $idPreProjeto = $this->getRequest()->getParam('idPreProjeto', null);
        $this->view->ehTecnico = false;

        $vwSolicitacoes = new Solicitacao_Model_vwPainelDeSolicitacaoProponente();

        $where = [];
        if ($idPronac) {
            $where['idPronac = ?'] = $idPronac;
        }

        if ($idPreProjeto) {
            $where['idProjeto = ?'] = $idPreProjeto;
        }

        # Proponente
        if (isset($this->usuario['cpf'])) {
            $where["(idAgente = {$this->idAgente} OR idSolicitante = {$this->idUsuario})"] = '';
        }

        if (isset($this->usuario['usu_codigo'])) {

            $grupos = new Autenticacao_Model_Grupos();
            $tecnicos = $grupos->buscarTecnicosPorOrgao($this->grupoAtivo->codOrgao)->toArray();

            if (in_array($this->grupoAtivo->codGrupo, array_column($tecnicos, 'gru_codigo'))) {
                $where['idTecnico = ?'] = $this->idUsuario;
                $this->view->ehTecnico = true;
            }

            if (isset($this->grupoAtivo->codOrgao)) {
                $where['idOrgao = ?'] = $this->grupoAtivo->codOrgao;
            }

            $where['siEncaminhamento = ?'] = Solicitacao_Model_TbSolicitacao::SOLICITACAO_ENCAMINHADA;
        }

        $solicitacoes = $vwSolicitacoes->buscar($where);

        $this->view->arrResult = $solicitacoes;
        $this->view->idPronac = $idPronac;

    }

    public function visualizarAction()
    {

        $urlAction = $this->_urlPadrao . "/solicitacao/mensagem/salvar";

        try {

            $idSolicitacao = $this->getRequest()->getParam('id', null);

            if (empty($idSolicitacao))
                throw new Exception("Informe o id da solicita&ccedil;&atilde;o para visualizar!");

            $where['idSolicitacao'] = $idSolicitacao;

            $vwSolicitacao = new Solicitacao_Model_vwPainelDeSolicitacaoProponente();
            $dataForm = $vwSolicitacao->findBy($where);

            if (empty($dataForm))
                throw new Exception("Nenhuma solicita&ccedil;&atilde;o encontrada!");


            $permissao = parent::verificarPermissaoAcesso($dataForm['idProjeto'], $dataForm['idPronac'], false, true);

            if ($permissao['status'] === false)
                throw new Exception("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar esta solicita&ccedil;&atilde;o");

            $arrConfig['dsResposta']['show'] = true;

            self::prepareForm($dataForm, $arrConfig, $urlAction);

        } catch (Exception $objException) {
            parent::message($objException->getMessage(), "/solicitacao/mensagem", "ALERT");
        }
    }


    public function solicitarAction()
    {
        $urlAction = $this->_urlPadrao . "/solicitacao/mensagem/salvar";
        $urlCallBack = $this->_urlPadrao . "/solicitacao/mensagem/index";

        try {

            if (empty($this->idPronac) && empty($this->idPreProjeto))
                throw new Exception("Informe o projeto ou proposta para realizar uma solicita&ccedil;&atilde;o");

            $dataForm = [
                'siEncaminhamento' => Solicitacao_Model_TbSolicitacao::SOLICITACAO_CADASTRADA
            ];

            $arrConfig['dsSolicitacao']['disabled'] = false;
            $arrConfig['actions']['show'] = true;

            if ($this->projeto) {
                $urlCallBack .= '/idPronac/' . $this->idPronac;
                $dataForm['idPronac'] = $this->idPronac;
            } else if ($this->proposta) {
                $urlCallBack .= '/idPreProjeto/' . $this->idPreProjeto;
                $dataForm['idProjeto'] = $this->idPreProjeto;
            }

            $mapperSolicitacao = new Solicitacao_Model_TbSolicitacaoMapper();

            if ($mapperSolicitacao->existeSolicitacaoNaoRespondida($dataForm))
                throw new Exception("Voc&ecirc; j&aacute; possui uma solicita&ccedil;&atilde;o aguardando resposta para este projeto!");

            self::prepareForm($dataForm, $arrConfig, $urlAction, $urlCallBack);


        } catch (Exception $objException) {
            parent::message($objException->getMessage(), $urlCallBack, "ALERT");
        }
    }

    public function salvarAction()
    {
        $status = false;

        if ($this->getRequest()->isPost()) {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $arrayForm = $this->getRequest()->getPost();

            $strUrl = '/solicitacao/mensagem/index';
            $strUrl .= ($arrayForm['idPronac']) ? '/idPronac/' . $arrayForm['idPronac'] : '';
            $strUrl .= ($arrayForm['idProposta']) ? '/idproposta/' . $arrayForm['idproposta'] : '';
            $arrayForm['idUsuario'] = $this->idUsuario;

            $mapperSolicitacao = new Solicitacao_Model_TbSolicitacaoMapper();
            $idSolicitacao = $mapperSolicitacao->salvar($arrayForm);

            if ($idSolicitacao) {
                $strUrl = '/solicitacao/mensagem/visualizar/id/' . $idSolicitacao;
                $status = true;
            }

            $this->_helper->json(array('status' => $status, 'msg' => $mapperSolicitacao->getMessages(), 'redirect' => $strUrl));

        }
    }

    /**
     */
    public function responderAction()
    {
        $idSolicitacao = $this->getRequest()->getParam('id', null);

        if (empty($idSolicitacao))
            throw new Exception("Informe o id da solicita&ccedil;&atilde;o para responder!");

        $where['idSolicitacao'] = $idSolicitacao;
        $where['dsResposta IS NOT NULL'] = '';

        $vwSolicitacao = new Solicitacao_Model_vwPainelDeSolicitacaoProponente();
        $jaRespondida = $vwSolicitacao->findBy($where, true);

        if ($jaRespondida)
            $this->redirect("/solicitacao/mensagem/visualizar/id/{$idSolicitacao}");

        $strActionBack = "/solicitacao/mensagem/index";
        try {

            if ($this->getRequest()->isPost()) {

                $status = false;

                $this->_helper->layout->disableLayout();
                $this->_helper->viewRenderer->setNoRender(true);
                $arrayForm = $this->getRequest()->getPost();

                $strUrl = '/solicitacao/mensagem/index';
                $strUrl .= ($arrayForm['idPronac']) ? '/idPronac/' . $arrayForm['idPronac'] : '';
                $strUrl .= ($arrayForm['idProposta']) ? '/idproposta/' . $arrayForm['idproposta'] : '';
                $mapperSolicitacao = new Solicitacao_Model_TbSolicitacaoMapper();

                $idSolicitacao = $mapperSolicitacao->responder($this->getRequest()->getPost());

                if ($idSolicitacao) {
                    $strUrl = '/solicitacao/mensagem/visualizar/id/' . $idSolicitacao;
                    $status = true;
                }

                $this->_helper->json(
                    array(
                        'status' => $status,
                        'msg' => $mapperSolicitacao->getMessages(),
                        'redirect' => $strUrl
                    )
                );

            } else {

                $vwSolicitacao = new Solicitacao_Model_vwPainelDeSolicitacaoProponente();
                $dataForm = $vwSolicitacao->findBy(['idSolicitacao' => $idSolicitacao]);

                if (empty($dataForm))
                    throw new Exception("Nenhuma solicita&ccedil;&atilde;o encontrada!");


                if ($dataForm['idTecnico'] != $this->idUsuario)
                    throw new Exception("Voc&ecirc; n&atilde;o tem permiss&atilde;o para responder esta solicita&ccedil;&atilde;o!");

                $arrConfig = [
                    'dsSolicitacao' => ['disabled' => true],
                    'dsResposta' => ['show' => true, 'disabled' => false],
                    'actions' => ['show' => true]
                ];

                self::prepareForm($dataForm, $arrConfig, '', $strActionBack);
            }

            $this->view->arrConfig['dsMensagem'] = ['disabled' => true];

        } catch (Exception $objException) {
            parent::message($objException->getMessage(), $strActionBack, "ALERT");
        }
    }

    public function abrirdocumentosolicitacaoAction()
    {
        $idDocumento = $this->getRequest()->getParam('id', null);

        try {
            $vwSolicitacao = new Solicitacao_Model_vwPainelDeSolicitacaoProponente();
            $solicitacao = $vwSolicitacao->findBy(['idDocumento' => $idDocumento]);

            if (empty($solicitacao))
                throw new Exception('Documento n&atilde;o encontrado!');

            $idProjeto = $solicitacao['idProjeto'] ? $solicitacao['idProjeto'] : false;
            $idPronac = $solicitacao['idPronac'] ? $solicitacao['idPronac'] : false;

            # verificar se o usuario tem permissao para acessar este documento por meio do id do projeto/proposta
            $permissao = parent::verificarPermissaoAcesso($idProjeto, $idPronac, false, true);

            if ($permissao['status'] === false)
                throw new Exception('Voc&ecirc; n&atilde;o tem permiss&atilde;o para baixar esse arquivo!');

            parent::abrirDocumento($idDocumento);

        } catch (Exception $e) {
            throw $e;
        }
    }

    public function listarAjaxAction()
    {
        $this->_helper->layout->disableLayout();
        $idPronac = $this->getRequest()->getParam('idPronac', null);
        $idPreProjeto = $this->getRequest()->getParam('idPreProjeto', null);
        $this->view->ehTecnico = false;

        $vwSolicitacoes = new Solicitacao_Model_vwPainelDeSolicitacaoProponente();

        $where = [];
        if ($idPronac) {
            $where['idPronac = ?'] = $idPronac;
        }

        if ($idPreProjeto) {
            $where['idProjeto = ?'] = $idPreProjeto;
        }

        # Proponente
        if (isset($this->usuario['cpf'])) {
            $where["(idAgente = {$this->idAgente} OR idSolicitante = {$this->idUsuario})"] = '';
            $where['siEncaminhamento = ?'] = Solicitacao_Model_TbSolicitacao::SOLICITACAO_FINALIZADA_MINC;
        }

        if (isset($this->usuario['usu_codigo'])) {

            $grupos = new Autenticacao_Model_Grupos();
            $tecnicos = $grupos->buscarTecnicosPorOrgao($this->grupoAtivo->codOrgao)->toArray();


            if (in_array($this->grupoAtivo->codGrupo, array_column($tecnicos, 'gru_codigo'))) {
                $where['idTecnico = ?'] = $this->idUsuario;
                $where['dsResposta IS NULL'] = '';
                $this->view->ehTecnico = true;
            }

            if (isset($this->grupoAtivo->codOrgao)) {
                $where['idOrgao = ?'] = $this->grupoAtivo->codOrgao;
            }

            $where['siEncaminhamento = ?'] = Solicitacao_Model_TbSolicitacao::SOLICITACAO_ENCAMINHADA;
        }
        $solicitacoes = $vwSolicitacoes->buscar($where, ['dtResposta DESC', 'dtSolicitacao DESC']);

        $this->view->arrResult = $solicitacoes;
        $this->view->idPronac = $idPronac;
    }

    public function contarSolicitacoesNaoLidasAjaxAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $resultado = 0;

        $vwSolicitacoes = new Solicitacao_Model_vwPainelDeSolicitacaoProponente();

        if ($this->usuario['usu_codigo']) {
            $resultado = $vwSolicitacoes->contarSolicitacoesNaoRespondidasTecnico($this->idUsuario, $this->grupoAtivo->codOrgao);
        } else {
            $resultado = $vwSolicitacoes->contarSolicitacoesNaoLidasUsuario($this->idUsuario, $this->idAgente);
        }

        $this->_helper->json(array('status' => true, 'msg' => $resultado));
    }
}