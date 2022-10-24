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
  <TransactionForm v-if="showTransactionModal" :type="type" :organization_id="organization_id" :portfolio="portfolio" @toggleModal="toggleTransactionModal" @updatePortfolios="updatePortfolios" />
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
      'store.organizations'(){
        this.getDetails();
      },
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
      getDetails(updatePrice=false){
        this.store.getOrganizationDetails(this.store.filteredOrganizations,updatePrice);
      },
      updatePortfolios(){
        this.store.updatePortfolio(this.portfolio);
      }
    },
    mounted(){
      this.updatePortfolios();
      this.getDetails();
      window.setInterval(() => {
        let d = new Date();
        if(d.getDay()<5 && d.getHours()>10 && d.getHours()<15){
          this.getDetails(true);
        }
      }, 60000);
    }
  }
</script>