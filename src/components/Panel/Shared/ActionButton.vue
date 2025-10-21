<template>
  <button :class="`btn ${type}`" @click="$emit('click')">
    <slot />
  </button>
</template>

<script>
export default {
  name: 'ActionButton',
  props: {
    type: { type: String, default: 'default' }
  }
}
</script>

<style scoped>
.btn {
  border: none;
  padding: 0.4rem 0.8rem;
  border-radius: 6px;
  color: white;
  cursor: pointer;
  margin-right: 0.4rem;
  transition: background 0.2s;
}
.edit {
  background: #3A506B;
}
.save {
  background: #00ADB5;
}
.delete {
  background: #F05454;
}
.btn:hover {
  opacity: 0.9;
}
</style>