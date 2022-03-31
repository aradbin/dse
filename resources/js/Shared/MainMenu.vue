<template>
  <div>
    <div class="mr-5">
      <Link class="group flex items-center" href="/organizations">
        <icon name="office" class="mr-2 w-4 h-4" :class="isUrl('organizations') ? 'fill-indigo-600' : 'fill-gray-700 group-hover:fill-indigo-600'" />
        <div class="font-semibold" :class="isUrl('organizations') ? 'text-indigo-600' : 'text-gray-700 group-hover:text-indigo-600 focus:text-indigo-600'">Organizations</div>
      </Link>
    </div>
    <div class="mr-5">
      <Link class="group flex items-center" href="/users/watchlist">
        <icon name="dashboard" class="mr-2 w-4 h-4" :class="isUrl('users/watchlist') ? 'fill-indigo-600' : 'fill-gray-700 group-hover:fill-indigo-600'" />
        <div class="font-semibold" :class="isUrl('users/watchlist') ? 'text-indigo-600' : 'text-gray-700 group-hover:text-indigo-600 focus:text-indigo-600'">My Watchlist</div>
      </Link>
    </div>
  </div>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3'
import Icon from '@/Shared/Icon'

export default {
  components: {
    Icon,
    Link,
  },
  methods: {
    isUrl(...urls) {
      let currentUrl = this.$page.url.substr(1)
      if (urls[0] === '') {
        return currentUrl === ''
      }
      return urls.filter((url) => currentUrl.startsWith(url)).length
    },
  },
}
</script>
