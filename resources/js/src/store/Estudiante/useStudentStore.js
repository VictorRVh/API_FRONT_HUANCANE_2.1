import { ref } from 'vue';
import { defineStore } from 'pinia';
// useEspecialidadStore
import useHttpRequest from '../../composables/useHttpRequest';

const useStudentsStore = defineStore('students', () => {
    const {
        //index: getStudents,
        show: getStudents,
        loading: studentsLoading,
        initialLoading: studentsFirstTimeLoading,
    } = useHttpRequest('/users-by-role');

    const student = ref(null);
    const students = ref([]);
    const teachers = ref([]);

    const setStudent = (authStudent) => {
        student.value = authStudent;
    };

    const loadStudents = async () => {
        const response = await getStudents(8);
        students.value = response;
    };

    const loadTeacher = async () => {
        const response = await getStudents(7);
        teachers.value = response;
    };

    return {
        student,
        setStudent,
        students,
        teachers,
        studentsLoading,
        studentsFirstTimeLoading,
        loadStudents,
        loadTeacher,
    };
});

export default useStudentsStore;
