<?php

class Projeto_Model_Menu extends MinC_Db_Table_Abstract
{

    public function obterMenu($idPronac) {
        return $this->obterArrayMenu($idPronac);
    }

    public function liberarLink($idPronac)
    {
        $auth = Zend_Auth::getInstance();

        if (!isset($auth->getIdentity()->usu_codigo)) {
            $this->view->blnProponente = $this->blnProponente;

            $proj = new Projetos();
            $cpf = $proj->buscarProponenteProjeto($idPronac);
            $cpf = $cpf->CgcCpf;
            $idUsuarioLogado = $auth->getIdentity()->IdUsuario;

            $links = new fnLiberarLinks();
            $linksXpermissao = $links->links(2, $cpf, $idUsuarioLogado, $idPronac);

            $linksGeral = str_replace(' ', '', explode('-', $linksXpermissao->links));

            $arrayLinks = array(
                'Permissao' => $linksGeral[0],
                'FaseDoProjeto' => $linksGeral[1],
                'Diligencia' => $linksGeral[2],
                'Recursos' => $linksGeral[3],
                'Readequacao' => $linksGeral[4],
                'ComprovacaoFinanceira' => $linksGeral[5],
                'RelatorioTrimestral' => $linksGeral[6],
                'RelatorioFinal' => $linksGeral[7],
                'Analise' => $linksGeral[8],
                'Execucao' => $linksGeral[9],
                'PrestacaoContas' => $linksGeral[10],
                'Readequacao_50' => $linksGeral[11],
                'Marcas' => $linksGeral[12],
                'SolicitarProrrogacao' => $linksGeral[13],
                'ReadequacaoPlanilha' => $linksGeral[14],
                'ReadequacaoTransferenciaRecursos' => $linksGeral[15]
            );
            $this->view->fnLiberarLinks = $arrayLinks;
            $projetos = new Projeto_Model_DbTable_Projetos();
            $this->view->isAdequarARealidade = $projetos->fnChecarLiberacaoDaAdequacaoDoProjeto($idPronac);
        }
    }

    public function obterArrayMenu($idPronac)
    {
        $idPronacHash = Seguranca::encrypt($idPronac);

        $menu = [];
        $menu['dadosprojeto'] = array(
            'id' => 'dadosdoprojeto',
            'label' => 'Dados básicos',
            'title' => '',
            'link' => '/projeto/convenio/visualizar/idPronac/' . $idPronacHash,
            'ajax' => false,
            'icon' => 'home',
            'submenu' => '',
            'grupo' => []
        );

        $menu['proponente'] = array(
            'id' => 'proponente',
            'label' => 'Proponente',
            'title' => '',
            'link' => '/default/consultardadosprojeto/dados-proponente/idPronac/' . $idPronacHash,
            'ajax' => true,
            'icon' => 'person',
            'submenu' => '',
            'grupo' => []
        );

        return $menu;
    }

}
