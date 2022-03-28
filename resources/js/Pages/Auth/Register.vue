<template>
  <Head title="Register" />
  <div class="flex items-center justify-center p-6 min-h-screen bg-indigo-800">
    <div class="w-full max-w-md">
      <logo class="block mx-auto w-full max-w-xs fill-white" height="50" />
      <form class="mt-8 bg-white rounded-lg shadow-xl overflow-hidden" @submit.prevent="login">
        <div class="px-10 py-12">
          <h1 class="text-center text-3xl font-bold">Register</h1>
          <div class="mt-6 mx-auto w-24 border-b-2" />
          <text-input v-model="form.first_name" :error="form.errors.first_name" class="mt-10" label="First Name" type="text" autofocus autocapitalize="off" />
          <text-input v-model="form.last_name" :error="form.errors.last_name" class="mt-10" label="Last Name" type="text" autocapitalize="off" />
          <text-input v-model="form.email" :error="form.errors.email" class="mt-10" label="Email" type="email" autocapitalize="off" />
          <text-input v-model="form.password" :error="form.errors.password" class="mt-6" label="Password" type="password" />
          <label class="flex items-center mt-6 select-none" for="remember">
            <input id="remember" v-model="form.remember" class="mr-1" type="checkbox" />
            <span class="text-sm">Remember Me</span>
          </label>
        </div>
        <div class="flex px-10 py-4 bg-gray-100 border-t border-gray-100">
          <loading-button :loading="form.processing" class="btn-indigo ml-auto" type="submit">Register</loading-button>
        </div>
        <div class="px-10 pt-2 pb-8 bg-gray-100 border-t border-gray-100">
          <h6 class="text-center font-bold">Already have an account? Login <Link class="underline text-indigo-600" href="/login">here</Link></h6>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Logo from '@/Shared/Logo'
import TextInput from '@/Shared/TextInput'
import LoadingButton from '@/Shared/LoadingButton'

export default {
  components: {
    Head,
    Link,
    LoadingButton,
    Logo,
    TextInput,
  },
  data() {
    return {
      form: this.$inertia.form({
        first_name: '',
        last_name: '',
        email: '',
        password: '',
        remember: false,
      }),
    }
  },
  methods: {
    login() {
      this.form.post('/register')
    },
  },
}
</script>
