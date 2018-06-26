<?php

class Projeto_Model_Menu extends MinC_Db_Table_Abstract
{
    private $projeto;
    private $usuarioInterno = false;
    private $usuarioExterno = false;
    private $fnLiberarLink;

    public function obterMenu($idPronac)
    {
//        $this->liberarLink($idPronac);
        return $this->obterArrayMenuMecenato($idPronac);
    }

    public function liberarLink($idPronac)
    {
        $auth = Zend_Auth::getInstance();

        if (!isset($auth->getIdentity()->usu_codigo)) {
            $this->usuarioExterno = true;

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
            $this->fnLiberarLink = $arrayLinks;
            $projetos = new Projeto_Model_DbTable_Projetos();
            $this->view->isAdequarARealidade = $projetos->fnChecarLiberacaoDaAdequacaoDoProjeto($idPronac);
        }
    }

    public function obterArrayMenuConvenio($idPronac)
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

    public function obterArrayMenuMecenato($idPronac)
    {
        $idPronacHash = Seguranca::encrypt($idPronac);
        $debug = true;

        $menu = [];
        $menu['dadosprojeto'] = [
            'id' => 'dadosdoprojeto',
            'label' => 'Dados básicos',
            'title' => '',
            'link' => '/projeto/convenio/visualizar/idPronac/' . $idPronacHash,
            'ajax' => false,
            'icon' => 'home',
            'submenu' => '',
            'grupo' => []
        ];

        $menu['proponente'] = [
            'id' => 'proponente',
            'label' => 'Proponente',
            'title' => '',
            'link' => '/default/consultardadosprojeto/dados-proponente/idPronac/' . $idPronacHash,
            'ajax' => true,
            'icon' => 'person',
            'submenu' => '',
            'grupo' => []
        ];

        $menu['outrasinformacoes'] = [
            'id' => 'outrasinformacoes',
            'label' => 'Outras Informações',
            'title' => 'Outras Informações',
            'link' => '',
            'ajax' => false,
            'icon' => 'style',
            'submenu' => '',
            'grupo' => []
        ];

        $menu['outrasinformacoes']['submenu'][] = [
            'label' => 'Certid&otilde;es Negativas',
            'title' => 'Ir para Dados Certid&otilde;es Negativas',
            'link' => '/default/consultardadosprojeto/certidoes-negativas/?idPronac=' . $idPronacHash,
            'ajax' => true,
            'grupo' => []
        ];

        $menu['outrasinformacoes']['submenu'][] = [
            'label' => 'Dados complementares do projeto',
            'title' => 'Ir para Dados complementares do projeto',
            'link' => '/default/consultardadosprojeto/dados-complementares/?idPronac=' . $idPronacHash,
            'ajax' => true,
            'grupo' => []
        ];

        $menu['outrasinformacoes']['submenu'][] = [
            'label' => 'Documentos anexados',
            'title' => 'Ir para  Documentos anexados',
            'link' => '/default/consultardadosprojeto/documentos-anexados/?idPronac=' . $idPronacHash,
            'ajax' => true,
            'grupo' => []
        ];

        $menu['outrasinformacoes']['submenu'][] = [
            'label' => 'Documentos assinados',
            'title' => 'Ir para Documentos assinados',
            'link' => '/assinatura/index/visualizar-documentos-assinatura-ajax/?idPronac=' . $idPronacHash,
            'ajax' => true,
            'grupo' => []
        ];

        $menu['outrasinformacoes']['submenu'][] = [
            'label' => 'Dilig&ecirc;ncias do projeto',
            'title' => 'Ir para Dilig&ecirc;ncias do projeto',
            'link' => '/default/consultardadosprojeto/diligencias/?idPronac=' . $idPronacHash,
            'ajax' => true,
            'grupo' => []
        ];

        $menu['outrasinformacoes']['submenu'][] = [
            'label' => 'Local de realiza&ccedil;&atilde;o/Deslocamento',
            'title' => 'Ir para Local de realiza&ccedil;&atilde;o/Deslocamento',
            'link' => '/default/consultardadosprojeto/local-realizacao-deslocamento/?idPronac=' . $idPronacHash,
            'ajax' => true,
            'grupo' => []
        ];

        if ($this->IN2017) {
            $menu['outrasinformacoes']['submenu'][] = [
                'label' => 'Plano de distribui&ccedil;&atilde;o',
                'title' => 'Ir para Plano de distribui&ccedil;&atilde;o',
                'link' => '/default/consultardadosprojeto/plano-de-distribuicao/?idPronac=' . $idPronacHash,
                'ajax' => true,
                'grupo' => []
            ];
        } else {
            $menu['outrasinformacoes']['submenu'][] = [
                'label' => 'Plano de distribui&ccedil;&atilde;o',
                'title' => 'Ir para Plano de distribui&ccedil;&atilde;o',
                'link' => '/proposta/visualizar-plano-distribuicao/visualizar/idPreProjeto/' . $this->projeto->idProjeto,
                'ajax' => true,
                'grupo' => []
            ];
        }

        $menu['outrasinformacoes']['submenu'][] = [
            'label' => 'Provid&ecirc;ncia tomada',
            'title' => 'Ir para Provid&ecirc;ncia tomada',
            'link' => '/default/consultardadosprojeto/providencia-tomada/?idPronac=' . $idPronacHash,
            'ajax' => false,
            'grupo' => []
        ];

        $menu['outrasinformacoes']['submenu'][] = [
            'label' => 'Tramita&ccedil;&atilde;o',
            'title' => 'Ir para Tramita&ccedil;&atilde;o',
            'link' => '/default/consultardadosprojeto/tramitacao/?idPronac=' . $idPronacHash,
            'ajax' => true,
            'grupo' => []
        ];

        if ($this->usuarioInterno || $debug) {
            $menu['outrasinformacoes']['submenu'][] = [
                'label' => 'Hist&oacute;rico encaminhamento',
                'title' => 'Ir para Hist&oacute;rico encaminhamento',
                'link' => '/default/consultardadosprojeto/historico-encaminhamento/?idPronac=' . $idPronacHash,
                'ajax' => true,
                'grupo' => []
            ];
        }

        # Análise e Aprovação
        if ($this->fnLiberarLinks['Analise'] || $this->usuarioInterno || $debug) {

            $menu['analiseaprovacao'] = [
                'id' => 'analiseaprovacao',
                'label' => 'An&aacute;lise e Aprova&ccedil;&atilde;o',
                'title' => 'An&aacute;lise e Aprova&ccedil;&atilde;o',
                'link' => '',
                'ajax' => false,
                'icon' => 'gavel',
                'submenu' => '',
                'grupo' => []
            ];

            $menu['analiseaprovacao']['submenu'][] = [
                'label' => 'An&aacute;lise do projeto',
                'title' => 'Ir para An&aacute;lise do projeto',
                'link' => '/default/consultardadosprojeto/analise-projeto/?idPronac=' . $idPronacHash,
                'ajax' => true,
                'grupo' => []
            ];

            $menu['analiseaprovacao']['submenu'][] = [
                'label' => 'Aprova&ccedil;&atilde;o',
                'title' => 'Ir para Aprova&ccedil;&atilde;o',
                'link' => '/default/consultardadosprojeto/aprovacao/?idPronac=' . $idPronacHash,
                'ajax' => true,
                'grupo' => []
            ];

            $menu['analiseaprovacao']['submenu'][] = [
                'label' => 'Recursos',
                'title' => 'Ir para Recursos',
                'link' => '/default/consultardadosprojeto/recurso/?idPronac=' . $idPronacHash,
                'ajax' => true,
                'grupo' => []
            ];

            $menu['analiseaprovacao']['submenu'][] = [
                'label' => 'Recursos',
                'title' => 'Ir para Recursos',
                'link' => '/default/consultardadosprojeto/recurso/?idPronac=' . $idPronacHash,
                'ajax' => true,
                'grupo' => []
            ];
        }

        # Execução
        if ($this->fnLiberarLinks['Execucao'] || $this->usuarioInterno || $debug) {

            $menu['execucao'] = [
                'id' => 'execucao',
                'label' => 'Execu&ccedil;&atilde;o',
                'title' => 'Menu Execu&ccedil;&atilde;o',
                'link' => '',
                'ajax' => false,
                'icon' => 'play_for_work',
                'submenu' => '',
                'grupo' => []
            ];

            if (in_array($this->fnLiberarLinks['FaseDoProjeto'], array('2', '3', '4', '5')) || $this->usuarioInterno || $debug) {

                $menu['execucao']['submenu'][] = [
                    'label' => 'Dados banc&aacute;rios',
                    'title' => 'Ir para Dados banc&aacute;rios',
                    'link' => '/default/consultardadosprojeto/dados-bancarios/?idPronac=' . $idPronacHash,
                    'ajax' => false,
                    'grupo' => []
                ];

                $menu['execucao']['submenu'][] = [
                    'label' => 'Dados da fiscaliza&ccedil;&atilde;o',
                    'title' => 'Ir para Dados da fiscaliza&ccedil;&atilde;o',
                    'link' => '/default/consultardadosprojeto/dados-fiscalizacao/?idPronac=' . $idPronacHash,
                    'ajax' => true,
                    'grupo' => []
                ];

            }

            $menu['execucao']['submenu'][] = [
                'label' => 'Dados das readequa&ccedil;&otilde;es',
                'title' => 'Ir para Dados das readequa&ccedil;&otilde;es',
                'link' => '/default/consultardadosprojeto/readequacoes/?idPronac=' . $idPronacHash,
                'ajax' => true,
                'grupo' => []
            ];

            $menu['execucao']['submenu'][] = [
                'label' => 'Marcas Anexadas',
                'title' => 'Ir para Marcas Anexadas',
                'link' => '/default/consultardadosprojeto/marcas-anexadas/?idPronac=' . $idPronacHash,
                'ajax' => true,
                'grupo' => []
            ];

            $menu['execucao']['submenu'][] = [
                'label' => 'Pedido de Prorroga&ccedil;&atilde;o',
                'title' => 'Ir para Pedido de Prorroga&ccedil;&atilde;o',
                'link' => '/default/consultardadosprojeto/pedido-prorrogacao/?idPronac=' . $idPronacHash,
                'ajax' => true,
                'grupo' => []
            ];
        }

        # Prestação de contas
        if ($this->fnLiberarLinks['PrestacaoContas'] || $this->usuarioInterno || $debug) {
            $menu['prestacaodecontas'] = [
                'id' => 'prestacaodecontas',
                'label' => 'Presta&ccedil;&atilde;o de Contas',
                'title' => 'Menu Presta&ccedil;&atilde;o de Contas',
                'link' => '',
                'ajax' => false,
                'icon' => 'list_alt',
                'submenu' => '',
                'grupo' => []
            ];

            $menu['prestacaodecontas']['submenu'][] = [
                'label' => 'Extrato Banc&aacute;rio',
                'title' => 'Ir para Extrato Banc&aacute;rio',
                'link' => '/default/consultardadosprojeto/extratos-bancarios/?idPronac=' . $idPronacHash,
                'ajax' => false,
                'grupo' => []
            ];

            $menu['prestacaodecontas']['submenu'][] = [
                'label' => 'Concilia&ccedil;&atilde;o Banc&aacute;ria',
                'title' => 'Ir para Concilia&ccedil;&atilde;o Banc&aacute;ria',
                'link' => '/default/consultardadosprojeto/conciliacao-bancaria/?idPronac=' . $idPronacHash,
                'ajax' => true,
                'grupo' => []
            ];

            $menu['prestacaodecontas']['submenu'][] = [
                'label' => 'Execu&ccedil;&atilde;o da receita e despesa',
                'title' => 'Ir para Execu&ccedil;&atilde;o da receita e despesa',
                'link' => '/default/consultardadosprojeto/execucao-receita-despesa/?idPronac=' . $idPronacHash,
                'ajax' => true,
                'grupo' => []
            ];

            $menu['prestacaodecontas']['submenu'][] = [
                'label' => 'Relat&oacute;rio f&iacute;sico',
                'title' => 'Ir para Relat&oacute;rio f&iacute;sico',
                'link' => '/default/consultardadosprojeto/relatorio-fisico/?idPronac=' . $idPronacHash,
                'ajax' => true,
                'grupo' => []
            ];

            $menu['prestacaodecontas']['submenu'][] = [
                'label' => 'Pagamentos Consolidados',
                'title' => 'Ir para Pagamentos Consolidados',
                'link' => '/default/consultardadosprojeto/pagamentos-consolidados-por-uf-municipio/?idPronac=' . $idPronacHash,
                'ajax' => true,
                'grupo' => []
            ];

            if (in_array($this->fnLiberarLinks['FaseDoProjeto'], array('2', '3', '4', '5')) || $this->usuarioInterno || $debug) {
                $menu['prestacaodecontas']['submenu'][] = [
                    'label' => 'Relat&oacute;rios trimestrais',
                    'title' => 'Ir para Relat&oacute;rios trimestrais',
                    'link' => '/default/consultardadosprojeto/relatorios-trimestrais/?idPronac=' . $idPronacHash,
                    'ajax' => true,
                    'grupo' => []
                ];
            }

            if (in_array($this->fnLiberarLinks['FaseDoProjeto'], array('4', '5')) || $this->usuarioInterno || $debug) {
                $menu['prestacaodecontas']['submenu'][] = [
                    'label' => 'Relat&oacute;rio de cumprimento do objeto',
                    'title' => 'Ir para Relat&oacute;rio de cumprimento do objeto',
                    'link' => '/default/consultardadosprojeto/relatorio-final/?idPronac=' . $idPronacHash,
                    'ajax' => true,
                    'grupo' => []
                ];
            }
        }

        # Readequacao
        if ($this->usuarioExterno && ($this->fnLiberarLinks['Readequacao'] || $this->fnLiberarLinks['Readequacao_50']) || $debug) {
            $menu['readequacao'] = [
                'id' => 'readequacao',
                'label' => 'Readequa&ccedil;&atilde;o',
                'title' => 'Menu Readequa&ccedil;&atilde;o',
                'link' => '',
                'ajax' => false,
                'icon' => 'update',
                'submenu' => '',
                'grupo' => []
            ];

            $menu['readequacao']['submenu'][] = [
                'label' => 'Local de realiza&ccedil;&atilde;o',
                'title' => 'Readequar Local de realizaca&ccedil;&atilde;o',
                'link' => '/readequacao/local-realizacao/index/?idPronac=' . $idPronacHash,
                'ajax' => false,
                'grupo' => []
            ];

            if ($this->fnLiberarLinks['ReadequacaoPlanilha'] || $debug) {

                $menu['readequacao']['submenu'][] = [
                    'label' => 'Planilha orçament&aacute;ria',
                    'title' => 'Readequar Planilha orçament&aacute;ria',
                    'link' => '/readequacao/readequacoes/planilha-orcamentaria/?idPronac=' . $idPronacHash,
                    'ajax' => false,
                    'grupo' => []
                ];
            }

            $menu['readequacao']['submenu'][] = [
                'label' => 'Plano de Distribui&ccedil;&atilde;o',
                'title' => 'Readequar Plano de Distribui&ccedil;&atilde;o',
                'link' => '/readequacao/plano-distribuicao/index/?idPronac=' . $idPronacHash,
                'ajax' => false,
                'grupo' => []
            ];

            if ($this->fnLiberarLinks['Readequacao_50'] || $debug) {
                $menu['readequacao']['submenu'][] = [
                    'label' => 'Remanejamento &le; 50%',
                    'title' => 'Readequar Remanejamento &le; 50%',
                    'link' => '/readequacao/remanejamento-menor/index/?idPronac=' . $idPronacHash,
                    'ajax' => false,
                    'grupo' => []
                ];
            }

            if ($this->fnLiberarLinks['ReadequacaoTransferenciaRecursos'] || $debug) {
                $menu['readequacao']['submenu'][] = [
                    'label' => 'Transfer&ecirc;ncia de recursos',
                    'title' => 'Readequar Transfer&ecirc;ncia de recursos',
                    'link' => '/readequacao/transferencia-recursos/index/?idPronac=' . $idPronacHash,
                    'ajax' => false,
                    'grupo' => []
                ];
            }

            $menu['readequacao']['submenu'][] = [
                'label' => 'Diversas',
                'title' => 'Readequa&ccedil;&otilde;es Diversas',
                'link' => '/readequacao/transferencia-recursos/index/?idPronac=' . $idPronacHash,
                'ajax' => false,
                'grupo' => []
            ];

        }

        if ($this->fnLiberarLinks['SolicitarProrrogacao'] || $debug) {
            $menu['prorrogacao'] = [
                'id' => 'prorrogacao',
                'label' => 'Solicitar Prorroga&ccedil;&atilde;o',
                'title' => 'Menu Solicitar Prorroga&ccedil;&atilde;o',
                'link' => '/default/solicitarprorrogacao/index/idpronac/' . $idPronacHash,
                'ajax' => false,
                'icon' => 'av_timer',
                'submenu' => '',
                'grupo' => []
            ];
        }

        if ($this->isAdequarARealidade || $debug) {
            $menu['adequacao'] = [
                'id' => 'prorrogacao',
                'label' => 'Adequar &agrave; realidade',
                'title' => 'Adequar &agrave; realidade ou Encaminhar projeto adequado para o MinC',
                'link' => 'proposta/manterpropostaincentivofiscal/identificacaodaproposta/idPreProjeto/' . $this->projeto->idProjeto,
                'ajax' => false,
                'icon' => 'timer',
                'submenu' => '',
                'grupo' => []
            ];
        }

        return $menu;
    }

}
