<template>
  <div>
    <Head title="Organizations" />
    <h1 class="mb-8 text-3xl font-bold" v-if="isUrl('organizations') || isUrl('')">Organizations</h1>
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
      <!-- <Link class="btn-indigo" href="/organizations/sync/amarstock" v-if="auth.user && auth.user.owner==1">
        <span>Sync</span>
        <span class="hidden md:inline">&nbsp;Organizations</span>
      </Link> -->
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-1 2xl:grid-cols-1 gap-4 xl:gap-0">
      <card v-for="organization in this.store.filteredOrganizationsByPage" :key="organization.id" :organization="organization" />
    </div>
    <PaginationFront class="mt-6" :total="this.store.filteredOrganizations.length" :current_page="form.current_page" :per_page="form.per_page" @changePage="changePage" />
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import throttle from 'lodash/throttle'
import PaginationFront from '@/Shared/PaginationFront'
import SearchFilter from '@/Shared/SearchFilter'
import Card from '@/Shared/Card'
import { store } from '../../store'

export default {
  components: {
    Head,
    Icon,
    Link,
    PaginationFront,
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
        current_page: 1,
        watchlist: false
      },
      store
    }
  },
  watch: {
    'store.organizations'(){
      store.updateQuery(this.form);
    },
    form: {
      deep: true,
      handler: throttle(function () {
        store.updateQuery(this.form);
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
        current_page: 1,
        watchlist: null
      };
    },
    changePage(page){
      this.form.current_page = page;
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
    }else{
      this.form.watchlist = false;
    }
    if(this.store.organizations.length > 0){
      this.store.updateQuery(this.form);
    }
  }
}
</script>