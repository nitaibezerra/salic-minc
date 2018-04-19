<?php

final class Manutencao_AtualizacaoController extends Manutencao_GenericController
{

    public function atualizacaoDeVersaoAction(){
        $post = Zend_Registry::get('post');
        $this->view->ultimaVersaoRemota = self::ultimaVersaoRemota();
        $this->view->ultimaVersaoLocal = self::ultimaVersaoLocal();
    }

    public function atualizarSistemaAction(){
        try{
            $curl_command="curl -X POST jenkins.cultura.gov.br/view/Salic/job/SalicHomologacaoPipeline/buildWithParameters?token=790d6d8175da3e7a3ddce28344678696&containerApplicationEnviroment=development&branch=hmg";
            exec($curl_command);
            $this->_helper->json(array('deu certo?'=>true));
        }catch(Exception $e){
            $this->_helper->json(array('deu certo?'=>false));

        }
    }

    private static function ultimaVersaoRemota(){
        $command = "git ls-remote --tags git://github.com/culturagovbr/salic-minc.git | awk -F/ '{ print $3 }' | tail -n 1";
        return exec($command);
    }

    private static function ultimaVersaoLocal(){
        $command = "git describe --abbrev=0 --tags";
        return exec($command);
    }

}