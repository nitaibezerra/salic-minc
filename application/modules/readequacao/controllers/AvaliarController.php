<?php

class Readequacao_AvaliarController extends Readequacao_GenericController implements MinC_Assinatura_Controller_IDocumentoAssinaturaController
{
    private $idPronac;

    /**
     * @var MinC_Assinatura_Documento_IDocumentoAssinatura $servicoDocumentoAssinatura
     */
    private $servicoDocumentoAssinatura;

    public function init()
    {
        parent::perfil();
        parent::init();
        $this->auth = Zend_Auth::getInstance();
        $this->grupoAtivo = new Zend_Session_Namespace('GrupoAtivo');
    }

    /**
     * @return Admissibilidade_EnquadramentoDocumentoAssinaturaController
     */
    public function obterServicoDocumentoAssinatura()
    {
        if (!isset($this->servicoDocumentoAssinatura)) {
            require_once __DIR__ . DIRECTORY_SEPARATOR . "ReadequacaoDocumentoAssinatura.php";
            $this->servicoDocumentoAssinatura = new Readequacao_ReadequacaoDocumentoAssinaturaController(
                $this->getRequest()->getPost()
            );
        }
        return $this->servicoDocumentoAssinatura;
    }

    public function encaminharAssinaturaAction()
    {
        try {
            $this->encaminharProjetosParaAssinatura();
            $this->carregarListaEncaminhamentoAssinatura();
        } catch (Exception $objException) {
            parent::message($objException->getMessage(), '/readequacao/avaliar/encaminhar-assinatura');
        }
    }

    private function encaminharProjetosParaAssinatura()
    {
        $get = $this->getRequest()->getParams();
        $post = $this->getRequest()->getPost();
        $servicoDocumentoAssinatura = $this->obterServicoDocumentoAssinatura();

        if (isset($get['IdPRONAC']) && !empty($get['IdPRONAC']) && $get['encaminhar'] == 'true') {
            $servicoDocumentoAssinatura->idPronac = $get['IdPRONAC'];
            $servicoDocumentoAssinatura->encaminharProjetoParaAssinatura();
            parent::message('Projeto encaminhado com sucesso.', '/admissibilidade/enquadramento/encaminhar-assinatura', 'CONFIRM');
        } elseif (isset($post['IdPRONAC']) && is_array($post['IdPRONAC']) && count($post['IdPRONAC']) > 0) {
            foreach ($post['IdPRONAC'] as $idPronac) {
                $servicoDocumentoAssinatura->idPronac = $idPronac;
                $servicoDocumentoAssinatura->encaminharProjetoParaAssinatura();
            }
            parent::message('Projetos encaminhados com sucesso.', '/admissibilidade/enquadramento/encaminhar-assinatura', 'CONFIRM');
        }
    }

    private function carregarListaEncaminhamentoAssinatura()
    {
        $this->view->idUsuarioLogado = $this->auth->getIdentity()->usu_codigo;
        $enquadramento = new Admissibilidade_Model_Enquadramento();

        $this->view->dados = array();
        $ordenacao = array("dias desc");
        $dados = $enquadramento->obterProjetosEnquadradosParaAssinatura($this->grupoAtivo->codOrgao, $ordenacao);

        foreach ($dados as $dado) {
            $dado->desistenciaRecursal = $enquadramento->verificarDesistenciaRecursal($dado->IdPRONAC);
            $this->view->dados[] = $dado;
        }

        $this->view->codGrupo = $this->grupoAtivo->codGrupo;
        $this->view->codOrgao = $this->grupoAtivo->codOrgao;
    }

    public function visualizarDevolucaoAssinaturaAction()
    {
        try {
            $get = $this->getRequest()->getParams();

            if (!$get['IdPRONAC']) {
                throw new Exception("Identificador do projeto n&atilde;o informado.");
            }

            $objDespacho = new Proposta_Model_DbTable_TbDespacho();
            $despacho = $objDespacho->consultarDespachoAtivo($get['IdPRONAC']);

            $this->_helper->json(
                array(
                    'status' => 1,
                    'despacho' => utf8_encode($despacho['Despacho']),
                    'data' => Data::tratarDataZend($despacho['Data'], 'brasileiro', true)
                )
            );
        } catch (Exception $objException) {
            $this->_helper->json(array('status' => 0, 'msg' => $objException->getMessage()));
        }
    }
}