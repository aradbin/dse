<template>
  <div>
    <Head title="Portfolio" />
    <div class="mb-4 flex justify-between flex-col md:flex-row">
      <div class="flex items-center mb-3 md:mb-0">
        <span class="text-3xl font-bold mr-2">{{ store.portfolio?.name }}</span>
        <!-- <span class="badge" :class="(store.portfolio?.profit >= 0) ? 'badge-success' : 'badge-danger'">{{ store.portfolio?.profit && store.formatCurrency(store.portfolio?.profit) }} <span v-if="store.portfolio?.profitPercent && isFinite(store.portfolio?.profitPercent)">({{ store.portfolio?.profitPercent }}%)</span></span> -->
      </div>
      <!-- <button class="btn-indigo btn-sm" v-on:click="toggleModal()">Add New Portfolio</button> -->
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-4 2xl:grid-cols-4 gap-4 mt-8 mb-8">
      <div class="text-center px-6 py-4 font-bold border border-solid bg-white">
        <span class="block text-center text-xs">Portfolio Value</span>
        <div class="text-center mb-4 flex items-center justify-center" :class="(store.portfolio?.gain >= 0) ? 'text-green-600' : 'text-red-600'">
          <span class="text-2xl mr-2">{{ store.portfolio?.value && store.formatCurrency(store.portfolio?.value) }}</span>
          <span class="badge" :class="(store.portfolio?.gain >= 0) ? 'badge-success' : 'badge-danger'">{{ store.portfolio?.gain && store.formatCurrency(store.portfolio?.gain) }} ({{ store.portfolio?.gainPercent }}%)</span>
        </div>
        <div class="grid grid-cols-3 gap-y-4">
          <span class="block text-left col-span-2 text-sm">Available Balance</span>
          <span class="block text-right col-span-1 text-sm">{{ store.portfolio?.balance && store.formatCurrency(store.portfolio?.balance) }}</span>
          <span class="block text-left col-span-2 text-sm">Total Deposit</span>
          <span class="block text-right col-span-1 text-sm">{{ store.portfolio?.deposit && store.formatCurrency(store.portfolio?.deposit) }}</span>
          <span class="block text-left col-span-2 text-sm">Total Withdraw</span>
          <span class="block text-right col-span-1 text-sm">{{ store.portfolio?.withdraw && store.formatCurrency(store.portfolio?.withdraw) }}</span>
        </div>
      </div>
      <div class="text-center px-6 py-4 font-bold border border-solid bg-white">
        <span class="block text-center text-xs">Total Expense</span>
        <h6 class="text-center text-2xl mb-4">{{ store.portfolio?.expense && store.formatCurrency(store.portfolio?.expense) }}</h6>
        <div class="grid grid-cols-3 gap-y-4">
          <span class="block text-left col-span-2 text-sm">Total Commission Paid</span>
          <span class="block text-right col-span-1 text-sm">{{ store.portfolio?.paid_commission && store.formatCurrency(store.portfolio?.paid_commission) }}</span>
          <span class="block text-left col-span-2 text-sm">Total Charge Paid</span>
          <span class="block text-right col-span-1 text-sm">{{ store.portfolio?.paid_charge && store.formatCurrency(store.portfolio?.paid_charge) }}</span>
          <span class="block text-left col-span-2 text-sm">Total Tax Paid</span>
          <span class="block text-right col-span-1 text-sm">{{ store.portfolio?.paid_tax && store.formatCurrency(store.portfolio?.paid_tax) }}</span>
        </div>
      </div>
      <div class="text-center px-6 py-4 font-bold border border-solid bg-white">
        <span class="block text-center text-xs">Total Income</span>
        <h6 class="text-center text-2xl mb-4">{{ store.portfolio?.income && store.formatCurrency(store.portfolio?.income) }}</h6>
        <div class="grid grid-cols-3 gap-y-4">
          <span class="block text-left col-span-2 text-sm">Realized Gain</span>
          <span class="block text-right col-span-1 text-sm">{{ store.portfolio?.realized_gain && store.formatCurrency(store.portfolio?.realized_gain) }}</span>
          <span class="block text-left col-span-2 text-sm">Total Cash Dividend</span>
          <span class="block text-right col-span-1 text-sm">{{ store.portfolio?.cash_dividend && store.formatCurrency(store.portfolio?.cash_dividend) }}</span>
          <span class="block text-left col-span-2 text-sm">Unrealized Gain</span>
          <span class="block text-right col-span-1 text-sm">{{ store.portfolio?.gain && store.formatCurrency(store.portfolio?.gain) }}</span>
        </div>
      </div>
      <div class="text-center px-6 py-4 font-bold border border-solid bg-white">
        <span class="block text-center text-xs">Total Profit (%)</span>
        <h6 class="text-center text-2xl mb-4" :class="(store.portfolio?.profit >= 0) ? 'text-green-600' : 'text-red-600'">{{ store.portfolio?.profit && store.formatCurrency(store.portfolio?.profit) }} <span v-if="store.portfolio?.profitPercent && isFinite(store.portfolio?.profitPercent)">({{ store.portfolio?.profitPercent }}%)</span></h6>
        <div class="grid grid-cols-3 gap-y-4">
          <span class="block text-left col-span-2 text-sm">Total Invested</span>
          <span class="block text-right col-span-1 text-sm">{{ store.portfolio?.buy && store.formatCurrency(store.portfolio?.buy) }}</span>
          <span class="block text-left col-span-2 text-sm">Total Income</span>
          <span class="block text-right col-span-1 text-sm">{{ store.portfolio?.income && store.formatCurrency(store.portfolio?.income) }}</span>
          <span class="block text-left col-span-2 text-sm">Total Expense</span>
          <span class="block text-right col-span-1 text-sm">{{ store.portfolio?.expense && store.formatCurrency(store.portfolio?.expense) }}</span>
        </div>
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