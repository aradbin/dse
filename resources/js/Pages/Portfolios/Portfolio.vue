<template>
  <div>
    <Head title="Portfolio" />
    <h1 class="mb-4 text-3xl font-bold">
      {{ store.portfolio?.name }}
      <button class="btn-indigo float-right" v-on:click="toggleModal()">Add New Portfolio</button>
    </h1>
    <div class="grid grid-cols-4 sm:grid-cols-2 shadow">
      <div class="text-center px-3 py-2 font-bold text-white border border-solid" :class="store.portfolio?.value < 0 ? 'bg-red-600' : 'bg-green-600'">
        <span class="block text-center text-xs">Portfolio Value</span>
        <h6 class="text-center text-2xl">{{ store.portfolio?.value ? store.portfolio?.value.toFixed(2) : 0 }}</h6>
      </div>
      <div class="text-center px-3 py-2 font-bold text-white border border-solid" :class="(!store.portfolio?.gain || store.portfolio?.gain < 0) ? 'bg-red-600' : 'bg-green-600'">
        <span class="block text-center text-xs">Gain (%)</span>
        <h6 class="text-center text-2xl">{{ store.portfolio?.gain || 0 }} ({{ store.portfolio?.gainPercent }}%)</h6>
      </div>
      <div class="text-center px-3 py-2 font-bold text-white border border-solid" :class="store.portfolio?.balance < 0 ? 'bg-red-600' : 'bg-green-600'">
        <span class="block text-center text-xs">Available Balance</span>
        <h6 class="text-center text-2xl">{{ store.portfolio?.balance }}</h6>
      </div>
      <div class="text-center px-3 py-2 font-bold text-white border border-solid" :class="store.portfolio?.value + store.portfolio?.balance < 0 ? 'bg-red-600' : 'bg-green-600'">
        <span class="block text-center text-xs">Total Assets</span>
        <h6 class="text-center text-2xl">{{ store.portfolio?.value ? (store.portfolio?.value + store.portfolio?.balance).toFixed(2) : store.portfolio?.balance }}</h6>
      </div>
    </div>
    <div class="mt-4 mb-4">
      <Organizations @toggleModal="toggleTransactionModal" />
    </div>
    <div class="mt-8 mb-4">
      <Transactions :transactions="store.portfolio?.transactions" @toggleModal="toggleTransactionModal" />
    </div>
  </div>

  <PortfolioForm v-if="showModal" @toggleModal="toggleModal" />
  <TransactionForm v-if="showTransactionModal" :type="type" :organization_id="organization_id" :portfolio="portfolio" @toggleModal="toggleTransactionModal" />
</template>
  
<script>
  import { Head, Link } from '@inertiajs/inertia-vue3'
  import Layout from '@/Shared/Layout'
  import { store } from '../../store'
  import PortfolioForm from "./PortfolioForm";
  import TransactionForm from "./TransactionForm";
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
        type: 1,
        organization_id: null,
      }
    },
    watch: {
      'store.portfolios'(){
        this.updatePortfolio();
      },
      portfolio: {
        deep: true,
        handler: function () {
          this.updatePortfolio();
        },
      }
    },
    methods: {
      toggleModal: function(){
        this.showModal = !this.showModal;
      },
      toggleTransactionModal: function(type,organization_id=null){
        this.type = type;
        this.organization_id = organization_id;
        this.showTransactionModal = !this.showTransactionModal;
      },
      updatePortfolio: function(){
        this.store.updatePortfolio(this.portfolio);
      }
    },
    mounted(){
      if(this.store.organizations.length > 0 && this.store.portfolios.length > 0){
        this.updatePortfolio();
      }
    }
  }
</script>