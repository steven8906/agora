<template>
    <app-main selected="2" titulo="Multialmacenes" :usuario="usuario">
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
<!--                        <el-table-column label="Estatus" prop="condicion" align="center">-->
<!--                            <template #default="scope">-->
<!--                                <el-tag type="success" v-if="scope.row.condicion == 1" effect="dark">Activo</el-tag>-->
<!--                                <el-tag type="danger" effect="dark" v-else>Inactivo</el-tag>-->
<!--                            </template>-->
<!--                        </el-table-column>-->
                        <el-table-column label="Producto" prop="producto"></el-table-column>
                        <el-table-column label="Código" prop="codigo"></el-table-column>
                        <el-table-column label="Stock total actual" prop="stock"></el-table-column>
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
                        <el-table-column align="center"
                                         fixed="right"
                                         width="250">
                            <template #header>
                                <el-input v-model="search" size="mini" placeholder="Buscar..."/>
                            </template>
                            <template #default="scope">
                                <el-tooltip content="Multialmacén center" placement="top" effect="light">
                                    <el-button size="mini" icon="el-icon-connection"
                                               @click="verMultialmacen(scope.row.id_producto)">
                                    </el-button>
                                </el-tooltip>
                                <el-popconfirm v-if="scope.row.condicion == 1"
                                               confirmButtonText='Aceptar'
                                               cancelButtonText='Cancelar'
                                               icon="el-icon-info"
                                               iconColor="red"
                                               title="¿Confirma esta operación?"
                                               @confirm="desactivarActivar(scope.row, true)"
                                >
                                    <template #reference>
                                        <el-button size="mini" type="danger" icon="el-icon-delete"></el-button>
                                    </template>
                                </el-popconfirm>
                                <el-popconfirm v-else
                                               confirmButtonText='Aceptar'
                                               cancelButtonText='Cancelar'
                                               icon="el-icon-info"
                                               iconColor="red"
                                               title="¿Confirma esta operación?"
                                               @confirm="desactivarActivar(scope.row, false)"
                                >
                                    <template #reference>
                                        <el-button size="mini" type="success" icon="el-icon-check"></el-button>
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
        <el-dialog title="Formulario de Multi almacénes" v-model="modalForm" width="30%">
            <el-form ref="formMultialmacen" label-width="150px" :model="model.multialmacen">
                <div v-for="(item, index) in multialmacenesProducto" :key="index">
                    <div>
                        <el-card class="box-card">
                            <template #header>
                                <div class="card-header">
                                    <div style="display: flex;justify-content: space-between;align-items: center;">
                                        <div>
                                            <span><b>{{item.almacen}}</b></span>
                                        </div>
                                        <div>
                                            <el-tag
                                                v-if="item.condicion === 1"
                                                :type="'success'"
                                                effect="dark">
                                                Activo
                                            </el-tag>
                                            <el-tag
                                                v-else
                                                :type="'danger'"
                                                effect="dark">
                                                Inactivo
                                            </el-tag>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <div class="text item">
                                <el-form-item label="Stock:"
                                              prop="stock">
                                    <el-input-number :precision="2"
                                                     :controls="false"
                                                     placeholder="Stock"
                                                     v-model.number="model.multialmacen[index].stock"
                                                     clearable
                                                     :disabled="item.condicion !== 1"></el-input-number>
                                </el-form-item>
                                <el-form-item label="Stock minímo:"
                                              prop="stock_minimo">
                                    <el-input-number :precision="2"
                                                     :controls="false"
                                                     placeholder="Stock minímo"
                                                     v-model.number="model.multialmacen[index].stock_minimo"
                                                     clearable
                                                     :disabled="item.condicion !== 1"></el-input-number>
                                </el-form-item>
                                <el-form-item label="Stock máximo:"
                                              prop="stock_maximo">
                                    <el-input-number :precision="2"
                                                     :controls="false"
                                                     placeholder="Stock máximo"
                                                     v-model.number="model.multialmacen[index].stock_maximo"
                                                     clearable
                                                     :disabled="item.condicion !== 1"></el-input-number>
                                </el-form-item>
                            </div>
                        </el-card>
                        <el-divider></el-divider>
                    </div>
                </div>
                <error-form v-if="errores !== null" :errores="errores"></error-form>
            </el-form>
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
        props:['productos', 'usuario', 'multialmacenes', 'multialmacenes_all'],
        components:{AppMain, ErrorForm, Cargando},
        data(){
            return{
                dataProductos: this.multialmacenes,
                //bloque obligatorio para paginacion de tabla
                page: 1,
                pageSize: 10,
                total: 0,
                search: "",
                //bloque obligatorio para paginacion de tabla
                modalForm:false,
                model:{
                    multialmacen:[]
                },
                multialmacenesProducto: [],
                loading: false,
                errores:null
            }
        },//bloque obligatorio para paginacion de tabla
        computed:{
            displayData() {
                if (!this.dataProductos || this.dataProductos.length === 0) return [];
                return this.dataProductos.slice(this.pageSize * this.page - this.pageSize, this.pageSize * this.page)
            },
        },
        watch:{
            search: function (val) {
                console.log(val)
                if (val !== ""){
                    this.dataProductos =  this.dataProductos.filter(data => !val || data.producto.toLowerCase().includes(val.toLowerCase()))
                }else{
                    this.dataProductos = this.productos
                }
            },
            multialmacenesProducto: function (val) {
                this.model.multialmacen = [];
                val.forEach(item =>{
                    this.model.multialmacen.push({
                        id:item.id,
                        id_producto: item.id_producto,
                        id_almacen:item.id_almacen,
                        stock:item.stock,
                        stock_minimo:item.stock_minimo,
                        stock_maximo:item.stock_maximo,
                    })
                })
            }
        },
        methods:{
            //bloque obligatorio para paginacion de tabla
            handleCurrentChange(val) {
                this.page = val;
            },
            //bloque obligatorio para paginacion de tabla
            verMultialmacen(id_producto){
                this.modalForm = true;
                axios.get('multialmacenes/consulta/' + id_producto)
                    .then(res=>{
                        this.multialmacenesProducto = res.data.info;
                    })
                    .catch(error =>{
                        console.log(error)
                    })
            },
            guardar() {
                //this.loading = true;
                this.$confirm('¿En verdad desea hacer esto?', 'Alerta', {
                    confirmButtonText: 'Sí',
                    cancelButtonText: 'No',
                    type: 'warning',
                    center: true
                }).then(() => {let url = this.model.hasOwnProperty('id') ? 'multialmacenes.update' : 'multialmacenes.store';
                    axios.post(route(url), this.model)
                        .then((res) => {
                            this.$notify({
                                title: 'Transacción exitosa',
                                message: 'Solicitud realizada con éxito',
                                type: 'success'
                            });
                            this.errores = null;
                            this.modalForm = false;
                        })
                        .catch((error) => {
                            let aux = [];
                            if (error.hasOwnProperty('response')){
                                let info = Object.values(error.response.data.errors);
                                info.forEach((item) => aux.push(item[0]));
                                this.errores = aux;
                            }else{
                                this.$notify({
                                    title: 'Transacción fallida',
                                    message: error,
                                    type: 'danger'
                                });
                            }
                        }).finally(() =>
                        setTimeout(() => {
                            this.loading = false
                        }, 1000)
                    );
                }).catch(() => {
                    this.errores = null;
                    this.modalForm = false;
                });
            },
            desactivarActivar(row, condicion){
                console.log(row, condicion)
            }
        },
    }
</script>

<style scoped>

</style>
