Vue.component('projeto-menu-contexto', {
    template: `
        <ul id="sidenav" class="side-nav fixed">
            <li v-for="item in menu" :class="[item.subnivel ? 'no-padding' : 'bold']">
                <a  v-if="item.subnivel" 
                    :id="item.id"
                    :href="[item.ajax ? 'javascript:void(0)' : item.link]"
                    v-on:click="carregarDados(item)"
                    ><i v-if="item.icon" class="material-icons left">{{ item.icon}}</i><span v-html="item.label"></span></a>
                <ul v-else class="collapsible collapsible-accordion">
                    <li class="bold">
                        <a 
                           :id="item.id"
                           class="collapsible-header waves-effect waves-cyan"
                           :href="item.link">
                            <i v-if="item.icon" class="material-icons left">{{ item.icon}}</i>
                           <span v-html="item.label"></span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    `,
    data: function () {
        return {
            active: true,
            loading: true,
            menu: {},
        }
    },
    mounted: function () {
        if(this.id != 0) {
            this.obterMenu();
        }
    },
    props: {
        id: 0
    },
    methods: {
        obterMenu: function () {
            let self = this;
            $3.ajax({
                type: "GET",
                url: "/projeto/menu/obter-menu/",
                data: {
                    idPronac: self.id,
                }
            }).done(function (response) {
                console.log(response);
                if (response) {
                    self.menu = response;
                }
            });
        },
        carregarDados: function (item)
        {
            if (item.ajax == false || item.link == '') {
                return
            }
            console.log('testeste');
            let divRetorno = 'conteudo';
            $("#"+divRetorno).html('carregando...');
            $3.ajax({
                url : item.link,
                success: function(data){
                    $("#"+divRetorno).html(data);
                },
                type : 'post'
            });
        }
    }
});