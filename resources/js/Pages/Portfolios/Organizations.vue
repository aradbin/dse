<template>
  <h1 class="mb-8 text-l font-bold">
    Organizations
    <button class="btn-success float-right" @click="$emit('toggleModal',3)">Buy</button>
    <!-- <button class="btn-danger float-right" @click="$emit('toggleModal',4)">Sell</button> -->
  </h1>
  <div class="bg-white rounded-md shadow overflow-x-auto w-full">
    <table class="w-full whitespace-nowrap">
      <tr class="text-left font-bold">
        <th class="pb-4 pt-6 px-6">Company</th>
        <th class="pb-4 pt-6 px-6">Quantity</th>
        <th class="pb-4 pt-6 px-6">Cost</th>
        <th class="pb-4 pt-6 px-6">Total Cost</th>
        <th class="pb-4 pt-6 px-6">Market Price</th>
        <th class="pb-4 pt-6 px-6">Market Value</th>
        <th class="pb-4 pt-6 px-6">Gain</th>
        <th class="pb-4 pt-6 px-6">Gain (%)</th>
        <th class="pb-4 pt-6 px-6">Action</th>
      </tr>
      <tr v-for="org in store.portfolio.organizations" :key="org.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
        <td class="border-t px-6 py-4">{{ org.organization.code }}</td>
        <td class="border-t px-6 py-4">{{ org.quantity }}</td>
        <td class="border-t px-6 py-4">{{ org.amount }}</td>
        <td class="border-t px-6 py-4">{{ org.amount * org.quantity }}</td>
        <td class="border-t px-6 py-4">{{ org.organization.price || 0 }}</td>
        <td class="border-t px-6 py-4">{{ org.organization.price ? (org.organization.price * org.quantity) : 0 }}</td>
        <td class="border-t px-6 py-4">
          <span class="badge" :class="(!org.organization.price || (org.amount > org.organization.price)) ? 'badge-danger' : 'badge-success'">{{ org.organization.price ? ((org.organization.price * org.quantity) - (org.amount * org.quantity)).toFixed(2) : (0 - (org.amount * org.quantity)).toFixed(2) }}</span>
        </td>
        <td class="border-t px-6 py-4">
          <span class="badge" :class="(!org.organization.price || (org.amount > org.organization.price)) ? 'badge-danger' : 'badge-success'">{{ org.organization.price ? ((((org.organization.price * org.quantity) - (org.amount * org.quantity)) / (org.amount * org.quantity) * 100).toFixed(2)) : (((0 - (org.amount * org.quantity)) / (org.amount * org.quantity) * 100).toFixed(2)) }}%</span>
        </td>
        <td class="border-t px-6 py-4">
          <button class="btn-success px-4 py-2 mr-2" @click="$emit('toggleModal',3,org.organization.id)">Buy More</button>
          <button class="btn-danger px-4 py-2" @click="$emit('toggleModal',4,org.organization.id)">Sell</button>
        </td>
      </tr>
      <tr v-if="organizations?.length === 0">
        <td class="border-t px-6 py-4 text-center" colspan="8">No organizations found</td>
      </tr>
    </table>
  </div>
</template>

<script>
import Badge from '@/Shared/Badge'
import { store } from '../../store'

export default {
  components: {
    Badge
  },
  emits: ['toggleModal'],
  props: {
    
  },
  data() {
    return {
      store
    }
  }
}
</script>