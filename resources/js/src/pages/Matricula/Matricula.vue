<script setup>
import { ref, onMounted, watch } from "vue";
import { useRouter } from "vue-router";
import Table from "../../components/table/Table.vue";
import THead from "../../components/table/THead.vue";
import TBody from "../../components/table/TBody.vue";
import Tr from "../../components/table/Tr.vue";
import Th from "../../components/table/Th.vue";
import Td from "../../components/table/Td.vue";
import CreateButton from "../../components/ui/CreateButton.vue";
import EditButton from "../../components/ui/EditButton.vue";
import DeleteButton from "../../components/ui/DeleteButton.vue";
import ViewButton from "../../components/ui/ViewButton.vue";
import AuthorizationFallback from "../../components/page/AuthorizationFallback.vue";
import EnrollmentSlider from "../../components/page/Matricula/MatriculaSlider.vue";

import useEnrollmentStudentsStore from "../../store/Matricula/useMatriculaStore";
import useSpecialtyStore from "../../store/Especialidad/useEspecialidadStore";
import usePlanStore from "../../store/Especialidad/usePlanFormativoStore";
import useSlider from "../../composables/useSlider";
import useModalToast from "../../composables/useModalToast";
import useHttpRequest from "../../composables/useHttpRequest";
import useAuth from "../../composables/useAuth";

import useGroupsStore from "../../store/Grupo/useGrupoStore";
import { generatePdfMatricula } from "../../components/pdf/generatePdf";

const router = useRouter();
const enrollmentStore = useEnrollmentStudentsStore();
const specialtiesStore = useSpecialtyStore();
const planStore = usePlanStore();
const groupStore = useGroupsStore();

const { slider, sliderData, showSlider, hideSlider } = useSlider("enrollment-crud");
const { showConfirmModal, showToast } = useModalToast();
const { destroy: deleteEnrollment, deleting } = useHttpRequest("/matricula");
const { isUserAuthenticated } = useAuth();

const selectedPlan = ref(0);
const selectedSpecialty = ref(0);
const selectedGroup = ref(0);

onMounted(async () => {
  if (!specialtiesStore.specialties?.length) await specialtiesStore.loadSpecialties();
  if (!planStore.plans?.length) await planStore.loadPlans();

  selectedPlan.value = planStore.plans[0]?.id_plan || 0;
  selectedSpecialty.value = specialtiesStore.specialties[0]?.id_especialidad || 0;
  selectedGroup.value = groupStore.groups[0]?.id_grupo || 0;

  await loadEnrollments();
});

const loadEnrollments = async () => {
  await enrollmentStore.loadEnrollmentBySpecialtiesAndGroup(
    selectedPlan.value,
    selectedSpecialty.value,
    selectedGroup.value
  );
};

const onDelete = async (enrollment) => {
  if (deleting.value) return;

  showConfirmModal(null, async (confirmed) => {
    if (!confirmed) return;

    const isDeleted = await deleteEnrollment(enrollment?.id_Enrollment);
    if (isDeleted) {
      showToast(`Enrollment "${enrollment?.nombre_Enrollment}" deleted successfully.`);
      await loadEnrollments();
      isUserAuthenticated();
    }
  });
};

const SeeMore = (id) => {
  router.push({ name: "UnidadDidactica", params: { idPrograma: id } });
};

const fichaMatricula = async (idGrupo) => {

  console.log('ID DE GRUPO', idGrupo)

  try {
    const response = await fetch(`/api/fichaMatricula/${idGrupo}`);

    if (!response.ok) {
      throw new Error('Error al obtener los datos del grupo');
    }

    const data = await response.json();

    generatePdfMatricula(data);

    console.log(data)
  } catch (error) {
    console.error('Error en la consulta de matrícula:', error);
  }
}

const onPlanOrSpecialtyChange = () => {
  loadEnrollments();
};

watch([selectedPlan, selectedSpecialty], ([newPlan, newSpecialty]) => {
  groupStore?.loadGroups(newPlan, newSpecialty);
});
</script>

<template>
  <AuthorizationFallback :permissions="['enrollmentStudent-all', 'enrollmentStudent-view']">
    <div class="w-full space-y-4 py-6">
      <div class="flex justify-between">
        <h2 class="text-black font-bold text-2xl dark:text-white">Matricula</h2>
        <CreateButton @click="showSlider(true)" />
      </div>

      <div class="flex justify-between">
        <select v-model="selectedPlan" @change="onPlanOrSpecialtyChange" class="border rounded-md p-2">
          <option disabled value="0">Select a plan</option>
          <option v-for="plan in planStore.plans" :key="plan.id_plan" :value="plan.id_plan">
            {{ plan.nombre_plan }}
          </option>
        </select>

        <select v-model="selectedGroup" @change="onPlanOrSpecialtyChange" class="border rounded-md p-2">
          <option disabled value="0">Select a group</option>
          <option v-for="grupo in groupStore.groups" :key="grupo.id_grupo" :value="grupo.id_grupo">
            {{ grupo.nombre_grupo }}
          </option>
        </select>

        <select v-model="selectedSpecialty" @change="onPlanOrSpecialtyChange" class="border rounded-md p-2">
          <option disabled value="0">Select a specialty</option>
          <option v-for="specialty in specialtiesStore.specialties" :key="specialty.id_especialidad"
            :value="specialty.id_especialidad">
            {{ specialty.nombre_especialidad }}
          </option>
        </select>
      </div>

      <div class="w-full">
        <Table class="border-collapse divide-y divide-transparent">
          <THead>
            <Tr>
              <Th>Id</Th>
              <Th>Nombre</Th>
              <Th>Apellidos</Th>
              <Th>DNI</Th>
              <Th>Grupo</Th>
              <Th>Action</Th>
            </Tr>
          </THead>

          <TBody>
            <Tr v-for="enrollment in enrollmentStore.EnrollmentGroup" :key="enrollment.id_Enrollment">
              <Td class="py-2 px-4 border-0 text-black">{{ enrollment?.id_matricula }}</Td>
              <Td class="py-2 px-4 border-0 text-black">{{ enrollment.estudiante?.name }}</Td>
              <Td class="py-2 px-4 border-0 text-black">
                {{ enrollment.estudiante?.apellido_paterno }} {{ enrollment.estudiante?.apellido_materno }}
              </Td>
              <Td class="py-2 px-4 border-0 text-black">{{ enrollment.estudiante?.dni }}</Td>
              <Td class="py-2 px-4 border-0 text-black">{{ enrollment.grupos?.nombre_grupo }}</Td>
              <Td class="py-2 px-4 border-0">
                <div class="flex gap-2 justify-center items-center">
                  <ViewButton @click="SeeMore(enrollment.id_matricula)" />
                  <EditButton @click="showSlider(true, enrollment)" />
                  <DeleteButton @click="onDelete(enrollment)" />
                  <button @click="fichaMatricula(enrollment.id_estudiante)">PDF</button>
                </div>
              </Td>
            </Tr>
          </TBody>
        </Table>
      </div>
    </div>

    <EnrollmentSlider :specialtyId="specialtiesStore.specialties" :planId="planStore.plans" :show="slider"
      :Enrollment="sliderData" :searchId="[selectedPlan, selectedSpecialty]" @hide="hideSlider" />
  </AuthorizationFallback>
</template>

<style scoped>
/* No se requiere CSS adicional, todo está gestionado con Tailwind */
</style>
