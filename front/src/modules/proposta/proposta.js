// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'

import numeral from 'numeral'
import moment from 'moment';
// import router from '../../router'

Vue.config.productionTip = false
//
// /* eslint-disable no-new */
// new Vue({
//     el: '#container-vue',
//     components: { App },
//     template: '<App/>',
//     created:  function () {
//         console.log("componente principalllllll");
//     }
// });


import TableEasy from '../../components/salic-table-easy.vue';
import TextoSimples from '../../components/salic-texto-simples.vue';
import Proposta from './visualizar/salic-proposta.vue';
import PropostaCustosVinculados from './visualizar/salic-proposta-custos-vinculados.vue';
import PropostaDetalhamentoPlanoDistribuicao from './visualizar/salic-proposta-detalhamento-plano-distribuicao.vue';
import PropostaDiff from './visualizar/salic-proposta-diff.vue';
import PropostaAlteracoes from './visualizar/salic-proposta-alteracoes.vue';
import PropostaDocumentos from './visualizar/salic-proposta-documentos.vue';
import PropostaFontesDeRecursos from './visualizar/salic-proposta-fontes-de-recursos.vue';
import PropostaHistoricoAvaliacoes from './visualizar/salic-proposta-historico-avaliacoes.vue';
import PropostaIdentificacao from './visualizar/salic-proposta-identificacao.vue';
import PropostaLocalRealizacaoDeslocamento from './visualizar/salic-proposta-local-realizacao-deslocamento.vue';
import PropostaPlanilhaOrcamentaria from './visualizar/salic-proposta-planilha-orcamentaria.vue';
import PropostaPlanoDistribuicao from './visualizar/salic-proposta-plano-distribuicao.vue';

import AgenteProponente from '../agente/visualizar/salic-agente-proponente.vue';
import AutenticacaoUsuario from '../autenticacao/index/salic-autenticacao-usuario.vue';

/*
* Inicialize com Vue.component para componentes globais que podem ser usados dentro de
* outros components.
* A declaração de componentes dentro de 'new Vue' torna os componentes locais,
* só em relação ao #container-vue.
*/

Vue.component('salic-table-easy', TableEasy);
Vue.component('salic-texto-simples', TextoSimples);

Vue.component('salic-proposta', Proposta);
Vue.component('salic-proposta-custos-vinculados', PropostaCustosVinculados);
Vue.component('salic-proposta-detalhamento-plano-distribuicao', PropostaDetalhamentoPlanoDistribuicao);
Vue.component('salic-proposta-diff', PropostaDiff);
Vue.component('salic-proposta-alteracoes', PropostaAlteracoes);
Vue.component('salic-proposta-documentos', PropostaDocumentos);
Vue.component('salic-proposta-fontes-de-recursos', PropostaFontesDeRecursos);
Vue.component('salic-proposta-historico-avaliacoes', PropostaHistoricoAvaliacoes);
Vue.component('salic-proposta-identificacao', PropostaIdentificacao);
Vue.component('salic-proposta-local-realizacao-deslocamento', PropostaLocalRealizacaoDeslocamento);
Vue.component('salic-proposta-planilha-orcamentaria', PropostaPlanilhaOrcamentaria);
Vue.component('salic-proposta-plano-distribuicao', PropostaPlanoDistribuicao);

Vue.component('salic-agente-proponente', AgenteProponente);
Vue.component('salic-autenticacao-usuario', AutenticacaoUsuario);

new Vue({
    el: '#container-vue',
    created:  function () {
        console.log("componente principalllllll");
    }
});