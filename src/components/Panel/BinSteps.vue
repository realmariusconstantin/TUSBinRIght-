<template>
  <div class="page">
    <h1>Bin Steps</h1>

    <form @submit.prevent="addStep" class="form">
      <InputField v-model="form.description" placeholder="Step description" required />
      <select v-model="form.bin_type_id" class="input" required>
        <option disabled value="">Select Bin Type</option>
        <option v-for="type in binTypes" :key="type.id" :value="type.id">
          {{ type.bin_type }}
        </option>
      </select>
      <ActionButton type="save">Add</ActionButton>
    </form>

    <DataTable :headers="['ID', 'Description', 'Bin Type', 'Actions']">
      <tr v-for="step in steps" :key="step.id">
        <td>{{ step.id }}</td>
        <td>
          <InputField v-if="editId === step.id" v-model="editData.description" />
          <span v-else>{{ step.step_description }}</span>
        </td>
        <td>
          <select v-if="editId === step.id" v-model="editData.bin_type_id" class="input">
            <option v-for="type in binTypes" :key="type.id" :value="type.id">
              {{ type.bin_type }}
            </option>
          </select>
          <span v-else>{{ step.bin_type }}</span>
        </td>
        <td class="actions">
          <ActionButton v-if="editId === step.id" type="save" @click="saveStep(step.id)">Save</ActionButton>
          <ActionButton v-else type="edit" @click="editStep(step)">Edit</ActionButton>
          <ActionButton type="delete" @click="deleteStep(step.id)">Delete</ActionButton>
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
  name: 'BinSteps',
  components: { DataTable, InputField, ActionButton },
  data() {
    return {
      steps: [],
      binTypes: [],
      form: { description: '', bin_type_id: '' },
      editId: null,
      editData: { description: '', bin_type_id: '' }
    };
  },
  async mounted() {
    await Promise.all([this.loadSteps(), this.loadBinTypes()]);
  },
  methods: {
    async loadSteps() {
      const { data } = await api.get('/admin/bin-steps');
      this.steps = data.steps || [];
    },
    async loadBinTypes() {
      const { data } = await api.get('/admin/bin-types');
      this.binTypes = data.bin_types || [];
    },
    async addStep() {
      if (!this.form.description || !this.form.bin_type_id) return;
      await api.post('/admin/bin-steps/create', this.form);
      this.form.description = '';
      this.form.bin_type_id = '';
      await this.loadSteps();
    },
    editStep(step) {
      this.editId = step.id;
      this.editData = {
        description: step.step_description,
        bin_type_id: step.bin_type_id || 1
      };
    },
    async saveStep(id) {
      await api.post('/admin/bin-steps/update', { id, ...this.editData });
      this.editId = null;
      await this.loadSteps();
    },
    async deleteStep(id) {
      if (!confirm('Delete this step?')) return;
      await api.post('/admin/bin-steps/delete', { id });
      await this.loadSteps();
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
