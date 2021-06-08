<template>
  <app-main selected="14" titulo="Cotizaciones" :usuario="usuario">
        <inertia-link  :href="route('cotizaciones.create')">
        <el-button type="primary" icon="el-icon-plus">Nueva Cotizacion</el-button>
        </inertia-link>
    <el-divider></el-divider>

    <cargando :mostrarCargando="loading"></cargando>
  </app-main>
</template>

<script>
import AppMain from "@/Layouts/AppMain";
import ErrorForm from "@/Pages/Componentes/ErrorForm";
import Cargando from "@/Pages/Componentes/Cargando";

export default {
  props: ["clientes", "usuario"],
  components: { AppMain, ErrorForm, Cargando },
  data() {
    return {
      dataCotizaciones: this.cotizaciones,
      modalForm: false,
      //bloque obligatorio para paginacion de tabla
      page: 1,
      pageSize: 10,
      total: 0,
      search: "",
      //bloque obligatorio para paginacion de tabla
      model: {
        nombre: "",
        rfc: "",
        direccion: "",
        telefono: "",
        email: "",
      },
      errores: null,
      loading: false,
    };
  },
};
</script>

<style>
</style>