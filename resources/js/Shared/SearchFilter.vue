<template>
  <div class="flex items-end">
    <slot />
    <div class="w-1/5 mr-3">
      <label class="block text-gray-700">Search:</label>
      <input class="form-input pl-3 mt-2" autocomplete="off" type="text" name="search" :value="modelValue" @input="$emit('update:modelValue', $event.target.value)" placeholder="" />
    </div>
    <div class="w-1/5 mr-2 text-center">
      <button class="btn-indigo bg-red-600 text-white float-left" type="button" @click="$emit('reset')">Reset</button>
    </div>
  </div>
</template>

<script>
import Dropdown from '@/Shared/Dropdown'

export default {
  components: {
    Dropdown,
  },
  props: {
    modelValue: String,
    maxWidth: {
      type: Number,
      default: 300,
    },
  },
  emits: ['update:modelValue', 'reset'],
}
</script>
