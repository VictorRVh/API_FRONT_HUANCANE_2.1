import { ref } from 'vue';
import { defineStore } from 'pinia';

import useHttpRequest from '../composables/useHttpRequest';

const useRoleStore = defineStore('roles', () => {
    const {
        index: getRoles,
        loading: rolesLoading,
        initialLoading: rolesFirstTimeLoading,
    } = useHttpRequest('/roles');

    const roles = ref([]);
    const role = ref(null);

    const loadRoles = async () => {
        const res = await getRoles();
        roles.value = res;
    };

    const setRole = (authRole) => {
          role.value = authRole;
    };

    return {
        role,
        roles,
        loadRoles,
        setRole,
        rolesLoading,
        rolesFirstTimeLoading,
    };
});

export default useRoleStore;
