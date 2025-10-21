<template>
  <div class="page">
    <h1>Bin Steps</h1>

    <form @submit.prevent="addStep" class="form">
      <InputField v-model="form.description" placeholder="Step description" required />
      <ActionButton type="save">Add</ActionButton>
    </form>

    <DataTable :headers="['ID', 'Description', 'Actions']">
      <tr v-for="step in steps" :key="step.id">
        <td>{{ step.id }}</td>
        <td>
          <InputField v-if="editId === step.id" v-model="editData.description" />
          <span v-else>{{ step.description }}</span>
        </td>
        <td>
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
      form: { description: '' },
      editId: null,
      editData: { description: '' }
    };
  },
  async mounted() {
    await this.loadSteps();
  },
  methods: {
    async loadSteps() {
      const { data } = await api.get('/admin/bin-steps');
      this.steps = data;
    },
    async addStep() {
      await api.post('/admin/bin-steps', this.form);
      this.form.description = '';
      await this.loadSteps();
    },
    editStep(step) {
      this.editId = step.id;
      this.editData = { description: step.description };
    },
    async saveStep(id) {
      await api.put(`/admin/bin-steps/${id}`, this.editData);
      this.editId = null;
      await this.loadSteps();
    },
    async deleteStep(id) {
      if (!confirm('Delete this step?')) return;
      await api.delete(`/admin/bin-steps/${id}`);
      await this.loadSteps();
    }
  }
};
</script>

<style scoped>
.page { font-family: 'Inter', sans-serif; }
.form { display: flex; gap: 0.5rem; margin-bottom: 1.5rem; }
</style>