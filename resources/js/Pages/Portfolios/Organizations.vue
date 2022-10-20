<template>
  <h1 class="mb-4 text-l font-bold">
    Organizations
    <button class="btn-success float-right mr-2" @click="$emit('toggleModal',3)">Buy</button>
    <button class="btn-danger float-right" @click="$emit('toggleModal',4)">Sell</button>
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
      <tr v-for="item in organizations" :key="item.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
        <td class="border-t px-6 py-4">{{ item.organization_id }}</td>
        <td class="border-t px-6 py-4">{{ item.quantity }}</td>
        <td class="border-t px-6 py-4">{{ item.amount }}</td>
        <td class="border-t px-6 py-4">{{ item.amount * item.quantity }}</td>
        <td class="border-t px-6 py-4"></td>
        <td class="border-t px-6 py-4"></td>
        <td class="border-t px-6 py-4"></td>
        <td class="border-t px-6 py-4">%</td>
        <td class="border-t px-6 py-4">
          <button class="btn-success float-right px-4 py-2 mr-2" @click="$emit('toggleModal',3,item.id)">Buy More</button>
          <button class="btn-danger float-right px-4 py-2" @click="$emit('toggleModal',4),item.id">Sell</button>
        </td>
      </tr>
      <tr v-if="organizations?.length === 0">
        <td class="border-t px-6 py-4 text-center" colspan="8">No organizations found</td>
      </tr>
    </table>
  </div>
</template>

<script>
export default {
  emits: ['toggleModal'],
  props: {
    organizations: Array
  }
}
</script>