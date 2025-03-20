<template>
    <div class="relative">
      <button
        @click="toggleDropdown"
        type="button"
        class="p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition"
        title="Filtrar"
      >
        <svg xmlns="http://www.w3.org/2000/svg"
             fill="none"
             viewBox="0 0 24 24"
             stroke-width="1.5"
             stroke="currentColor"
             class="w-6 h-6 text-gray-600 dark:text-gray-300">
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 0 1-.659 1.591l-5.432 5.432a2.25 2.25 0 0 0-.659 1.591v2.927a2.25 2.25 0 0 1-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 0 0-.659-1.591L3.659 7.409A2.25 2.25 0 0 1 3 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0 1 12 3Z" />
        </svg>
      </button>
  
      <div v-if="dropdownOpen"
           class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded shadow-md z-50 dark:bg-gray-800 dark:border-gray-700">
        <ul class="text-sm text-gray-700 dark:text-gray-200">
          <li><button @click="emitFilter('name-asc')" class="block w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">Nombre A-Z</button></li>
          <li><button @click="emitFilter('name-desc')" class="block w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">Nombre Z-A</button></li>
        </ul>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  
  const dropdownOpen = ref(false);
  const emit = defineEmits(['filter']);
  
  const toggleDropdown = () => {
    dropdownOpen.value = !dropdownOpen.value;
  };
  
  const emitFilter = (option) => {
    emit('filter', option);
    dropdownOpen.value = false;
  };
  </script>
  