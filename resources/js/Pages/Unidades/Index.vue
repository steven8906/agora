<template>
    <app-main selected="10" titulo="Unidades" :usuario="usuario">
        <el-button-group>
            <el-button type="primary" icon="el-icon-edit" @click="modalForm = true"></el-button>
            <el-button type="primary" icon="el-icon-paperclip"></el-button>
        </el-button-group>
        <!--        Tabla y paginacion-->
        <br>
        <el-divider></el-divider>
        <el-row :gutter="20">
            <el-col :span="24">
                <el-card class="box-card">
                    <el-table v-if="dataUnidades.length > 0"
                              :data="displayData"
                              stripe
                              border
                              style="width: 100%">
                        <el-table-column label="Unidad" prop="unidad"></el-table-column>
                        <el-table-column label="Clave" prop="clave"></el-table-column>
                        <el-table-column label="Tipo" prop="tipo"></el-table-column>
                        <el-table-column align="right"
                                         fixed="right"
                                         width="250">
                            <template #header>
                                <el-input v-model="search" size="mini" placeholder="Buscar..."/>
                            </template>
                            <template #default="scope">
                                <el-button size="mini" type="warning" icon="el-icon-edit"
                                           @click="editarModal(scope.row)">Editar
                                </el-button>
                                &nbsp;
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
                            :total="dataUnidades.length">
                        </el-pagination>
                    </div>
                </el-card>
            </el-col>
        </el-row>
        <!--        Tabla y paginacion-->
        <!--  Inicio-Formulario de registro y actualizacion-->
        <el-dialog title="Formulario de Unidades" v-model="modalForm" width="30%">
            <el-form ref="form"  label-width="120px" :model="model">
                <el-form-item label="Unidad:"
                              prop="unidad"
                              :rules="[{required:true, message:'Campo obligatorio'}]">
                    <el-input placeholder="Unidad" v-model="model.unidad" clearable></el-input>
                </el-form-item>
                <el-form-item label="Clave:"
                              prop="clave"
                              :rules="[{required:true, message:'Campo obligatorio'}]">
                    <el-input placeholder="Clave" v-model="model.clave" clearable></el-input>
                </el-form-item>
                <el-form-item
                    label="Tipo:"
                    prop="tipo"
                    :rules="[{required:true, message:'Campo obligatorio'}]">
                    <el-input v-model="model.tipo" placeholder="Tipo" clearable></el-input>
                </el-form-item>
            </el-form>
            <error-form :errores="errores"></error-form>
            <template #footer>
                <span class="dialog-footer">
                  <el-button @click="modalForm = false">Cancelar</el-button>
                  <el-button type="primary" @click="guardar">Aceptar</el-button>
                </span>
            </template>
        </el-dialog>
        <!--  Fin-Formulario de registro y actualizacion-->
        <cargando :mostrarCargando="loading"></cargando>
    </app-main>
</template>

<script>
    import AppMain from "@/Layouts/AppMain";
    import ErrorForm from "@/Pages/Componentes/ErrorForm";
    import Cargando from "@/Pages/Componentes/Cargando";

    export default {
        name: "Index",
        props: ['unidades', 'usuario'],
        components: {AppMain, ErrorForm, Cargando},
        data() {
            return{
                dataUnidades: this.unidades,
                modalForm: false,
                //bloque obligatorio para paginacion de tabla
                page: 1,
                pageSize: 10,
                total: 0,
                search: "",
                //bloque obligatorio para paginacion de tabla
                modalForm: false,
                model: {},
                errores: null,
                loading: false,
            }
        },//bloque obligatorio para paginacion de tabla
        computed:{
            displayData() {
                if (!this.dataUnidades || this.dataUnidades.length === 0) return [];
                return this.dataUnidades.slice(this.pageSize * this.page - this.pageSize, this.pageSize * this.page)
            }
        },
        watch:{
            search: function (val) {
                if (val !== ""){
                    this.dataUnidades =  this.dataUnidades.filter(data => !val || data.unidad.toLowerCase().includes(val.toLowerCase()))
                }else{
                    this.dataUnidades = this.unidad
                }
            }
        },
        methods:{
            guardar() {
                this.loading = true;
                let url = this.model.hasOwnProperty('id') ? 'unidades.update' : 'unidades.store';
                axios.post(route(url), this.model)
                    .then((res) => {
                        this.dataUnidades = res.data.info;
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
                let model = this.dataUnidades.filter(unidad => unidad.id == row.id)[0];
                this.model = model;
            },
            desactivar(row) {
                console.log(row.id);
            },
            //bloque obligatorio para paginacion de tabla
            handleCurrentChange(val) {
                this.page = val;
            },
            //bloque obligatorio para paginacion de tabla
            limpiarModelo(){
                this.model = {};
            }
        }
    }
</script>

<style scoped>

</style>
