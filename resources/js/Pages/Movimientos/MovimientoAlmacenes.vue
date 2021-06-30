<template>
    <el-dialog title="Movimiento regular" v-model="modalForm" width="40%">
        <el-form ref="form" label-width="150px" :model="model" :disabled="disableForm">

        </el-form>
        <br>
        <template #footer>
                <span class="dialog-footer">
                  <el-button @click="modalForm = false">Cancelar</el-button>
                  <el-button type="primary" @click="guardar">Aceptar</el-button>
                </span>
        </template>
        <error-form :errores="errores"></error-form>
    </el-dialog>
</template>

<script>
export default {
    name: "MovimientoAlmacenes",
    data(){
        return{
            modalForm: false,
            model:{}
        }
    }
}
</script>

<style scoped>

</style>
