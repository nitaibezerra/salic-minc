<?php
class MantertabelaitensController  extends MinC_Test_ControllerActionTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->idPreProjeto = $this->getIdPreProjeto();
        $this->autenticar();

        //reset para garantir respostas.
        $this->resetRequest()
            ->resetResponse();

        // trocar para perfil Proponente
        $this->perfilParaProponente();

        //reset para garantir respostas.
        $this->resetRequest()
            ->resetResponse();
    }

    private function getIdPreProjeto()
    {
        $config = new Zend_Config_Ini(
            APPLICATION_PATH . '/configs/application.ini',
            APPLICATION_ENV
        );

        $projetos = new Projetos();
        $select = $projetos->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('p' => 'PreProjeto'),
            'p.idPreProjeto AS idPreProjeto',
            'sac.dbo'
        );

        $select->joinInner(
            array('a' => 'Agentes'),
            'a.idAgente = p.idAgente',
            array(''),
            'agentes.dbo'
        );

        $select->where('p.stEstado = ?', 1);
        $select->where('a.cnpjcpf = ?', $config->test->params->login);
        $select->limit(1);

        $result = $projetos->fetchAll($select);
        if (count($result) > 0)
        {
            return $result[0]['idPreProjeto'];
        } else {
            return false;
        }
    }
    public function testIndexAction()
    {

        $url = '/proposta/mantertabelaitens/index?idPreProjeto='. $this->idPreProjeto;
        $this->request->setMethod('GET');
        $this->dispatch($url);

        $this->assertModule('proposta');
        $this->assertController('mantertabelaitens');
        $this->assertAction('index');
        $this->assertQuery('div.container-fluid div', 'Tabelas de Itens ');
    }

    public function testMinhasSolicitacoesAction()
    {

        $url = '/proposta/mantertabelaitens/minhas-solicitacoes?idPreProjeto='. $this->idPreProjeto;
        $this->request->setMethod('GET');
        $this->dispatch($url);

        $this->assertModule('proposta');
        $this->assertController('mantertabelaitens');
        $this->assertAction('minhas-solicitacoes');
        $this->assertQuery('div.container-fluid div', 'Minhas Solicitações');
    }
}
