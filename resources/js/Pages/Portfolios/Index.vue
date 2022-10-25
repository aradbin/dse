<template>
  <div>
    <Head title="Portfolio" />
    <h1 class="mb-4 text-3xl font-bold">
      My Portfolio
      <button class="btn-indigo float-right" v-on:click="toggleModal()">Add New Portfolio</button>
    </h1>
    <div class="mt-4 mb-4">
      <Portfolios v-if="this.store.portfolios.length > 0" :portfolios="this.store.portfolios" />
    </div>
  </div>

  <PortfolioForm v-if="showModal" :brokers="brokers" @toggleModal="toggleModal" @updatePortfolios="updatePortfolios" />
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
      },
      updatePortfolios(){
        // this.store.updatePortfolio(this.portfolio);
      }
    }
  }
</script>