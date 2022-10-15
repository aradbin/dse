<template>
  <h1 class="mb-4 text-l font-bold">Charges</h1>
  <div class="bg-white rounded-md shadow overflow-x-auto">
    <table class="w-full whitespace-nowrap">
      <tr class="text-left font-bold">
        <th class="pb-4 pt-6 px-6">Type</th>
        <th class="pb-4 pt-6 px-6">Amount</th>
        <th class="pb-4 pt-6 px-6">Date</th>
      </tr>
      <tr v-for="item in charges" :key="item.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
        <td class="border-t px-6 py-4">{{ item.type===1 ? 'BO Charge' : ( item.type===2 ? 'Trading Charge' : 'IPO Charge') }}</td>
        <td class="border-t px-6 py-4">{{ item.amount }}</td>
        <td class="border-t px-6 py-4">{{ item.created_at }}</td>
      </tr>
      <tr v-if="charges?.length === 0">
        <td class="border-t px-6 py-4 text-center" colspan="3">No charges found</td>
      </tr>
    </table>
  </div>
</template>

<script>
export default {
    props: {
      charges: Array
  }
}
</script>