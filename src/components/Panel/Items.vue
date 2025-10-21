<template>
  <div class="page">
    <h1>Items</h1>

    <form @submit.prevent="addItem" class="form">
      <InputField v-model="form.name" placeholder="Name" required />
      <InputField v-model="form.description" placeholder="Description" />
      <InputField v-model="form.bar_code" placeholder="Barcode" />
      <InputField v-model="form.qr_code" placeholder="QR Code" />
      <ActionButton type="save">Add</ActionButton>
    </form>

    <DataTable :headers="['ID', 'Name', 'Description', 'Barcode', 'QR Code', 'Actions']">
      <tr v-for="item in items" :key="item.id">
        <td>{{ item.id }}</td>
        <td>
          <InputField v-if="editId === item.id" v-model="editData.name" />
          <span v-else>{{ item.name }}</span>
        </td>
        <td>
          <InputField v-if="editId === item.id" v-model="editData.description" />
          <span v-else>{{ item.description }}</span>
        </td>
        <td>
          <InputField v-if="editId === item.id" v-model="editData.bar_code" />
          <span v-else>{{ item.bar_code }}</span>
        </td>
        <td>
          <InputField v-if="editId === item.id" v-model="editData.qr_code" />
          <span v-else>{{ item.qr_code }}</span>
        </td>
        <td>
          <ActionButton v-if="editId === item.id" type="save" @click="saveItem(item.id)">Save</ActionButton>
          <ActionButton v-else type="edit" @click="editItem(item)">Edit</ActionButton>
          <ActionButton type="delete" @click="deleteItem(item.id)">Delete</ActionButton>
        </td>
      </tr>
    </DataTable>
  </div>
</template>

<script>
import api from '@/lib/api';
import DataTable from './Shared/DataTable.vue';
import InputField from './Shared/InputField.vue';
import ActionButton from './Shared/ActionButton.vue';

export default {
  name: 'Items',
  components: { DataTable, InputField, ActionButton },
  data() {
    return {
      items: [],
      form: { name: '', description: '', bar_code: '', qr_code: '' },
      editId: null,
      editData: { name: '', description: '', bar_code: '', qr_code: '' }
    };
  },
  async mounted() {
    await this.loadItems();
  },
  methods: {
    async loadItems() {
      const { data } = await api.get('/admin/items');
      this.items = data.items;
    },
    async addItem() {
      await api.post('/admin/items', this.form);
      this.form = { name: '', description: '', bar_code: '', qr_code: '' };
      await this.loadItems();
    },
    editItem(item) {
      this.editId = item.id;
      this.editData = { ...item };
    },
    async saveItem(id) {
      await api.put(`/admin/items/${id}`, this.editData);
      this.editId = null;
      await this.loadItems();
    },
    async deleteItem(id) {
      if (!confirm('Delete this item?')) return;
      await api.delete(`/admin/items/${id}`);
      await this.loadItems();
    }
  }
};
</script>

<style scoped>
.page { font-family: 'Inter', sans-serif; }
.form { display: flex; gap: 0.5rem; margin-bottom: 1.5rem; }
</style>