<template>
  <Head title="Admin Overview"/>
  <Shell title="Admin Overview"><h1>Dashboard</h1>
    <p class="muted">Manage your shared catalog and registered shops.</p>
    <div class="stats">
      <div v-for="(value,label) in stats" :key="label" class="stat"><span>{{ label }}</span><strong>{{ value }}</strong>
      </div>
    </div>
    <div class="panel"><h2>Quick access</h2>
      <div class="actions">
        <Link class="btn secondary" href="/admin/shops">Manage shops</Link>
        <Link class="btn secondary" href="/admin/products">Manage catalog</Link>
        <Link class="btn secondary" href="/admin/owners">Manage owners</Link>
      </div>
    </div>
  </Shell>
</template>

<script setup>import Shell from '../../Components/Shell.vue';
import {Head, Link} from '@inertiajs/vue3';

defineProps({stats: Object});
</script>
