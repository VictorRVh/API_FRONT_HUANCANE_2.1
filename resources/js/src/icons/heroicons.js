// src/plugins/heroicons.js
import { defineAsyncComponent } from 'vue';

// Importa los íconos en estilo outline
const icons = {
  HomeIcon: defineAsyncComponent(() => import('@heroicons/vue/24/outline/HomeIcon')),
  BeakerIcon: defineAsyncComponent(() => import('@heroicons/vue/24/outline/BeakerIcon')),
  UserIcon: defineAsyncComponent(() => import('@heroicons/vue/24/outline/UserIcon')),
  AcademicCapIcon: defineAsyncComponent(() => import('@heroicons/vue/24/outline/AcademicCapIcon')),
  UsersIcon: defineAsyncComponent(() => import('@heroicons/vue/24/outline/UsersIcon')),
  BookOpenIcon: defineAsyncComponent(() => import('@heroicons/vue/24/outline/BookOpenIcon')),
  BuildingOfficeIcon: defineAsyncComponent(() => import('@heroicons/vue/24/outline/BuildingOfficeIcon')),
  PresentationChartLineIcon: defineAsyncComponent(() => import('@heroicons/vue/24/outline/PresentationChartLineIcon')),
  ChartBarIcon: defineAsyncComponent(() => import('@heroicons/vue/24/outline/ChartBarIcon')),
  FolderIcon: defineAsyncComponent(() => import('@heroicons/vue/24/outline/FolderIcon')),
  BookmarkIcon: defineAsyncComponent(() => import('@heroicons/vue/24/outline/BookmarkIcon')),
  BookmarkSquareIcon: defineAsyncComponent(() => import('@heroicons/vue/24/outline/BookmarkSquareIcon')),
  EyeIcon: defineAsyncComponent(() => import('@heroicons/vue/24/outline/EyeIcon')),
  PencilSquareIcon: defineAsyncComponent(() => import('@heroicons/vue/24/outline/PencilSquareIcon')),
  TrashIcon: defineAsyncComponent(() => import('@heroicons/vue/24/outline/TrashIcon')),
  CalendarIcon: defineAsyncComponent( () => import('@heroicons/vue/24/outline/CalendarIcon')),
  // Agrega más íconos aquí según sea necesario
};

// Registra los íconos como componentes globales
export function registerHeroIcons(app) {
  for (const [name, icon] of Object.entries(icons)) {
    app.component(name, icon);
  }
}
