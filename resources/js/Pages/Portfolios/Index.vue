<template>
  <div>
    <Head title="Portfolio" />
    <h1 class="mb-4 text-3xl font-bold">
      My Portfolio
      <span class="px-3 py-1 rounded text-white text-sm leading-4 font-bold whitespace-nowrap hover:bg-orange-400 focus:bg-orange-400" :class="store.gain >= 0 ? 'bg-green-600' : 'bg-red-600'">{{ store.gain }} ({{ store.gainPercent ? store.gainPercent : 0 }}%)</span>
      <button class="btn-indigo float-right" v-on:click="toggleModal()">Add New Portfolio</button>
    </h1>
    <div class="mt-4 mb-4">
      <Portfolios v-if="this.store.portfolios.length > 0" :portfolios="this.store.portfolios" />
    </div>
  </div>

  <PortfolioForm v-if="showModal" @toggleModal="toggleModal" />
</template>
  
<script>
  import { Head, Link } from '@inertiajs/inertia-vue3'
  import Layout from '@/Shared/Layout'
  import { store } from '../../store'
  import PortfolioForm from "./PortfolioForm";
  import Portfolios from "./Portfolios";
  
  export default {
    components: {
      Head,
      Link,
      PortfolioForm,
      Portfolios
    },
    layout: Layout,
    data() {
      return {
        store,
        showModal: false,
      }
    },
    methods: {
      toggleModal: function(){
        this.showModal = !this.showModal;
      }
    }
  }
</script>