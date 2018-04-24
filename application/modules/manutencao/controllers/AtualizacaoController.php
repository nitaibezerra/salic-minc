<?php

final class Manutencao_AtualizacaoController extends Manutencao_GenericController
{

    private static $repositorio = 'git://github.com/culturagovbr/salic-minc.git';

    public function atualizacaoDeVersaoAction(){
        $this->view->ambiente = self::ambienteAtual();

        if (self::ambienteAtual() == 'staging') {
            $this->view->ultimaVersaoRemota = self::ultimaVersaoHashRemoto();
            $this->view->ultimaVersaoLocal = self::ultimaVersaoHashLocal();
        }else{
            $this->view->ultimaVersaoRemota = self::ultimaVersaoTagRemota();
            $this->view->ultimaVersaoLocal = self::ultimaVersaoTagLocal();
        }
    }

    public function atualizarSistemaAction(){
        try{
            $jenkinsCommand = $this->gerarUrlBuildJenkins();
            exec($jenkinsCommand);
            $this->_helper->json(['success' => true]);
        }catch(Exception $e){
            $this->_helper->json(['success' => false]);
        }
    }

    private function gerarUrlBuildJenkins(){
        $configJenkins = self::obterConfiguracoesJenkins();
        $curlCommand = "curl -X POST ";
        $build = '';
        $job = '';
        $tipoJob = 'buildWithParameters?';
        $jenkinsUrl = $configJenkins['url'];

        $parametrosBuild = [
            'token' => $configJenkins['token'],
            'containerApplicationEnviroment' => 'development',
            'branch' => 'hmg'
        ];

        $parametrosJob = [
            'view' => 'Salic',
            'job' => 'SalicHomologacaoPipeline'
        ];

        foreach ($parametrosJob as $param => $value) {
            $job .= "{$param}/{$value}/";
        }

        foreach ($parametrosBuild as $param => $value) {
            if($param == 'token'){
                $build .= "{$param}={$value}";

            }else{
                $build .= "&{$param}={$value}";
            }
        }

        $deployCommand = $curlCommand . $jenkinsUrl . $job . $tipoJob . $build;

        return $deployCommand;
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

    private static function obterConfiguracoesJenkins(){
        return Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('jenkins');
    }

}