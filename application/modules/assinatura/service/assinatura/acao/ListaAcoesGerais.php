<?php

namespace Application\Modules\Assinatura\Service\Assinatura\Acao;

use MinC\Assinatura\Acao\IListaAcoesGerais;
use \Assinatura_Model_DbTable_TbAssinatura as TbAssinaturaDbTable,
    Application\Modules\Readequacao\Service\Assinatura\Acao\ListaAcoesModulo as ListaAcoesReadequacao,
    Application\Modules\Admissibilidade\Service\Assinatura\Acao\ListaAcoesModulo as ListaAcoesEnquadramento;

class ListaAcoesGerais implements IListaAcoesGerais
{

    public function obterLista(): array
    {
        return [
            TbAssinaturaDbTable::TIPO_ATO_PARECER_TECNICO_READEQUACAO_DE_PROJETO => new ListaAcoesReadequacao(),
            TbAssinaturaDbTable::TIPO_ATO_PARECER_TECNICO_AJUSTE_DE_PROJETO => new ListaAcoesReadequacao(),
            TbAssinaturaDbTable::TIPO_ATO_READEQUACAO_XXXXXXXXXX => new ListaAcoesReadequacao(),
            TbAssinaturaDbTable::TIPO_ATO_ENQUADRAMENTO => new ListaAcoesEnquadramento(),
        ];
    }
}