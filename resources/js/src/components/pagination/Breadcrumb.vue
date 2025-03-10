<template>
  <nav class="text-sm py-2">
    <ul class="flex space-x-2">
      <li v-for="(breadcrumb, index) in breadcrumbs" :key="index" class="flex items-center">
        <RouterLink 
          v-if="breadcrumb.path" 
          :to="breadcrumb.path"
          class="text-blue-500 hover:underline"
        >
          {{ breadcrumb.text }}
        </RouterLink>
        <span v-else class="text-gray-700 font-semibold"> {{ breadcrumb.text }}</span>
        <span v-if="index < breadcrumbs.length - 1" class="mx-2 text-gray-400">/</span>
      </li>
    </ul>
  </nav>
</template>

<script setup>
import { useBreadcrumb } from '../../composables/useBreadcrumb';
const { breadcrumbs } = useBreadcrumb();
</script>

