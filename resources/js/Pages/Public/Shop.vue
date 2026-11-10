<template>
  <Head :title="shop.name"/>
  <div class="public-header">
    <Link href="/" class="brand">Small Commerce</Link>
  </div>
  <div class="public-container">
    <Link href="/" class="back-link">← All shops</Link>
    <div class="hero"><span class="eyebrow">SHOP</span>
      <h1>{{ shop.name }}</h1>
      <p>{{ shop.description || 'Browse products available at this shop.' }}</p></div>
    <div class="catalog-grid">
      <article v-for="listing in listings" :key="listing.id" class="catalog-card">
        <div class="product-photo"><img v-if="listing.thumbnail_url" :src="listing.thumbnail_url"
                                        :alt="listing.name"><span v-else>No image available</span></div>
        <div class="catalog-info"><h2>{{ listing.name }}</h2>
          <p class="muted">{{ listing.details || 'No description available.' }}</p>
          <strong>৳{{ Number(listing.price).toFixed(2) }}</strong></div>
      </article>
    </div>
    <div v-if="!listings.length" class="empty-state">No products listed in this shop yet.</div>
  </div>
</template>

<script setup>
import {Head, Link} from '@inertiajs/vue3';

defineProps({shop: Object, listings: Array});
</script>
