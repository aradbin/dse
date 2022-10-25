<template>
  <div v-if="total > per_page">
    <div class="flex flex-wrap -mb-1">
      <button class="mb-1 mr-1 px-4 py-3 focus:text-indigo-500 text-sm leading-4 hover:bg-white border focus:border-indigo-500 rounded" @click="onPageChange(current_page-1)">Previous</button>
      <button v-for="page in getTotalPageNumber()" :key="page" class="mb-1 mr-1 px-4 py-3 focus:text-indigo-500 text-sm leading-4 hover:bg-white border focus:border-indigo-500 rounded" :class="{ 'bg-white': page===current_page }" v-html="page" @click="onPageChange(page)" />
      <button class="mb-1 mr-1 px-4 py-3 focus:text-indigo-500 text-sm leading-4 hover:bg-white border focus:border-indigo-500 rounded" @click="onPageChange(current_page+1)">Next</button>
    </div>
  </div>
</template>

<script>

export default {
  components: {
    
  },
  props: {
    total: Number,
    current_page: Number,
    per_page: Number
  },
  emits: ['changePage'],
  methods: {
    getTotalPageNumber(){
      let pageNumber = Math.floor(this.total / this.per_page);
      if(this.total % this.per_page !== 0){
        pageNumber++;
      }
      return pageNumber;
    },
    onPageChange(page){
      if(page > 0 && page <= this.getTotalPageNumber()){
        this.$emit('changePage',page);
      }
    }
  }
}
</script>
