<template>
    <app-main selected="2" titulo="Categorias" :usuario="usuario">
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
                    <el-table v-if="dataCategorias.length > 0"
                              :data="displayData"
                              style="width: 100%">
                        <el-table-column label="Estatus" prop="condicion">
                            <template #default="scope">
                                <el-tag type="success" v-if="scope.row.condicion == 1" effect="dark">Activo</el-tag>
                                <el-tag type="danger" effect="dark" v-else>Inactivo</el-tag>
                            </template>
                        </el-table-column>
                        <el-table-column label="Categoría" prop="nombre"></el-table-column>
                        <el-table-column label="Descripción" prop="descripcion"></el-table-column>
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
                            :total="dataCategorias.length">
                        </el-pagination>
                    </div>
                </el-card>
            </el-col>
        </el-row>
        <!--        Tabla y paginacion-->
        <el-dialog title="Formulario de Categorías" v-model="modalForm" width="30%">
            <el-input placeholder="Categoría" v-model="model.nombre" clearable
                      :rules="[{required:true, message:'Campo obligatorio'}, {type:'number', message: 'Ingrese una categoría válida'}]">
            </el-input>
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
    import AppMain from "@/Layouts/AppMain";
    import ErrorForm from "@/Pages/Componentes/ErrorForm";
    import Cargando from "@/Pages/Componentes/Cargando";

    export default {
        name: "Index",
        props:['categorias', 'usuario'],
        components:{AppMain, ErrorForm, Cargando},
        data(){
            return{
                dataCategorias: this.categorias,
                modalForm: false,
                //bloque obligatorio para paginacion de tabla
                page: 1,
                pageSize: 10,
                total: 0,
                search: "",
                //bloque obligatorio para paginacion de tabla
                modalForm: false,
                model: {
                    nombre: "",
                    descripcion: "",
                },
                errores: null,
                loading: false,
            }
        },
        //bloque obligatorio para paginacion de tabla
        computed:{
            displayData() {
                if (!this.dataCategorias || this.dataCategorias.length === 0) return [];
                return this.dataCategorias.slice(this.pageSize * this.page - this.pageSize, this.pageSize * this.page)
            }
        },
        watch:{
            search: function (val) {
                if (val !== ""){
                    this.dataCategorias =  this.dataCategorias.filter(data => !val || data.nombre.toLowerCase().includes(val.toLowerCase()))
                }else{
                    this.dataCategorias = this.categorias
                }
            }
        },
        methods:{
            guardar() {
                this.loading = true;
                let url = this.model.hasOwnProperty('id') ? 'categorias.update' : 'categorias.store';
                axios.post(route(url), this.model)
                    .then((res) => {
                        this.$notify({
                            title: 'Transacción exitosa',
                            message: 'Solicitud realizada con éxito',
                            type: 'success'
                        });
                        this.dataCategorias = res.data.info;
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
                let model = this.dataCategorias.filter(categoria => categoria.id == row.id)[0];
                this.model.nombre = model.nombre;
                this.model.descripcion = model.descripcion;
                this.model.id = model.id;
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
                this.model.nombre = "";
                this.model.descripcion = "";
                delete this.model.id;
            }
        }
    }
</script>
