<?php

class Projeto_Model_DbTable_Enquadramento extends MinC_Db_Table_Abstract
{
    protected $_schema = 'sac';
    protected $_name = 'Enquadramento';
    protected $_primary = 'IdEnquadramento';

    public function obterProjetoAreaSegmento($where, $order = array())
    {
        if (empty($where) || !is_array($where)) {
            return [];
        }

        $sql = $this->select()
            ->setIntegrityCheck(false)
            ->from(
                ['a' => 'Projetos'],
                [
                    'a.IdPRONAC',
                    'a.NomeProjeto',
                    'a.CgcCpf AS CNPJCPF',
                    'a.UfProjeto',
                    'a.DtInicioExecucao',
                    'a.DtFimExecucao',
                    'a.ResumoProjeto',
                    'a.OrgaoOrigem',
                    'a.SolicitadoReal AS VlSolicitado',
                    'a.SolicitadoReal AS CustoProjeto',
                    new Zend_Db_Expr('a.AnoProjeto + a.Sequencial AS Pronac'),
                    new Zend_Db_Expr('dbo.fnNomeDoProponente(a.IdPRONAC) AS Proponente'),
                    new Zend_Db_Expr('sac.dbo.fnOutrasFontes(a.IdPRONAC) AS VlOutrasFontes'),
                    new Zend_Db_Expr('dbo.fnOutrasFontes(a.IdPRONAC) AS VlOutrasFontesAprovado'),
                    new Zend_Db_Expr('sac.dbo.fnValorDaProposta(a.idProjeto) AS VlProjeto'),
                    new Zend_Db_Expr('dbo.fnValorDaProposta(a.idProjeto) AS CustoTotal')
                ],
                $this->_schema
            );

        $sql->joinLeft(
            ['b' => $this->_name],
            'a.AnoProjeto = b.AnoProjeto AND a.Sequencial = b.Sequencial',
            [
                new Zend_Db_Expr("
                        CASE WHEN b.Enquadramento = '1' THEN 'Artigo 26' 
                        WHEN b.Enquadramento = '2' THEN 'Artigo 18' 
                        ELSE 'N&atilde;o enquadrado' END AS Enquadramento
                "),
                'b.DtEnquadramento',
                'b.Observacao AS AvaliacaoTecnica',
                'b.IdEnquadramento'
            ],
            $this->_schema
        );

        $sql->join(
            ['c' => 'Segmento'],
            'a.Segmento = c.Codigo',
            ['c.Descricao AS Segmento'],
            $this->_schema
        );

        $sql->join(
            ['d' => 'Area'],
            'a.Area = d.Codigo',
            ['d.Descricao AS Area'],
            $this->_schema
        );

        foreach ($where as $coluna => $valor) {
            $sql->where($coluna, $valor);
        }

        if ($order) {
            $sql->order($order);
        }
        return $this->fetchAll($sql);
    }

    public function obterProjetosApreciadosCnic($where, $order = [])
    {
        if (empty($where)) {
            return [];
        }

        $sql = $this->select()
            ->setIntegrityCheck(false)
            ->from(
                ['a' => 'Projetos'],
                [
                    new Zend_Db_Expr('a.AnoProjeto+a.Sequencial as Pronac'),
                    'a.IdPRONAC',
                    'a.NomeProjeto',
                    'a.DtInicioExecucao',
                    'a.DtFimExecucao',
                    'a.Orgao as idUnidade',
                    new Zend_Db_Expr('sac.dbo.fnTotalAprovadoProjeto(a.AnoProjeto,a.Sequencial) as vlHomologado')
                ],
                $this->_schema
            );

        $sql->join(
            ['b' => $this->_name],
            'a.IdPRONAC = b.IdPRONAC',
            [
                new Zend_Db_Expr("
                        CASE WHEN b.Enquadramento = '1' THEN 'Artigo 26' 
                        WHEN b.Enquadramento = '2' THEN 'Artigo 18' 
                        ELSE 'N&atilde;o enquadrado' END AS Enquadramento
                "),
                'b.DtEnquadramento'
            ],
            $this->_schema
        );

        $sql->joinLeft(
            ['c' => 'tbPauta'],
            'a.IdPRONAC = c.IdPRONAC',
            [],
            'BDCORPORATIVO.scSAC'
        );

        $sql->joinLeft(
            ['d' => 'tbReuniao'],
            'c.idNrReuniao = d.idNrReuniao',
            ['d.NrReuniao'],
            $this->_schema
        );

        $sql->join(
            ['e' => 'Orgaos'],
            'a.OrgaoOrigem = e.Codigo',
            ['e.idSecretaria AS idOrgaoSuperior'],
            $this->_schema
        );

        $sql->where('a.Situacao = ?', Projeto_Model_Situacao::PROJETO_APRECIADO_PELA_CNIC);

        foreach ($where as $coluna => $valor) {
            $sql->where($coluna, $valor);
        }

        if ($order) {
            $sql->order($order);
        }

        return $this->fetchAll($sql);
    }
}
