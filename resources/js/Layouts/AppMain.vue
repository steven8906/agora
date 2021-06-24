<template>
  <el-container style="height: 100vh; border: 1px solid #eee;">
    <el-aside width="15vw" style="background-color: #06163A">
      <div style="border-bottom: 1px solid #132146">
        <inertia-link :href="route('dashboard')" style="padding: 1rem;color:white;text-align: center">
          <h2>Agora</h2>
        </inertia-link>
      </div>
      <el-menu :default-openeds="[selected]" style="background: #06163A">
        <el-submenu index="0" style="background-color: #06163A">
          <template #title><b><i class="el-icon-message c-white" style="vertical-align: baseline"></i></b><span class="c-white">Almacénes</span></template>
          <inertia-link :href="route('almacenes.index')">
            <el-menu-item index="1" class="b-primary-l c-white">
              <b><i class="el-icon-box c-white" style="vertical-align: baseline"></i></b>Almacénes
            </el-menu-item>
          </inertia-link>
          <inertia-link :href="route('categorias.index')">
            <el-menu-item index="2" class="b-primary-l c-white">
              <b><i class="el-icon-star-off c-white" style="vertical-align: baseline"></i></b>Categorías
            </el-menu-item>
          </inertia-link>
          <inertia-link :href="route('movimientos.index')">
            <el-menu-item index="3" class="b-primary-l c-white" >
              <b><i class="el-icon-sort c-white" style="vertical-align: baseline"></i></b>Movimientos
            </el-menu-item>
          </inertia-link>
        </el-submenu>
        <el-submenu index="4" style="background-color: #06163A">
          <template #title><b><i class="el-icon-message c-white" style="vertical-align: baseline"></i></b><span class="c-white">Productos</span></template>
            <inertia-link :href="route('productos.index')">
                <el-menu-item index="5" class="b-primary-l c-white" >
                    <b><i class="el-icon-sort c-white" style="vertical-align: baseline"></i></b>Productos
                </el-menu-item>
            </inertia-link>
          <inertia-link :href="route('multialmacenes.index')">
            <el-menu-item index="14" class="b-primary-l c-white">
              <b><i class="el-icon-box c-white" style="vertical-align: baseline"></i></b>Multialmacenes
            </el-menu-item>
          </inertia-link>
          <inertia-link :href="route('kit.index')">
            <el-menu-item index="6" class="b-primary-l c-white">
              <b><i class="el-icon-box c-white" style="vertical-align: baseline"></i></b>Kits
            </el-menu-item>
          </inertia-link>
          <inertia-link :href="route('unidades.index')">
            <el-menu-item index="7" class="b-primary-l c-white">
              <b><i class="el-icon-star-off c-white" style="vertical-align: baseline"></i></b>Unidades
            </el-menu-item>
          </inertia-link>
        </el-submenu>
        <el-submenu index="8" style="background-color: #06163A">
          <template #title><b><i class="el-icon-message c-white" style="vertical-align: baseline"></i></b><span class="c-white">Compras</span></template>
            <inertia-link :href="''">
                <el-menu-item index="9" class="b-primary-l c-white" >
                    <b><i class="el-icon-sort c-white" style="vertical-align: baseline"></i></b>Ingresos
                </el-menu-item>
            </inertia-link>
          <inertia-link :href="route('proveedores.index')">
            <el-menu-item index="10" class="b-primary-l c-white">
              <b><i class="el-icon-box c-white" style="vertical-align: baseline"></i></b>Proveedores
            </el-menu-item>
          </inertia-link>
        </el-submenu>
        <el-submenu index="11" style="background-color: #06163A">
          <template #title><b><i class="el-icon-message c-white" style="vertical-align: baseline"></i></b><span class="c-white">Ventas</span></template>
            <inertia-link :href="''">
                <el-menu-item index="12" class="b-primary-l c-white" >
                    <b><i class="el-icon-sort c-white" style="vertical-align: baseline"></i></b>Ventas
                </el-menu-item>
            </inertia-link>
          <inertia-link :href="route('clientes.index')">
            <el-menu-item index="13" class="b-primary-l c-white">
              <b><i class="el-icon-box c-white" style="vertical-align: baseline"></i></b>Clientes
            </el-menu-item>
          </inertia-link>
        <inertia-link :href="route('cotizaciones.index')">
            <el-menu-item index="14" class="b-primary-l c-white">
                <b><i class="el-icon-box c-white" style="vertical-align: baseline"></i></b>Cotizaciones
            </el-menu-item>
        </inertia-link>
        </el-submenu>
      </el-menu>
    </el-aside>

    <el-container>
      <el-header style="text-align: right; font-size: 12px">
        <p style="font-weight: bold;font-size: 1rem; float: left;clear: both;">{{titulo}}</p>
        <el-dropdown>
          <i class="el-icon-setting" style="margin-right: 15px"></i>
          <template #dropdown>
            <el-dropdown-menu>
              <el-dropdown-item><i class="el-icon-user" style="vertical-align: baseline"></i> Mi perfil</el-dropdown-item>
              <inertia-link @click="$inertia.post(route('logout'))">
                <el-dropdown-item><i class="el-icon-lock" style="vertical-align: baseline"></i> Cerrar sesión</el-dropdown-item>
              </inertia-link>
            </el-dropdown-menu>
          </template>
        </el-dropdown>
        <span><i class="el-icon-user-solid" style="vertical-align: baseline"></i> {{usuario.name}}</span>
      </el-header>

      <el-main>
        <slot></slot>
      </el-main>
    </el-container>
  </el-container>
</template>

<script scoped>
export default {
  name: "AppMain",
  props: ["selected", "titulo", "usuario"],
};
</script>


<style>
.el-container {
  border: none !important;
}
.el-header {
  background-color: #ffffff;
  color: #333;
  line-height: 60px;
}

.el-menu {
  border-right: solid 5px #ff601c;
  list-style: none;
  position: relative;
  margin: 0;
  padding-left: 0;
  background-color: #fff;
}

.c-white {
  color: white !important;
}
.b-primary-l {
  background: #132146 !important;
}
.b-primary-l:hover {
  background: #6583a8 !important;
}
.el-submenu__title:hover {
  background-color: #6583a8;
  color: white !important;
}
.el-main {
  background: #edf5f8;
}
</style>
