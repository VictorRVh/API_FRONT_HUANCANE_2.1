import useHttpRequest from './useHttpRequest';
import useUserStore from '../store/useUserStore';
import useRoleStore from "../store/useRoleStore"

const useAuth = () => {
    const { index: verify } = useHttpRequest('/auth/verify');
    const userStore = useUserStore();
    const userRole = useRoleStore();

    const isUserAuthenticated = async () => {
        const user = await verify();
        if (!Array.isArray(user) && user?.id) {
            userStore.setUser(user);
            userRole.setRole(user.roles);
            
            return true;
        }
        return false;
    };

    return {
        isUserAuthenticated,
    };
};

export default useAuth;
