import { ENDPOINTS } from "@/constants/api.constant";
import { METHOD } from "@/constants/method.constant";
import { apiService } from "@/utils/api.util";

export const getUsers = async (page = 1, perPage = 10) => {
    const params = {
        page: page,
        per_page: perPage
    };
    return await apiService(ENDPOINTS.ADMIN_USERS, METHOD.GET, null, {}, params);
};

export const addUser = async (data) => {
    return await apiService(ENDPOINTS.ADMIN_USERS, METHOD.POST, data);
}

export const updateUser = async (userId, data) => {
    return await apiService(ENDPOINTS.ADMIN_USER(userId), METHOD.PUT, data);
};

export const deleteUser = async (userId) => {
    return await apiService(ENDPOINTS.ADMIN_USER(userId), METHOD.DELETE);
}