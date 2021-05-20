<div id="app">
<form @submit.prevent="enviaForm">
    <label for="pixType">Tipo de Chave</label>
    <select name="pixType" v-model="pixType">
        <option>CPF/CNPJ</option>
        <option>CEL</option>
    </select>

    <label for="pixKey">Chave Pix</label>
    <input name="pixKey" id="pixKey" v-model="pixKey">

    <label for="amount">Valor</label>
    <input name="amount" id="amount" v-model="amount">

    <button>Gerar QR CODE</button>
</form>

<h1>QR CODE PIX</h1>

    
<br>

<img :src="src">


</div>
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.12"></script>
<script>
    new Vue({
        data: () => ({
            pixKey : '',
            amount : '',
            pixType : '',
            src : ''
        }),
        created() {
            // this.getValidationData()
        },
        methods: {
            enviaForm() {
                console.log(this.pixKey);
                this.src = 'http://pix.localhost/QRCode.php?pixKey='+this.pixKey+'&amount='+this.amount+'&pixType='+this.pixType;
                console.log(this.src);
            }
        }
    }).$mount('#app');
</script>