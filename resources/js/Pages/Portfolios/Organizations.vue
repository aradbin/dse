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
      <tr v-for="org in store.filteredOrganizations" :key="org.code" class="hover:bg-gray-100 focus-within:bg-gray-100">
        <td class="border-t px-6 py-4">{{ org.code }}</td>
        <td class="border-t px-6 py-4">{{ getOrganization(org.code).quantity }}</td>
        <td class="border-t px-6 py-4">{{ getOrganization(org.code).amount }}</td>
        <td class="border-t px-6 py-4">{{ getOrganization(org.code).amount * getOrganization(org.code).quantity }}</td>
        <td class="border-t px-6 py-4">{{ org.price }}</td>
        <td class="border-t px-6 py-4">{{ org.price * getOrganization(org.code).quantity }}</td>
        <td class="border-t px-6 py-4">{{ (getOrganization(org.code).amount * getOrganization(org.code).quantity) - (org.price * getOrganization(org.code).quantity) }}</td>
        <td class="border-t px-6 py-4">{{ ((getOrganization(org.code).amount * getOrganization(org.code).quantity) - (org.price * getOrganization(org.code).quantity)) / (getOrganization(org.code).amount * getOrganization(org.code).quantity) * 100 }}</td>
        <td class="border-t px-6 py-4">
          <button class="btn-success px-4 py-2 mr-2" @click="$emit('toggleModal',3,org.id)">Buy More</button>
          <button class="btn-danger px-4 py-2" @click="$emit('toggleModal',4,org.id)">Sell</button>
        </td>
      </tr>
      <tr v-if="organizations?.length === 0">
        <td class="border-t px-6 py-4 text-center" colspan="8">No organizations found</td>
      </tr>
    </table>
  </div>
</template>

<script>
import { store } from '../../store'

export default {
  emits: ['toggleModal'],
  props: {
    organizations: Array
  },
  data() {
    return {
      store
    }
  },
  methods: {
    getOrganization(code){
      this.organizations.filter(item => item.organization.code===code)[0]
    }
  }
}
</script>