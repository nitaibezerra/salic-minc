<template>
    <div v-if="texto && texto.length > 0" v-html="texto"></div>
    <div v-else>Não informado(a)</div>
</template>

<script>
    export default {
        name:'salic-texto-simples',
        props: ['texto']
    };

</script>