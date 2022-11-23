<template>
  <h1 class="mb-4 text-l font-bold">Portfolios</h1>
  <div class="bg-white rounded-md shadow overflow-x-auto">
    <table class="w-full whitespace-nowrap">
      <tr class="text-left font-bold">
        <th class="pb-4 pt-6 px-6">Name</th>
        <th class="pb-4 pt-6 px-6">BO Account</th>
        <th class="pb-4 pt-6 px-6">Broker</th>
        <th class="pb-4 pt-6 px-6">Broker User ID</th>
        <th class="pb-4 pt-6 px-6">Commission (%)</th>
        <th class="pb-4 pt-6 px-6">Available Balance</th>
        <th class="pb-4 pt-6 px-6">Unrealized Gain (%)</th>
        <th class="pb-4 pt-6 px-6">Actions</th>
      </tr>
      <tr v-for="item in store.portfolios" :key="item.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
        <td class="border-t px-6 py-4">{{ item.name }}</td>
        <td class="border-t px-6 py-4">{{ item.bo_account }}</td>
        <td class="border-t px-6 py-4">{{ item.broker_id }}</td>
        <td class="border-t px-6 py-4">{{ item.broker_user_id }}</td>
        <td class="border-t px-6 py-4">{{ item.commission }} %</td>
        <td class="border-t px-6 py-4">{{ item.balance }}</td>
        <td class="border-t px-6 py-4">
          <span class="badge" :class="(!item.gain || item.gain >= 0) ? 'badge-success' : 'badge-danger'">{{ item.gain || 0 }} ({{ item.gainPercent }}%)</span>
        </td>
        <td class="border-t px-6 py-4"><Link class="btn-indigo px-4 py-2" :href="'/portfolio/'+item.id">Details</Link></td>
      </tr>
      <tr v-if="store.portfolios?.length === 0">
        <td class="border-t px-6 py-4 text-center" colspan="3">No portfolio found</td>
      </tr>
    </table>
  </div>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3'
import { store } from '../../store'

export default {
  components: {
    Link,
  },
  data() {
    return {
      store
    }
  },
}
</script>