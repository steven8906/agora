<template>
    <app-main selected="6" titulo="Movimientos" :usuario="usuario">
        <el-button-group>
            <el-button type="primary" icon="el-icon-edit" @click="modalForm = true"></el-button>
            <el-button type="primary" icon="el-icon-plus" @click="modalFormTipo = true"></el-button>
            <el-button type="primary" icon="el-icon-paperclip"></el-button>
        </el-button-group>
        <br>
        <br>
        <h2><b>Listado de tipos de movimientos</b></h2>
        <el-divider></el-divider>
        <br>
        <br>
        <h2><b>Listado de productos</b></h2>
        <el-divider></el-divider>
        <el-row :gutter="20">
            <el-col :span="24">
                <el-card class="box-card">
                    <el-table v-if="dataProductos.length > 0"
                              :data="displayProductos"
                              stripe
                              border
                              style="width: 100%"
                              ref="tablaProductos"
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
        <el-dialog title="Movimientos entre almacenes" v-model="modalForm" width="50%" lock-scroll="false">
            <el-form ref="form" label-width="150px" :model="model" :disabled="disableForm">
                <el-form-item label="Tipo de movimiento:"
                              prop="tipoMovimiento"
                              :rules="[{required:true, message:'Seleccione una opcion'}]">
                    <el-radio-group v-model="model.tipoMovimiento">
                        <el-radio label="ENTRE_ALMACENES" border>Movimiento entre almacenes&nbsp;&nbsp;&nbsp;</el-radio>
                        <el-radio label="REGULAR" border>Movimiento estandar o regular</el-radio>
                    </el-radio-group>
                </el-form-item>
                <el-divider content-position="left">Productos seleccionados para este movimiento</el-divider>
                <br>
                <el-row class="row-bg" justify="center">
                    <el-col>
                        <div v-if="model.tipoMovimiento === 'ENTRE_ALMACENES'">
                            <el-col :span="10">
                                <div style="height: 100px;">
                                    <el-steps  direction="vertical">
                                        <el-step title="Almacén de salida"></el-step>
                                        <el-step title="Almacén de destino"></el-step>
                                    </el-steps>
                                </div>
                            </el-col>
                            <el-col :span="14">
                                <el-form-item prop="almacenEntrada" :rules="[{required:true, message:'Seleccione una opcion'}]">
                                    <el-select v-model="model.almacenEntrada" placeholder="Seleccione...">
                                        <el-option
                                            v-for="item in dataAlmacenes"
                                            :key="item.id"
                                            :label="item.almacen"
                                            :value="item.id">
                                        </el-option>
                                    </el-select>
                                </el-form-item>
                                <el-form-item prop="almacenDestino" :rules="[{required:true, message:'Seleccione una opcion'}]">
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
                        <div v-else-if="model.tipoMovimiento === 'REGULAR'">
                            <el-form-item>
                                <el-date-picker
                                    v-model="model.fecha"
                                    type="date"
                                    placeholder="Seleccione una fecha">
                                </el-date-picker>
                            </el-form-item>
                            <el-form-item style="width: 50%">
                                <el-input placeholder="Inserte folio documento" v-model="model.folio_documento"></el-input>
                            </el-form-item>
                            <el-form-item>
                                <el-select v-model="model.ubicacion" placeholder="Tipo de movimiento">
                                    <el-option
                                        v-for="item in dataTipoMovimientos"
                                        :key="item.id"
                                        :label="item.descripcion"
                                        :value="item.descripcion">
                                    </el-option>
                                </el-select>
                                &nbsp;
                                &nbsp;
                                &nbsp;
                                <el-select v-model="model.almacenEntrada" placeholder="Seleccione almacén...">
                                    <el-option
                                        v-for="item in dataAlmacenes"
                                        :key="item.id"
                                        :label="item.almacen"
                                        :value="item.id">
                                    </el-option>
                                </el-select>
                            </el-form-item>
                        </div>
                    </el-col>
                    <br>
                    <el-col>
                        <el-form-item>
                            <el-upload
                                style="width: 100%;"
                                class="el-upload-dragger"
                                action=""
                                :multiple="true"
                                :limit="10"
                                :on-change="upload"
                                :auto-upload = "false">
                                <i class="el-icon-upload"></i>
                                <div class="el-upload__text">Suelta tu archivo aquí o <em>haz clic para cargar</em></div>
                            </el-upload>
                        </el-form-item>
                        <br>
                    </el-col>
                    <el-col>
                        <el-card class="box-card" style="width: 30%;">
                            <template #header>
                                <div class="card-header">
                                    <span>Productos</span>
                                </div>
                            </template>
                            <p v-for="(producto, index) in model.productoSeleccion" :key="index"><b>{{producto.nombre}}</b>
                                <el-input placeholder="Cantidad" v-model="cantidades[index].cantidad"> </el-input></p>
                        </el-card>
                    </el-col>
                </el-row>
            </el-form>
            <br>
            <template #footer>
                <span class="dialog-footer">
                  <el-button @click="modalForm = false">Cancelar</el-button>
                  <el-button type="primary" @click="guardar">Aceptar</el-button>
                </span>
            </template>
            <error-form :errores="errores" v-if="errores !== null"></error-form>
        </el-dialog>

        <el-dialog title="Tipo de movimientos" v-model="modalFormTipo" width="50%" lock-scroll="false">
            <el-form ref="formTipoMovimiento" label-width="150px" :model="modelTipo">
                <el-form-item label="Tipo de movimiento:"
                              prop="descripcion"
                              :rules="[{required:true, message:'Debe insertar tipo de movimiento'}]">
                    <el-input placeholder="Tipo de movimiento" v-model="modelTipo.descripcion"></el-input>
                </el-form-item>
            </el-form>
            <template #footer>
                <span class="dialog-footer">
                  <el-button @click="modalFormTipo = false">Cancelar</el-button>
                  <el-button type="primary" @click="guardarTipo">Aceptar</el-button>
                </span>
            </template>
        </el-dialog>
        <cargando :mostrarCargando="loading"></cargando>
        <pre>{{cantidades}}</pre>
    </app-main>
