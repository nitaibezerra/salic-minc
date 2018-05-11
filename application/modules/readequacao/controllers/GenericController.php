<?php

abstract class Readequacao_GenericController extends MinC_Controller_Action_Abstract
{
    protected $idAgente = 0;
    protected $idUsuario = 0;
    protected $idOrgao = 0;
    protected $idPerfil = 0;

    protected $idPronac;
    protected $projeto;

    public function init()
    {
        parent::init();
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $this->idOrgao = $GrupoAtivo->codOrgao;
        $this->idPerfil = $GrupoAtivo->codGrupo;
        $this->view->usuarioInterno = false;

        // verifica as permissoes
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = Autenticacao_Model_Grupos::PROPONENTE;
        $PermissoesGrupo[] = Autenticacao_Model_Grupos::PRESIDENTE_VINCULADA_SUBSTITUTO;
        $PermissoesGrupo[] = Autenticacao_Model_Grupos::DIRETOR_DEPARTAMENTO;

        $auth = Zend_Auth::getInstance();

        if (!$auth->hasIdentity()) {
            $url = Zend_Controller_Front::getInstance()->getBaseUrl();
            $this->redirect($url);
        }

        if (isset($auth->getIdentity()->usu_codigo)) { // autenticacao novo salic
            $this->view->usuarioInterno = true;
            $this->idUsuario = $auth->getIdentity()->usu_codigo;

            $Usuario = new Autenticacao_Model_Usuario(); // objeto usu�rio
            $grupos = $Usuario->buscarUnidades($auth->getIdentity()->usu_codigo, 21);
            foreach ($grupos as $grupo) {
                $PermissoesGrupo[] = $grupo->gru_codigo;
            }

            parent::perfil(1, $PermissoesGrupo);
            $this->idAgente = UsuarioDAO::getIdUsuario($auth->getIdentity()->usu_codigo);
            $this->idAgente = ($this->idAgente) ? $this->idAgente["idAgente"] : 0;
        } else { // autenticacao scriptcase
            parent::perfil(4, $PermissoesGrupo);
            $this->idUsuario = (isset($_GET["idusuario"])) ? $_GET["idusuario"] : 0;

            $this->verificarPermissaoAcesso(false, true, false);
        }

        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }
        $this->view->idPronac = $idPronac;
        $this->idPronac = $idPronac;

        if($idPronac) {
            $tbProjetos = new Projeto_Model_DbTable_Projetos();
            $this->projeto = (new Projeto_Model_TbProjetos($tbProjetos->findBy(['idPronac' => $idPronac])));
        }
    }
}