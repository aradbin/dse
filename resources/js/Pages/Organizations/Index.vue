<template>
  <div>
    <Head title="Organizations" />
    <h1 class="mb-8 text-3xl font-bold" v-if="isUrl('organizations')">Organizations</h1>
    <h1 class="mb-8 text-3xl font-bold" v-if="isUrl('watchlist')">My Watchlist</h1>
    <div class="flex items-end justify-between mb-6">
      <search-filter v-model="form.search" class="w-full" @reset="reset">
        <div class="w-1/5 mr-2">
          <label class="block text-gray-700">Per Page:</label>
          <select v-model="form.per_page" class="form-select w-full mt-2">
            <option value="20">20</option>
            <option value="50">50</option>
            <option value="100">100</option>
            <option value="200">200</option>
            <option value="300">300</option>
            <option value="400">400</option>
            <option value="500">500</option>
          </select>
        </div>
        <div class="w-1/5 mr-2">
          <label class="block text-gray-700">Index:</label>
          <select v-model="form.se_index" class="form-select w-full mt-2">
            <option :value="null">All</option>
            <option value="DS30">DS30</option>
          </select>
        </div>
        <div class="w-1/5 mr-2">
          <label class="block text-gray-700">Category:</label>
          <select v-model="form.category" class="form-select w-full mt-2">
            <option :value="null">All</option>
            <option value="A">A</option>
            <option value="B">B</option>
            <option value="G">G</option>
            <option value="N">N</option>
            <option value="Z">Z</option>
          </select>
        </div>
        <div class="w-2/5">
          <label class="block text-gray-700">Sector:</label>
          <select v-model="form.sector" class="form-select w-full mt-2">
            <option :value="null">All</option>
            <option v-for="sector in this.store.sectors" :key="sector.sector" :value="sector.sector">{{ sector.sector }}</option>
          </select>
        </div>
      </search-filter>
      <!-- <Link class="btn-indigo" href="/organizations/sync" v-if="auth.user && auth.user.owner==1">
        <span>Sync</span>
        <span class="hidden md:inline">&nbsp;Organizations</span>
      </Link> -->
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
      <card v-for="organization in this.store.filteredOrganizations" :key="organization.id" :organization="organization" />
    </div>
    <!-- <pagination class="mt-6" :links="organizations.links" /> -->
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import throttle from 'lodash/throttle'
import Pagination from '@/Shared/Pagination'
import SearchFilter from '@/Shared/SearchFilter'
import Card from '@/Shared/Card'
import { store } from '../../store'

export default {
  components: {
    Head,
    Icon,
    Link,
    Pagination,
    SearchFilter,
    Card
  },
  layout: Layout,
  props: {
    auth: Object
  },
  data() {
    return {
      form: {
        search: null,
        se_index: null,
        category: null,
        sector: null,
        per_page: 20,
        page: 1,
        watchlist: false
      },
      store
    }
  },
  watch: {
    'store.organizations'(){
      this.getDetails();
    },
    form: {
      deep: true,
      handler: throttle(function () {console.log(this.form)
        store.updateQuery(this.form)
        this.getDetails();
      }, 150),
    }
  },
  methods: {
    reset() {
      this.form = {
        search: null,
        se_index: null,
        category: null,
        sector: null,
        per_page: 20,
        page: 1,
        watchlist: null
      };
    },
    async getDetails(updatePrice=false){
      await Promise.all(this.store.filteredOrganizations.map((org) => {
        if(updatePrice || !org.price){
          return fetch('/organizations/show/' + org.code)
            .then(response => response.json())
            .then(data => {
              let total_dividend = 0;
              let avg_dividend = 0;
              if(org?.dividends?.length>0){
                org.dividends.map(function(dividend){
                  total_dividend = total_dividend + dividend.cash;
                });
                avg_dividend = (((total_dividend/org.dividends.length)/10)/data.LastTrade)*100;
              }
              this.store.updateOrganization({
                'id' : org.id,
                'code' : org.code,
                'name' : data.FullName,
                'category' : data.MarketCategory,
                'sector' : org.sector,
                'price' : data.LastTrade,
                'eps' : data.EPS,
                'pe' : data.AuditedPE,
                'upe' : data.UnAuditedPE,
                'pnav' : data.NavPrice,
                'pepnav' : (data.AuditedPE * data.NavPrice).toFixed(2),
                'upepnav' : (data.UnAuditedPE * data.NavPrice).toFixed(2),
                'div' : data.DividentYield,
                'agm' : data.LastAGMHeld,
                'listingYear' : data.ListingYear,
                'longLoan' : data.LongLoan,
                'shortLoan' : data.ShortLoan,
                'marketCap' : data.MarketCap,
                'website' : data.Web,
                'is_watch_listed' : org.is_watch_listed,
                'dividends': org.dividends,
                'avg_dividend': avg_dividend.toFixed(2)
              });
            });
        }
        return true;
      }));
    },
    isUrl(url) {
      if(this.$page.url.substr(1)===url){
        return true;
      }
      return false;
    }
  },
  mounted(){
    if(this.isUrl('watchlist')){
      this.form.watchlist = true;
      store.updateQuery(this.form)
    }else{
      this.form.watchlist = false;
      store.updateQuery(this.form)
    }
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