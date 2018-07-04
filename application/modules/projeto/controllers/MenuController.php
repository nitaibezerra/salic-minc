<?php

class Projeto_MenuController extends Projeto_GenericController
{
    protected $idPronac;

    public function init()
    {
        parent::init();

        $this->_helper->getHelper('contextSwitch')
            ->addActionContext('obter-menu', 'json')
            ->initContext('json');

        $idPronac = $this->_request->getParam("idPronac"); // pega o id do pronac via get
        $this->view->idPronac = $idPronac;

        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }
        $this->idPronac = $idPronac;
    }

    public function obterMenuAction()
    {
        $modelProjeto = new Projeto_Model_Menu();
        $modelProjeto->setDebug();
        $menu = $modelProjeto->obterMenu($this->idPronac);
        $this->getResponse()->setHttpResponseCode(200);
        $this->_helper->json($menu);
    }
}