<template>
  <div>
    <Head title="Portfolio" />
    <h1 class="mb-4 text-3xl font-bold">
      {{ portfolio.name }}
      <button class="btn-indigo float-right" v-on:click="toggleModal()">Add New Portfolio</button>
    </h1>
    <div class="mt-4 mb-4">
      <Organizations :organizations="portfolio.organizations" @toggleModal="toggleTransactionModal" />
    </div>
    <div class="mt-8 mb-4">
      <Transactions :transactions="portfolio.transactions" @toggleModal="toggleTransactionModal" />
    </div>
  </div>

  <PortfolioForm v-if="showModal" :brokers="brokers" @toggleModal="toggleModal" @updatePortfolios="updatePortfolios" />
  <TransactionForm v-if="showTransactionModal" :type="type" :portfolio="portfolio.id" @toggleModal="toggleTransactionModal" @updatePortfolios="updatePortfolios" />
</template>
  
<script>
  import { Head, Link } from '@inertiajs/inertia-vue3'
  import Layout from '@/Shared/Layout'
  import { store } from '../../store'
  import PortfolioForm from "./PortfolioForm";
  import TransactionForm from "./TransactionForm";
  import Portfolios from "./Portfolios";
  import Transactions from "./Transactions";
  import Organizations from "./Organizations";
  
  export default {
    components: {
      Head,
      Link,
      PortfolioForm,
      TransactionForm,
      Transactions,
      Organizations
    },
    layout: Layout,
    props: {
      portfolio: Object
    },
    data() {
      return {
        store,
        showModal: false,
        showTransactionModal: false,
        type: 1
      }
    },
    watch: {
      // portfolio(){
      //   this.selectedPortfolio = this.store.portfolios.filter(item => item.id === this.portfolio)[0];
      // },
    },
    methods: {
      toggleModal: function(){
        this.showModal = !this.showModal;
      },
      toggleTransactionModal: function(type){
        this.type = type;
        this.showTransactionModal = !this.showTransactionModal;
      },
      updatePortfolios(){
        this.store.updatePortfolio(this.portfolio);
      }
    },
    mounted(){
      this.updatePortfolios()
    }
  }
</script>