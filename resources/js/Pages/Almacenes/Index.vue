<template>
    <app-main selected="1" titulo="Almacénes" :usuario="usuario">
        <el-button-group>
            <el-button type="primary" icon="el-icon-circle-plus-outline" @click="modalForm = true"></el-button>
<!--            <el-button type="primary" icon="el-icon-paperclip"></el-button>-->
        </el-button-group>
        <!--        Tabla y paginacion-->
        <br>
        <el-divider></el-divider>
        <el-row :gutter="20">
            <el-col :span="24">
                <el-card class="box-card">
                    <el-table v-if="dataAlmacenes.length > 0"
                        :data="displayData"
                        style="width: 100%">
                        <el-table-column label="Estatus" prop="condicion">
                            <template #default="scope">
                                <el-tag type="success" v-if="scope.row.condicion == 1" effect="dark">Activo</el-tag>
                                <el-tag type="danger" effect="dark" v-else>Inactivo</el-tag>
                            </template>
                        </el-table-column>
                        <el-table-column label="Almacén" prop="almacen">
                        </el-table-column>
                        <el-table-column label="Descripción" prop="descripcion">
                        </el-table-column>
                        <el-table-column align="right">
                            <template #header>
                                <el-input v-model="search" size="mini" placeholder="Buscar..."/>
                            </template>
                            <template #default="scope">
                                <el-button size="mini" type="warning" icon="el-icon-edit"
                                           @click="editarModal(scope.row)">Editar
                                </el-button>
                                <el-popconfirm
                                    confirmButtonText='Aceptar'
                                    cancelButtonText='Cancelar'
                                    icon="el-icon-info"
                                    iconColor="red"
                                    title="¿Confirma esta operación?"
                                    @confirm="desactivar(scope.row)"
                                >
                                    <template #reference>
                                        <el-button size="mini" type="danger" icon="el-icon-delete">Desactivar</el-button>
                                    </template>
                                </el-popconfirm>
                            </template>
                        </el-table-column>
                    </el-table>
                    <el-alert v-else
                        title="Sin datos"
                        type="warning"
                        description="No existen datos para mostrar"
                        show-icon>
                    </el-alert>
                    <el-divider></el-divider>
                    <div style="text-align: center">
                        <el-pagination
                            background
                            layout="prev, pager, next"
                            @current-change="handleCurrentChange"
                            :page-size="pageSize"
                            :total="dataAlmacenes.length">
                        </el-pagination>
                    </div>
                </el-card>
            </el-col>
        </el-row>
        <!--        Tabla y paginacion-->
        <el-dialog title="Formulario de almacénes" v-model="modalForm" width="30%">
            <el-input placeholder="Almacén" v-model="model.almacen" clearable></el-input>
            <br>
            <br>
            <el-input type="textarea" :rows="2" placeholder="Descripción" v-model="model.descripcion" clearable></el-input>
            <template #footer>
                <span class="dialog-footer">
                  <el-button @click="modalForm = false">Cancelar</el-button>
                  <el-button type="primary" @click="guardar">Aceptar</el-button>
                </span>
            </template>
            <error-form :errores="errores"></error-form>
        </el-dialog>
        <cargando :mostrarCargando="loading"></cargando>
    </app-main>
</template>

<script>
    import AppMain from "../../Layouts/AppMain";
    import ErrorForm from "@/Pages/Componentes/ErrorForm";
    import Cargando from "@/Pages/Componentes/Cargando";

    export default {
        name: "Index",
        components: {AppMain, ErrorForm, Cargando},
        props: ["almacenes", "usuario"],
        data() {
            return {
                dataAlmacenes: this.almacenes,
                modalForm: false,
                //bloque obligatorio para paginacion de tabla
                page: 1,
                pageSize: 10,
                total: 0,
                search: "",
                //bloque obligatorio para paginacion de tabla
                model: {
                    almacen: "",
                    descripcion: "",
                },
                errores: null,
                loading: false,
            };
        },
        //bloque obligatorio para paginacion de tabla
        computed:{
            displayData() {
                if (!this.dataAlmacenes || this.dataAlmacenes.length === 0) return [];
                return this.dataAlmacenes.slice(this.pageSize * this.page - this.pageSize, this.pageSize * this.page)
            }
        },
        watch:{
          search: function (val) {
              if (val !== ""){
                  this.dataAlmacenes =  this.dataAlmacenes.filter(data => !val || data.almacen.toLowerCase().includes(val.toLowerCase()))
              }else{
                  this.dataAlmacenes= this.almacenes
              }
          }
        },
        //bloque obligatorio para paginacion de tabla
        methods: {
            guardar() {
                this.loading = true;
                let url = this.model.hasOwnProperty('id') ? 'almacenes.update' : 'almacenes.store';
                axios.post(route(url), this.model)
                    .then((res) => {
                        this.$notify({
                            title: 'Transacción exitosa',
                            message: 'Solicitud realizada con éxito',
                            type: 'success'
                        });
                        this.dataAlmacenes = res.data.info;
                        this.errores = null;
                        this.modalForm = false;
                        this.limpiarModelo();
                    })
                    .catch((error) => {
                        let aux = [];
                        let info = Object.values(error.response.data.errors);
                        info.forEach((item) => aux.push(item[0]));
                        this.errores = aux;
                    }).finally(() =>
                    setTimeout(() => {
                        this.loading = false
                    }, 1000)
                );
            },
            editarModal(row) {
                this.modalForm = true;
                let model = this.dataAlmacenes.filter(almacen => almacen.id == row.id)[0];
                this.model.almacen = model.almacen;
                this.model.descripcion = model.descripcion;
                this.model.id = model.id;
            },
            desactivar(row) {
                this.loading = true
                axios.post(route('almacenes.disable'), {id: row.id, condicion: row.condicion})
                    .then((res) => {
                        this.$notify({
                            title: 'Transacción exitosa',
                            message: 'Solicitud realizada con éxito',
                            type: 'success'
                        });
                        this.dataAlmacenes = res.data.almacenes;
                    })
                    .catch((error) => {
                        this.$notify({
                            title: 'Transacción exitosa',
                            message: error.response.data.errors,
                            type: 'danger'
                        });
                    })
                    .finally(() =>
                        setTimeout(() => {
                            this.loading = false
                        }, 1000)
                    );
            },
            //bloque obligatorio para paginacion de tabla
            handleCurrentChange(val) {
                this.page = val;
            },
            //bloque obligatorio para paginacion de tabla
            limpiarModelo(){
                this.model.almacen = "";
                this.model.descripcion = "";
                delete this.model.id;
            }
        },
    };
</script>
