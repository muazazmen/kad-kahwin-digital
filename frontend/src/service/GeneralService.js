import { ENDPOINTS } from "@/constants/api.constant";
import { METHOD } from "@/constants/method.constant";
import { apiService } from "@/utils/api.util";

export const getUsers = async (params = {}) => {
    return await apiService(ENDPOINTS.ADMIN_USERS, METHOD.GET, null);
};
