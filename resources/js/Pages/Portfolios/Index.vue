<template>
  <div>
    <Head title="Portfolio" />
    <h1 class="mb-4 text-3xl font-bold">
      My Portfolio
      <span class="badge" :class="(store.gain >= 0) ? 'badge-success' : 'badge-danger'">{{ store.gain }} ({{ store.gainPercent }}%)</span>
      <button class="btn-indigo float-right" v-on:click="toggleModal()">Add New Portfolio</button>
    </h1>
    <div class="mt-4 mb-4">
      <Portfolios v-if="this.store.portfolios.length > 0" />
    </div>
  </div>

  <PortfolioForm v-if="showModal" @toggleModal="toggleModal" />
</template>
  
<script>
  import { Head, Link } from '@inertiajs/inertia-vue3'
  import Layout from '@/Shared/Layout'
  import { store } from '../../store'
  import PortfolioForm from "./PortfolioForm"
  import Portfolios from "./Portfolios"

  
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