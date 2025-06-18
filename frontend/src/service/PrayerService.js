import { ENDPOINTS } from '@/constants/api.constant';
import { METHOD } from '@/constants/method.constant';
import { apiService } from '@/utils/api.util';

export const getPrayers = async (page = 1, perPage = 10) => {
    const params = {
        page: page,
        per_page: perPage
    };
    return await apiService(ENDPOINTS.ADMIN_PRAYERS, METHOD.GET, null, {}, params);
};

export const getPrayerById = async (prayerId) => {
    return await apiService(ENDPOINTS.ADMIN_PRAYER(prayerId), METHOD.GET);
}

export const addPrayer = async (data) => {
    return await apiService(ENDPOINTS.ADMIN_PRAYERS, METHOD.POST, data);
}

export const updatePrayer = async (prayerId, data) => {
    return await apiService(ENDPOINTS.ADMIN_PRAYER(prayerId), METHOD.PUT, data);
}

export const deletePrayer = async (prayerId) => {
    return await apiService(ENDPOINTS.ADMIN_PRAYER(prayerId), METHOD.DELETE);
}

export const restorePrayer = async (prayerId) => {
    return await apiService(ENDPOINTS.ADMIN_PRAYER_RESTORE(prayerId), METHOD.PUT);
}