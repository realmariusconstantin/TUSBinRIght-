<template>
  <button
    :class="['btn', type, { disabled }]"
    :disabled="disabled"
    @click.stop="$emit('action')"
  >
    <slot />
  </button>
</template>

<script>
export default {
  name: 'ActionButton',
  props: {
    type: { type: String, default: 'default' },
    disabled: { type: Boolean, default: false }
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
  transition: background 0.2s, opacity 0.2s;
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

.btn:hover:not(.disabled) {
  opacity: 0.9;
}

.btn.disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
</style>
