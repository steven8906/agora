<template>
    <app-main selected="13" titulo="Clientes" :usuario="usuario">
        <el-button-group>
            <el-button
                type="primary"
                icon="el-icon-edit"
                @click="modalForm = true"
            ></el-button>
            <el-button type="primary" icon="el-icon-paperclip"></el-button>
        </el-button-group>
        <!--        Tabla y paginacion-->
        <br />
        <el-divider></el-divider>
        <el-row :gutter="20">
            <el-col :span="24">
                <el-card class="box-card">
                    <el-table
                        v-if="dataClientes.length > 0"
                        :data="displayData"
                        style="width: 100%"
                    >
                        <el-table-column label="Nombre" prop="nombre">
                        </el-table-column>
                        <el-table-column label="RFC" prop="rfc">
                        </el-table-column>
                        <el-table-column label="Direccíon" prop="direccion">
                        </el-table-column>
                        <el-table-column label="Telefono" prop="telefono">
                        </el-table-column>
                        <el-table-column label="Correo" prop="email">
                        </el-table-column>

                        <el-table-column align="right">
                            <template #header>
                                <el-input
                                    v-model="search"
                                    size="mini"
                                    placeholder="Buscar..."
                                />
                            </template>
                            <template #default="scope">
                                <el-button
                                    size="mini"
                                    type="warning"
                                    icon="el-icon-edit"
                                    @click="editarModal(scope.row)"
                                    >Editar
                                </el-button>
                                <el-popconfirm
                                    confirmButtonText="Aceptar"
                                    cancelButtonText="Cancelar"
                                    icon="el-icon-info"
                                    iconColor="red"
                                    title="¿Confirma esta operación?"
                                    @confirm="desactivar(scope.row)"
                                >
                                    <template #reference>
                                        <el-button
                                            size="mini"
                                            type="danger"
                                            icon="el-icon-delete"
                                            >Desactivar</el-button
                                        >
                                    </template>
                                </el-popconfirm>
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
                            :total="dataClientes.length"
                        >
                        </el-pagination>
                    </div>
                </el-card>
            </el-col>
        </el-row>

        <!--        Tabla y paginacion-->
        <el-dialog
            title="Formulario de Clientes"
            v-model="modalForm"
            width="30%"
        >
            <el-input
                placeholder="Nombre"
                v-model="model.nombre"
                clearable
            ></el-input>
            <br />
            <br />
            <el-input
                placeholder="RFC"
                v-model="model.rfc"
                clearable
            ></el-input>
            <br />
            <br />
            <el-input
                placeholder="Dirección"
                v-model="model.direccion"
                clearable
            ></el-input>
            <br />
            <br />
            <el-input
                placeholder="Telefono"
                v-model="model.telefono"
                clearable
            ></el-input>
            <br />
            <br />
            <el-input
                placeholder="Correo"
                v-model="model.email"
                clearable
            ></el-input>
            <br />
            <br />

            <template #footer>
                <span class="dialog-footer">
                    <el-button @click="modalForm = false">Cancelar</el-button>
                    <el-button type="primary" @click="guardar"
                        >Aceptar</el-button
                    >
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
    components: { AppMain, ErrorForm, Cargando },
    props: ["clientes", "usuario"],
    data() {
        return {
            dataClientes: this.clientes,
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
    //bloque obligatorio para paginacion de tabla
    computed: {
        displayData() {
            if (!this.dataClientes || this.dataClientes.length === 0) return [];
            return this.dataClientes.slice(
                this.pageSize * this.page - this.pageSize,
                this.pageSize * this.page
            );
        },
    },
    watch: {
        search: function (val) {
            if (val !== "") {
                this.dataClientes = this.dataClientes.filter(
                    (data) =>
                        !val ||
                        data.nombre.toLowerCase().includes(val.toLowerCase()) ||
                        data.rfc.toLowerCase().includes(val.toLowerCase()) ||
                        data.direccion.toLowerCase().includes(val.toLowerCase()) ||
                        data.telefono.toLowerCase().includes(val.toLowerCase()) ||
                        data.email.toLowerCase().includes(val.toLowerCase())
                );
            } else {
                this.dataClientes = this.clientes;
            }
        },
    },
    //bloque obligatorio para paginacion de tabla
    methods: {
        guardar() {
            this.loading = true;
            let url = this.model.hasOwnProperty("id")
                ? "clientes.update"
                : "clientes.store";
            axios
                .post(route(url), this.model)
                .then((res) => {
                    this.dataClientes = res.data.info;
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
                        this.loading = false;
                    }, 1000)
                );
        },
        editarModal(row) {
            this.modalForm = true;
            let model = this.dataClientes.filter(
                (cliente) => cliente.id == row.id
            )[0];
            this.model.nombre = model.nombre;
            this.model.rfc = model.rfc;
            this.model.direccion = model.direccion;
            this.model.telefono = model.telefono;
            this.model.email = model.email;
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
        limpiarModelo() {
            this.model.nombre = "";
            this.model.rfc = "";
            this.model.direccion = "";
            this.model.telefono = "";
            this.model.email = "";
            delete this.model.id;
        },
    },
};
</script>

<style scoped></style>
