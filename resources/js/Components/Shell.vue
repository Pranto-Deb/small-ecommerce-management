<template>
  <div class="shell">
    <aside class="sidebar"><Link href="/" class="brand">Small Commerce</Link><div class="side-caption">MANAGEMENT</div>
      <template v-if="user?.role==='admin'"><Link href="/admin">Overview</Link><Link href="/admin/shops">Shops</Link><Link href="/admin/products">Product Catalog</Link><Link href="/admin/owners">Shop Owners</Link></template>
      <template v-if="user?.role==='owner'"><Link href="/owner">Overview</Link><Link href="/owner/catalog">Shared Catalog</Link><Link href="/owner/listings">My Products</Link></template>
      <div class="side-caption">PUBLIC</div><Link href="/">Browse Shops</Link><button v-if="user" class="logout" @click="logout">Sign out</button><Link v-else href="/login">Sign in</Link>
    </aside><div class="main"><header class="topbar"><span>{{ title }}</span><span class="muted">{{ user?.name ?? 'Guest' }}</span></header><main class="content"><div v-if="flash" class="success">{{ flash }}</div><slot /></main></div>
  </div>
</template>

<script setup>
import { Link, usePage, router } from '@inertiajs/vue3';
import { computed } from 'vue';
const props = defineProps({ title: String });
const page=usePage();
const user=computed(()=>page.props.auth?.user);
const flash=computed(()=>page.props.flash?.success);
function logout(){router.post('/logout');}
</script>
