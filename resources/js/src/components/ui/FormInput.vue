<script setup>
import { computed, ref } from "vue";
import FormLabelError from "./FormLabelError.vue";
import { v4 } from "uuid";

const props = defineProps({
  modelValue: { required: false },
  type: { type: String, default: () => "text" },
  label: { type: String },
  labelClass: { type: String, default: () => "" },
  placeholder: { type: String },
  disabled: { type: Boolean, default: () => false },
  inputClass: { type: String, default: () => "" },
  error: { type: [String, null], default: () => null },
  errorClass: { type: String, default: () => "" },
  focus: { type: Boolean, default: () => false },
  required: { type: Boolean, default: () => false },
  step: { type: [String, Number, null], default: () => null },
  vModelOnBlur: { type: Boolean, default: () => false },
});

const emit = defineEmits(["update:modelValue", "focus", "blur"]);

const id = ref(`input-${v4()}`);
const isPasswordVisible = ref(false);

// 👇 Centramos la lógica del tipo de input
const inputType = computed(() =>
  props.type === "password" ? (isPasswordVisible.value ? "text" : "password") : props.type
);

// 👇 Lógica final para decidir si aplicar mayúscula visual
const applyUppercase = computed(() => {
  return !["password", "email"].includes(props.type);
});

const value = computed({
  get: () => props.modelValue,
  set: (val) => {
    let processedVal = val;
    if (typeof val === "string" && applyUppercase.value) {
      processedVal = val.toUpperCase();
    }
    if (!props.vModelOnBlur) emit("update:modelValue", processedVal);
  },
});

const onKeydown = (event) => {
  if (props.type !== "number") return;
  disableKeys(event, ["e", "E"]);
};

const onFocus = (event) => {
  emit("focus", event?.target?.value || null, event);
};

const onBlur = (event) => {
  const val = event?.target?.value || null;
  emit("blur", val, event);
  if (props.vModelOnBlur) {
    let processedVal = val;
    if (typeof val === "string" && applyUppercase.value) {
      processedVal = val.toUpperCase();
    }
    emit("update:modelValue", processedVal);
  }
};

const disableKeys = (event, keys = ["e", "E", "+", "-"]) => {
  if (!keys) return;
  keys.includes(event.key) && event.preventDefault();
};
</script>

<template>
  <FormLabelError
    :label="label"
    :label-class="`${labelClass} text-gray-500 text-sm`"
    :error="error"
    error-class="errorClass"
    :required="required"
  >
    <div class="relative w-full">
      <input
        v-model="value"
        v-focus.select="focus"
        :id="id"
        :type="inputType"
        :step="step ? step : type === 'number' ? 'any' : null"
        :placeholder="placeholder || ''"
        :class="[
          'border-b border-gray-300 bg-transparent focus:border-b-2 focus:border-active focus:ring-0 text-sm w-full py-1 px-2  outline-none transition-all duration-200 ease-in-out text-gray-700 tracking-wide dark:text-gray-300 dark:border-gray-500 dark:focus:border-b-2 dark:focus:border-active dark:bg-transparent',
          applyUppercase ? 'uppercase' : '',
          disabled && 'cursor-not-allowed',
          inputClass
        ]"
        :disabled="disabled"
        @keydown="onKeydown"
        @focus="onFocus"
        @blur="onBlur"
      />

      <!-- Botón ver/ocultar contraseña -->
      <button
        v-if="props.type === 'password'"
        type="button"
        class="absolute right-2 top-[35%] -translate-y-1/2 text-gray-500 hover:text-gray-700 dark:hover:text-white focus:outline-none"
        @click="isPasswordVisible = !isPasswordVisible"
        tabindex="-1"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          class="w-5 h-5"
          stroke="currentColor"
          stroke-width="1.5"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"
          />
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"
          />
        </svg>
      </button>
    </div>
  </FormLabelError>
</template>
