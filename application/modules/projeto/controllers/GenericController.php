<?php

abstract class Projeto_GenericController extends MinC_Controller_Action_Abstract
{
    private $idUsuarioExterno = 0;
    private $idUsuarioInterno = 0;
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

        $this->cpfLogado = isset($this->autenticacao['usu_codigo']) ? $this->autenticacao['usu_identificacao'] : $this->autenticacao['cpf'];

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

        $this->view->usuario = !empty($this->usuarioExterno) ? $this->usuarioExterno : $this->usuarioInterno;
        $this->view->idAgente = $this->idAgente;
        $this->view->idUsuario = !empty($this->idUsuarioExterno) ? $this->idUsuarioExterno : $this->idUsuarioInterno;

        $PermissoesGrupo = [];
        if (isset($this->idUsuarioInterno)) {
            $Usuario = new Autenticacao_Model_DbTable_Usuario();
            $grupos = $usuarioDAO->buscarUnidades($this->idUsuarioInterno, 21);
            foreach ($grupos as $grupo) {
                $PermissoesGrupo[] = $grupo->gru_codigo;
            }
        }

        ($this->idUsuarioInterno) ? parent::perfil(1, $PermissoesGrupo) : parent::perfil(4, $PermissoesGrupo);
    }
}
