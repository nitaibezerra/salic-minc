<?php

namespace MinC\Assinatura\Autenticacao;

interface IAdapter
{
    /**
     * @return boolean
     */
    public function autenticar();

    /**
     * @return array
     */
    public function obterInformacoesAssinante();

    /**
     * @return array
     */
    public function obterTemplateAutenticacao();

}