Vue.component('readequacao-plano-distribuicao', {
    template: `
        <div class="readequacao-plano-distribuicao padding10">
             <div v-if="!produtos.length" class="padding10">
                <b>Aguarde! Carregando....</b>
            </div>
            <plano-distribuicao-listagem
                :id-projeto="idPronac"
                :disabled="disabled"
                :active="active"
                :array-produtos="produtos"
                :array-detalhamentos="detalhamentos"
                :componente-detalhamento="componenteDetalhamento"
                :array-locais="locais"
            ></plano-distribuicao-listagem>
        </div>
    `,
    data: function () {
        return {
            produtos: {},
            detalhamentos: {},
            locais: {},
            active: true,
            componenteDetalhamento: "readequacao-plano-distribuicao-detalhamentos"
        }
    },
    props: {
        'idPronac': '',
        'disabled' : false
    },
    mixins: [utils],
    watch: {
        idPronac: function (value) {
            this.fetch(value);
        }
    },
    mounted: function () {
        if (typeof this.idPronac != 'undefined') {
            this.obterPlanoDistribuicao(this.idPronac);
            this.obterLocaisRealizacao(this.idPronac);
        }
    },
    created: function () {
        let self = this;
        detalhamentoEventBus.$on('busAtualizarProdutos', function (response) {
            self.obterPlanoDistribuicao(self.idPronac);
        });
    },
    methods: {
        obterPlanoDistribuicao: function (id) {
            let self = this;
            $3.ajax({
                type: "GET",
                url: "/readequacao/plano-distribuicao/obter-plano-distribuicao-detalhamentos-ajax",
                data: {
                    idPronac: id
                }
            }).done(function (response) {
                let dados = response.data;
                self.produtos = dados.planodistribuicao;
                self.detalhamentos = dados.detalhamentos;
            }).fail(function (response) {
                self.mensagemErro(response.responseJSON.msg)
            });
        },
        obterLocaisRealizacao: function (id) {
            let self = this;
            $3.ajax({
                type: "GET",
                url: "/readequacao/local-realizacao/obter-locais-de-realizacao-ajax",
                data: {
                    idPronac: id
                }
            }).done(function (response) {
                self.locais = response.data;
            }).fail(function (response) {
                self.mensagemErro(response.responseJSON.msg)
            });
        }
    }
});