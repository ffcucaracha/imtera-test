<script setup lang="ts">
import { ReviewType } from '@/pages/Reviews.vue';
import StarRating from './StarRating.vue';
import YIcon from './YIcon.vue';

const props = defineProps<ReviewType>();

</script>

<template>
  <div class="border border-gray-200 shadow-lg rounded-xl p-4 pr-0 ">
    <div class=" bottom-0 w-full bg-gray-100 rounded-lg p-4 flex flex-col justify-between">
      
      <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-3">
          <span class="text-sm font-semibold">{{ props.published_at }}</span>
          <span class="text-sm font-semibold">{{ props.title }}</span>
          <span><YIcon /></span> 
        </div>
        
        <StarRating :rating="props.rating" />
      </div>

      <div class="flex items-center gap-3 mb-3">
        <span class="font-bold">{{ props.author }}</span>
        <span class="text-sm font-semibold">{{ props.phone }}</span>
      </div>

      <div class="leading-relaxed overflow-hidden">
        {{ props.text }}
      </div>
    </div>
  </div>
</template>
