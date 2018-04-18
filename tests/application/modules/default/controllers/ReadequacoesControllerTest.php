<?php
/**
 * ReadequacoesControllerTest
 *
 * @package
 * @author
 */
class ReadequacoesControllerTest extends MinC_Test_ControllerActionTestCase
{
    public function setUp()
    {
        parent::setUp();
        
        $Readequacao_Model_tbReadequacao = new Readequacao_Model_tbReadequacao();
        $this->idPronac = $this->getProjetoAptoReadequacao();

        $projetos = new Projetos();
        $projeto = $projetos->buscar(
            array(
                'IdPRONAC = ?' => $this->idPronac
            )
        )->current();
        
        $this->pronac = $projeto->AnoProjeto . $projeto->Sequencial;
        
        $this->hashPronac = Seguranca::encrypt($this->idPronac);
        
        $this->autenticar();
        
        $this->resetRequest()
            ->resetResponse();
        
        $this->perfilParaProponente();
        
        $this->resetRequest()
            ->resetResponse();
        
    }

    private function getProjetoAptoReadequacao()
    {
        $projetos = new Projetos();

        $select = $projetos->select();
        
        $select->setIntegrityCheck(false);
        $select->from(
            array('p' => 'projetos'),
            'p.IdPRONAC AS idPronac'
        );

        $select->joinLeft(
            array('l' => 'liberacao'),
            'l.AnoProjeto = p.AnoProjeto AND l.Sequencial = p.Sequencial',
            array(''),
            'sac.dbo'
        );
        $select->where(new Zend_Db_Expr('p.DtInicioExecucao < GETDATE()'));
        $select->where(new Zend_Db_Expr('p.DtFimExecucao > GETDATE()'));
        $select->where('p.AnoProjeto > ?', 16);
        $select->where('p.AnoProjeto < ?', 90);
        $select->limit(30);
        
        $result = $projetos->fetchAll($select);
        
        $Readequacao_Model_tbReadequacao = new Readequacao_Model_tbReadequacao();
        $tbCumprimentoObjeto = new tbCumprimentoObjeto();
        foreach ($result as $item) {
            
            $existeReadequacaoEmAndamento = $Readequacao_Model_tbReadequacao->existeReadequacaoEmAndamento($item->idPronac);
            $existeReadequacaoPlanilhaEmEdicao = $Readequacao_Model_tbReadequacao->existeReadequacaoPlanilhaEmEdicao($item->idPronac);
            $existeReadequacaoParcialEmEdicao = $Readequacao_Model_tbReadequacao->existeReadequacaoParcialEmEdicao($item->idPronac);
            $possuiRelatorioDeCumprimento = $tbCumprimentoObjeto->possuiRelatorioDeCumprimento($item->idPronac);
            
            $Readequacao = false;
            if (!$existeReadequacaoEmAndamento && !$existeReadequacaoEmAndamento) {
                $Readequacao = true;
            } else if ($existeReadequacaoEmAndamento && $existeReadequacaoPlanilhaEmEdicao) {
                $ReadequacaoPlanilha = true;
            } else if ($existeReadequacaoEmAndamento && $existeReadequacaoParcialEmEdicao) {
                $Readequacao = false;
            } else if ($existeReadequacaoEmAndamento && !$existeReadequacaoPlanilhaEmEdicao)  {
                $Readequacao = false;
            }
            if ($possuiRelatorioDeCumprimento) {
                $Readequacao = false;
            }
            
            if ($Readequacao) {
                $idPronac = $item->idPronac;
                break;
            }                
        }
        return $idPronac;
    }
    
    /**
     * TestIndexAction
     *
     * @access public
     * @return void
     */    
    public function testIndexAction()
    {
        $this->dispatch('/readequacoes?idPronac=' . $this->hashPronac);
        $this->assertUrl('default','readequacoes', 'index');
    }

    /**
     * TestIndexIdPassandoUfAction
     *
     * @access public
     * @return void
     */    
    public function TestIndexIdPassandoUfAction()
    {
        $iduf = 43; // RS
        
        $this->request->setMethod('POST')
            ->setPost([
                'iduf' => $iduf
            ]);        
        $this->dispatch('/readequacoes?idPronac=' . $this->hashPronac);
        $this->assertUrl('default', 'readequacoes', 'index');
    }
    
    /**
     * TestPlanilhaOrcamentariaAction
     *
     * @access public
     * @return void
     */    
    public function testPlanilhaOrcamentariaAction()
    {
        $this->dispatch('/readequacoes/planilha-orcamentaria?idPronac=' . $this->hashPronac);
        $this->assertUrl('default','readequacoes', 'planilha-orcamentaria');
    }

    /**
     * TestPlanilhaOrcamentariaCondicoesNaoSatisfeitas()
     *
     * @access public
     * @return void
     */    
    public function testPlanilhaOrcamentariaCondicoesNaoSatisfeitas()
    {
        $Readequacao_Model_tbReadequacao = new Readequacao_Model_tbReadequacao();
        $possuiReadequacao = $Readequacao_Model_tbReadequacao->existeReadequacaoEmAndamento($this->idPronac);
        
        $this->assertFalse($possuiReadequacao);

    }

    /**
     * TestPainelAction
     * 
     * @access public
     * @return void
     */
    public function testPainelAction()
    {
        $this->alterarPerfil(Autenticacao_Model_Grupos::COORDENADOR_ACOMPANHAMENTO, Orgaos::ORGAO_GEAR_SACAV);
        
        $this->dispatch('/readequacoes/painel');
        $this->assertUrl('default','readequacoes', 'painel');
        
    }

