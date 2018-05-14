<template>
    <div class="proposta">
        <div v-if="Object.keys(dadosHistorico).length > 2">
            <salic-proposta-alteracoes :idpreprojeto="idpreprojeto" :dadosAtuais="dadosAtuais"
                                       :dadosHistorico="dadosHistorico"></salic-proposta-alteracoes>
        </div>
        <div v-else-if="Object.keys(dadosAtuais).length > 2">
            <div class="card padding20">
                <div class="nao-existe-versao-proposta">
                    <h4><i class="material-icons small left">report</i>Não existe versionamento de alterações para o
                        projeto informado...</h4>
                    <p style="margin-left: 44px">O proponente não fez alterações no projeto no prazo estabelecido.</p>
                </div>
            </div>
            <salic-proposta :idpreprojeto="idpreprojeto" :proposta="dadosAtuais"></salic-proposta>
        </div>
        <div v-else>
            <div class="card padding20">
                <div class="center-align padding20">
                    <h5>Carregando ...</h5>
                </div>
            </div>
        </div>
    </div>
</template>
<script>

    export default {
        data: function () {
            return {
                dadosAtuais: {
                    type: Object,
                    default: function () {
                        return {}
                    }
                },
                dadosHistorico: {
                    type: Object,
                    default: function () {
                        return {}
                    }
                }
            }
        },
        props: ['idpreprojeto', 'tipo'],
        mounted: function () {
            console.log("compnente mounted");
            if (typeof this.idpreprojeto != 'undefined') {
                this.buscar_dados();
            }
        },
        created: function() {
          console.log("comoponte diff createadddd");
        },
        methods: {
            buscar_dados: function () {
                let vue = this;
                $3.ajax({
                    url: '/proposta/visualizar/obter-proposta-cultural-versionamento/idPreProjeto/' + vue.idpreprojeto + '/tipo/' + vue.tipo
                }).done(function (response) {
                    vue.dadosAtuais = response.data.atual;
                    vue.dadosHistorico = response.data.historico;
                });
            }
        }
    };

</script>