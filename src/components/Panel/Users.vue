<template>
  <div class="page">
    <h1>Users</h1>

    <DataTable :headers="['ID', 'User', 'Email', 'Type', 'Actions']">
      <tr v-for="u in paginatedUsers" :key="u.id">
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

    <!-- Pagination -->
    <div class="pagination" v-if="totalPages > 1">
      <button 
        @click="currentPage = Math.max(1, currentPage - 1)" 
        :disabled="currentPage === 1"
        class="pagination-btn"
      >
        <i class="fas fa-chevron-left"></i> Previous
      </button>

      <div class="page-numbers">
        <button 
          v-for="page in visiblePages" 
          :key="page"
          @click="currentPage = page"
          :class="{ active: currentPage === page }"
          class="page-number"
        >
          {{ page }}
        </button>
      </div>

      <button 
        @click="currentPage = Math.min(totalPages, currentPage + 1)" 
        :disabled="currentPage === totalPages"
        class="pagination-btn"
      >
        Next <i class="fas fa-chevron-right"></i>
      </button>

      <span class="pagination-info">
        Page {{ currentPage }} of {{ totalPages }} ({{ users.length }} total users)
      </span>
    </div>
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
      editData: { user: '', email: '', user_type_id: '' },
      currentPage: 1,
      itemsPerPage: 10
    };
  },
  computed: {
    totalPages() {
      return Math.ceil(this.users.length / this.itemsPerPage);
    },
    paginatedUsers() {
      const start = (this.currentPage - 1) * this.itemsPerPage;
      const end = start + this.itemsPerPage;
      return this.users.slice(start, end);
    },
    visiblePages() {
      const pages = [];
      const maxVisible = 5;
      const half = Math.floor(maxVisible / 2);

      let start = Math.max(1, this.currentPage - half);
      let end = Math.min(this.totalPages, this.currentPage + half);

      if (start === 1) {
        end = Math.min(this.totalPages, maxVisible);
      } else if (end === this.totalPages) {
        start = Math.max(1, this.totalPages - maxVisible + 1);
      }

      for (let i = start; i <= end; i++) {
        pages.push(i);
      }
      return pages;
    }
  },
  async mounted() {
    await this.loadUsers();
  },
  methods: {
    async loadUsers() {
      const { data } = await api.get('/admin/users');
      this.users = data.users;
      this.currentPage = 1;
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

/* Pagination Styles */
.pagination {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  margin-top: 30px;
  padding: 20px;
  background: linear-gradient(145deg, #ffffff 0%, #f7f9fc 100%);
  border-radius: 12px;
  border: 1px solid rgba(15, 23, 42, 0.1);
  flex-wrap: wrap;
}

.pagination-btn {
  padding: 8px 16px;
  background: linear-gradient(135deg, #4CAF50 0%, #2E7D32 100%);
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 6px;
  transition: all 0.3s ease;
}

.pagination-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
}

.pagination-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-numbers {
  display: flex;
  gap: 6px;
}

.page-number {
  width: 36px;
  height: 36px;
  padding: 0;
  border: 1px solid #ccc;
  background: white;
  color: #333;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.2s ease;
}

.page-number:hover {
  border-color: #4CAF50;
  color: #4CAF50;
}

.page-number.active {
  background: linear-gradient(135deg, #4CAF50 0%, #2E7D32 100%);
  color: white;
  border-color: #4CAF50;
}

.pagination-info {
  font-size: 14px;
  color: #666;
  font-weight: 500;
}

@media (max-width: 768px) {
  .pagination {
    gap: 8px;
  }

  .pagination-btn {
    padding: 6px 12px;
    font-size: 12px;
  }

  .page-number {
    width: 30px;
    height: 30px;
    font-size: 12px;
  }

  .pagination-info {
    flex-basis: 100%;
    text-align: center;
    order: 4;
  }
}
</style>