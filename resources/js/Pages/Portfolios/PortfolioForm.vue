<template>
  <!-- Portfolio Form Modal -->
  <div class="overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center flex flex-wrap">
    <form class="bg-white rounded-lg shadow-xl overflow-hidden" @submit.prevent="submitForm" style="width: 450px">
        <div class="px-6 py-8">
            <h3 class="text-center text-xl font-semibold">Add New Portfolio</h3>
            <div class="mt-2 mx-auto w-24 border-b-2" />
            <text-input v-model="form.name" :error="form.errors.name" class="mt-4" label="Portfolio Name" type="text" autocapitalize="off" />
            <text-input v-model="form.bo_account" :error="form.errors.bo_account" class="mt-4" label="BO Account" type="text" autocapitalize="off" />
            <select-input v-model="form.broker_id" :error="form.errors.broker_id" class="mt-6" label="Broker">
                <option :value="null">Test</option>
                <option v-for="broker in store.brokers" :key="broker.id" :value="broker.id">{{ broker.name }}</option>
            </select-input>
            <text-input v-model="form.broker_user_id" :error="form.errors.broker_user_id" class="mt-4" label="Broker User ID" type="text" autocapitalize="off" />
            <text-input v-model="form.commission" :error="form.errors.commission" class="mt-4" label="Trading Commission (%)" type="number" step=".01" autocapitalize="off" />
            <text-input v-model="form.initial_deposit" :error="form.errors.initial_deposit" class="mt-4" label="Initial Deposit" type="number" step=".01" autocapitalize="off" />
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

export default {
    components: {
        LoadingButton,
        SelectInput,
        TextInput,
    },
    data() {
      return {
        store,
        form: this.$inertia.form({
            name: null,
            bo_account: null,
            broker_id: null,
            broker_user_id: null,
            commission: 0.5,
            initial_deposit: 0
        })
      }
    },
    emits: ['toggleModal'],
    methods: {
      submitForm(){
        this.form.post('/portfolio', {
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