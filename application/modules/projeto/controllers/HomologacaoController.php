<?php

class Projeto_HomologacaoController extends Projeto_GenericController
{

    private $arrBreadCrumb = [];
    private $situacaoParaHomologacao = Projeto_Model_Situacao::PROJETO_APRECIADO_PELA_CNIC;

    public function init()
    {
        $this->arrBreadCrumb[] = array('url' => '/principal', 'title' => 'In&iacute;cio', 'description' => 'Ir para in&iacute;cio');
        parent::init();
    }

    public function indexAction()
    {
        $this->arrBreadCrumb[] = array('url' => '', 'title' => 'Homologacao de Projetos', 'description' => 'Tela atual');
        $this->view->arrBreadCrumb = $this->arrBreadCrumb;
    }

    public function listarAction()
    {
        $this->_helper->layout->disableLayout();
        $dbTableEnquadramento = new Projeto_Model_DbTable_Enquadramento();

        $this->view->arrResult = $dbTableEnquadramento->obterProjetosApreciadosCnic(
            ['a.Orgao = ?' => (int) $_SESSION['GrupoAtivo']['codOrgao']], ['NrReuniao', 'Pronac']
        );
    }

    public function visualizarAction()
    {
        $this->_helper->layout->disableLayout();
        self::prepareData($this->getRequest()->getParam('id'));
    }

    /**
     * @todo confirmar se setIdAtoDeGestao e o IdEnquadramento.
     */
    public function encaminharAction()
    {
        $this->_helper->layout->disableLayout();
        if ($this->getRequest()->isPost()) {
            $this->_helper->viewRenderer->setNoRender(true);
            $mapper = new Projeto_Model_TbHomologacaoMapper();
            $arrPost = $this->getRequest()->getPost();
//            $arrPost['conteudo'] = self::gerarDocumentoAssinatura($arrPost['idPronac']);
            $this->_helper->json(array('status' => $mapper->encaminhar($arrPost), 'msg' => $mapper->getMessages(), 'close' => 1));
        } else {
            self::prepareData($this->getRequest()->getParam('id'));
            $this->view->urlAction = '/projeto/homologacao/encaminhar';
        }
    }

    public function homologarAction()
    {
        $this->_helper->layout->disableLayout();

        if ($this->getRequest()->isPost()) {
            $this->_helper->viewRenderer->setNoRender(true);
            $mapper = new Projeto_Model_TbHomologacaoMapper();
            $arrPost = $this->getRequest()->getPost();
            $arrPost['stDecisao'] = (isset($arrPost['stDecisao'])) ? 2 : 1;
            $this->_helper->json(array('status' => $mapper->save($arrPost), 'msg' => $mapper->getMessages(), 'close' => 1));
        } else {
            $dbTableEnquadramento = new Projeto_Model_DbTable_Enquadramento();
            $this->view->urlAction = '/projeto/homologacao/homologar';
            $intId = $this->getRequest()->getParam('id');
            $dbTable = new Projeto_Model_DbTable_TbHomologacao();

            $arrValue = $dbTable->getBy(['idPronac' => $intId, 'tpHomologacao' => '1']);
            if (empty($arrValue)) {
                $arrValue = $dbTableEnquadramento->obterProjetosApreciadosCnic(['a.IdPRONAC = ?' => $intId])->current()->toArray();
                $arrValue['idPronac'] = $arrValue['IdPRONAC'];
                $arrValue['tpHomologacao'] = 1;
            }

            $arrValue['enquadramentoProjeto'] = $dbTableEnquadramento->obterProjetoAreaSegmento(
                [ 'a.IdPRONAC = ?' => $intId, 'a.Situacao = ?' => $this->situacaoParaHomologacao]
            )->current()->toArray();

            $this->view->dataForm = $arrValue;
        }
    }

    /**
     * Metodo responsavel por preparar o formulario conforme cada acao.
     */
    private function prepareData($intIdPronac)
    {
        $dbTableParecer = new Parecer();
        $dbTableAcaoProjeto = new tbAcaoAlcanceProjeto();
        $dbTableHomologacao = new Projeto_Model_DbTable_TbHomologacao();
        $dbTableEnquadramento = new Projeto_Model_DbTable_Enquadramento();
        $arrValue = $dbTableEnquadramento->obterProjetosApreciadosCnic(['a.IdPRONAC = ?' => $intIdPronac])->current()->toArray();
        $arrValue['enquadramentoProjeto'] = $dbTableEnquadramento->obterProjetoAreaSegmento(
            [
                'a.IdPRONAC = ?' => $intIdPronac,
                'a.Situacao = ?' => $this->situacaoParaHomologacao
            ]
        )->current()->toArray();

        $arrValue['parecer'] = $dbTableParecer->findBy(['TipoParecer' => '1', 'idTipoAgente' => '1', 'IdPRONAC' => $intIdPronac]);
        $arrValue['acaoProjeto'] = $dbTableAcaoProjeto->findBy(['tpAnalise' => '1', 'idPronac' => $intIdPronac]); # 3
        $arrValue['aparicaoComissario'] = $dbTableParecer->findBy(['TipoParecer' => '1', 'idTipoAgente' => '6', 'IdPRONAC' => $intIdPronac]); # 4
        $arrValue['parecerHomologacao'] = $dbTableHomologacao->getBy(['idPronac' => $intIdPronac, 'tpHomologacao' => '1']); # 5

        if (isset($arrValue['IdPRONAC'])) $arrValue['idPronac'] = $arrValue['IdPRONAC'];

        $this->view->arrValue = $arrValue;
        return $arrValue;
    }

    function gerarDocumentoAssinatura($intIdPronac)
    {
        $view = new Zend_View();
        $view->setScriptPath(__DIR__ . DIRECTORY_SEPARATOR . '../views/scripts/');
        $view->arrValue = self::prepareData($intIdPronac);
        return $view->render('homologacao/partials/documento-assinatura.phtml');
    }
}
