Vue.component('plano-distribuicao-detalhamentos-visualizar', {
    template: `
    <div  class="plano-distribuicao-detalhamentos-visualizar">
        <ul class="collapsible" data-collapsible="expandable">
            <li v-for="( detalhamento, index ) in detalhamentos">
                <div class="collapsible-header">
                    <i class="material-icons">place</i>
                    Detalhamento - {{detalhamento[0].DescricaoUf}} - {{detalhamento[0].DescricaoMunicipio}}
                </div>
                <div class="collapsible-body no-padding margin20 scroll-x">
                    <table>
                        <thead v-if="detalhamento.length > 0">
                            <tr>
                                <th rowspan="2">Categoria</th>
                                <th rowspan="2">Qtd.</th>
                                <th class="center-align gratuito" rowspan="2">
                                    Dist. <br>Gratuita
                                </th>
                                <th class="center-align popular" colspan="3">
                                    Pre&ccedil;o Popular
                                </th>
                                <th class="center-align proponente" colspan="3">
                                    Proponente
                                </th>
                                <th rowspan="2" class="center-align">Receita <br> Prevista</th>
                            </tr>
                            <tr>
                                <th class="right-align popular">Qtd. Inteira</th>
                                <th class="right-align popular">Qtd. Meia</th>
                                <th class="right-align popular">Pre&ccedil;o <br> Unit&aacute;rio</th>
                                <th class="right-align proponente">Qtd. Inteira</th>
                                <th class="right-align proponente">Qtd. Meia</th>
                                <th class="right-align proponente">Pre&ccedil;o <br> Unit&aacute;rio</th>
                            </tr>
                        </thead>
                        <tbody v-if="detalhamento.length > 0">
                            <tr v-for="( item, index ) in detalhamento">
                                <td>{{item.dsProduto}}</td>
                                <td class="right-align">{{ item.qtExemplares }}</td>
            
                                <td class="right-align">{{ parseInt(item.qtGratuitaDivulgacao) +
                                    parseInt(item.qtGratuitaPatrocinador) + parseInt(item.qtGratuitaPopulacao) }}
                                </td>
            
                                <td class="right-align">{{ item.qtPopularIntegral }}</td>
                                <td class="right-align">{{ item.qtPopularParcial }}</td>
                                <td class="right-align">{{ formatarValor(item.vlUnitarioPopularIntegral) }}</td>
            
                                <td class="right-align">{{ item.qtProponenteIntegral }}</td>
                                <td class="right-align">{{ item.qtProponenteParcial }}</td>
                                <td class="right-align">{{ formatarValor(item.vlUnitarioProponenteIntegral) }}</td>
            
                                <td class="right-align">{{ formatarValor(item.vlReceitaPrevista) }}</td>
            
                            </tr>
                        </tbody>
                        <plano-distribuicao-detalhamentos-consolidacao :detalhamentos="detalhamento"></plano-distribuicao-detalhamentos-consolidacao>
                    </table>               
                </div>
            </li>
        </ul>
    </div>
    `,
    data: function () {
        return {
            detalhamentos: [],
        }
    },
    props: [
        'arrayDetalhamentos'
    ],
    mixins: [utils],
    watch: {
        arrayDetalhamentos: function (value) {
            this.detalhamentos = this.montarVisualizacao(value);
        }
    },
    mounted: function () {
        if (typeof this.arrayDetalhamentos != 'undefined') {
            this.iniciarCollapsible();
            this.detalhamentos = this.montarVisualizacao(this.arrayDetalhamentos);
        }
    },
    methods: {
        fetch: function () {
            let vue = this;

            $3.ajax({
                type: "GET",
                url: "/proposta/visualizar/obter-detalhamento-plano-distribuicao",
                data: {
                    idPreProjeto: vue.idpreprojeto,
                }
            }).done(function (response) {
                vue.detalhamentos = response.data;
            });
        },
        iniciarCollapsible: function () {
            $3('.detalhamento-plano-distribuicao .collapsible').each(function () {
                $3(this).collapsible();
            });
        },
        montarVisualizacao(detalhamentos) {

            let novoDetalhamento = {};
            let i = 0;
            let idMunicipio = '';

            detalhamentos.forEach((element) => {
                if (element.idMunicipio != idMunicipio) {
                    novoDetalhamento[element.idMunicipio] = [];
                    i = 0;
                    idMunicipio = element.idMunicipio;
                }

                novoDetalhamento[element.idMunicipio][i] = element;

                i++;
            });
            return novoDetalhamento;
        }
    }
});