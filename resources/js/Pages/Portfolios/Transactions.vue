<template>
  <h1 class="mb-4 text-l font-bold">Transactions</h1>
  <div class="flex items-end justify-end mb-6">
    <button class="btn-success mr-2" v-on:click="toggleModal()">Deposit</button>
    <button class="btn-danger" v-on:click="toggleModal()">Withdrawl</button>
  </div>
  <div class="bg-white rounded-md shadow overflow-x-auto">
    <table class="w-full whitespace-nowrap">
      <tr class="text-left font-bold">
        <th class="pb-4 pt-6 px-6">Type</th>
        <th class="pb-4 pt-6 px-6">Amount</th>
        <th class="pb-4 pt-6 px-6">Date</th>
      </tr>
      <tr v-for="item in transactions" :key="item.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
        <td class="border-t px-6 py-4">{{ item.type===1 ? 'Deposit' : (item.type===2 ? 'Withdrawl' : (item.type===3 ? 'Buy' : (item.type===4 ? 'Sell' : (item.type===5 ? 'BO Charge' : 'IPO Charge')))) }}</td>
        <td class="border-t px-6 py-4">{{ item.amount }}</td>
        <td class="border-t px-6 py-4">{{ item.created_at }}</td>
      </tr>
      <tr v-if="transactions?.length === 0">
        <td class="border-t px-6 py-4 text-center" colspan="3">No transactions found</td>
      </tr>
    </table>
  </div>
</template>

<script>
export default {
  props: {
      transactions: Array
  }
}
</script>