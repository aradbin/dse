<template>
  <span class="px-3 py-1 rounded text-white text-sm leading-4 font-bold whitespace-nowrap" :class="colorClass">{{ text }}</span>
</template>

<script>
export default {
  props: {
    color: String,
    text: String
  },
  data() {
    return {
      colorClass: '',
    }
  },
  mounted(){
    if(this.color==='danger'){
      this.colorClass = 'bg-red-600';
    }else if(this.color==='success'){
      this.colorClass = 'bg-green-600';
    }
  }
}
</script>
