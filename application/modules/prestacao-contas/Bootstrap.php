<?php
class PrestacaoContas_Bootstrap extends Zend_Application_Module_Bootstrap
{
    public function _initREST()
    {
        $frontController = Zend_Controller_Front::getInstance();

        /* $route = new Zend_Controller_Router_Route( */
        /*     'prestacao-contas/comprovante-pagamento/index/*', */
        /*     [ */
        /*         'module' => 'prestacao-contas', */
        /*         'controller' => 'comprovante-pagamento', */
        /*         'action'     => 'index' */
        /*     ] */
        /* ); */

        /* $frontController->getRouter()->addRoute('comprovacao-pagamento', $route); */

        $restRoute = new Zend_Rest_Route(
            $frontController,
            [],
            [
                'prestacao-contas' => [
                    'comprovante-pagamento',
                    'planilha-aprovacao'
                ]
            ]
        );

        $frontController->getRouter()->addRoute('rest', $restRoute);
    }
}
