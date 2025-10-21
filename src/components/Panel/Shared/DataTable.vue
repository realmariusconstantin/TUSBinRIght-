<template>
  <table class="data-table">
    <thead>
      <tr>
        <th v-for="(header, index) in headers" :key="index">{{ header }}</th>
      </tr>
    </thead>
    <tbody>
      <slot></slot>
    </tbody>
  </table>
</template>

<script>
export default {
  name: 'DataTable',
  props: { headers: Array }
}
</script>

<style scoped>
.data-table {
  width: 100%;
  border-collapse: collapse;
  background: white;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
}
th, td {
  padding: 0.8rem;
  border-bottom: 1px solid #eee;
  text-align: left;
}
th {
  background: #00ADB5;
  color: white;
  font-weight: 600;
}
tr:hover {
  background: #f9f9f9;
}
</style>