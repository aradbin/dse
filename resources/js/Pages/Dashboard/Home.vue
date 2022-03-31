<template>
  <div>
    <Head title="Home" />
    <img class="w-full" src="/images/quote-1.jpg" />
  </div>
</template>

<script>
import { Head } from '@inertiajs/inertia-vue3'
import Layout from '@/Shared/Layout'
export default {
  components: {
    Head,
  },
  layout: Layout,
}
</script>