<script setup>
defineProps({
  show: {
    type: Boolean,
    default: () => false,
  },
  title: {
    type: String,
  },
  sliderClass: {
    type: String,
    default: () => "w-full max-w-[90%] md:w-[1000px]",
  },
  transitionClass: {
    type: String,
    default: () => "right-[-520px]",
  },
  transitionActiveClass: {
    type: String,
    default: () => "transition-all duration-300",
  },
});
const emit = defineEmits(["hide"]);
</script>

<template>
  <!-- Fondo oscuro ofuscado -->
  <Teleport to="body">
    <div
      v-if="show"
      class="fixed inset-0 z-[308] bg-black/80 backdrop-blur-sm"
      @click="emit('hide', false)"
    ></div>
  </Teleport>

  <!-- Slider centrado en pantalla -->
  <Teleport to="body">
    <Transition
      :enter-from-class="transitionClass"
      :leave-to-class="transitionClass"
      :enter-active-class="transitionActiveClass"
      :leave-active-class="transitionActiveClass"
    >
      <div
        v-if="show"
        class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-[309] bg-white dark:bg-gray-800 border border-gray-400 dark:border-gray-900 shadow-lg rounded-2xl max-h-[90vh] overflow-hidden flex flex-col"
        :class="[sliderClass]"
      >
        <!-- Encabezado -->
        <div class="p-4">
          <div class="flex justify-between items-center w-full mb-2">
            <div class="w-full">
              <slot name="title">
                <div class="text-gray-900 text-xl dark:text-white font-medium  text-center">
                  {{ title ? title : "Create or update data" }}
                </div>
             
              </slot>
            </div>
            <div class="flex items-center ml-4">
              <div class="mr-4">
                <slot name="before_close"></slot>
              </div>
              <slot name="close">
                <span class="text-xl cursor-pointer" @click="emit('hide', false)">
                  <svg
                    viewBox="0 0 24 24"
                    width="24"
                    height="24"
                    stroke="currentColor"
                    stroke-width="2"
                    fill="none"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="text-gray-700 dark:text-white"
                  >
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                  </svg>
                </span>
              </slot>
            </div>
          </div>
        </div>

        <!-- Contenido scrollable -->
        <div class="flex-grow px-4 pb-4 overflow-y-auto overflow-x-hidden custom-scrollbar max-h-[calc(90vh-4rem)]">
          <slot></slot>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped>
.custom-scrollbar {
  overflow-x: hidden;
}
.custom-scrollbar::-webkit-scrollbar {
  width: 8px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: #a0a0a0;
  border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background-color: #8a8a8a;
}
</style>
