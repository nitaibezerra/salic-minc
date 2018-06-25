Vue.component('projeto-menu-contexto', {
    template: `<div>
        <ul id="sidenav" class="sidenav-apoio side-nav fixed">
            <li class="bold">
                <a href="#" data-activates="slide-out" class="button-collapse2 waves-effect waves-cyan">
                    <i class="material-icons left">menu</i>
                    <span>Menu Principal</span>
                </a>
            </li>
            <li v-for="item in menu" :class="[item.submenu ? 'no-padding' : 'bold']">
                 <ul v-if="item.submenu"  class="collapsible collapsible-accordion">
                    <li class="bold">
                        <a 
                           class="collapsible-header waves-effect waves-cyan"
                           href="javascript:void(0)"
                        >
                            <i v-if="item.icon" class="material-icons left">{{ item.icon}}</i>
                           <span v-html="item.label"></span>
                        </a>
                        <div class="collapsible-body">
                            <ul>
                                <li v-for="subitem in item.submenu">
                                    <a class="waves-effect waves-cyan"
                                       :href="[subitem.ajax ? 'javascript:void(0)' : subitem.link]"
                                       v-on:click="carregarDados(subitem)"
                                       title="Ir para" 
                                       v-html="subitem.label"
                                       ></a>
                                </li>
                            </ul>
                        </div>
                    </li>
                </ul>
                <a  v-else 
                    :href="[item.ajax ? 'javascript:void(0)' : item.link]"
                    v-on:click="carregarDados(item)"
                    ><i v-if="item.icon" class="material-icons left">{{ item.icon}}</i><span v-html="item.label"></span>
                </a>
            </li>
        </ul>
  </div>
  
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
    updated: function () {
        this.iniciarCollapsible();
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
        obterMenuPrincipal: function () {
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
            $3("#"+divRetorno).html('carregando...');
            $3.ajax({
                url : item.link,
                success: function(data){
                    $("#"+divRetorno).html(data);
                },
                type : 'post'
            });
        },
        iniciarCollapsible: function () {
            $3(".button-collapse2").sideNav();
            $3(".button-collapse3").sideNav('hide');


            $3('.collapsible').each(function () {
                $3(this).collapsible();
            });
        }
    }
});
