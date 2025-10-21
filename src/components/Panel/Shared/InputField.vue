<template>
  <input
    class="input"
    :type="type"
    v-model="model"
    :placeholder="placeholder"
  />
</template>

<script>
export default {
  name: 'InputField',
  props: {
    modelValue: [String, Number],
    type: { type: String, default: 'text' },
    placeholder: String
  },
  emits: ['update:modelValue'],
  computed: {
    model: {
      get() {
        return this.modelValue
      },
      set(value) {
        this.$emit('update:modelValue', value)
      }
    }
  }
}
</script>

<style scoped>
.input {
  width: 100%;
  padding: 0.4rem;
  border-radius: 6px;
  border: 1px solid #ccc;
  font-family: 'Inter', sans-serif;
}
</style>