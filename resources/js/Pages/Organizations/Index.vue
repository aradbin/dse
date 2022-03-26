<template>
  <div>
    <Head title="Organizations" />
    <h1 class="mb-8 text-3xl font-bold">Organizations</h1>
    <div class="flex items-end justify-between mb-6">
      <search-filter v-model="form.search" class="mr-4 w-full" @reset="reset">
        <div class="w-1/4 mr-2">
          <label class="block text-gray-700">Per Page:</label>
          <select v-model="form.per_page" class="form-select w-full mt-2">
            <option :value="null">20</option>
            <option value="50">50</option>
            <option value="100">100</option>
            <option value="200">200</option>
            <option value="300">300</option>
            <option value="400">400</option>
            <option value="500">500</option>
          </select>
        </div>
        <div class="w-1/4 mr-2">
          <label class="block text-gray-700">Index:</label>
          <select v-model="form.se_index" class="form-select w-full mt-2">
            <option :value="null">All</option>
            <option value="DS30">DS30</option>
          </select>
        </div>
      </search-filter>
      <Link class="btn-indigo" href="/organizations/sync" v-if="auth.user && auth.user.owner==1">
        <span>Sync</span>
        <span class="hidden md:inline">&nbsp;Organizations</span>
      </Link>
    </div>
    <!-- <div class="bg-white rounded-md shadow overflow-x-auto">
      <table class="w-full whitespace-nowrap">
        <thead>
          <tr class="text-left font-bold">
            <th class="pb-4 pt-6 px-6">Code</th>
            <th class="pb-4 pt-6 px-6 w-36 text-center">Price</th>
            <th class="pb-4 pt-6 px-6 w-36 text-center">P/E</th>
            <th class="pb-4 pt-6 px-6 w-36 text-center">P/NAV</th>
            <th class="pb-4 pt-6 px-6 w-36 text-center">P/E * P/NAV</th>
            <th class="pb-4 pt-6 px-6 w-36 text-center">Dividend Yield</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="organization in organizationsArray" :key="organization.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
            <td class="border-t items-center px-6 py-4 focus:text-indigo-500">{{ organization.code }}</td>
            <td class="border-t items-center px-0 py-0 text-center focus:text-indigo-500">
              <span class="inline-flex items-center justify-center w-full px-6 py-4 font-bold text-white bg-indigo-800">{{ organization.price }}</span>
            </td>
            <td class="border-t items-center px-0 py-0 text-center focus:text-indigo-500">
              <span class="inline-flex items-center justify-center w-full px-6 py-4 font-bold text-white " :class="organization.pe<15 ? 'bg-green-600' : 'bg-red-600'">{{ organization.pe }}</span>
            </td>
            <td class="border-t items-center px-0 py-0 text-center focus:text-indigo-500">
              <span class="inline-flex items-center justify-center w-full px-6 py-4 font-bold text-white " :class="organization.pnav<1.5 ? 'bg-green-600' : 'bg-red-600'">{{ organization.pnav }}</span>
            </td>
            <td class="border-t items-center px-0 py-0 text-center focus:text-indigo-500">
              <span class="inline-flex items-center justify-center w-full px-6 py-4 font-bold text-white " :class="organization.pepnav<22.5 ? 'bg-green-600' : 'bg-red-600'">{{ organization.pepnav }}</span>
            </td>
            <td class="border-t items-center px-0 py-0 text-center focus:text-indigo-500">
              <span class="inline-flex items-center justify-center w-full px-6 py-4 font-bold text-white " :class="organization.div>0 ? 'bg-green-600' : 'bg-red-600'">{{ organization.div }}</span>
            </td>
          </tr>
          <tr v-if="organizations.data.length === 0">
            <td class="px-6 py-4 border-t" colspan="6">No organizations found.</td>
          </tr>
        </tbody>
      </table>
    </div> -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
      <card v-for="organization in organizationsArray" :key="organization.id" :organization="organization" />
    </div>
    <pagination class="mt-6" :links="organizations.links" />
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Icon from '@/Shared/Icon'
import pickBy from 'lodash/pickBy'
import Layout from '@/Shared/Layout'
import throttle from 'lodash/throttle'
import mapValues from 'lodash/mapValues'
import Pagination from '@/Shared/Pagination'
import SearchFilter from '@/Shared/SearchFilter'
import Card from '@/Shared/Card'

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
    auth: Object,
    filters: Object,
    organizations: Object,
  },
  data() {
    return {
      form: {
        search: this.filters.search,
        se_index: this.filters.se_index,
        per_page: this.filters.per_page,
      },
      organizationsArray: []
    }
  },
  watch: {
    form: {
      deep: true,
      handler: throttle(function () {
        this.$inertia.get('/organizations', pickBy(this.form), {
          preserveState: true,
          onSuccess: () => {this.getDetails()}
        })
      }, 150),
    },
  },
  methods: {
    reset() {
      this.form = mapValues(this.form, () => null)
    },
    async getDetails(){
      this.organizationsArray = this.organizations.data;
      await Promise.all(this.organizations.data.map((org, index, array) => {
        return fetch('/organizations/show/' + org.code)
          .then(response => response.json())
          .then(data => {
              this.organizationsArray[index] = {
              'id' : org.id,
              'code' : org.code,
              'name' : data.FullName,
              'category' : data.MarketCategory,
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
              'website' : data.Web
            }
          })
      }));
    }
  },
  mounted(){
    this.getDetails();
    window.setInterval(() => {
      let d = new Date();
      if(d.getDay()<5 && d.getHours()>10 && d.getHours()<15){
        this.getDetails();
      }
    }, 60000);
  }
}
</script>