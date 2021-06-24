<template>
    <app-main selected="6" titulo="Kits" :usuario="usuario">
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
                    <el-table v-if="dataKits.length > 0"
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
                            :total="dataKits.length">
                        </el-pagination>
                    </div>
                </el-card>
            </el-col>
        </el-row>
        <!--        Tabla y paginacion-->
        <el-dialog title="Formulario de Categorías" v-model="modalForm" width="30%">
            <el-form ref="form" label-width="150px" :model="model">
                <el-form-item label="Código de barras:">
                    <el-autocomplete
                        v-model="model.producto"
                        :fetch-suggestions="buscarProducto"
                        placeholder="Busque producto..."
                        @select="handleSelect"
                        clearable
                    ></el-autocomplete>
                </el-form-item>
                <el-image
                    v-for="(image, index) in model.imagenes"
                    :key="index"
                    style="width: 100px; height: 100px"
                    :preview-src-list="[image]"
                    :src="image">
                </el-image>
                <el-divider></el-divider>
                <el-form-item label="Código de barras:"
                              prop="codigo"
                              :rules="[{required:true, message:'Campo obligatorio'}, {type:'number', message:'Campo ingresado debe ser numérico'}]">
                    <el-input placeholder="Código de barras" v-model="model.codigo" :model-value="codigo_barras" clearable disabled>{{codigo_barras}}</el-input>
                </el-form-item>

                <el-form-item label="Nombre del kit:"
                              prop="nombre"
                              :rules="[{required:true, message:'Campo obligatorio'}]">
                    <el-input placeholder="Nombre del kit" v-model="model.nombre" clearable></el-input>
                </el-form-item>

                <el-form-item label="Precio compra:"
                              prop="precio_compra"
                              :rules="[{required:true, message:'Campo obligatorio'}, {type:'number', message:'Campo ingresado debe ser numérico'}]">
                    <el-input placeholder="Precio compra" v-model.number="model.precio_compra" clearable></el-input>
                </el-form-item>

                <el-form-item label="Precio venta:"
                              prop="precio_venta"
                              :rules="[{required:true, message:'Campo obligatorio'}, {type:'number', message:'Campo ingresado debe ser numérico'}]">
                    <el-input placeholder="Precio venta" v-model.number="model.precio_venta" clearable></el-input>
                </el-form-item>

                <el-form-item label="Descripción:"
                              prop="descripcion"
                              :rules="[{required:true, message:'Campo obligatorio'}]">
                    <el-input placeholder="Descripción" v-model="model.descripcion" clearable></el-input>
                </el-form-item>

                <el-form-item label="Ubicación:"
                              prop="ubicacion"
                              :rules="[{required:true, message:'Campo obligatorio'}]">
                    <el-input placeholder="Ubicación" v-model="model.ubicacion" clearable></el-input>
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
        <cargando :mostrarCargando="loading"></cargando>
    </app-main>
</template>

<script>
    import AppMain from "@/Layouts/AppMain";
    import ErrorForm from "@/Pages/Componentes/ErrorForm";
    import Cargando from "@/Pages/Componentes/Cargando";
    export default {
        name: "Index",
        props:['usuario', 'unidades', 'codigo_barras', 'productos', 'kits'],
        components:{AppMain, ErrorForm, Cargando},
        data(){
            return {
                dataKits: this.kits,
                modalForm: false,
                //bloque obligatorio para paginacion de tabla
                page: 1,
                pageSize: 10,
                total: 0,
                search: "",
                //bloque obligatorio para paginacion de tabla
                model: {
                    codigo:this.codigo_barras,
                    productos : [],
                    imagenes: []
                },
                errores: null,
                loading: false,
            }
        },
        //bloque obligatorio para paginacion de tabla
        computed:{
            displayData() {
                if (!this.dataKits || this.dataKits.length === 0) return [];
                return this.dataKits.slice(this.pageSize * this.page - this.pageSize, this.pageSize * this.page)
            }
        },
        watch:{
            search: function (val) {
                if (val !== ""){
                    this.dataKits =  this.dataKits.filter(data => !val || data.nombre.toLowerCase().includes(val.toLowerCase()))
                }else{
                    this.dataKits = this.kits
                }
            }
        },
        methods:{
            guardar() {
                this.loading = true;
                let url = this.model.hasOwnProperty('id') ? 'kit.update' : 'kit.store';
                axios.post(route(url), this.model)
                    .then((res) => {
                        this.$notify({
                            title: 'Transacción exitosa',
                            message: 'Solicitud realizada con éxito',
                            type: 'success'
                        });
                        this.dataKits = res.data.info;
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
                let model = this.dataKits.filter(kit => kit.id == row.id)[0];
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
            handleSelect(item){
                let productos = this.model.productos.filter(producto => producto == item.key);
                if (productos.length === 0){
                    this.model.productos.push(item.key);
                    this.model.imagenes.push(item.icon);
                }
            },
            buscarProducto(query, appendItems){
                if (query.length >= 3){
                    let timeout;
                    clearTimeout(timeout);
                    timeout = setTimeout(() => {
                        axios.get(route('productos.producto', query))
                            .then(res => {
                                let data = [];
                                res.data.info.map((producto) => {
                                    data.push({ key:producto.id, value: producto.nombre, icon: producto.path_imagen })
                                })
                                appendItems(data);
                            })
                    }, 2000);
                }
            }
        }
    }
</script>

<style scoped>

</style>
