<template>
  <div class="page">
    <h1>Disposal Rules</h1>

    <form @submit.prevent="createRule" class="form">
      <select v-model="newRule.item_type_id" class="input" required>
        <option disabled value="">Select Item Type</option>
        <option v-for="t in itemTypes" :key="t.id" :value="t.id">{{ t.item_type }}</option>
      </select>

      <select v-model="newRule.location_id" class="input" required>
        <option disabled value="">Select Location</option>
        <option v-for="l in locations" :key="l.id" :value="l.id">{{ l.location }}</option>
      </select>

      <select v-model="newRule.bin_type_id" class="input" required>
        <option disabled value="">Select Bin Type</option>
        <option v-for="b in binTypes" :key="b.id" :value="b.id">{{ b.bin_type }}</option>
      </select>

      <InputField v-model="newRule.description" placeholder="Description" required />
      <ActionButton type="save">Add</ActionButton>
    </form>

    <DataTable :headers="['ID', 'Item Type', 'Location', 'Bin Type', 'Description', 'Actions']">
      <tr v-for="r in paginatedRules" :key="r.id">
        <td>{{ r.id }}</td>

        <td>
          <select v-if="editId === r.id" v-model="editData.item_type_id" class="input">
            <option v-for="t in itemTypes" :key="t.id" :value="t.id">{{ t.item_type }}</option>
          </select>
          <span v-else>{{ r.item_type }}</span>
        </td>

        <td>
          <select v-if="editId === r.id" v-model="editData.location_id" class="input">
            <option v-for="l in locations" :key="l.id" :value="l.id">{{ l.location }}</option>
          </select>
          <span v-else>{{ r.location }}</span>
        </td>

        <td>
          <select v-if="editId === r.id" v-model="editData.bin_type_id" class="input">
            <option v-for="b in binTypes" :key="b.id" :value="b.id">{{ b.bin_type }}</option>
          </select>
          <span v-else>{{ r.bin_type }}</span>
        </td>

        <td>
          <InputField v-if="editId === r.id" v-model="editData.description" />
          <span v-else>{{ r.description }}</span>
        </td>

        <td>
          <ActionButton v-if="editId === r.id" type="save" @click="saveRule(r.id)">Save</ActionButton>
          <ActionButton v-else type="edit" @click="editRule(r)">Edit</ActionButton>
          <ActionButton type="delete" @click="deleteRule(r.id)">Delete</ActionButton>
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
        Page {{ currentPage }} of {{ totalPages }} ({{ rules.length }} total rules)
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
  name: 'DisposalRules',
  components: { DataTable, InputField, ActionButton },
  data() {
    return {
      rules: [],
      itemTypes: [],
      locations: [],
      binTypes: [],
      editId: null,
      editData: { description: '', item_type_id: '', location_id: '', bin_type_id: '' },
      newRule: { item_type_id: '', location_id: '', bin_type_id: '', description: '' },
      currentPage: 1,
      itemsPerPage: 10
    };
  },
  computed: {
    totalPages() {
      return Math.ceil(this.rules.length / this.itemsPerPage);
    },
    paginatedRules() {
      const start = (this.currentPage - 1) * this.itemsPerPage;
      const end = start + this.itemsPerPage;
      return this.rules.slice(start, end);
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
    await Promise.all([this.loadDropdowns(), this.loadRules()]);
  },
  methods: {
    async loadDropdowns() {
      const [itemRes, locRes, binRes] = await Promise.all([
        api.get('/admin/item-types'),
        api.get('/admin/locations'),
        api.get('/admin/bin-types')
      ]);
      this.itemTypes = itemRes.data.item_types || [];
      this.locations = locRes.data.locations || [];
      this.binTypes = binRes.data.bin_types || [];
    },
    async loadRules() {
      const { data } = await api.get('/admin/disposal-rules');
      this.rules = data.rules || [];
      this.currentPage = 1;
    },
    async createRule() {
      if (!this.newRule.item_type_id || !this.newRule.location_id || !this.newRule.bin_type_id) return;
      await api.post('/admin/disposal-rules/create', this.newRule);
      this.newRule = { item_type_id: '', location_id: '', bin_type_id: '', description: '' };
      await this.loadRules();
    },
    editRule(rule) {
      this.editId = rule.id;
      this.editData = {
        description: rule.description,
        item_type_id: rule.item_type_id || this.findIdByName(this.itemTypes, 'item_type', rule.item_type),
        location_id: rule.location_id || this.findIdByName(this.locations, 'location', rule.location),
        bin_type_id: rule.bin_type_id || this.findIdByName(this.binTypes, 'bin_type', rule.bin_type)
      };
    },
    findIdByName(list, key, value) {
      const match = list.find(item => item[key] === value);
      return match ? match.id : '';
    },
    async saveRule(id) {
      await api.post('/admin/disposal-rules/update', { id, ...this.editData });
      this.editId = null;
      await this.loadRules();
    },
    async deleteRule(id) {
      if (!confirm('Delete this rule?')) return;
      await api.post('/admin/disposal-rules/delete', { id });
      await this.loadRules();
    }
  }
};
</script>

