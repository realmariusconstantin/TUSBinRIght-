<template>
  <div class="page">
    <h1>Users</h1>

    <DataTable :headers="['ID', 'User', 'Email', 'Type', 'Actions']">
      <tr v-for="u in users" :key="u.id">
        <td>{{ u.id }}</td>
        <td>
          <InputField v-if="editId === u.id" v-model="editData.user" />
          <span v-else>{{ u.user }}</span>
        </td>
        <td>
          <InputField v-if="editId === u.id" v-model="editData.email" />
          <span v-else>{{ u.email }}</span>
        </td>
        <td>
          <select v-if="editId === u.id" v-model="editData.user_type_id" class="input">
            <option value="1">Admin</option>
            <option value="2">User</option>
          </select>
          <span v-else>{{ u.user_type }}</span>
        </td>
        <td>
          <ActionButton v-if="editId === u.id" type="save" @click="saveUser(u.id)">Save</ActionButton>
          <ActionButton v-else type="edit" @click="editUser(u)">Edit</ActionButton>
          <ActionButton type="delete" @click="deleteUser(u.id)">Delete</ActionButton>
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
  name: 'Users',
  components: { DataTable, InputField, ActionButton },
  data() {
    return {
      users: [],
      editId: null,
      editData: { user: '', email: '', user_type_id: '' }
    };
  },
  async mounted() {
    await this.loadUsers();
  },
  methods: {
    async loadUsers() {
      const { data } = await api.get('/admin/users');
      this.users = data.users;
    },
    editUser(user) {
      this.editId = user.id;
      this.editData = { user: user.user, email: user.email, user_type_id: user.user_type_id || 2 };
    },
    async saveUser(id) {
      await api.put(`/admin/users/${id}`, this.editData);
      this.editId = null;
      await this.loadUsers();
    },
    async deleteUser(id) {
      if (!confirm('Delete this user?')) return;
      await api.delete(`/admin/users/${id}`);
      await this.loadUsers();
    }
  }
};
</script>

<style scoped>
.page { font-family: 'Inter', sans-serif; }
.input { width: 100%; padding: 0.4rem; border-radius: 6px; border: 1px solid #ccc; }
</style>
