<template>
  <!-- Transaction Form Modal -->
  <div class="overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center flex flex-wrap">
    <form class="bg-white rounded-lg shadow-xl overflow-hidden" @submit.prevent="submitForm" style="width: 450px">
        <div class="px-6 py-8">
            <h3 class="text-center text-xl font-semibold">{{ getTransactionType(type) }}</h3>
            <div class="mt-2 mx-auto w-24 border-b-2" />
            <text-input v-model="form.type" :error="form.errors.type" class="mt-4" label="" type="hidden" autocapitalize="off" />
            <text-input v-model="form.portfolio_id" :error="form.errors.portfolio_id" class="mt-4" label="" type="hidden" autocapitalize="off" />
            <select-input v-if="type===3 || type===4 || type===7" v-model="form.organization_id" :error="form.errors.organization_id" class="mt-6" label="Stock">
                <option v-if="type===3" v-for="org in store.organizations" :key="org.id" :value="org.id">{{ org.code }}</option>
                <option v-if="type===4 || type===7" v-for="org in portfolio.organizations" :key="org.organization.id" :value="org.organization.id">{{ org.organization.code }}</option>
            </select-input>
            <text-input v-model="form.amount" :error="form.errors.amount" class="mt-4" :label="(type===3 || type===4) ? 'Price (Per share)' : 'Amount'" type="number" step=".01" autocapitalize="off" />
            <text-input v-if="type===3 || type===4" v-model="form.quantity" :error="form.errors.quantity" class="mt-4" label="Quantity" type="number" autocapitalize="off" />
            <text-input v-if="type===1 || type===2 || type===6" v-model="form.name" :error="form.errors.name" class="mt-4" label="Remarks" type="text" autocapitalize="off" />
        </div>
        <div class="flex px-6 py-4 bg-gray-100 border-t border-gray-100">
            <loading-button :loading="form.processing" class="btn-indigo ml-auto mr-2" type="submit">Add</loading-button>
            <button class="btn-indigo bg-red-600 text-white" type="button" @click="$emit('toggleModal')">Close</button>
        </div>
    </form>
  </div>
  <div class="opacity-25 fixed inset-0 z-40 bg-black"></div>
</template>

<script>
import TextInput from '@/Shared/TextInput'
import LoadingButton from '@/Shared/LoadingButton'
import SelectInput from '@/Shared/SelectInput'
import { store } from '../../store'
import getTransactionTypeString from "../../Helpers/string"

export default {
    components: {
        LoadingButton,
        SelectInput,
        TextInput,
    },
    props: {
      type: Number,
      portfolio: Object,
      organization_id: Number
    },
    data() {
      return {
        form: this.$inertia.form({
            name: null,
            type: this.type,
            organization_id: this.organization_id,
            portfolio_id: this.portfolio.id,
            amount: 0,
            quantity: 1
        }),
        store,
      }
    },
    emits: ['toggleModal'],
    methods: {
      getTransactionType(type){
        return getTransactionTypeString(type);
      },
      submitForm(){
        this.form.post('/transactions', {
            // preserveScroll: true,
            onSuccess: () => {
                this.$emit('toggleModal');
            },
        });
      }
    },
    mounted(){
      
    }
}
</script>