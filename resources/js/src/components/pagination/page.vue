<template>
    <nav aria-label="Navegación de páginas">
        <ul class="flex items-center -space-x-px h-8 text-sm">
            <!-- Botón de página anterior -->
            <PaginationItem
                icon="prev"
                :disabled="currentPage === 1"
                @click="goToPage(currentPage - 1)"
            />

            <!-- Botones de número de página -->
            <PaginationItem
                v-for="page in totalPages"
                :key="page"
                :page="page"
                :active="page === currentPage"
                @click="goToPage(page)"
            />

            <!-- Botón de página siguiente -->
            <PaginationItem
                icon="next"
                :disabled="currentPage === totalPages"
                @click="goToPage(currentPage + 1)"
            />
        </ul>
    </nav>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue';
import PaginationItem from './paginationItem.vue';

const props = defineProps({
    currentPage: {
        type: Number,
        required: true
    },
    totalPages: {
        type: Number,
        required: true
    }
});

const emit = defineEmits(['page-changed']);

const goToPage = (page) => {
    if (page >= 1 && page <= props.totalPages) {
        emit('page-changed', page);
    }
};
</script>
