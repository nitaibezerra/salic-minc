<?php

class MinC_Assinatura_Autenticacao_Padrao implements MinC_Assinatura_Autenticacao_IAutenticacaoAdapter
{
    /**
     * @var Autenticacao_Model_DbTable_Usuario $usuario
     */
    private $usuario;

    public function __construct($post, $identidadeUsuarioLogado)
    {
        $this->usuario = new Autenticacao_Model_DbTable_Usuario();
        $this->usuario->setUsuIdentificacao($identidadeUsuarioLogado->usu_identificacao);
        $this->usuario->setUsuSenha($post['password']);
    }

    /**
     * @return boolean
     */
    public function autenticar()
    {
        $isUsuarioESenhaValidos = $this->usuario->isUsuarioESenhaValidos();
        if ($isUsuarioESenhaValidos) {
            return true;
        }
    }

    /**
     * @return array
     */
    public function obterInformacoesAssinante()
    {
        $usuariosBuscar = $this->usuario->buscar(array('usu_identificacao = ?' => $this->usuario->getUsuIdentificacao()))->current();
        return $usuariosBuscar->toArray();
    }

    /**
     * @return string
     * @todo melhorar meneira de obtenção dos templates de tipos de documento passando como parametro
     * pelo application.ini
     */
    public function obterTemplateAutenticacao()
    {
        $view = new Zend_View();
        $view->setScriptPath(__DIR__ . '/templates');
        return $view->render('padrao.phtml');
    }
}