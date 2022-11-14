<template>
  <h1 class="mb-8 text-l font-bold">
    Transactions
    <button class="btn-danger float-right" @click="$emit('toggleModal',2)">Withdraw</button>
    <button class="btn-success float-right mr-2" @click="$emit('toggleModal',1)">Deposit</button>
  </h1>
  <div class="bg-white rounded-md shadow overflow-x-auto w-full">
    <table class="w-full whitespace-nowrap">
      <tr class="text-left font-bold">
        <th class="pb-4 pt-6 px-6">Date</th>
        <th class="pb-4 pt-6 px-6">Type</th>
        <th class="pb-4 pt-6 px-6">Amount</th>
        <!-- <th class="pb-4 pt-6 px-6">Sector</th> -->
        <th class="pb-4 pt-6 px-6">Quantity</th>
        <th class="pb-4 pt-6 px-6">Commission</th>
        <th class="pb-4 pt-6 px-6">Remarks</th>
      </tr>
      <tr v-for="item in transactions" :key="item.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
        <td class="border-t px-6 py-4">{{ getDate(item.created_at) }}</td>
        <td class="border-t px-6 py-4">{{ getTransactionType(item.type) }}</td>
        <td class="border-t px-6 py-4">{{ item.amount }}</td>
        <!-- <td class="border-t px-6 py-4">{{ item.organization_id && item.organization_id }}</td> -->
        <td class="border-t px-6 py-4">{{ item.quantity }}</td>
        <td class="border-t px-6 py-4">{{ item.commission }}</td>
        <td class="border-t px-6 py-4">{{ item.name || 'N/A' }}</td>
        
      </tr>
      <tr v-if="transactions?.length === 0">
        <td class="border-t px-6 py-4 text-center" colspan="3">No transactions found</td>
      </tr>
    </table>
  </div>
</template>

<script>
import getTransactionTypeString from "../../Helpers/string";
import getDateString from "../../Helpers/date";

export default {
  emits: ['toggleModal'],
  props: {
      transactions: Array
  },
  methods: {
    getTransactionType(type){
      return getTransactionTypeString(type);
    },
    getDate(date){
      return getDateString(date);
    }
  }
}
</script>