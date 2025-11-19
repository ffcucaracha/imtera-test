<script setup>

defineProps({
  rating: {
    type: Number,
    required: true
  },
  scale: {
    type: Number,
    default: 2
  }
});
</script>

<template>
  <div>
    <span
      v-for="i in 5"
      :key="i"
      v-bind:class="'text-yellow-400 text-'+scale+'xl'"
    >
      {{ i <= Math.round(rating) ? '★' : '☆' }}
    </span>
  </div>
</template>