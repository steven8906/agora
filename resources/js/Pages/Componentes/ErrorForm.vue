<template>
  <div>
    <br><br>
    <el-alert type="error" show-icon v-show="errores != null">
      <template #title>
        <ul>
          <li v-for="(item, index) in errores" :key="index">
            {{ item }}
          </li>
        </ul>
      </template>
    </el-alert>
  </div>
</template>

<script>
export default {
  name: "ErrorForm",
  props: ["errores"],
};
</script>
