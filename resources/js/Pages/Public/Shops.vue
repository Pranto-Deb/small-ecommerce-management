<template>
  <Head title="Browse Shops" />

  <div class="directory-page">
    <header class="public-header directory-header">
      <Link href="/" class="brand directory-brand">Small <span>Commerce</span></Link>
      <span class="directory-header-label">Shop directory</span>
    </header>

    <main class="public-container directory-container">
      <section class="directory-intro" aria-labelledby="directory-title">
        <div class="directory-intro-copy">
          <span class="eyebrow directory-eyebrow">DISCOVER LOCAL STORES</span>
          <h1 id="directory-title">Explore our shops<span class="directory-period">.</span></h1>
          <p>Find products from independent shops, all in one place.</p>
        </div>
        <div class="directory-count" aria-label="Available shops">
          <strong>{{ shops.length }}</strong>
          <span>{{ shops.length === 1 ? 'shop available' : 'shops available' }}</span>
        </div>
      </section>

      <section class="directory-section" aria-labelledby="directory-list-title">
        <div class="directory-section-heading">
          <div>
            <span class="directory-section-kicker">OUR DIRECTORY</span>
            <h2 id="directory-list-title">Browse stores</h2>
          </div>
          <span class="directory-results">{{ shops.length }} {{ shops.length === 1 ? 'store' : 'stores' }}</span>
        </div>

        <div v-if="shops.length" class="shop-grid directory-grid">
          <Link
            v-for="shop in shops"
            :key="shop.id"
            :href="'/shops/' + shop.slug"
            class="shop-card directory-card"
          >
            <div class="directory-card-top">
              <div class="shop-icon directory-shop-icon" aria-hidden="true">
                {{ (shop.name || 'S').charAt(0).toUpperCase() }}
              </div>
              <span class="directory-card-type">SHOP</span>
            </div>
            <div class="directory-card-body">
              <h3>{{ shop.name }}</h3>
              <p>{{ shop.description || 'Visit the shop to browse its products.' }}</p>
            </div>
            <div class="card-bottom directory-card-footer">
              <span class="directory-product-count">
                {{ shop.listings_count }} {{ shop.listings_count === 1 ? 'product' : 'products' }}
              </span>
              <span class="directory-view-link">View shop <span aria-hidden="true">→</span></span>
            </div>
          </Link>
        </div>

        <div v-else class="empty-state directory-empty">
          <div class="directory-empty-icon" aria-hidden="true">S</div>
          <h3>No shops available yet</h3>
          <p>Check back later for new shops and their products.</p>
        </div>
      </section>
    </main>
  </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
  shops: {
    type: Array,
    default: () => [],
  },
});
</script>
