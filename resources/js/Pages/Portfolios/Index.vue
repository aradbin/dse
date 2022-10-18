<template>
  <div>
    <Head title="Portfolio" />
    <h1 class="mb-4 text-3xl font-bold">My Portfolio</h1>
    <div class="flex items-end justify-between mb-6" v-if="this.store.portfolios.length > 0">
      <div>
        <!-- <label class="block text-gray-700">Portfolio:</label> -->
        <select v-model="portfolio" class="form-select w-full" style="width: 250px">
          <option v-for="portfolio in this.store.portfolios" :key="portfolio.id" :value="portfolio.id">{{portfolio.name}}</option>
        </select>
      </div>
      <div>
        <button class="btn-indigo" v-on:click="toggleModal()">Add Portfolio</button>
      </div>
    </div>
    <div class="flex items-end justify-center mb-6" v-else>
      <div class="flex flex-wrap items-end">
        <div class="w-full flex mb-4">
          <button class="btn-indigo" v-on:click="toggleModal()">Add Portfolio</button>
        </div>
      </div>
    </div>
    <div class="flex items-end justify-end mb-6">
      <button class="btn-success mr-2" v-on:click="toggleTransactionModal(1)">Deposit</button>
      <button class="btn-danger" v-on:click="toggleTransactionModal(2)">Withdrawl</button>
    </div>
    <div class="mt-4 mb-4">
      <Trades v-if="selectedPortfolio && selectedPortfolio.trades" :trades="selectedPortfolio.trades" />
    </div>
    <div class="mt-8 mb-4">
      <Transactions v-if="selectedPortfolio && selectedPortfolio.transactions" :transactions="selectedPortfolio.transactions" />
    </div>
  </div>

  <PortfolioForm v-if="showModal" :brokers="brokers" @toggleModal="toggleModal" @updatePortfolios="updatePortfolios" />
  <TransactionForm v-if="showTransactionModal" :type="type" :portfolio="portfolio" @toggleModal="toggleTransactionModal" @updatePortfolios="updatePortfolios" />
</template>
  
<script>
  import { Head, Link } from '@inertiajs/inertia-vue3'
  import Layout from '@/Shared/Layout'
  import { store } from '../../store'
  import PortfolioForm from "./PortfolioForm";
  import TransactionForm from "./TransactionForm";
  import Transactions from "./Transactions";
  import Trades from "./Trades";
  
  export default {
    components: {
      Head,
      Link,
      PortfolioForm,
      TransactionForm,
      Transactions,
      Trades
    },
    layout: Layout,
    props: {
      auth: Object,
      brokers: Array,
      portfolios: Array
    },
    data() {
      return {
        portfolio: null,
        selectedPortfolio: null,
        store,
        showModal: false,
        showTransactionModal: false,
        type: 1
      }
    },
    watch: {
      portfolio(){
        this.selectedPortfolio = this.store.portfolios.filter(item => item.id === this.portfolio)[0];
      },
    },
    methods: {
      toggleModal: function(){
        this.showModal = !this.showModal;
      },
      toggleTransactionModal: function(type=null){
        this.type = type;
        this.showTransactionModal = !this.showTransactionModal;
      },
      updatePortfolios(){
        this.store.updatePortfolios(this.portfolios);
        if(!this.portfolio && this.store.portfolios.length > 0){
          this.portfolio = this.store.portfolios[0].id;
        }
      }
    },
    mounted(){
      this.updatePortfolios()
    }
  }
</script>