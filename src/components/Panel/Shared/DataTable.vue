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
  background: var(--bg-primary);
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
  transition: background-color 0.3s ease;
  table-layout: fixed;
}
th, td {
  padding: 0.8rem 1rem;
  border-bottom: 1px solid var(--border-color);
  text-align: left;
  color: var(--text-primary);
  vertical-align: middle;
  transition: border-color 0.3s ease, color 0.3s ease;
  height: 52px;
}

/* Column widths for consistent alignment */
th:first-child,
td:first-child {
  width: 60px;
  text-align: center;
}

th:last-child,
td:last-child {
  width: 160px;
  text-align: center;
  white-space: nowrap;
}

th {
  background: #00ADB5;
  color: white;
  font-weight: 600;
}
tbody tr {
  background: var(--bg-primary);
  transition: background-color 0.3s ease;
}
tbody tr:hover {
  background: var(--bg-secondary);
}

/* Action buttons inside last column */
td:last-child button {
  margin: 0 4px;
  vertical-align: middle;
}
</style>