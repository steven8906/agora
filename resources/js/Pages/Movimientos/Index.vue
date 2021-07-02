<template>
    <app-main selected="6" titulo="Movimientos" :usuario="usuario">

        <el-button-group>
            <el-button type="primary" icon="el-icon-edit" @click="modalForm = true"></el-button>
            <el-button type="primary" icon="el-icon-paperclip"></el-button>
        </el-button-group>
        <br>
        <el-divider></el-divider>
        <el-row :gutter="20">
            <el-col :span="24">
                <el-card class="box-card">
                    <el-table v-if="dataProductos.length > 0"
                              :data="displayProductos"
                              stripe
                              border
                              style="width: 100%"
                              @selection-change="handleSelectionChange">
                        <el-table-column
                            type="selection"
                            width="55">
                        </el-table-column>
                        <el-table-column label="Producto" prop="nombre"></el-table-column>
                        <el-table-column label="Código" prop="codigo"></el-table-column>
                        <el-table-column
                            label="Imagén">
                            <template #default="scope">
                                <div class="demo-image__preview">
                                    <el-image
                                        style="width: 100px;"
                                        :src="scope.row.path_imagen"
                                        lazy
                                        :preview-src-list="[scope.row.path_imagen]">
                                    </el-image>
                                </div>
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
                            :total="dataProductos.length">
                        </el-pagination>
                    </div>
                </el-card>
            </el-col>
        </el-row>
        <el-dialog title="Movimientos entre almacenes" v-model="modalForm" width="40%" lock-scroll="false">
            <el-form ref="form" label-width="150px" :model="model" :disabled="disableForm">
                <el-form-item label="Tipo de movimiento:"
                              prop="tipoMovimiento"
                              :rules="[{required:true, message:'Seleccione una opcion'}]">
                    <el-radio-group v-model="model.tipoMovimiento">
                        <el-radio label="ENTRE_ALMACENES" border>Movimiento entre almacenes&nbsp;&nbsp;&nbsp;</el-radio>
                        <br>
                        <el-radio label="REGULAR" border>Movimiento estandar o regular</el-radio>
                    </el-radio-group>
                </el-form-item>
                <div v-if="model.tipoMovimiento=='ENTRE_ALMACENES'">
                    <el-col :span="10">
                        <div style="height: 100px;">
                            <el-steps  direction="vertical">
                                <el-step title="Almacén de salida"></el-step>
                                <el-step title="Almacén de destino"></el-step>
                            </el-steps>
                        </div>
                    </el-col>
                    <el-col :span="14">
                        <el-form-item :rules="[{required:true, message:'Seleccione una opcion',trigger:'change'}]">
                            <el-select v-model="model.almacenEntrada" placeholder="Seleccione...">
                                <el-option
                                    v-for="item in dataAlmacenes"
                                    :key="item.id"
                                    :label="item.almacen"
                                    :value="item.id">
                                </el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item :rules="[{required:true, message:'Seleccione una opcion',trigger:'change'}]">
                            <el-select v-model="model.almacenDestino" placeholder="Seleccione...">
                                <el-option
                                    v-for="item in dataAlmacenes"
                                    :key="item.id"
                                    :label="item.almacen"
                                    :value="item.id">
                                </el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                </div>
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
        <cargando :mostrarCargando="loading"></cargando>
    </app-main>
</template>

<script>
    import AppMain from "@/Layouts/AppMain";
    import ErrorForm from "@/Pages/Componentes/ErrorForm";
    import Cargando from "@/Pages/Componentes/Cargando";
    import axios from "axios";
    export default {
        name: "Index",
        props:['usuario', 'unidades', 'productos', 'movimientos','almacenes'],
        components:{AppMain, ErrorForm, Cargando},
        data(){
            return{
                model:{
                    tipoMovimiento:"",
                    almacenEntrada:"",
                    almacenDestino:"",
                    productoSeleccion: [],
                },
                //bloque obligatorio para paginacion de tabla
                page: 1,
                pageSize: 10,
                total: 0,
                search: "",
                //bloque obligatorio para paginacion de tabla
                disableForm:true,
                modalForm: false,
                errores: null,
                loading: false,
                dataProductos: this.productos,
                dataAlmacenes: this.almacenes,
            }
        },
        computed:{
            displayData() {
                if (!this.dataMovimientos || this.dataMovimientos.length === 0) return [];
                return this.dataMovimientos.slice(this.pageSize * this.page - this.pageSize, this.pageSize * this.page)
            },
            displayProductos() {
                if (!this.dataProductos || this.dataProductos.length === 0) return [];
                return this.dataProductos.slice(this.pageSize * this.page - this.pageSize, this.pageSize * this.page)
            }
        },
        watch:{
            search: function (val) {
                if (val !== ""){
                    this.dataMovimientos =  this.dataMovimientos.filter(data => !val || data.nombre.toLowerCase().includes(val.toLowerCase()))
                }else{
                    this.dataMovimientos = this.movimientos
                }
            }
        },
        methods:{
            guardar(){
                if(this.model.tipoMovimiento = 'ENTRE_ALMACENES') {
                    this.$refs["form"].validate((valid) => {
                        if (valid) {
                            axios.post(route('movimientos.store'),this.model)
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
                                })
                                .finally(() =>
                                    setTimeout(() => {
                                        this.loading = false
                                    }, 1000))
                        } else {
                            console.log('error submit!!');
                            setTimeout(() => {
                                this.loading = false
                            }, 1000)
                        }
                    });
                }
                if(this.model.tipoMovimiento = 'REGULAR') {
                    this.$refs["form"].validate((valid) => {
                        if (valid) {
                            console.log(this.model.tipoMovimiento)
                        } else {
                            console.log("falló")
                        }
                    });
                }
            },
            //bloque obligatorio para paginacion de tabla
            handleCurrentChange(val) {
                this.page = val;
            },
            //bloque obligatorio para paginacion de tabla
            limpiarModelo(){
                this.model = {};
            },
            handleSelectionChange(val) {
                this.model.productoSeleccion = val;
                if(this.model.productoSeleccion.length>0){
                    this.disableForm = false;
                }else if(this.model.productoSeleccion.length==0){
                    this.disableForm = true;
                }
            }
        }
    }
</script>

<style scoped>

</style>
