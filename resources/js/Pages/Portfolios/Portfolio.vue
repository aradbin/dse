<template>
  <div>
    <Head title="Portfolio" />
    <h1 class="mb-4 text-3xl font-bold">
      {{ store.portfolio?.name }}
      <button class="btn-indigo float-right" v-on:click="toggleModal()">Add New Portfolio</button>
    </h1>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-4 2xl:grid-cols-4 shadow">
      <div class="text-center px-6 py-3 font-bold text-white border border-solid bg-indigo-800">
        <span class="block text-center text-xs">Portfolio Value</span>
        <h6 class="text-center text-2xl mb-4">{{ store.portfolio?.value || 0 }}</h6>
        <div class="grid grid-cols-2 gap-4">
          <span class="block text-left text-s">Available Balance</span>
          <span class="block text-right text-s">{{ store.portfolio?.balance }}</span>
          <span class="block text-left text-s">Total Deposit</span>
          <span class="block text-right text-s">{{ store.portfolio?.deposit }}</span>
          <span class="block text-left text-s">Total Withdraw</span>
          <span class="block text-right text-s">{{ store.portfolio?.withdraw }}</span>
        </div>
      </div>
      <div class="text-center px-3 py-2 font-bold text-white border border-solid bg-indigo-800">
        <span class="block text-center text-xs">Total Expense</span>
        <h6 class="text-center text-2xl">{{ store.portfolio?.expense || 0 }}</h6>
        <span class="block text-center text-xs">Total Commission Paid</span>
        <h6 class="text-center text-2xl">{{ store.portfolio?.paid_commission }}</h6>
        <span class="block text-center text-xs">Total Charge Paid</span>
        <h6 class="text-center text-2xl">{{ store.portfolio?.paid_charge }}</h6>
        <span class="block text-center text-xs">Total Tax Paid</span>
        <h6 class="text-center text-2xl">{{ store.portfolio?.paid_tax }}</h6>
      </div>
      <div class="text-center px-3 py-2 font-bold text-white border border-solid bg-indigo-800">
        <span class="block text-center text-xs">Total Income</span>
        <h6 class="text-center text-2xl">{{ store.portfolio?.income || 0 }}</h6>
        <span class="block text-center text-xs">Realized Gain</span>
        <h6 class="text-center text-2xl">{{ store.portfolio?.realized_gain }}</h6>
        <span class="block text-center text-xs">Total Cash Dividend</span>
        <h6 class="text-center text-2xl">{{ store.portfolio?.cash_dividend }}</h6>
        <span class="block text-center text-xs">Unrealized Gain (%)</span>
        <h6 class="text-center text-2xl">{{ store.portfolio?.gain || 0 }} ({{ store.portfolio?.gainPercent }}%)</h6>
      </div>
      <div class="text-center px-3 py-2 font-bold text-white border border-solid bg-indigo-800">
        <span class="block text-center text-xs">Total Profit</span>
        <h6 class="text-center text-2xl">{{ store.portfolio?.profit || 0 }}</h6>
        <span class="block text-center text-xs">Total Invested</span>
        <h6 class="text-center text-2xl">{{ store.portfolio?.buy }}</h6>
      </div>
    </div>
    <div class="mt-4 mb-4">
      <Organizations @toggleModal="toggleTransactionModal" />
    </div>
    <div class="mt-8 mb-4">
      <Transactions @toggleModal="toggleTransactionModal" />
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
      this.updatePortfolio();
    }
  }
</script>