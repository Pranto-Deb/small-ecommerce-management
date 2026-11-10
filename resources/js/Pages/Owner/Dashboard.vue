<template>
  <Head title="Shop Overview"/>
  <Shell title="Shop Overview"><h1>{{ shop?.name || 'My Shop' }}</h1>
    <p v-if="!shop" class="notice">An administrator must assign a shop to your account before you can manage
      listings.</p>
    <p v-else class="muted">{{ shop.description || 'Your shop management dashboard' }}</p>
    <div class="stats">
      <div class="stat"><span>My listings</span><strong>{{ listingCount }}</strong></div>
      <div class="stat"><span>Shared products</span><strong>{{ catalogCount }}</strong></div>
    </div>
    <div class="panel"><h2>Actions</h2>
      <div class="actions">
        <Link class="btn secondary" href="/owner/catalog">Browse catalog</Link>
        <Link class="btn secondary" href="/owner/listings">Manage my products</Link>
        <Link v-if="shop" class="btn secondary" :href="'/shops/'+shop.slug">Public shop page</Link>
      </div>
    </div>
  </Shell>
</template>

<script setup>import Shell from '../../Components/Shell.vue';
import {Head, Link} from '@inertiajs/vue3';

defineProps({shop: Object, listingCount: Number, catalogCount: Number});
</script>
