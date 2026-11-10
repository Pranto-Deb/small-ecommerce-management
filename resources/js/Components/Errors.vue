<template>
  <div v-if="Object.keys(errors||{}).length" class="errors">
    <div v-for="(error,key) in errors" :key="key">{{ error }}</div>
  </div>
</template>

<script setup>defineProps({errors: Object});</script>

