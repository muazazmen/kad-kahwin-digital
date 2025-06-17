import { ENDPOINTS } from '@/constants/api.constant';
import { METHOD } from '@/constants/method.constant';
import { apiService } from '@/utils/api.util';

export const getFonts = async (page = 1, perPage = 10) => {
    const params = {
        page: page,
        per_page: perPage
    }
    return await apiService(ENDPOINTS.SUPER_ADMIN_FONTS, METHOD.GET, null, {}, params);
};

export const getFontById = async (fontId) => {
    return await apiService(ENDPOINTS.SUPER_ADMIN_FONT(fontId), METHOD.GET);
}

export const addFont = async (data) => {
    return await apiService(ENDPOINTS.SUPER_ADMIN_FONTS, METHOD.POST, data, { 'Content-Type': 'multipart/form-data' });
}

export const updateFont = async (fontId, data) => {
    return await apiService(ENDPOINTS.SUPER_ADMIN_FONT(fontId), METHOD.POST, data, { 'Content-Type': 'multipart/form-data' });
}

export const deleteFont = async (fontId) => {
    return await apiService(ENDPOINTS.SUPER_ADMIN_FONT(fontId), METHOD.DELETE);
}

export const restoreFont = async (fontId) => {
    return await apiService(ENDPOINTS.SUPER_ADMIN_FONT_RESTORE(fontId), METHOD.PUT);
}