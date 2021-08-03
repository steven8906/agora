<template>
    <app-main selected="10" titulo="Proveedores" :usuario="usuario">
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
                    <el-table v-if="dataProveedores.length > 0"
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
                        <el-table-column label="Proveedor" prop="nombre"></el-table-column>
                        <el-table-column label="Teléfono" prop="telefono"></el-table-column>
                        <el-table-column label="Email" prop="email"></el-table-column>
                        <el-table-column label="Contacto" prop="contacto"></el-table-column>
                        <el-table-column label="Tel. Contacto" prop="telefono_contacto"></el-table-column>
<!--                        <el-table-column label="Tipo Documento" prop="tipo_documento"></el-table-column>-->
<!--                        <el-table-column label="N. Documento" prop="num_documento"></el-table-column>-->
                        <el-table-column label="Dirección" prop="direccion"></el-table-column>
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
                            :total="dataProveedores.length">
                        </el-pagination>
                    </div>
                </el-card>
            </el-col>
        </el-row>
        <!--        Tabla y paginacion-->
        <!--  Inicio-Formulario de registro y actualizacion-->
        <el-dialog title="Formulario de Proveedores" v-model="modalForm" width="30%">
            <el-form ref="form"  label-width="120px" :model="model">
                <el-form-item label="Proveedor"
                              prop="nombre"
                              :rules="[{required:true, message:'Campo obligatorio'}]">
                    <el-input placeholder="Proveedor" v-model="model.nombre" clearable></el-input>
                </el-form-item>
                <el-form-item label="Teléfono:"
                              prop="telefono"
                              :rules="[
                                  {required:true, message:'Campo obligatorio'},
                                  {type: 'number', message: 'Debe ingresar un teléfono válido'},
                                  ]">
                    <el-input placeholder="Teléfono" v-model.number="model.telefono" clearable></el-input>
                </el-form-item>
                <el-form-item
                    label="Email"
                    prop="email"
                    :rules="[{type: 'email', message: 'Debe ingresar un email válido', trigger: ['blur', 'change'] }]"
                >
                    <el-input v-model="model.email" placeholder="Email" clearable></el-input>
                </el-form-item>
                <el-form-item label="Contacto:"
                              prop="contacto"
                              :rules="[{required:true, message:'Campo obligatorio'}]">
                    <el-input placeholder="Contacto" v-model="model.contacto" clearable></el-input>
                </el-form-item>
                <el-form-item label="Tel. Contacto:"
                              prop="telefono_contacto"
                              :rules="[
                                  {required:true, message:'Campo obligatorio'},
                                  {type: 'number', message: 'Debe ingresar un teléfono válido', trigger: ['blur', 'change']},
                              ]">
                    <el-input placeholder="Tel. Contacto" v-model.number="model.telefono_contacto" clearable></el-input>
                </el-form-item>
                <!--<el-form-item label="Tipo Documento" prop="tipo_documento">
                    <el-select v-model="model.tipo_documento" placeholder="Tipo Documento">
                        <el-option label="DNI" value="DNI"></el-option>
                        <el-option label="RUC" value="RUC"></el-option>
                        <el-option label="PASS" value="PASS"></el-option>
                    </el-select>
                </el-form-item>-->
                <!--<el-form-item label="N. Documento:"
                              prop="num_documento"
                              :rules="[{type: 'number', message: 'Debe ingresar un número de documento válido', trigger: ['blur', 'change'] }]"
                >
                    <el-input placeholder="N. Documento" v-model.number="model.num_documento" clearable></el-input>
                </el-form-item>-->
                <el-form-item label="Dirección:">
                    <el-input placeholder="Dirección" v-model="model.direccion" clearable></el-input>
                </el-form-item>
                <error-form :errores="errores" v-show="errores !== null"></error-form>
            </el-form>
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
        props: ['proveedores', 'usuario'],
        components: {AppMain, ErrorForm, Cargando},
        data() {
            return{
                dataProveedores: this.proveedores,
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
                if (!this.dataProveedores || this.dataProveedores.length === 0) return [];
                return this.dataProveedores.slice(this.pageSize * this.page - this.pageSize, this.pageSize * this.page)
            }
        },
        watch:{
            search: function (val) {
                if (val !== ""){
                    this.dataProveedores =  this.dataProveedores.filter(data => !val || data.nombre.toLowerCase().includes(val.toLowerCase()))
                }else{
                    this.dataProveedores = this.proveedores
                }
            }
        },
        methods:{
            guardar() {
                this.loading = true;
                let url = this.model.hasOwnProperty('id') ? 'proveedores.update' : 'proveedores.store';
                axios.post(route(url), this.model)
                    .then((res) => {
                        this.$notify({
                            title: 'Transacción exitosa',
                            message: 'Solicitud realizada con éxito',
                            type: 'success'
                        });
                        this.dataProveedores = res.data.info;
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
                            this.model = {}
                        }, 1000)
                    );
            },
            editarModal(row) {
                this.modalForm = true;
                let model = this.dataProveedores.filter(proveedor => proveedor.id == row.id)[0];
                this.model = model;
                this.model.telefono = parseInt(model.telefono);
                this.model.telefono_contacto = parseInt(model.telefono_contacto);
            },
            desactivar(row) {
                this.loading = true
                axios.post(route('proveedores.disable'), {id: row.id, condicion: row.condicion})
                    .then((res) => {
                        this.$notify({
                            title: 'Transacción exitosa',
                            message: 'Solicitud realizada con éxito',
                            type: 'success'
                        });
                        this.dataProveedores = res.data.proveedores;
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
                this.model = {};
            }
        }
    }
</script>

<style scoped>

</style>
