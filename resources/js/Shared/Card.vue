<template>
  <div class="bg-white rounded-md shadow grid grid-cols-2 xl:grid-cols-10">
    <div class="text-center px-6 xl:px-4 py-4 xl:py-6 font-bold text-white border border-solid bg-indigo-800 col-span-2 xl:col-span-1">
      <span class="float-left cursor-pointer xl:hidden" v-on:click="watchList()"><i class="fa-solid fa-heart" :class="organization.is_watch_listed && 'text-yellow-400'"></i></span>
      {{ organization.code }}
      <span class="float-right cursor-pointer xl:hidden" v-on:click="toggleModal()"><i class="fa-solid fa-circle-info"></i></span>
    </div>
    <div class="px-3 py-2 font-bold text-white border border-solid" :class="((organization.nav / organization.price)<1.5 && organization.pe<15 && organization.upe<15 && (organization.pe * (organization.nav / organization.price))<22.5 && (organization.upe * (organization.nav / organization.price))<22.5 && organization.div>0) ? (organization.pe>10 || organization.upe>10 || organization.div<5 ? 'bg-orange-600' : 'bg-green-600') : 'bg-red-600'">
      <span class="block text-center text-xs">Price</span>
      <h4 class="text-center text-2xl">{{ organization.price }}</h4>
    </div>
    <div class="px-3 py-2 font-bold text-white border border-solid" :class="(organization.nav / organization.price)<1.5 ? 'bg-green-600' : 'bg-red-600'">
      <span class="block text-center text-xs">P/NAV</span>
      <h4 class="text-center text-2xl">{{ (organization.nav / organization.price).toFixed(2) }}</h4>
    </div>
    <div class="px-3 py-2 font-bold text-white border border-solid" :class="organization.pe<15 ? (organization.pe<10 ? 'bg-green-600' : 'bg-orange-600') : 'bg-red-600'">
      <span class="block text-center text-xs">P/E</span>
      <h4 class="text-center text-2xl">{{ organization.pe }}</h4>
    </div>
    <div class="px-3 py-2 font-bold text-white border border-solid" :class="organization.upe<15 ? (organization.upe<10 ? 'bg-green-600' : 'bg-orange-600') : 'bg-red-600'">
      <span class="block text-center text-xs">U P/E</span>
      <h4 class="text-center text-2xl">{{ organization.upe }}</h4>
    </div>
    <div class="px-3 py-2 font-bold text-white border border-solid" :class="(organization.pe * (organization.nav / organization.price))<22.5 ? 'bg-green-600' : 'bg-red-600'">
      <span class="block text-center text-xs">P/E * P/NAV</span>
      <h4 class="text-center text-2xl">{{ (organization.pe * (organization.nav / organization.price)).toFixed(2) }}</h4>
    </div>
    <div class="px-3 py-2 font-bold text-white border border-solid" :class="(organization.upe * (organization.nav / organization.price))<22.5 ? 'bg-green-600' : 'bg-red-600'">
      <span class="block text-center text-xs">U P/E * P/NAV</span>
      <h4 class="text-center text-2xl">{{ (organization.upe * (organization.nav / organization.price)).toFixed(2) }}</h4>
    </div>
    <div class="px-3 py-2 font-bold text-white border border-solid" :class="organization.div>0 ? (organization.div>5 ? 'bg-green-600' : 'bg-orange-600') : 'bg-red-600'">
      <span class="block text-center text-xs">Dividend Yield</span>
      <h4 class="text-center text-2xl">{{ organization.div }}</h4>
    </div>
    <div class="px-3 py-2 font-bold text-white border border-solid" :class="organization.avg_dividend>0 ? (organization.avg_dividend>5 ? 'bg-green-600' : 'bg-orange-600') : 'bg-red-600'">
      <span class="block text-center text-xs">Avg Cash Dividend</span>
      <h4 class="text-center text-2xl">{{ organization.avg_dividend }} %</h4>
    </div>
    <div class="px-6 py-6 font-bold text-white border border-solid bg-indigo-800 text-center hidden xl:grid grid-cols-2">
      <span class="cursor-pointer" v-on:click="watchList()"><i class="fa-solid fa-heart" :class="organization.is_watch_listed && 'text-yellow-400'"></i></span>
      <span class="cursor-pointer" v-on:click="toggleModal()"><i class="fa-solid fa-circle-info"></i></span>
    </div>
  </div>

  <!-- Details Modal -->
  <div v-if="showModal" class="overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center flex flex-wrap">
    <div class="relative w-auto my-6 mx-auto max-w-3xl">
      <!--content-->
      <div class="border-0 rounded-lg shadow-lg relative flex flex-col w-full bg-white outline-none focus:outline-none">
        <!--header-->
        <div class="flex items-start justify-between p-5 rounded-t bg-indigo-800 text-white">
          <h3 class="text-2xl font-semibold">
            {{ organization.name }} ({{ organization.code }})
          </h3>
          <button class="text-red-500 border-2 border-solid border-red-500 hover:bg-red-500 hover:text-white active:bg-red-600 rounded ease-linear transition-all duration-150 p-1 ml-5 bg-white float-right text-3xl leading-none font-semibold outline-none focus:outline-none" v-on:click="toggleModal()">
            <span class="bg-transparent h-6 w-6 text-2xl block outline-none focus:outline-none leading-5">
              ×
            </span>
          </button>
        </div>
        <!--body-->
        <div class="relative flex-auto">
          <div class="grid grid-cols-2">
            <div class="px-3 py-2 font-bold text-white border-solid border-t border-r border-b border-l" :class="(organization.pnav<1.5 && organization.pe<15 && organization.upe<15 && organization.pepnav<22.5 && organization.upepnav<22.5 && organization.div>0) ? (organization.pe>10 || organization.upe>10 || organization.div<5 ? 'bg-orange-600' : 'bg-green-600') : 'bg-red-600'">
              <span class="block text-center text-xs">Price</span>
              <h4 class="text-center text-2xl">{{ organization.price }}</h4>
            </div>
            <div class="px-3 py-2 font-bold text-white border-solid border-t border-r border-b border-l-0" :class="organization.pnav<1.5 ? 'bg-green-600' : 'bg-red-600'">
              <span class="block text-center text-xs">P/NAV</span>
              <h4 class="text-center text-2xl">{{ organization.pnav }}</h4>
            </div>
            <div class="px-3 py-2 font-bold text-white border-solid border-t-0 border-r border-b border-l" :class="organization.pe<15 ? (organization.pe<10 ? 'bg-green-600' : 'bg-orange-600') : 'bg-red-600'">
              <span class="block text-center text-xs">P/E</span>
              <h4 class="text-center text-2xl">{{ organization.pe }}</h4>
            </div>
            <div class="px-3 py-2 font-bold text-white border-solid border-t-0 border-r border-b border-l-0" :class="organization.upe<15 ? (organization.upe<10 ? 'bg-green-600' : 'bg-orange-600') : 'bg-red-600'">
              <span class="block text-center text-xs">U P/E</span>
              <h4 class="text-center text-2xl">{{ organization.upe }}</h4>
            </div>
            <div class="px-3 py-2 font-bold text-white border-solid border-t-0 border-r border-b border-l" :class="organization.pepnav<22.5 ? 'bg-green-600' : 'bg-red-600'">
              <span class="block text-center text-xs">P/E * P/NAV</span>
              <h4 class="text-center text-2xl">{{ organization.pepnav }}</h4>
            </div>
            <div class="px-3 py-2 font-bold text-white border-solid border-t-0 border-r border-b border-l-0" :class="organization.upepnav<22.5 ? 'bg-green-600' : 'bg-red-600'">
              <span class="block text-center text-xs">U P/E * P/NAV</span>
              <h4 class="text-center text-2xl">{{ organization.upepnav }}</h4>
            </div>
            <div class="px-3 py-2 font-bold text-white border-solid border-t-0 border-r border-b border-l" :class="organization.div>0 ? (organization.div>5 ? 'bg-green-600' : 'bg-orange-600') : 'bg-red-600'">
              <span class="block text-center text-xs">Dividend Yield</span>
              <h4 class="text-center text-2xl">{{ organization.div }}</h4>
            </div>
            <div class="px-3 py-2 font-bold text-white border-solid border-t-0 border-r border-b border-l-0" :class="organization.avg_dividend>0 ? (organization.avg_dividend>5 ? 'bg-green-600' : 'bg-orange-600') : 'bg-red-600'">
              <span class="block text-center text-xs">Avg Cash Dividend</span>
              <h4 class="text-center text-2xl">{{ organization.avg_dividend }} %</h4>
            </div>
            <!-- <div class="px-3 py-2 font-bold text-white border-solid border-t-0 border-r border-b border-l-0" :class="organization.eps>0 ? 'bg-green-600' : 'bg-red-600'">
              <span class="block text-center text-xs">EPS</span>
              <h4 class="text-center text-2xl">{{ organization.eps }}</h4>
            </div> -->
            <div class="px-3 py-2 font-bold text-white border-solid border-t-0 border-r border-b border-l bg-indigo-800">
              <span class="block text-center text-xs">Category</span>
              <h4 class="text-center text-2xl">{{ organization.category }}</h4>
            </div>
            <div class="px-3 py-2 font-bold text-white border-solid border-t-0 border-r border-b border-l-0 bg-indigo-800">
              <span class="block text-center text-xs">Sector</span>
              <h4 class="text-center text-2xl">{{ organization.sector }}</h4>
            </div>
            <!-- <div class="px-3 py-2 font-bold text-white border-solid border-t-0 border-r border-b border-l bg-indigo-800 col-span-2">
              <span class="block text-center text-xs">Market Capital (mn)</span>
              <h4 class="text-center text-2xl">{{ organization.marketCap }}</h4>
            </div> -->
            <div class="px-3 py-2 font-bold text-white border-solid border-t-0 border-r border-b border-l bg-indigo-800">
              <span class="block text-center text-xs">Long-term Loan (mn)</span>
              <h4 class="text-center text-2xl">{{ organization.longLoan }}</h4>
            </div>
            <div class="px-3 py-2 font-bold text-white border-solid border-t-0 border-r border-b border-l-0 bg-indigo-800">
              <span class="block text-center text-xs">Short-term Loan (mn)</span>
              <h4 class="text-center text-2xl">{{ organization.shortLoan }}</h4>
            </div>
            <div class="px-3 py-2 font-bold text-white border-solid border-t-0 border-r border-b border-l bg-indigo-800">
              <span class="block text-center text-xs">Listing Year</span>
              <h4 class="text-center text-2xl">{{ organization.listingYear }}</h4>
            </div>
            <div class="px-3 py-2 font-bold text-white border-solid border-t-0 border-r border-b border-l-0 bg-indigo-800">
              <span class="block text-center text-xs">Last AGM</span>
              <h4 class="text-center text-2xl">{{ organization.agm }}</h4>
            </div>
            <div class="px-6 py-4 font-bold text-white border-solid border-t-0 border-r border-b border-l bg-indigo-800 col-span-2">
              <span class="block text-center text-xs">Website</span>
              <h4 class="text-center text-xl"><a :href="organization.website" target="_blank" class="underline-offset-auto">{{ organization.website }}</a></h4>
            </div>
          </div>
        </div>
        <!--footer-->
        <div class="flex items-center justify-end p-3 rounded-b bg-indigo-800 border-solid border-t-0 border-r border-b border-l">
          <button class="text-red-500 bg-white border border-solid border-red-500 hover:bg-red-500 hover:text-white active:bg-red-600 font-bold uppercase text-sm px-3 py-2 rounded outline-none focus:outline-none ease-linear transition-all duration-150" type="button" v-on:click="toggleModal()">
            Close
          </button>
        </div>
      </div>
    </div>
  </div>
  <div v-if="showModal" class="opacity-25 fixed inset-0 z-40 bg-black"></div>
</template>

<script>
import { usePage } from '@inertiajs/inertia-vue3';
import { store } from '../store'
export default {
  props: {
    organization: {}
  },
  data() {
    return {
      showModal: false,
      store
    }
  },
  methods: {
    toggleModal: function(){
      this.showModal = !this.showModal;
    },
    watchList: function(){
      if(usePage().props.value.auth.user){
        this.store.toggleWatchlist(this.organization);
        fetch('/organizations/watch/' + this.organization.id)
          .then(response => response.json())
          .catch((error) => {
            this.store.toggleWatchlist(this.organization);
          });
      }else{
        this.$inertia.get('/login');
      }
    }
  }
}
</script>
