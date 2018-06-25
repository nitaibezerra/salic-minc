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
            'link' => '/default/consultardadosprojeto/index/idPronac/' . $idPronacHash,
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

        $menu['localderealizacoes'] = array(
            'id' => 'localderealizacoes',
            'label' => 'Local de realiza&ccedil;&atilde;o',
            'title' => '',
            'link' => array(
                'module' => 'proposta',
                'controller' => 'localderealizacao',
                'action' => 'index',
                'idPreProjeto' => $idPronac
            ),
            'icon' => 'pin_drop',
            'submenu' => '',
            'grupo' => []
        );

        $menu['planodedistribuicao'] = array(
            'id' => 'planodedistribuicao',
            'label' => 'Plano de distribui&ccedil;&atilde;o',
            'title' => 'Ir para plano de distribui&ccedil;&atilde;o',
            'link' => array(
                'module' => 'proposta',
                'controller' => 'plano-distribuicao',
                'action' => 'index',
                'idPreProjeto' => $idPronac
            ),
            'icon' => 'multiline_chart',
            'submenu' => '',
            'grupo' => []
        );

        $menu['planilhaorcamentaria'] = array(
            'id' => 'planodedistribuicao',
            'label' => 'Or&ccedil;amento do projeto',
            'title' => '',
            'link' => '',
            'icon' => 'insert_chart',
            'submenu' => '',
            'grupo' => []
        );
        $menu['planilhaorcamentaria']['submenu'][] = array(
            'label' => 'Custos Vinculados',
            'title' => '',
            'link' => array(
                'module' => 'proposta',
                'controller' => 'manterorcamento',
                'action' => 'custosvinculados',
                'idPreProjeto' => $idPronac
            ),
            'grupo' => []
        );
        $menu['planilhaorcamentaria']['submenu'][] = array(
            'label' => 'Custos por produtos',
            'title' => '',
            'link' => array('module' => 'proposta',
                'controller' => 'manterorcamento',
                'action' => 'produtoscadastrados',
                'idPreProjeto' => $idPronac
            ),
            'grupo' => []
        );
        $menu['planilhaorcamentaria']['submenu'][] = array(
            'label' => 'Visualizar planilha',
            'title' => '',
            'link' => array('module' => 'proposta',
                'controller' => 'manterorcamento',
                'action' => 'planilhaorcamentariageral',
                'idPreProjeto' => $idPronac
            ),
            'grupo' => []
        );

        $menu['itensorcamentario'] = array(
            'id' => 'itensorcamentario',
            'label' => 'Itens or&ccedil;ament&aacute;rios',
            'title' => '',
            'link' => '',
            'icon' => 'show_chart',
            'submenu' => '',
            'grupo' => []
        );
        $menu['itensorcamentario']['submenu'][] = array(
            'label' => 'Solicitar inclus&atilde;o de itens',
            'title' => 'Ir para Solicitar inclus&atilde;o de itens',
            'link' => array(
                'module' => 'proposta',
                'controller' => 'mantertabelaitens',
                'action' => 'index',
                'idPreProjeto' => $idPronac
            ),
            'grupo' => []
        );
        $menu['itensorcamentario']['submenu'][] = array(
            'label' => 'Minhas solicita&ccedil;&otilde;es',
            'title' => 'Ir para Minhas solicita&ccedil;&otilde;es',
            'link' => array(
                'module' => 'proposta',
                'controller' => 'mantertabelaitens',
                'action' => 'minhas-solicitacoes',
                'idPreProjeto' => $idPronac,
                'tipoFiltro' => 'solicitado'
            ),
            'grupo' => []
        );

        $menu['anexardocumentos'] = array(
            'id' => 'anexardocumentos',
            'label' => 'Anexar documentos',
            'title' => '',
            'link' => array(
                'module' => 'proposta',
                'controller' => 'manterpropostaedital',
                'action' => 'enviararquivoedital',
                'idPreProjeto' => $idPronac
            ),
            'icon' => 'attachment',
            'submenu' => '',
            'grupo' => []
        );

        $menu['imprimir'] = array(
            'id' => 'imprimir',
            'label' => 'Gerar PDF',
            'title' => '',
            'link' => array(
                'module' => 'admissibilidade',
                'controller' => 'admissibilidade',
                'action' => 'imprimirpropostacultural',
                'idPreProjeto' => $idPronac
            ),
            'icon' => 'picture_as_pdf',
            'submenu' => '',
            'grupo' => []
        );

        $menu['mensagensenviadas'] = array(
            'id' => 'mensagensenviadas',
            'label' => 'Dilig&ecirc;ncias',
            'title' => '',
            'link' => array(
                'module' => 'proposta',
                'controller' => 'diligenciar',
                'action' => 'listardiligenciaproponente',
                'idPreProjeto' => $idPronac
            ),
            'icon' => 'announcement',
            'submenu' => '',
            'grupo' => []
        );

        $menu['solicitacoes'] = array(
            'id' => 'minhassolicitacoes',
            'label' => 'Minhas solicita&ccedil;&otilde;es',
            'title' => '',
            'icon' => 'message',
            'link' => array(
                'module' => 'solicitacao',
                'controller' => 'mensagem',
                'action' => 'index',
                'idPreProjeto' => $idPronac
            ),
            'submenu' => '',
            'grupo' => []
        );

        if (count($this->view->recursoEnquadramentoVisaoProponente) > 0) {
            $menu['enquadramento'] = [
                'id' => 'menu_enquadramento',
                'label' => 'Enquadramento',
                'title' => 'Recurso de Enquadramento',
                'icon' => 'build',
                'menuClass' => ' light-green lighten-4',
                'link' =>
                    [
                        'module' => 'recurso',
                        'controller' => 'recurso-proposta',
                        'action' => 'visao-proponente',
                        'idPreProjeto' => $idPronac
                    ],
                'grupo' => []
            ];
        }
        if ($this->isEditavel) {
            if (!$this->isEditarProjeto) {

                $menu['excluirproposta'] = array(
                    'id' => 'excluirproposta',
                    'label' => 'Excluir proposta',
                    'title' => '',
                    'link' => '',
                    'icon' => 'delete_forever',
                    'submenu' => '',
                    'grupo' => []
                );

                $menu['enviarproposta'] = array(
                    'id' => 'enviarproposta',
                    'label' => 'Enviar proposta ao MinC',
                    'title' => '',
                    'link' => array(
                        'module' => 'proposta',
                        'controller' => 'manterpropostaincentivofiscal',
                        'action' => 'enviar-proposta',
                        'idPreProjeto' => $idPronac
                    ),
                    'icon' => 'send',
                    'submenu' => '',
                    'grupo' => []
                );
            } else {
                $menu['encaminharprojetoaominc'] = array(
                    'id' => 'encaminharprojetoaominc',
                    'label' => 'Devolver projeto ao MinC',
                    'title' => '',
                    'link' => array(
                        'module' => 'proposta',
                        'controller' => 'manterpropostaincentivofiscal',
                        'action' => 'encaminharprojetoaominc',
                        'idPreProjeto' => $idPronac
                    ),
                    'icon' => 'send',
                    'submenu' => '',
                    'grupo' => []
                );
            }

        }

        return $menu;
    }

}
