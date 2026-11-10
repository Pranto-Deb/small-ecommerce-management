
<template>
  <div class="overlay" @click.self="$emit('close')">
    <div class="dialog">
      <div class="dialog-head"><h2>{{ title }}</h2>
        <button type="button" class="icon-button" @click="$emit('close')">×</button>
      </div>
      <slot/>
    </div>
  </div>
</template>

<script setup>defineProps({title: String});
defineEmits(['close']);</script>
