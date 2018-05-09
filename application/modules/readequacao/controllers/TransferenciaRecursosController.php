<?php

class Readequacao_TransferenciaRecursosController extends MinC_Controller_Action_Abstract
{
    public function init()
    {
        parent::init();
        
        $idPronac = $this->_request->getParam('idPronac');
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }
        $this->idPronac = $idPronac;
        $this->view->idPronac = $idPronac;
    }

    public function indexAction()
    {
        $projetos = new Projetos();
        $this->view->projeto = $projetos->buscar(
            [
                'IdPRONAC = ?' => $this->idPronac
            ]
        )->current();
    }

    public function listarRecebedoresAction()
    {
        $this->_helper->layout->disableLayout();
        
        $this->arrResult = [];        
    }
    
    public function incluirSolicitacaoTransferenciaRecursosAction()
    {
        try {
            $tbReadequacao = new Readequacao_Model_tbReadequacao();

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