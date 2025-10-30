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
      <tr v-for="r in rules" :key="r.id">
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
      newRule: { item_type_id: '', location_id: '', bin_type_id: '', description: '' }
    };
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

.form { 
  display: flex; 
  gap: 0.5rem; 
  margin-bottom: 1.5rem; 
  align-items: center;
}

.form .input,
.form input,
.form select {
  height: 34px;
  line-height: 34px;
  font-size: 0.95rem;
  padding: 0 0.6rem;
  border: 1px solid #ccc;
  border-radius: 6px;
}
</style>
