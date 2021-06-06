<template>
    <app-main selected="10" titulo="Productos" :usuario="usuario">
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
                    <el-table v-if="dataProductos.length > 0"
                              :data="displayData"
                              stripe
                              border
                              style="width: 100%">
                        <el-table-column label="Estatus" prop="condicion">
                            <template #default="scope">
                                <el-tag type="success" v-if="scope.row.condicion == 1" effect="dark">Activo</el-tag>
                                <el-tag type="danger" effect="dark" v-else>Inactivo</el-tag>
                            </template>
                        </el-table-column>
                        <el-table-column label="Categoría" prop="categoria"></el-table-column>
                        <el-table-column label="Producto" prop="proveedor"></el-table-column>
                        <el-table-column label="Código" prop="codigo"></el-table-column>
                        <el-table-column label="Tipo" prop="tipo"></el-table-column>
                        <el-table-column label="Ubicación" prop="ubicacion"></el-table-column>
                        <el-table-column
                            label="Imagén"
                            width="120">
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
                        <el-table-column label="P. Venta" prop="precio_venta"></el-table-column>
                        <el-table-column align="right"
                                         fixed="right"
                                         width="250">
                            <template #header>
                                <el-input v-model="search" size="mini" placeholder="Buscar..."/>
                            </template>
                            <template #default="scope">
                                <el-tooltip content="Multialmacén center" placement="top" effect="light">
                                    <el-button size="mini" icon="el-icon-connection"
                                               @click="verMultialmacen(scope.row.id)">
                                    </el-button>
                                </el-tooltip>
                                <el-tooltip content="Editar center" placement="top" effect="light">
                                    <el-button size="mini" type="warning" icon="el-icon-edit"
                                               @click="editarModal(scope.row)">
                                    </el-button>
                                </el-tooltip>
                                <el-popconfirm
                                    confirmButtonText='Aceptar'
                                    cancelButtonText='Cancelar'
                                    icon="el-icon-info"
                                    iconColor="red"
                                    title="¿Confirma esta operación?"
                                    @confirm="desactivar(scope.row)"
                                >
                                    <template #reference>
                                        <el-button size="mini" type="danger" icon="el-icon-delete"></el-button>
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
                            :total="dataProductos.length">
                        </el-pagination>
                    </div>
                </el-card>
            </el-col>
        </el-row>
        <!--        Tabla y paginacion-->
        <!--  Inicio-Formulario de registro y actualizacion-->
        <el-dialog title="Formulario de Productos" v-model="modalForm" width="40%">
            <el-form ref="form"  label-width="150px" :model="model">
                <el-form-item label="Categoría:"
                              :rules="[{required:true, message:'Campo obligatorio'}]">
                    <el-select v-model="model.idcategoria" placeholder="Categoría">
                        <el-option v-for="(categoria, index) in categorias" :key="index" :label="categoria.nombre" :value="categoria.id"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="Proveedor:"
                              :rules="[{required:true, message:'Campo obligatorio'}]">
                    <el-select v-model="model.idproveedor" placeholder="Proveedor">
                        <el-option v-for="(proveedor, index) in proveedores" :key="index" :label="proveedor.nombre" :value="proveedor.id"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="Producto:"
                              prop="nombre"
                              :rules="[{required:true, message:'Campo obligatorio'}]">
                    <el-input placeholder="Producto" v-model="model.nombre" clearable></el-input>
                </el-form-item>
                <el-form-item label="Código:"
                              prop="codigo"
                              :rules="[{required:true, message:'Campo obligatorio'}]">
                    <el-input placeholder="Código" v-model="model.codigo" clearable disabled></el-input>
                </el-form-item>
                <el-form-item label="Código alterno:"
                              prop="codigo_alterno">
                    <el-input placeholder="Código alterno" v-model="model.codigo_alterno" :value="0" clearable></el-input>
                </el-form-item>
                <el-form-item label="Tipo:"
                              :rules="[{required:true, message:'Campo obligatorio'}]">
                    <el-select v-model="model.tipo" placeholder="Tipo">
                        <el-option value="Producto">Producto</el-option>
                        <el-option value="Servicio">Servicio</el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="U. Entrada:"
                              :prop="unidad_entrada"
                              :rules="[{required:true, message:'Campo obligatorio'}]">
                    <el-select v-model="model.unidad_entrada" placeholder="U. Entrada" filterable >
                        <el-option v-for="(unidad, index) in unidades" :key="index" :value="unidad.id" :label="unidad.unidad">{{unidad.unidad}}</el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="U. Salida:"
                              :prop="unidad_salida"
                              :rules="[{required:true, message:'Campo obligatorio'}]">
                    <el-select v-model="model.unidad_salida" placeholder="U. Salida" filterable >
                        <el-option v-for="(unidad, index) in unidades" :key="index" :value="unidad.id" :label="unidad.unidad">{{unidad.unidad}}</el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="Precio compra:"
                              prop="precio_compra"
                              :rules="[{required:true, message:'Campo obligatorio'}, {type:'number', message: 'Ingrese un precio válido'}]">
                    <el-input placeholder="Precio compra" v-model.number="model.precio_compra" clearable></el-input>
                </el-form-item>
                <el-form-item label="Precio venta:"
                              :prop="precio_venta"
                              :rules="[{required:true, message:'Campo obligatorio'}, {type:'number', message: 'Ingrese un precio válido'}]">
                    <el-input placeholder="Precio venta" v-model.number="model.precio_venta" clearable></el-input>
                </el-form-item>
                <el-collapse>
                    <el-collapse-item title="Precio minímo" name="2">
                        <el-form-item label="Precio minímo:"
                                      :prop="precio_minimo"
                                      :rules="[{type:'number', message: 'Ingrese un precio válido'}]">
                            <el-input placeholder="Precio minímo" v-model.number="model.precio_minimo" model-value="0" clearable></el-input>
                        </el-form-item>
                    </el-collapse-item>
                    <el-collapse-item title="Precio liquidación" name="3">
                        <el-form-item label="Precio liquidación:"
                                      :prop="precio_liquidacion"
                                      :rules="[{type:'number', message: 'Ingrese un precio válido'}]">
                            <el-input placeholder="Precio liquidación" v-model.number="model.precio_liquidacion" model-value="0" clearable></el-input>
                        </el-form-item>
                    </el-collapse-item>
                    <el-collapse-item title="Precio mayorista" name="4">
                        <el-form-item label="Precio mayorista:"
                                      :prop="precio_mayorista"
                                      :rules="[{type:'number', message: 'Ingrese un precio válido'}]">
                            <el-input placeholder="Precio mayorista" v-model.number="model.precio_mayorista" model-value="0" clearable></el-input>
                        </el-form-item>
                    </el-collapse-item>
                </el-collapse>
                <br>
                <br>
                <el-form-item label="Ubicación:"
                              :prop="ubicacion">
                    <el-input placeholder="Ubicación" v-model.number="model.ubicacion" clearable></el-input>
                </el-form-item>
                <el-form-item label="Descripción:">
                    <el-input type="textarea" placeholder="Descripción" v-model="model.descripcion"></el-input>
                </el-form-item>
                <el-form-item label="Imágen:">
                    <el-upload
                        style="width: 100%;"
                        class="el-upload-dragger"
                        action=""
                        :multiple="false"
                        :limit="1"
                        :on-change="upload"
                        :auto-upload = "false">
                        <i class="el-icon-upload"></i>
                        <div class="el-upload__text">Suelta tu archivo aquí o <em>haz clic para cargar</em></div>
                    </el-upload>
                </el-form-item>
                <error-form :errores="errores" v-show="errores !== null"></error-form>
            </el-form>
            <el-divider></el-divider>
            <template #footer>
                <img :data-value="this.model.codigo" :data-text="this.model.codigo" class="codigo" style="margin: auto;"/>
                <el-divider></el-divider>
                <span class="dialog-footer">
                  <el-button @click="modalForm = false">Cancelar</el-button>
                  <el-button type="primary" @click="guardar">Aceptar</el-button>
                </span>
            </template>
        </el-dialog>
        <!--  Fin-Formulario de registro y actualizacion-->
        <!--  Inicio-Formulario de registro multialmacen-->
        <el-dialog
            title="Formulario de registro a multialmacén"
            v-model="modalMultialmacen"
            width="30%">
            <el-alert v-show="modelMultialmacen === null || modelMultialmacen.length === 0"
                      title="Sin datos para mostrar"
                      type="info"
                      description="Éste producto no tiene almacén asignado"
                      :closable="false"
                      show-icon>
            </el-alert>
            <el-divider></el-divider>
            <el-form ref="formMultialmacen" label-width="150px" :model="modelMultialmacen">
                <el-form-item label="Almacénes:"
                              prop="almacenes"
                              :rules="[{required:true, message:'Campo obligatorio'}]">
                    <el-select v-model="modelMultialmacen.almacenes" placeholder="Seleccione almacén" multiple filterable clearable>
                        <el-option v-for="item in almacenes" :key="item.id" :label="item.almacen" :value="item.id"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="Stock:"
                              prop="stock"
                              :rules="[{required:true, message:'Campo obligatorio'}, {type:'number', message:'Campo ingresado debe ser numérico'}]">
                    <el-input placeholder="Stock" v-model.number="modelMultialmacen.stock" clearable></el-input>
                </el-form-item>
                <el-form-item label="Stock minímo:"
                              prop="stock_minimo"
                              :rules="[{required:true, message:'Campo obligatorio'}, {type:'number', message:'Campo ingresado debe ser numérico'}]">
                    <el-input placeholder="Stock" v-model.number="modelMultialmacen.stock_minimo" clearable></el-input>
                </el-form-item>
                <el-form-item label="Stock máximo:"
                              prop="stock_maximo"
                              :rules="[{required:true, message:'Campo obligatorio'}, {type:'number', message:'Campo ingresado debe ser numérico'}]">
                    <el-input placeholder="Stock" v-model.number="modelMultialmacen.stock_maximo" clearable></el-input>
                </el-form-item>
                <error-form :errores="errores" v-show="errores !== null"></error-form>
            </el-form>
            <template #footer>
                <span class="dialog-footer">
                  <el-button @click="modalMultialmacen = false">Cancelar</el-button>
                  <el-button type="primary" @click="guardarMultialmacen">Aceptar</el-button>
                </span>
            </template>
        </el-dialog>
        <!--  Fin-Formulario de registro multialmacen-->
        <cargando :mostrarCargando="loading"></cargando>
    </app-main>
