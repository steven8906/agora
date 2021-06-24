<template>
  <app-main selected="14" titulo="Cotizaciones" :usuario="usuario">
    <inertia-link :href="route('cotizaciones.create')">
      <el-button type="primary" icon="el-icon-plus">Crear Cotización</el-button>
    </inertia-link>
    <el-divider></el-divider>
    <!-- agregar listado de cotizaciones -->
    <el-row :gutter="20">
      <el-col :span="24">
        <el-card class="box-card">
          <el-table
            v-if="dataCotizaciones.length > 0"
            :data="displayData"
            stripe
            style="width: 100%"
          >
            <el-table-column label="Folio" prop="folio"> </el-table-column>
            <el-table-column label="Cliente" prop="cliente.cliente"> </el-table-column>
            <el-table-column label="Obra" prop="obra"> </el-table-column>
            <el-table-column label="Ubicación" prop="ubicacion">
            </el-table-column>
            <el-table-column label="Monto Total" prop="monto_total">
            </el-table-column>
            <el-table-column label="Fecha" prop="fecha"> </el-table-column>

            <el-table-column align="right">
              <template #header>
                <el-input
                  v-model="search"
                  size="mini"
                  placeholder="Buscar..."
                />
              </template>
              <template #default="scope">
                <inertia-link :href="route('cotizaciones.edit', scope.row.id)">
                  <el-button size="mini" type="warning" icon="el-icon-edit"
                    >Editar
                  </el-button>
                </inertia-link>
                <!-- <el-popconfirm
                  confirmButtonText="Aceptar"
                  cancelButtonText="Cancelar"
                  icon="el-icon-info"
                  iconColor="red"
                  title="¿Confirma esta operación?"
                  @confirm="desactivar(scope.row)"
                >
                  <template #reference>
                    <el-button size="mini" type="danger" icon="el-icon-delete"
                      >Desactivar</el-button
                    >
                  </template>
                </el-popconfirm> -->
              </template>
            </el-table-column>
          </el-table>
          <el-alert
            v-else
            title="Sin datos"
            type="warning"
            description="No existen datos para mostrar"
            show-icon
          >
          </el-alert>
          <el-divider></el-divider>
          <div style="text-align: center">
            <el-pagination
              background
              layout="prev, pager, next"
              @current-change="handleCurrentChange"
              :page-size="pageSize"
              :total="dataCotizaciones.length"
            >
            </el-pagination>
          </div>
        </el-card>
      </el-col>
    </el-row>

    <cargando :mostrarCargando="loading"></cargando>
  </app-main>
</template>

<script>
import AppMain from "@/Layouts/AppMain";
import ErrorForm from "@/Pages/Componentes/ErrorForm";
import Cargando from "@/Pages/Componentes/Cargando";

export default {
  props: ["usuario", "cotizaciones"],
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
  computed: {
    displayData() {
      if (!this.dataCotizaciones || this.dataCotizaciones.length === 0)
        return [];
      return this.dataCotizaciones.slice(
        this.pageSize * this.page - this.pageSize,
        this.pageSize * this.page
      );
    },
  },
  watch: {
    search: function (val) {
      if (val !== "") {
        this.dataCotizaciones = this.dataCotizaciones.filter(
          (data) =>
            !val ||
            data.obra.toLowerCase().includes(val.toLowerCase()) ||
            data.folio.toLowerCase().includes(val.toLowerCase()) ||
            data.ubicacion.toLowerCase().includes(val.toLowerCase()) ||
            data.fecha.toLowerCase().includes(val.toLowerCase())
        );
      } else {
        this.dataCotizaciones = this.cotizaciones;
      }
    },
  },
  methods: {
    //bloque obligatorio para paginacion de tabla
    handleCurrentChange(val) {
      this.page = val;
    },
  },
};
</script>

<style>
</style>