<template>
    <app-main selected="6" titulo="Movimientos" :usuario="usuario">
        <el-button-group>
            <el-button type="primary" icon="el-icon-edit" @click="modalForm = true"></el-button>
            <el-button type="primary" icon="el-icon-paperclip"></el-button>
        </el-button-group>
        <!--        Tabla y paginacion-->
        <br>
        <el-divider></el-divider>

        <cargando :mostrarCargando="loading"></cargando>
    </app-main>
</template>

<script>
    import AppMain from "@/Layouts/AppMain";
    import ErrorForm from "@/Pages/Componentes/ErrorForm";
    import Cargando from "@/Pages/Componentes/Cargando";
    export default {
        name: "Index",
        props:['usuario', 'unidades', 'productos', 'movimientos'],
        components:{AppMain, ErrorForm, Cargando},
        data(){
            return{

            }
        }
    }
</script>

<style scoped>

</style>
