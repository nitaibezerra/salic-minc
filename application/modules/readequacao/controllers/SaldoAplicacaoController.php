<?php

class Readequacao_SaldoAplicacaoController extends Readequacao_GenericController 
{
    public function init()
    {
        parent::init();
        
        $idPronac = $this->_request->getParam('idPronac');
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }
        $this->idPronac = $idPronac;
        $this->view->idTipoReadequacao = Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_SALDO_APLICACAO;
        $this->view->idPronac = $idPronac;
    }

    public function indexAction()
    {
        $this->_helper->layout->disableLayout();
        echo "Saldo de aplicação financeira";
    }
}