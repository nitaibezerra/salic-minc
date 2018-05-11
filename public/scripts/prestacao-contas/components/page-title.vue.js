Vue.component('page-title', {
    data: function () {
        return {
            count: 0,
            title: 'Inicio'
        }
    },
    template: `
    <div class="page-title">
        <div class="row">
            <div class="col s12 m9 l10">
                <h1>Presta&ccedil;&atilde;o de Contas: {{title}}</h1>
            </div>
            <div class="col s12 m3 l2 right-align">
                <a href="javascript:voltar();" class="btn small grey lighten-3 grey-text z-depth-0 chat-toggle"><i class="material-icons">keyboard_return</i></a>
            </div>
        </div>
    </div>
    `,
    watch:{
        $route (to, from){
            console.log(to.meta.title);
            this.title = to.meta.title;
        }
    }
})
