<template>
    <div class="tabelas">
        <div class="row">
            <salic-table-easy v-bind:dados="dados"></salic-table-easy>
        </div>
    </div>
</template>

<script>
    export default {

        data: function () {
            return {
                dados: []
            }
        },
        props: ['idpreprojeto', 'arrayCustos'],
        mounted: function () {
            if (typeof this.idpreprojeto != 'undefined') {
                this.buscar_dados(this.idpreprojeto);
            }

            if (typeof this.arrayCustos != 'undefined') {
                this.dados = this.arrayCustos;
            }

        },
        watch: {
            idpreprojeto: function (value) {
                this.buscar_dados(value);
            },
            arrayCustos: function (value) {
                this.dados = value;
            }
        },
        methods: {
            buscar_dados: function (id) {
                if (id) {
                    let vue = this;
                    $3.ajax({
                        url: '/proposta/visualizar/obter-custos-vinculados/idPreProjeto/' + id
                    }).done(function (response) {
                        vue.dados = response.data;
                    });
                }
            },
        }
    };
</script>