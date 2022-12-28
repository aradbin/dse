<template>
  <div>
    <Head title="Portfolio" />
    <div class="mb-4 flex justify-between flex-col md:flex-row">
      <div class="flex items-center mb-3 md:mb-0">
        <span class="text-3xl font-bold mr-2">My Portfolio</span>
        <span class="badge" :class="(store.profit >= 0) ? 'badge-success' : 'badge-danger'">{{ store.profit }} <span v-if="isFinite(store.profitPercent)">({{ store.profitPercent }}%)</span></span>
      </div>
      <button class="btn-indigo btn-sm" v-on:click="toggleModal()">Add New Portfolio</button>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-4 2xl:grid-cols-4 gap-4 mt-8 mb-8">
      <div class="text-center px-6 py-4 font-bold border border-solid bg-white">
        <span class="block text-center text-xs">Portfolio Value</span>
        <div class="text-center mb-4 flex items-center justify-center" :class="(store.gain >= 0) ? 'text-green-600' : 'text-red-600'">
          <span class="text-2xl mr-2">{{ store.value || 0 }}</span>
          <span class="badge" :class="(store.gain >= 0) ? 'badge-success' : 'badge-danger'">{{ store.gain }} ({{ store.gainPercent }}%)</span>
        </div>
        <div class="grid grid-cols-3 gap-y-4">
          <span class="block text-left col-span-2 text-sm">Available Balance</span>
          <span class="block text-right col-span-1 text-sm">{{ store.balance }}</span>
          <span class="block text-left col-span-2 text-sm">Total Deposit</span>
          <span class="block text-right col-span-1 text-sm">{{ store.deposit }}</span>
          <span class="block text-left col-span-2 text-sm">Total Withdraw</span>
          <span class="block text-right col-span-1 text-sm">{{ store.withdraw }}</span>
        </div>
      </div>
      <div class="text-center px-6 py-4 font-bold border border-solid bg-white">
        <span class="block text-center text-xs">Total Expense</span>
        <h6 class="text-center text-2xl mb-4">{{ store.expense || 0 }}</h6>
        <div class="grid grid-cols-3 gap-y-4">
          <span class="block text-left col-span-2 text-sm">Total Commission Paid</span>
          <span class="block text-right col-span-1 text-sm">{{ store.paid_commission }}</span>
          <span class="block text-left col-span-2 text-sm">Total Charge Paid</span>
          <span class="block text-right col-span-1 text-sm">{{ store.paid_charge }}</span>
          <span class="block text-left col-span-2 text-sm">Total Tax Paid</span>
          <span class="block text-right col-span-1 text-sm">{{ store.paid_tax }}</span>
        </div>
      </div>
      <div class="text-center px-6 py-4 font-bold border border-solid bg-white">
        <span class="block text-center text-xs">Total Income</span>
        <h6 class="text-center text-2xl mb-4">{{ store.income || 0 }}</h6>
        <div class="grid grid-cols-3 gap-y-4">
          <span class="block text-left col-span-2 text-sm">Realized Gain</span>
          <span class="block text-right col-span-1 text-sm">{{ store.realized_gain }}</span>
          <span class="block text-left col-span-2 text-sm">Total Cash Dividend</span>
          <span class="block text-right col-span-1 text-sm">{{ store.cash_dividend }}</span>
          <span class="block text-left col-span-2 text-sm">Unrealized Gain</span>
          <span class="block text-right col-span-1 text-sm">{{ store.gain || 0 }}</span>
        </div>
      </div>
      <div class="text-center px-6 py-4 font-bold border border-solid bg-white">
        <span class="block text-center text-xs">Total Profit (%)</span>
        <h6 class="text-center text-2xl mb-4" :class="(store.profit >= 0) ? 'text-green-600' : 'text-red-600'">{{ store.profit || 0 }} <span v-if="isFinite(store.profitPercent)">({{ store.profitPercent }}%)</span></h6>
        <div class="grid grid-cols-3 gap-y-4">
          <span class="block text-left col-span-2 text-sm">Total Invested</span>
          <span class="block text-right col-span-1 text-sm">{{ store.buy }}</span>
          <span class="block text-left col-span-2 text-sm">Total Income</span>
          <span class="block text-right col-span-1 text-sm">{{ store.income || 0 }}</span>
          <span class="block text-left col-span-2 text-sm">Total Expense</span>
          <span class="block text-right col-span-1 text-sm">{{ store.expense || 0 }}</span>
        </div>
      </div>
    </div>
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