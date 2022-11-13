<template>
  <div>
    <Head title="Portfolio" />
    <h1 class="mb-4 text-3xl font-bold">
      My Portfolio
      <Badge :class="(store.gain >= 0) ? 'success' : 'danger'" :text="store.gain + ' (' +store.gainPercent ? store.gainPercent : 0 + '%)'" />
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
  import Badge from '@/Shared/Badge'
  import { store } from '../../store'
  import PortfolioForm from "./PortfolioForm"
  import Portfolios from "./Portfolios"

  
  export default {
    components: {
      Head,
      Link,
      Badge,
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