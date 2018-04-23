<?php

final class Manutencao_AtualizacaoController extends Manutencao_GenericController
{

    private static $repositorio = 'git://github.com/culturagovbr/salic-minc.git';

    public function atualizacaoDeVersaoAction(){
        $this->view->ambiente = self::ambienteAtual();

        if (self::ambienteAtual() == 'staging') {
            $this->view->ultimaVersaoRemota = self::ultimaVersaoHashRemoto();
//            xd($this->view->ultimaVersaoRemota);
            $this->view->ultimaVersaoLocal = self::ultimaVersaoHashLocal();
        }else{
            $this->view->ultimaVersaoRemota = self::ultimaVersaoTagRemota();
            $this->view->ultimaVersaoLocal = self::ultimaVersaoTagLocal();
        }
    }

    public function atualizarSistemaAction(){
        try{
            $curl_command="curl -X POST jenkins.cultura.gov.br/view/Salic/job/SalicHomologacaoPipeline/buildWithParameters?token=790d6d8175da3e7a3ddce28344678696&containerApplicationEnviroment=development&branch=hmg";
            exec($curl_command);

            $this->_helper->json(['success' => true]);
        }catch(Exception $e){
            $this->_helper->json(['success' => false]);
        }
    }

    private static function ultimaVersaoTagRemota(){
        $command = "git ls-remote --tags " . self::$repositorio . " | awk -F/ '{ print $3 }' | tail -n 1";
        return exec($command);
    }

    private static function ultimaVersaoTagLocal(){
        $command = "git describe --abbrev=0 --tags";
        return exec($command);
    }

    private static function ambienteAtual(){
        return getenv('APPLICATION_ENV');
    }

    private static function ultimaVersaoHashRemoto(){
        $actualBranch =  exec("git rev-parse --abbrev-ref HEAD");
        $command = "git ls-remote " . self::$repositorio . " {$actualBranch} HEAD | awk '{ print $1 }' | tail -n 1";
        return exec($command);
    }

    private static function ultimaVersaoHashLocal(){
        $command = "git rev-parse HEAD";
        return exec($command);
    }

}