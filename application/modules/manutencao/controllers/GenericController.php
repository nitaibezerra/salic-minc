<?php

abstract class Manutencao_GenericController extends MinC_Controller_Action_Abstract
{
    public function init()
    {
        parent::init();

        $auth = Zend_Auth::getInstance();
        $PermissoesGrupo = array();

        //Da permissao de acesso a todos os grupos do usuario logado afim de atender o UC75
        if (isset($auth->getIdentity()->usu_codigo)) {
            //Recupera todos os grupos do Usuario
            $Usuario = new Autenticacao_Model_Usuario();
            $grupos = $Usuario->buscarUnidades($auth->getIdentity()->usu_codigo, 21);
            foreach ($grupos as $grupo) {
                $PermissoesGrupo[] = $grupo->gru_codigo;
            }

            if (empty($this->grupoAtivo)) {
                $this->grupoAtivo = new Zend_Session_Namespace('GrupoAtivo');
            }
        }

        parent::perfil(1, $PermissoesGrupo);
    }
}
