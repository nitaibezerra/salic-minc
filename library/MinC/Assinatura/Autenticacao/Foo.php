<?php

class MinC_Assinatura_Autenticacao_Foo implements MinC_Assinatura_Autenticacao_IAutenticacaoAdapter
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
        throw new Exception ("Implementar esse m&eacute;todo.");
    }

    /**
     * @return array
     */
    public function obterInformacoesAssinante()
    {
        throw new Exception ("Implementar esse m&eacute;todo.");
    }

    /**
     * @return string
     * @todo melhorar meneira de obtenção dos templates de tipos de documento passando como parametro
     * pelo application.ini
     */
    public function obterTemplateAutenticacao()
    {
        $view = new Zend_View();
        $view->setScriptPath(APPLICATION_PATH . '/../library/MinC/Assinatura/Autenticacao/templates');
        return $view->render('foo.phtml');
    }
}