<style scoped>
.page { 
  font-family: 'Inter', sans-serif; 
  padding: 1rem;
  color: var(--text-primary);
}

.page h1 {
  color: var(--text-primary);
  transition: color 0.3s ease;
}

table {
  width: 100%;
  border-collapse: collapse;
  table-layout: fixed;
}

tbody tr {
  background: var(--bg-primary);
  transition: background-color 0.3s ease;
}

tbody tr:hover {
  background: var(--bg-secondary);
}

td, th {
  padding: 0.6rem 1rem;
  text-align: left;
  vertical-align: middle;
  border-bottom: 1px solid var(--border-color);
  height: 48px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  transition: border-color 0.3s ease;
  color: var(--text-primary);
}

th {
  background: #00ADB5;
  color: white;
  font-weight: 600;
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
  color: var(--text-primary);
  transition: background-color 0.3s ease, color 0.3s ease;
}

.input,
input,
select {
  border: 1px solid var(--border-color);
  background-color: var(--bg-secondary);
  color: var(--text-primary);
}

.input:focus,
input:focus,
select:focus {
  outline: none;
  border-color: var(--accent-green);
  box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
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
  border: 1px solid var(--border-color);
  background-color: var(--bg-secondary);
  cursor: pointer;
  padding: 0;
  color: var(--text-primary);
  transition: all 0.3s ease;
}

button:hover {
  background-color: var(--accent-green);
  color: white;
  border-color: var(--accent-green);
}

.form { 
  display: flex; 
  gap: 0.5rem; 
  margin-bottom: 1.5rem; 
  align-items: center;
  padding: 1rem;
  background: var(--bg-secondary);
  border-radius: 8px;
  border: 1px solid var(--border-color);
  transition: all 0.3s ease;
}
.form .input,
.form input,
.form select {
  height: 34px;
  line-height: 34px;
  font-size: 0.95rem;
  padding: 0 0.6rem;
  border: 1px solid var(--border-color);
  border-radius: 6px;
  background-color: var(--bg-primary);
  color: var(--text-primary);
}

/* Pagination Styles */
.pagination {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  margin-top: 30px;
  padding: 20px;
  background: var(--bg-secondary);
  border-radius: 12px;
  border: 1px solid var(--border-color);
  flex-wrap: wrap;
  transition: background-color 0.3s ease, border-color 0.3s ease;
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
  border: 1px solid var(--border-color);
  background: var(--bg-secondary);
  color: var(--text-primary);
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.2s ease;
}

.page-number:hover {
  border-color: var(--accent-green);
  color: var(--accent-green);
}

.page-number.active {
  background: linear-gradient(135deg, #4CAF50 0%, #2E7D32 100%);
  color: white;
  border-color: #4CAF50;
}

.pagination-info {
  font-size: 14px;
  color: var(--text-secondary);
  font-weight: 500;
}.page-number.active {
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