</template>

<script>
    import AppMain from "@/Layouts/AppMain";
    import ErrorForm from "@/Pages/Componentes/ErrorForm";
    import Cargando from "@/Pages/Componentes/Cargando";
    import axios from "axios";
    export default {
        name: "Index",
        props:['usuario', 'unidades', 'productos', 'movimientos','almacenes','multialmacenes', 'tipoMovimientos', 'all_movimientos'],
        components:{AppMain, ErrorForm, Cargando},
        data(){
            return{
                model:{
                    tipoMovimiento:"",
                    almacenEntrada:"",
                    almacenDestino:"",
                    productoSeleccion: [],
                },
                cantidades:[],
                modelTipo:{},
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
                dataTipoMovimientos:this.tipoMovimientos,
                dataAllMovimientos:this.all_movimientos,
                listaArchivo:[],
                modalFormTipo:false
            }
        },
        mounted() {
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
                console.log(this.model.tipoMovimiento)
                if(true) {
                    this.$refs["form"].validate((valid) => {
                        if (valid) {
                            let form = new FormData();
                            if (this.listaArchivo.length > 0){
                                this.listaArchivo.map(file => {
                                    form.append('archivo[]', file.raw)
                                })
                            }

                            this.model.productos = JSON.stringify(this.model.productoSeleccion);
                            Object.entries(this.model).forEach(([key, value]) => form.append(key, value));
                            form.append('cantidadProductos', JSON.stringify(this.cantidades));
                            axios.defaults.headers.post['Content-Type'] = 'multipart/form-data;application/json';
                            axios.post(route('movimientos.store'),form)
                                .then((res) => {
                                    if (res.data.success){
                                        this.$notify({
                                            title: 'Transacción exitosa',
                                            message: 'Solicitud realizada con éxito',
                                            type: 'success'
                                        });
                                        this.dataCategorias = res.data.info;
                                        this.errores = null;
                                        this.modalForm = false;
                                        this.limpiarModelo();
                                        this.dataAllMovimientos = res.data.all_movimientos
                                    }
                                })
                                .catch((error) => {
                                    this.$notify({
                                        title: 'Transacción fallida',
                                        message: error.response.data.info,
                                        type: 'warning'
                                    });
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
                if(this.model.tipoMovimiento === 'REGULAR') {
                    this.$refs["form"].validate((valid) => {
                        if (valid) {
                            console.log(this.model.tipoMovimiento)
                        } else {
                            console.log("falló")
                        }
                    });
                }
            },
            guardarTipo(){
                this.$refs["formTipoMovimiento"].validate((valid) => {
                    if (valid) {
                        axios.post(route('movimientos.storeTipoMovimiento'),this.modelTipo)
                            .then((res) => {
                                if (res.data.success){
                                    this.$notify({
                                        title: 'Transacción exitosa',
                                        message: 'Solicitud realizada con éxito',
                                        type: 'success'
                                    });
                                    this.dataTipoMovimientos = res.data.info;
                                    this.modalFormTipo = false;
                                    this.modelTipo = {};
                                }
                            })
                            .catch((error) => {
                                this.$notify({
                                    title: 'Transacción fallida',
                                    message: error.response.data.info,
                                    type: 'warning'
                                });
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
            },
            //bloque obligatorio para paginacion de tabla
            handleCurrentChange(val) {
                this.page = val;
            },
            //bloque obligatorio para paginacion de tabla
            limpiarModelo(){
                this.model = {};
                this.$refs["tablaProductos"].clearSelection();
            },
            handleSelectionChange(val) {
                this.model.productoSeleccion = val;
                if(this.model.productoSeleccion.length>0){
                    this.disableForm = false;
                }else if(this.model.productoSeleccion.length==0){
                    this.disableForm = true;
                }
                this.model.productoSeleccion.forEach(item => {
                    this.cantidades.push({producto:item.id,cantidad:null});
                })
            },
            upload(imagen, lista){
                this.listaArchivo = lista;
                console.log(imagen, lista)
            },
            addCantidades(producto){
                console.log(producto)
            }
        }
    }
</script>

<style scoped>

</style>
