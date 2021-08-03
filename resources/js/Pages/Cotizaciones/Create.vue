<template>
  <app-main selected="14" titulo="Nueva Cotización" :usuario="usuario">
    <inertia-link :href="route('cotizaciones.index')">
      <el-button icon="el-icon-back">Volver</el-button>
    </inertia-link>
    <el-row :gutter="10">
      <el-col :span="6"></el-col>
      <el-col :span="6" :offset="12" align="right">
        <!-- fecha de cotizacion -->
        <el-date-picker
          v-model="form.fecha"
          type="date"
          placeholder="Fecha de Cotización"
          :disabled-date="disabledDate"
          :shortcuts="shortcuts"
          format="YYYY/MM/DD"
        >
        </el-date-picker>
      </el-col>
    </el-row>
    <el-divider></el-divider>
    <el-row>
      <el-col :span="12">
        <h1 class="title__list">Datos de cliente</h1>
      </el-col>
      <el-col :span="12" align="right">
        <el-button type="primary" @click="dialogTableVisible = true"
          ><i class="el-icon-plus el-icon-right"></i>Agregar Cliente</el-button
        >
      </el-col>
    </el-row>
    <el-row :gutter="10">
      <el-col :span="12">
        <el-input
          placeholder="Cliente"
          v-model="usuarioSeleccionado.cliente"
          :disabled="true"
        ></el-input>
      </el-col>
      <el-col :span="12">
        <el-input
          placeholder="Contacto"
          v-model="usuarioSeleccionado.contacto"
          :disabled="true"
        ></el-input>
      </el-col>
    </el-row>
    <el-row :gutter="10">
      <el-col :span="8">
        <el-input
          placeholder="Email"
          v-model="usuarioSeleccionado.email"
          :disabled="true"
        ></el-input>
      </el-col>
      <el-col :span="8">
        <el-input
          placeholder="Telefono oficina"
          v-model="usuarioSeleccionado.telefono_oficina"
          :disabled="true"
        ></el-input>
      </el-col>
      <el-col :span="8">
        <el-input
          placeholder="Telefono Movil"
          v-model="usuarioSeleccionado.telefono_movil"
          :disabled="true"
        ></el-input>
      </el-col>
    </el-row>
    <h1 class="title__list">Datos de cotizacion</h1>
    <el-row :gutter="10">
      <el-col :span="6">
        <el-input placeholder="Número de Folio" v-model="form.folio"></el-input>
      </el-col>
      <el-col :span="6">
        <el-input
          placeholder="Nombre de la Obra"
          v-model="form.obra"
        ></el-input>
      </el-col>
      <el-col :span="6">
        <el-input placeholder="Ubicación" v-model="form.ubicacion"></el-input>
      </el-col>
      <el-col :span="6">
        <el-input
          placeholder="Tiempo de entrega"
          v-model="form.tiempo_entrega"
        ></el-input>
      </el-col>
    </el-row>
    <el-row :gutter="10">
      <el-col :span="24">
        <el-input
          placeholder="Condiciones"
          v-model="form.condiciones"
        ></el-input>
      </el-col>
    </el-row>
    <!-- listado de partidas -->
    <h1 class="title__list">Listado de partidas</h1>

    <el-row :gutter="20">
      <el-col :span="24">
        <el-card class="box-card">
          <el-table
            v-if="form.dataPartidas.length > 0"
            :data="form.dataPartidas"
            style="width: 100%"
          >
            <el-table-column
              align="center"
              width="100"
              label="Partida"
              prop="partida"
            ></el-table-column>
            <el-table-column
              align="center"
              width="100"
              label="Cantidad"
              prop="cantidad"
            ></el-table-column>
            <el-table-column
              prop="unidad"
              align="center"
              width="100"
              label="Unidad"
            >
              <template #default="scope">
                <span style="margin-left: 10px">{{
                  unidades[scope.row.unidad - 1].unidad
                }}</span>
              </template>
            </el-table-column>
            <el-table-column
              align="center"
              label="Descripcion"
              prop="descripcion"
            ></el-table-column>
            <el-table-column
              align="center"
              width="150"
              label="Precio"
              prop="precio"
            ></el-table-column>
            <el-table-column
              align="center"
              width="150"
              label="Total"
              prop="total"
            ></el-table-column>

            <el-table-column width="150" align="right">
              <template #header>
                <!-- <el-input
                  v-model="search"
                  size="mini"
                  placeholder="Buscar..."
                /> -->
              </template>
              <template #default="scope">
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
                      @click.prevent="removeItemForm(scope.$index)"
                      size="mini"
                      type="danger"
                      icon="el-icon-delete"
                      >Eliminar</el-button
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

          <!-- boton de nueva partida -->
          <div style="margin-top: 20px">
            <el-button @click="dialogAgregarPartida = true"
              ><i class="el-icon-plus el-icon-right"></i>Agregar
              Partida</el-button
            >
          </div>
          <!-- Boton de subtotal/total/iva -->
          <div v-show="true" align="right">
            <h4>
              Subtotal:
              {{
                new Intl.NumberFormat("de-DE", {
                  style: "currency",
                  currency: "MEX",
                }).format(displaySubtotal)
              }}
            </h4>
            <h4>
              Iva:
              {{
                new Intl.NumberFormat("de-DE", {
                  style: "currency",
                  currency: "MEX",
                }).format(form.monto_iva)
              }}
            </h4>
            <h4>
              Total:
              {{
                new Intl.NumberFormat("de-DE", {
                  style: "currency",
                  currency: "MEX",
                }).format(form.monto_total)
              }}
            </h4>
          </div>

          <el-divider></el-divider>
          <div style="text-align: center">
            <el-input
              placeholder="Observaciones(Opcional)"
              type="textarea"
              v-model="form.observaciones"
            ></el-input>
          </div>
        </el-card>
      </el-col>
    </el-row>
    <!-- upload file of excel  -->
    <h1 class="title__list">Archivo de Calculo de cotizacion</h1>
    <template v-if="true"
      ><el-upload
        class="upload-demo"
        ref="upload"
        action=""
        :multiple="false"
        :limit="1"
        :on-change="handleChange"
        :on-exceed="handleExceed"
        :auto-upload="false"
      >
        <el-button size="small" type="primary">Clic para subir</el-button>
        <template #tip>
          <div class="el-upload__tip">Archivo con un tamaño menor a 500kb</div>
        </template>
      </el-upload></template
    >

    <!-- botones de crear o cancelar -->
    <div align="right">
      <el-link
        :href="route('cotizaciones.index')"
        :underline="false"
        style="padding: 0 6px"
      >
        <el-button type="danger" plain>Cancelar</el-button>
      </el-link>
      <template v-if="true"
        ><el-button type="primary" :loading="loading" @click="crearCotizacion"
          >Crear Cotizacion</el-button
        ></template
      >
    </div>

    <!-- modal de nuevo cliente -->
    <el-dialog title="Listado de clientes" v-model="dialogTableVisible">
      <el-table :data="dataClientes">
        <el-table-column
          property="cliente"
          label="Cliente"
          width="300"
        ></el-table-column>
        <el-table-column property="contacto" label="Contacto"></el-table-column>
        <el-table-column property="Seleccion" label="">
          <template #default="scope">
            <el-button @click="selectCliente(scope.$index)" size="medium" round
              >Seleccionar</el-button
            >
          </template>
        </el-table-column>
      </el-table>
    </el-dialog>

    <!-- modal de agregar partida -->
    <el-dialog title="Agregar Partida" v-model="dialogAgregarPartida">
      <el-form
        :label-position="labelPosition"
        label-width="100px"
        :model="formPartida"
        ref="formPartida"
        :rules="rules"
      >
        <el-form-item label="Partida" prop="partida">
          <el-input-number
            v-model="formPartida.partida"
            @change="handleChange"
            :min="1"
          ></el-input-number>
        </el-form-item>

        <el-form-item label="Descripcion" prop="descripcion">
          <el-input
            type="textarea"
            v-model="formPartida.descripcion"
          ></el-input>
        </el-form-item>
        <el-form-item label="Cantidad" prop="cantidad">
          <el-input-number
            :precision="2"
            :step="0.1"
            v-model="formPartida.cantidad"
            :min="1"
          ></el-input-number>
        </el-form-item>
        <el-form-item label="Unidad" prop="unidad">
          <el-select v-model="formPartida.unidad" placeholder="Unidad">
            <el-option
              v-for="item in unidades"
              :key="item.id"
              :label="item.unidad"
              :value="item.id"
            >
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="Precio" prop="precio">
          <el-input-number
            :precision="2"
            :step="0.1"
            v-model="formPartida.precio"
            :min="1"
          ></el-input-number>
        </el-form-item>

        <el-form-item label="Total">
          <h1 style="font-weight: bold; color: #000">{{ calculatePrice }}</h1>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="submitFormPartida('formPartida')"
            >Agregar</el-button
          >
          <el-button @click="dialogAgregarPartida = false">Cancel</el-button>
        </el-form-item>
      </el-form>
    </el-dialog>

    <!-- modal de errores -->
    <el-dialog title="Error" v-model="erroresDialogVisible" width="30%" center>
      <span>Ha ocurrido un error, verifique los datos ingresados.</span>
      <error-form :errores="errores"></error-form>
      <template #footer>
        <span class="dialog-footer">
          <el-button @click="erroresDialogVisible = false">Cancelar</el-button>
          <el-button type="primary" @click="erroresDialogVisible = false"
            >Cerrar</el-button
          >
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
  props: ["clientes", "usuario", "unidades"],
  components: { AppMain, ErrorForm, Cargando },
  data() {
    return {
      disabledDate(time) {
        return time.getTime() > Date.now();
      },
      shortcuts: [
        {
          text: "Hoy",
          value: new Date(),
        },
        {
          text: "Ayer",
          value: (() => {
            const date = new Date();
            date.setTime(date.getTime() - 3600 * 1000 * 24);
            return date;
          })(),
        },
      ],
      //bloque obligatorio para paginacion de tabla
      page: 1,
      pageSize: 10,
      total: 0,
      search: "",
      unidades: this.unidades,
      //bloque obligatorio para paginacion de tabla
      usuarioSeleccionado: {
        cliente: "",
        contacto: "",
        email: "",
        telefono_oficina: "",
        telefono_movil: "",
      },
      form: {
        fecha: new Date(),
        folio: "",
        obra: "",
        tiempo_entrega: "",
        cliente_id: "",
        observaciones: "",
        dataPartidas: [],
        monto_subtotal: 0,
        monto_iva: 0,
        monto_total: 0,
        condiciones: "",
      },
      dialogTableVisible: false,
      dataClientes: this.clientes,
      dialogAgregarPartida: false,
      loading: false,
      fileCalculo: [],
      formPartida: {
        partida: 1,
        cantidad: 1.0,
        unidad: "",
        descripcion: "",
        precio: 1.0,
        total: 1.0,
      },
      erroresDialogVisible: false,
      rules: {
        partida: [
          {
            required: true,
            message: "Por favor, ingrese un numero de partida",
            trigger: "blur",
          },
          {
            type: "integer",
            message: "Debe ser un Número entero",
            trigger: "change",
          },
        ],
        cantidad: [
          {
            required: true,
            message: "Por favor, ingrese una cantidad",
            trigger: "blur",
          },
        ],
        unidad: [
          {
            required: true,
            message: "Por favor, ingrese una unidad",
            trigger: "blur",
          },
        ],
        descripcion: [
          {
            required: true,
            message: "Porfavor, ingrese una descripción",
            trigger: "blur",
          },
          { min: 10, message: "Coloque mas de 10 caracteres", trigger: "blur" },
        ],
        precio: [
          {
            required: true,
            message: "Por favor, ingrese un precio",
            trigger: "blur",
          },
        ],
        total: [
          {
            required: true,
            message: "Please select activity resource",
            trigger: "blur",
          },
          {
            type: "float",
            message: "Debe ser un valor decimal",
            trigger: "blur",
          },
        ],
      },
    };
  },
  //bloque obligatorio para paginacion de tabla
  computed: {
    calculatePrice() {
      this.formPartida.total =
        this.formPartida.precio * this.formPartida.cantidad;
      return new Intl.NumberFormat("de-DE", {
        style: "currency",
        currency: "MEX",
      }).format(this.formPartida.total);
    },
    displaySubtotal() {
      this.form.monto_subtotal = this.getSubtotal();
      this.form.monto_iva = this.form.monto_subtotal * 0.16;
      this.form.monto_total = this.form.monto_iva + this.form.monto_subtotal;
      return this.form.monto_subtotal;
    },
  },
  methods: {
    submitFormPartida(formName) {
      this.$refs[formName].validate((valid) => {
        if (valid) {
          this.form.dataPartidas.push({
            partida: this.formPartida.partida,
            cantidad: this.formPartida.cantidad,
            unidad: this.formPartida.unidad,
            descripcion: this.formPartida.descripcion,
            precio: this.formPartida.precio,
            total: this.formPartida.total,
          });
          //reset of form
          this.formPartida = {
            partida: this.formPartida.partida + 1,
            cantidad: 1.0,
            unidad: "",
            descripcion: "",
            precio: 1.0,
            total: 1.0,
          };
          this.dialogAgregarPartida = false;
        } else {
          console.log("error submit!!");
          return false;
        }
      });
    },
    removeItemForm(index) {
      if (index !== -1) {
        this.form.dataPartidas.splice(index, 1);
      }
    },
    getSubtotal() {
      var sum = 0;
      this.form.dataPartidas.forEach((value, index, array) => {
        sum = sum + value.total;
      });
      return sum;
    },
    selectCliente(index) {
      this.usuarioSeleccionado = this.dataClientes[index];
      this.form.cliente_id = this.dataClientes[index].id;
      this.dialogTableVisible = false;
    },
    crearCotizacion() {
      this.loading = true;
      // this.erroresDialogVisible = false;
      let url = "cotizaciones.store";
      // fecha
      let fecha = this.form.fecha.toLocaleDateString("en-CA", {
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
      });
      //enviar datos de cotizacion al server
      let formData = new FormData();
      //agregar archivo si es mayor a 0kb
      if (this.fileCalculo.length > 0)
        formData.append("ArchivoDeCalculo", this.fileCalculo[0].raw);
      //almacenar variables del form
      Object.entries(this.form).forEach(([key, value]) =>
        formData.append(key, value)
      );
      // agregar fecha procesada
      formData.append("fechacotizacion", fecha);
      //Agregar partidas
      formData.append("partidas", JSON.stringify(this.form.dataPartidas));
      //encabezadao de multiparts/form
      axios.defaults.headers.post["Content-Type"] = "multipart/form-data";
      axios
        .post(route(url), formData)
        .then((res) => {
          this.$message.success(`Cotización creada con exito.`);
          //Limpiar campos
          this.errores = null;
          this.form = {
            fecha: new Date(),
            folio: "",
            obra: "",
            tiempo_entrega: "",
            cliente_id: "",
            observaciones: "",
            dataPartidas: [],
            monto_subtotal: 0,
            monto_iva: 0,
            monto_total: 0,
            condiciones: "",
          };
          this.fileCalculo = [];
          this.formPartida = {
            partida: 1,
            cantidad: 1.0,
            unidad: "",
            descripcion: "",
            precio: 1.0,
            total: 1.0,
          };
          this.usuarioSeleccionado = {
            cliente: "",
            contacto: "",
            email: "",
            telefono_oficina: "",
            telefono_movil: "",
          };
        })
        .catch((error) => {
          //Se muestra modal con errores
          this.erroresDialogVisible = true;
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
    handleExceed(files, fileList) {
      this.$message.warning(
        `Solo esta permitido  ${files.length} un archivo de calculo.`
      );
    },
    handleChange(imagen, lista) {
      this.fileCalculo = lista;
    },
  },
};
</script>

<style>
.el-row {
  margin-bottom: 10px;
}

.title__list {
  font-weight: bold;
  font-size: 1rem;
  padding: 10px 0px;
}
</style>