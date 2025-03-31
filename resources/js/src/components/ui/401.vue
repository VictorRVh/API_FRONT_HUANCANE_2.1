<script setup>
import useUtils from '../../composables/useUtils';

defineProps({
    permissions: {
        type: Array,
        default: () => [],
    },
});

const { stringCapitalize } = useUtils();
</script>

<template>
    <div class="w-full h-[calc(100vh-80px)] flex-center px-20">
        <div class="flex flex-col items-center">
            <div class="text-red-500 font-bold text-[36px]">
                401 No autorizado
            </div>
            <br>
            <br>
            <br>
            <br>
            <div class="text-[80px] text-center">&#128546;</div>
            <br>
            <br>
            <br>
            <br>
            <div class="text-xl font-bold text-center">
                Lo sentimos, no tienes uno de los siguientes permisos
            </div>
            <div class="mt-4 text-red-500 dark:text-red-200 text-center">
                {{
                    permissions
                        .map((permission) =>
                            stringCapitalize(permission?.replaceAll('-', ' ')),
                        )
                        ?.join(', ')
                }}
            </div>
        </div>
    </div>
</template>
