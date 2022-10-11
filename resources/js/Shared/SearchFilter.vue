<template>
  <div class="flex flex-wrap items-end">
    <div class="w-full flex mb-4">
      <slot />
    </div>
    <div class="w-full flex items-end">
      <div class="w-3/4 mr-3">
        <label class="block text-gray-700">Search:</label>
        <input class="form-input pl-3 mt-2" autocomplete="off" type="text" name="search" :value="modelValue" @input="$emit('update:modelValue', $event.target.value)" placeholder="" />
      </div>
      <div class="w-1/4 text-center">
        <button class="btn-indigo bg-red-600 text-white float-left w-full" type="button" @click="$emit('reset')">Reset</button>
      </div>
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
