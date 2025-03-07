<template>
    <nav class="text-sm py-2">
      <ul class="flex space-x-2">
        <li v-for="(breadcrumb, index) in breadcrumbs" :key="index" class="flex items-center">
          <router-link 
            v-if="index !== breadcrumbs.length - 1" 
            :to="breadcrumb.path"
            class="text-blue-500 hover:underline"
          >
            {{ breadcrumb.text }} >
          </router-link>
          <span v-else class="text-gray-700 font-semibold"> / {{ breadcrumb.text }}</span>
        </li>
      </ul>
    </nav>
  </template>
  
  <script setup>
  import { useBreadcrumb } from '../../composables/useBreadcrumb';
  const { breadcrumbs } = useBreadcrumb();

  </script>
  