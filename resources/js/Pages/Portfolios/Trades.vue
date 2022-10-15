<template>
  <h1 class="mb-4 text-l font-bold">Trades</h1>
  <div class="bg-white rounded-md shadow overflow-x-auto">
    <table class="w-full whitespace-nowrap">
      <tr class="text-left font-bold">
        <th class="pb-4 pt-6 px-6">Company</th>
        <th class="pb-4 pt-6 px-6">Type</th>
        <th class="pb-4 pt-6 px-6">Average Cost</th>
        <th class="pb-4 pt-6 px-6">Quantity</th>
        <th class="pb-4 pt-6 px-6">Total Cost</th>
        <th class="pb-4 pt-6 px-6">Commission</th>
        <th class="pb-4 pt-6 px-6">Total</th>
        <th class="pb-4 pt-6 px-6">Date</th>
      </tr>
      <tr v-for="item in trades" :key="item.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
        <td class="border-t px-6 py-4">{{ item.organization_id }}</td>
        <td class="border-t px-6 py-4">{{ item.type===1 ? 'Buy' : 'Sell' }}</td>
        <td class="border-t px-6 py-4">{{ item.price }}</td>
        <td class="border-t px-6 py-4">{{ item.quantity }}</td>
        <td class="border-t px-6 py-4">{{ item.price * item.quantity }}</td>
        <td class="border-t px-6 py-4">{{ item.charge }}</td>
        <td class="border-t px-6 py-4">{{ (item.price * item.quantity) + item.charge }}</td>
        <td class="border-t px-6 py-4">{{ item.created_at }}</td>
      </tr>
      <tr v-if="trades?.length === 0">
        <td class="border-t px-6 py-4 text-center" colspan="8">No trades found</td>
      </tr>
    </table>
  </div>
</template>

<script>
export default {
    props: {
      trades: Array
  }
}
</script>