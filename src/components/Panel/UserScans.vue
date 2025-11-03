<template>
  <div class="page">
    <h1>User Scans</h1>

    <div class="form">
      <select v-model="filterUserId" class="input" @change="loadScans">
        <option value="">All Users</option>
        <option v-for="u in users" :key="u.id" :value="u.id">
          {{ u.email }} ({{ u.user }})
        </option>
      </select>

      <ActionButton
        type="delete"
        @action="deleteSelected"
        :disabled="selectedScans.length === 0"
      >
        Delete Selected
      </ActionButton>
    </div>

    <DataTable :headers="['Select', 'User Name', 'Item Type', 'Scan Time', 'Actions']">
      <tr v-for="s in scans" :key="s.id">
        <td>
          <input type="checkbox" v-model="selectedScans" :value="s.id" />
        </td>
        <td>{{ s.user_name }}</td>
        <td>{{ s.item_type }}</td>
        <td>{{ formatScanTime(s.created_at) }}</td>
        <td>
          <ActionButton type="delete" @action="deleteSingle(s.id)">Delete</ActionButton>
        </td>
      </tr>
    </DataTable>

    <div class="pagination">
      <div class="pagination-controls">
        <label>
          Page:
          <select v-model="page" class="input" @change="loadScans">
            <option v-for="p in totalPages" :key="p" :value="p">{{ p }}</option>
          </select>
        </label>

        <label>
          Limit:
          <select v-model="limit" class="input" @change="changeLimit">
            <option v-for="n in [5, 10, 25, 50]" :key="n" :value="n">{{ n }}</option>
          </select>
        </label>
      </div>

      <div class="pagination-info">
        Showing {{ scans.length }} / {{ totalCount }} scans
      </div>
    </div>
  </div>
</template>

<script>
import api from '@/lib/api';
import DataTable from './Shared/DataTable.vue';
import ActionButton from './Shared/ActionButton.vue';

export default {
  name: 'UserScans',
  components: { DataTable, ActionButton },
  data() {
    return {
      scans: [],
      users: [],
      filterUserId: '',
      selectedScans: [],
      page: 1,
      limit: 10,
      totalCount: 0,
      totalPages: 1
    };
  },
  async mounted() {
    await this.loadUsers();
    await this.loadScans();
  },
  methods: {
    async loadScans() {
      const offset = (this.page - 1) * this.limit;
      const { data } = await api.get('/admin/user-scans', {
        params: {
          limit: this.limit,
          offset,
          user_id: this.filterUserId || undefined
        }
      });

      this.scans = data.scans || [];
      this.totalCount = data.total || this.scans.length;
      this.totalPages = Math.max(1, Math.ceil(this.totalCount / this.limit));
    },

    async loadUsers() {
      const { data } = await api.get('/admin/users');
      this.users = data.users || [];
    },

    formatScanTime(time) {
      if (!time) return '—';
      const date = new Date(time.replace(' ', 'T'));
      if (isNaN(date)) return time;
      return date.toLocaleString();
    },

    async deleteSelected() {
      if (this.selectedScans.length === 0) return;
      if (!confirm(`Delete ${this.selectedScans.length} selected scans?`)) return;
      await api.delete('/admin/user-scans', { data: { scan_ids: this.selectedScans } });
      this.selectedScans = [];
      await this.loadScans();
    },

    async deleteSingle(id) {
      if (!confirm('Delete this scan?')) return;
      await api.delete('/admin/user-scans', { data: { scan_ids: [id] } });
      await this.loadScans();
    },

    async changeLimit() {
      this.page = 1;
      await this.loadScans();
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

td,
th {
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

.input,
select {
  border: 1px solid #ccc;
  border-radius: 6px;
  background-color: #fff;
  height: 34px;
  font-size: 0.95rem;
  padding: 0 0.6rem;
  box-sizing: border-box;
}

td:last-child {
  display: flex;
  gap: 0.4rem;
  align-items: center;
}

button {
  min-width: 70px;
  height: 34px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 6px;
  border: 1px solid #ccc;
  cursor: pointer;
}

.form {
  display: flex;
  gap: 0.5rem;
  margin-bottom: 1.5rem;
  align-items: center;
}

.pagination {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 1rem;
  flex-wrap: wrap;
}

.pagination-controls {
  display: flex;
  gap: 1rem;
  align-items: center;
}

.pagination-info {
  font-size: 0.9rem;
  color: #555;
}
</style>