    /**
     * TestPainelEmAnaliseAction
     * 
     * @access public
     * @return void
     */
    public function testPainelEmAnaliseAction()
    {
        $this->alterarPerfil(Autenticacao_Model_Grupos::COORDENADOR_ACOMPANHAMENTO, Orgaos::ORGAO_GEAR_SACAV);
        
        $this->dispatch('/readequacoes/painel?pronac=&qtde=10&tipoFiltro=em_analise');
        $this->assertUrl('default','readequacoes', 'painel');
        
    }

    /**
     * TestPainelEmAnalisadosAction
     * 
     * @access public
     * @return void
     */
    public function testPainelEmAnalisadosAction()
    {
        $this->alterarPerfil(Autenticacao_Model_Grupos::COORDENADOR_ACOMPANHAMENTO, Orgaos::ORGAO_GEAR_SACAV);
        
        $this->dispatch('/readequacoes/painel?pronac=&qtde=10&tipoFiltro=em_analisados');
        $this->assertUrl('default','readequacoes', 'painel');
        
    }

    /**
     * TestPainelAguardandoPublicacaoAction
     * 
     * @access public
     * @return void
     */
    public function testPainelAguardandoPublicacaoAction()
    {
        $this->alterarPerfil(Autenticacao_Model_Grupos::COORDENADOR_ACOMPANHAMENTO, Orgaos::ORGAO_GEAR_SACAV);
        
        $this->dispatch('/readequacoes/painel?pronac=&qtde=10&tipoFiltro=aguardando_publicacao');
        $this->assertUrl('default','readequacoes', 'painel');
        
    }

    /**
     * TestPainelAnalisadosPronac
     * 
     * @access public
     * @return void
     */
    public function TestPainelAnalisadosPronacAction()
    {
        $this->alterarPerfil(Autenticacao_Model_Grupos::COORDENADOR_ACOMPANHAMENTO, Orgaos::ORGAO_GEAR_SACAV);
        
        $idPronac = 154566;
        $this->dispatch('/readequacoes/painel?pronac=' . $idPronac . '&qtde=10&tipoFiltro=aguardando_publicacao');
        $this->assertUrl('default','readequacoes', 'painel');        

    }

    public function testPainelReadequacoesAction()
    {
        $this->dispatch('/readequacoes/painel-readequacoes?idPronac=' . $this->idPronac);
        $this->assertUrl('default', 'readequacoes', 'painel-readequacoes');
    }

    public function testPainelReadequacoesBuscaPronacAction()
    {
        $this->dispatch('/readequacoes/painel-readequacoes?pronac=' . $this->pronac . '&qtde=10&tipoFiltro=aguardando_distribuicao');
        $this->assertUrl('default', 'readequacoes', 'painel-readequacoes');
    }    
    
    public function testVisualizarReadequacaoAction()
    {
        $this->alterarPerfil(Autenticacao_Model_Grupos::COORDENADOR_ACOMPANHAMENTO, Orgaos::ORGAO_GEAR_SACAV);
        $this->dispatch('/readequacoes/visualizar-readequacao?idPronac=' . $this->idPronac);
        $this->assertUrl('default', 'readequacoes', 'visualizar-readequacao');
    }

    public function testCarregarValorEntrePlanilhasAction()
    {
        $this->alterarPerfil(Autenticacao_Model_Grupos::COORDENADOR_ACOMPANHAMENTO, Orgaos::ORGAO_GEAR_SACAV);
        $this->dispatch('/readequacoes/carregar-valor-entre-planilhas?idPronac=' . $this->idPronac);
        $this->assertUrl('default', 'readequacoes', 'carregar-valor-entre-planilhas');
    }

    public function testAvaliarReadequacaoAction()
    {
        $this->alterarPerfil(Autenticacao_Model_Grupos::COORDENADOR_ACOMPANHAMENTO, Orgaos::ORGAO_GEAR_SACAV);
        $this->dispatch('/readequacoes/avaliar-readequacao?idPronac=' . $this->hashPronac);
        $this->assertUrl('default', 'readequacoes', 'avaliar-readequacao');
    }

    public function testAvaliarReadequacaoTecnicoAction()
    {
        $this->alterarPerfil(Autenticacao_Model_Grupos::TECNICO_ACOMPANHAMENTO, Orgaos::ORGAO_GEAR_SACAV);
        $this->dispatch('/readequacoes/avaliar-readequacao?idPronac=' . $this->hashPronac);
        $this->assertUrl('default', 'readequacoes', 'avaliar-readequacao');
    }

    public function testEncaminharReadequacaoTecnicoAction()
    {
        $this->alterarPerfil(Autenticacao_Model_Grupos::TECNICO_ACOMPANHAMENTO, Orgaos::ORGAO_GEAR_SACAV);
        $this->dispatch('/readequacoes/encaminhar-analise-tecnica?id=' . $this->hashPronac . '&filtro=painel_do_tecnico');
        $this->assertUrl('default', 'readequacoes', 'encaminhar-analise-tecnica');
    }

    public function testFormAvaliarReadequacaoTecnicoAction()
    {
        $this->alterarPerfil(Autenticacao_Model_Grupos::TECNICO_ACOMPANHAMENTO, Orgaos::ORGAO_GEAR_SACAV);
        $this->dispatch('/readequacoes/form-avaliar-readequacao?id=' . $this->hashPronac . '&filtro=painel_do_tecnico');
        $this->assertUrl('default', 'readequacoes', 'form-avaliar-readequacao');
    }
}