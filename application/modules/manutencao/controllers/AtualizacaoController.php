<?php

final class Manutencao_AtualizacaoController extends Manutencao_GenericController
{

    public function atualizacaoDeVersaoAction(){
        $post = Zend_Registry::get('post');
    }

    public function atualizarSistemaAction(){
        $this->_helper->json(array('resposta'=>true));
    }
}