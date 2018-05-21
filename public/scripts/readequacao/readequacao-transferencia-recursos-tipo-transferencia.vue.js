Vue.component('readequacao-transferencia-recursos-tipo-transferencia', {
    template: `
        <div class="col s12">
            <span>Tipo de transfer&ecirc;ncia *</span>
                <select
                       class="browser-default"
                       ref="readequacaoTipoTransferencia"
                       v-model="valorSelecionado"
		    @change="selecionarTipoTransferencia"
		    >
                    <option v-for="tipo in tiposTransferencia" v-bind:value="tipo.id">{{tipo.nome}}</option>
                </select>
        </div>
    `,
    props: ['dsSolicitacao'],
    data: function() {
	var tiposTransferencia = [
	    {
		'id': 1,
		'nome': 'N\xE3o homologados'
	    },
	    {
		'id': 2,
		'nome': 'Homologados'
	    },
	    {
		'id': 3,
		'nome': 'Recursos remanescentes'
	    },
	];
	
	return {
	    tiposTransferencia,
	    valorSelecionado: ''
	}
    },
    methods: {
	selecionarTipoTransferencia: function() {
	    this.$emit('eventoAtualizarDsSolicitacao', this.valorSelecionado);
	}
    },
    watch: {
	dsSolicitacao: function(valor) {
	    this.valorSelecionado = valor;
	}
    }
});
