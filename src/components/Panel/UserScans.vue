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
        :disabled="scans.length === 0"
      >
        Delete Selected
      </ActionButton>
    </div>

    <DataTable :headers="['Select', 'User Name', 'Item Type', 'Scan Time', 'Actions']">
      <tr v-for="s in paginatedScans" :key="s.id">
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
        Page {{ currentPage }} of {{ totalPages }} ({{ scans.length }} total scans)
      </span>
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
      currentPage: 1,
      itemsPerPage: 10
    };
  },
  computed: {
    totalPages() {
      return Math.ceil(this.scans.length / this.itemsPerPage);
    },
    paginatedScans() {
      const start = (this.currentPage - 1) * this.itemsPerPage;
      const end = start + this.itemsPerPage;
      return this.scans.slice(start, end);
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
    await this.loadScans();
  },
  methods: {
    async loadScans() {
      const { data } = await api.get('/admin/user-scans', {
        params: {
          user_id: this.filterUserId || undefined
        }
      });

      this.scans = data.scans || [];
      this.currentPage = 1;
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
  min-width: 0;
  height: 36px;
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