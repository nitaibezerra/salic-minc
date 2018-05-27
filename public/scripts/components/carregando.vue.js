Vue.component('carregando', {
    template: `
    <div class="center-align card-panel">
        <h6 class="animated tada" v-html="text"></h6>
        <div class="preloader-wrapper small active">
            <div class="spinner-layer spinner-green-only">
              <div class="circle-clipper left">
                <div class="circle"></div>
              </div><div class="gap-patch">
                <div class="circle"></div>
              </div><div class="circle-clipper right">
                <div class="circle"></div>
              </div>
            </div>
        </div>
    </div>
    `,
    props: {
        text: {
            type: String,
            default: ''
        }
    }
});
