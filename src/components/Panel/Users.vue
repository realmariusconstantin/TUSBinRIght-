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
            <option value="1">Student</option>
            <option value="2">Admin</option>
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
      this.editData = { user: user.user, email: user.email, user_type_id: user.user_type_id };
    },
    async saveUser(id) {
      await api.post('/admin/users/update', { id, ...this.editData });
      this.editId = null;
      await this.loadUsers();
    },
    async deleteUser(id) {
      if (!confirm('Delete this user?')) return;
      await api.post('/admin/users/delete', { id });
      await this.loadUsers();
    }
  }
};
</script>

<style scoped>
.page { 
  font-family: 'Inter', sans-serif; 
  padding: 1rem;
}

table {
  width: 100%;
  border-collapse: collapse;
  table-layout: fixed;
}

td, th {
  padding: 0.6rem 1rem;
  text-align: left;
  vertical-align: middle;
  border-bottom: 1px solid #e0e0e0;
  height: 48px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

td:first-child {
  padding-left: 1.2rem;
}

td span,
.input,
input,
select {
  display: block;
  width: 100%;
  box-sizing: border-box;
  height: 34px;
  line-height: 34px;
  font-size: 0.95rem;
  border-radius: 6px;
  border: 1px solid transparent;
  padding: 0 0.6rem;
}

.input,
input,
select {
  border: 1px solid #ccc;
  background-color: #fff;
}

td:last-child {
  display: flex;
  gap: 0.4rem;
  justify-content: flex-start;
  align-items: center;
}

button {
  min-width: 70px;
  height: 34px;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  font-size: 0.9rem;
  border-radius: 6px;
  border: 1px solid #ccc;
  background-color: #f7f7f7;
  cursor: pointer;
  padding: 0;
}
</style>