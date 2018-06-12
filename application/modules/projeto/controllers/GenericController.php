<?php

abstract class Projeto_GenericController extends MinC_Controller_Action_Abstract
{
    private $idUsuarioExterno;
    private $idUsuarioInterno;
    private $idAgente = 0;
    private $cpfLogado;
    private $agente;
    private $isProponente = false;
    private $usuarioExterno;
    private $usuarioInterno;
    private $usuario;
    private $autenticacao;

    public function init()
    {
        parent::init();

        $this->autenticacao = array_change_key_case((array)Zend_Auth::getInstance()->getIdentity());

        $arrIdentity = array_change_key_case((array)Zend_Auth::getInstance()->getIdentity());
        $this->cpfLogado = isset($arrIdentity['usu_codigo']) ? $arrIdentity['usu_identificacao'] : $arrIdentity['cpf'];

        if (is_null($this->cpfLogado)) {
            $this->redirect('/');
        }

        // Busca na SGCAcesso
        $modelSgcAcesso = new Autenticacao_Model_Sgcacesso();
        $this->usuarioExterno = $modelSgcAcesso->findBy(array('cpf' => $this->cpfLogado));

        // Busca na Usuarios
        $usuarioDAO = new Autenticacao_Model_DbTable_Usuario();
        $this->usuarioInterno = $usuarioDAO->findBy(array('usu_identificacao' => $this->cpfLogado));

        // Busca na Agentes
        $tableAgentes = new Agente_Model_DbTable_Agentes();
        $this->agente = $tableAgentes->findBy(array('cnpjcpf' => trim($this->cpfLogado)));

        if ($this->usuarioExterno) $this->idUsuarioExterno = $this->usuarioExterno['idusuario'];
        if ($this->agente) $this->idAgente = $this->agente['idagente'];
        if ($this->usuarioInterno) $this->idUsuarioInterno = $this->usuarioInterno['usu_codigo'];
        if ($this->idAgente != 0) $this->isProponente = true;

        $this->usuario = empty($this->usuarioInterno) ? $this->usuarioExterno : $this->usuarioInterno;

    }
}
