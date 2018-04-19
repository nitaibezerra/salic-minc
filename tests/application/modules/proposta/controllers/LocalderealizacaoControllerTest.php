<?php
/**
 * LocalDeRealizacaoControllerTest
 * @author wouerner <wouerner@gmail.com>
 * @link http://www.cultura.gov.br
 */

class LocalderealizacaoControllerTest extends MinC_Test_ControllerActionTestCase
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

    public function testIndex()
    {
        $this->dispatch('proposta/localderealizacao?idPreProjeto='. $this->idPreProjeto);

        $this->assertModule('proposta', 'proposta');
        $this->assertController('localderealizacao');
        $this->assertAction('index');
    }

    public function testLocalderealizacaoAction()
    {
        $this->dispatch('/proposta/localderealizacao/index/idPreProjeto/' . $this->idPreProjeto);
        $this->assertUrl('proposta','localderealizacao', 'index');
    }

    public function testLocalderealizacaoNovoAction()
    {
        $this->dispatch('/proposta/localderealizacao/form-inserir/idPreProjeto/' . $this->idPreProjeto);
        $this->assertUrl('proposta','localderealizacao', 'form-inserir');
    }

    public function testLocalderealizacaoEditarAction()
    {
        $get = Zend_Registry::get('get');
        $idAbrangencia = $get->cod;

        $this->dispatch('/proposta/localderealizacao/form-local-de-realizacao/idPreProjeto/' . $this->idPreProjeto . '?cod=' . $idAbrangencia);
        $this->assertUrl('proposta','localderealizacao', 'form-local-de-realizacao');
    }        

}
