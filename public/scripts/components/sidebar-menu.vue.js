Vue.component('sidebar-menu', {
    template: `
    <div>
        <ul id="sidenav" class="sidenav-apoio side-nav fixed">
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
                                       href="javascript:void(0)"
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
                    href="javascript:void(0)"
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
            configs: {
                idDivRetorno: 'conteudo'
            }
        }
    },
    props: {
        id: 0,
        urlAjax: '',
        menu: {},
    },
    updated: function () {
        if(this.id != 0 && this.url != '') {
            this.obterMenu();
        }

        this.iniciarCollapsible();
    },
    methods: {
        obterMenu: function () {
            let self = this;
            $3.ajax({
                type: "GET",
                url: self.urlAjax,
                data: {
                    id: self.id,
                }
            }).done(function (response) {
                if (response) {
                    self.menu = response;
                }
            });
        },
        obterMenuPrincipal: function () {
            // let self = this;
            // $3.ajax({
            //     type: "GET",
            //     url: "/projeto/menu/obter-menu/",
            //     data: {
            //         idPronac: self.id,
            //     }
            // }).done(function (response) {
            //     console.log(response);
            //     if (response) {
            //         self.menu = response;
            //     }
            // });
        },
        carregarDados: function (item) {

            if (item.link == '') {
                return;
            }

            if (item.ajax != true) {
                window.location.href = item.link;
                return;
            }

            let divRetorno = this.configs.idDivRetorno;
            $3("#" + divRetorno).html('carregando...');
            $3.ajax({
                url: item.link,
                success: function (data) {
                    $("#" + divRetorno).html(data);
                },
                type: 'post'
            });
        },
        iniciarCollapsible: function () {
            $3('.collapsible').each(function () {
                $3(this).collapsible();
            });
        }
    }
});
