<script>
    export default {
        name: "Cargando",
        props:['mostrarCargando'],
        data(){
            return{
                flag:null
            }
        },
        watch:{
            mostrarCargando: function (val) {
                if (val){
                    this.flag = this.$loading({
                        lock: true,
                        text: 'Procesando solicitud...',
                        spinner: 'el-icon-loading',
                        background: 'rgba(0, 0, 0, 0.7)'
                    });
                }else{
                    this.flag.close();
                }
            }
        }
    }
</script>

<style scoped>

</style>