</template>

<script>
    import AppMain from "@/Layouts/AppMain";
    import ErrorForm from "@/Pages/Componentes/ErrorForm";
    import Cargando from "@/Pages/Componentes/Cargando";

    export default {
        name: "Index",
        props: ['categorias', 'proveedores', 'productos', 'codigo_barras', 'unidades', 'usuario', 'almacenes'],
        components: {AppMain, ErrorForm, Cargando},
        data() {
            return{
                dataProductos: this.productos,
                modalForm: false,
                //bloque obligatorio para paginacion de tabla
                page: 1,
                pageSize: 10,
                total: 0,
                search: "",
                //bloque obligatorio para paginacion de tabla
                modalForm: false,
                modalMultialmacen:false,
                model: {
                    codigo: this.codigo_barras
                },
                modelMultialmacen:null,
                imagen:null,
                listaArchivo:[],
                errores: null,
                loading: false,
            }
        },//bloque obligatorio para paginacion de tabla
        computed:{
            displayData() {
                if (!this.dataProductos || this.dataProductos.length === 0) return [];
                return this.dataProductos.slice(this.pageSize * this.page - this.pageSize, this.pageSize * this.page)
            }
        },
        watch:{
            search: function (val) {
                if (val !== ""){
                    this.dataProductos =  this.dataProductos.filter(data => !val || data.nombre.toLowerCase().includes(val.toLowerCase()))
                }else{
                    this.dataProductos = this.proveedores
                }
            }
        },
        methods:{
            guardar() {
                this.loading = true;
                let url = this.model.hasOwnProperty('id') ? 'productos.update' : 'productos.store';
                let form = new FormData();
                if (this.listaArchivo.length > 0) form.append('imagen', this.listaArchivo[0].raw);
                Object.entries(this.model).forEach(([key, value]) => form.append(key, value));
                axios.defaults.headers.post['Content-Type'] = 'multipart/form-data';
                axios.post(route(url), form)
                    .then((res) => {
                        this.$notify({
                            title: 'Transacción exitosa',
                            message: 'Solicitud realizada con éxito',
                            type: 'success'
                        });
                        this.dataProductos = res.data.info.productos;
                        this.errores = null;
                        this.modalForm = false;
                        this.limpiarModelo();
                        this.model.codigo = res.data.codigo_barras;
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
                let model = this.dataProductos.filter(proveedor => proveedor.id == row.id)[0];
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
            },
            upload(imagen, lista){
                this.listaArchivo = lista;
            },
            verMultialmacen(id_producto){
                this.loading = true;
                axios.get(`/multialmacen/producto/${id_producto}`)
                    .then(res => {
                        this.modalMultialmacen = true;
                        this.modelMultialmacen = res.data
                        this.modelMultialmacen.id_producto = id_producto;
                    })
                    .catch((error) => {
                        let aux = [];
                        let info = Object.values(error.response.data.errors);
                        info.forEach((item) => aux.push(item[0]));
                        this.errores = aux;
                    }).finally(() =>
                    setTimeout(() => {
                        this.loading = false
                    }, 1000));
            },
            guardarMultialmacen(){
                this.errores = null;
                this.loading = true
                const model = {
                    id_producto : this.modelMultialmacen.id_producto,
                    almacenes : this.modelMultialmacen.almacenes,
                    stock : this.modelMultialmacen.stock,
                    stock_minimo : this.modelMultialmacen.stock_minimo,
                    stock_maximo : this.modelMultialmacen.stock_maximo,
                }
                axios.post(route('multialmacen.store'), model)
                    .then((res)=>{
                        this.$notify({
                            title: 'Transacción exitosa',
                            message: 'Solicitud realizada con éxito',
                            type: 'success'
                        });
                        this.modelMultialmacen = res.data.info.multialmacenes;
                        this.modalMultialmacen = false;
                        this.dataProductos = res.data.info.productos;
                        this.model.codigo = res.data.codigo_barras;
                    })
                    .catch((error) => {
                        let aux = [];
                        let info = Object.values(error.response.data.errors);
                        info.forEach((item) => aux.push(item[0]));
                        this.errores = aux;
                    }).finally(() =>
                    setTimeout(() => {
                        this.loading = false
                    }, 1000));
            }
        },
        mounted() {
            JsBarcode(".codigo").init();
        }
    }
</script>
