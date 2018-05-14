<template>
    <tfoot style="opacity: 0.8">
    <tr>
        <td><b>Totais</b></td>
        <td class="right-align">{{ qtExemplaresTotal }}</td>

        <td class="right-align">
            {{
            parseInt(qtGratuitaDivulgacaoTotal) +
            parseInt(qtGratuitaPatrocinadorTotal) +
            parseInt(qtGratuitaPopulacaoTotal)
            }}
        </td>

        <td class="right-align">{{ qtPopularIntegralTotal }}</td>
        <td class="right-align">{{ qtPopularParcialTotal }}</td>
        <td class="right-align"> ---</td>

        <td class="right-align">{{ qtProponenteIntegralTotal }}</td>
        <td class="right-align">{{ qtProponenteParcialTotal }}</td>
        <td class="right-align"> ---</td>
        <td class="right-align">{{ receitaPrevistaTotal }}</td>
    </tr>
    </tfoot>
</template>

<script>
    import numeral from 'numeral'
    
    export default {
        name: "salic-proposta-detalhamento-consolidacao",
        props: {
            items: {},
        },
        computed: {
            // Total de exemplares
            qtExemplaresTotal: function () {
                total = 0;
                for (var i = 0; i < this.items.length; i++) {
                    total += parseInt(this.items[i]['qtExemplares']);
                }
                return total;
            },
            // Total de divulgação gratuita.
            qtGratuitaDivulgacaoTotal: function () {
                total = 0;
                for (var i = 0; i < this.items.length; i++) {
                    total += parseInt(this.items[i]['qtGratuitaDivulgacao']);
                }
                return total;
            },
            // Total de divulgação Patrocinador
            qtGratuitaPatrocinadorTotal: function () {
                total = 0;
                for (var i = 0; i < this.items.length; i++) {
                    total += parseInt(this.items[i]['qtGratuitaPatrocinador']);
                }
                return total;
            },
            // Total de divulgação gratuita.
            qtGratuitaPopulacaoTotal: function () {
                total = 0;
                for (var i = 0; i < this.items.length; i++) {
                    total += parseInt(this.items[i]['qtGratuitaPopulacao']);
                }
                return total;
            },
            //Preço Popular: Quantidade de Inteira
            qtPopularIntegralTotal: function () {
                total = 0;
                for (var i = 0; i < this.items.length; i++) {
                    total += parseInt(this.items[i]['qtPopularIntegral']);
                }
                return total;
            },
            //Preço Popular: Quantidade de meia entrada
            qtPopularParcialTotal: function () {
                total = 0;
                for (var i = 0; i < this.items.length; i++) {
                    total += parseInt(this.items[i]['qtPopularParcial']);
                }
                return total;
            },
            vlReceitaPopularIntegralTotal: function () {
                total = 0;
                for (var i = 0; i < this.items.length; i++) {
                    var vl = (this.items[i]['vlReceitaPopularIntegral']);
                    total += numeral(vl).value();
                }
                return numeral(total).format('0,0.00');
            },
            vlReceitaPopularParcialTotal: function () {
                total = 0;
                for (var i = 0; i < this.items.length; i++) {
                    var vl = (this.items[i]['vlReceitaPopularParcial']);
                    total += numeral(vl).value();
                }
                return numeral(total).format('0,0.00');
            },
            qtProponenteIntegralTotal: function () {
                total = 0;
                for (var i = 0; i < this.items.length; i++) {
                    total += parseInt(this.items[i]['qtProponenteIntegral']);
                }
                return total;
            },
            qtProponenteParcialTotal: function () {
                total = 0;
                for (var i = 0; i < this.items.length; i++) {
                    total += parseInt(this.items[i]['qtProponenteParcial']);
                }
                return total;
            },
            vlReceitaProponenteIntegralTotal: function () {
                total = 0;
                for (var i = 0; i < this.items.length; i++) {
                    vl = (this.items[i]['vlReceitaProponenteIntegral']);
                    total += this.converterParaMoedaAmericana(vl);
                }
                return numeral(total).format('0,0.00');
            },
            vlReceitaProponenteParcialTotal: function () {
                total = 0;
                for (var i = 0; i < this.items.length; i++) {
                    var vl = (this.items[i]['vlReceitaProponenteParcial']);
                    total += this.converterParaMoedaAmericana(vl);
                }
                return numeral(total).format('0,0.00');
            },
            receitaPrevistaTotal: function () {
                var total = numeral();

                for (var i = 0; i < this.items.length; i++) {
                    var vl = this.items[i]['vlReceitaPrevista'];
                    total.add(parseFloat(vl));
                }
                return total.format('0,0.00');
            }
        },
        methods: {
            converterParaMoedaAmericana: function (valor) {
                if (!valor)
                    valor = '0';

                valor = valor.replace(/\./g, '');
                valor = valor.replace(/\,/g, '.');
                valor = parseFloat(valor);
                valor = valor.toFixed(2);

                if (isNaN(valor))
                    valor = 0;

                return valor;
            }
        }ß
    }
</script>

<style scoped>

</style>