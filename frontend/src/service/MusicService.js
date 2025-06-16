import { ENDPOINTS } from '@/constants/api.constant';
import { METHOD } from '@/constants/method.constant';
import { apiService } from '@/utils/api.util';

export const getMusics = async (page = 1, perPage = 10) => {
    const params = {
        page: page,
        per_page: perPage
    };
    return await apiService(ENDPOINTS.ADMIN_MUSICS, METHOD.GET, null, {}, params);
};

export const getMusicById = async (musicId) => {
    return await apiService(ENDPOINTS.ADMIN_MUSIC(musicId), METHOD.GET);
}

export const addMusic = async (data) => {
    return await apiService(ENDPOINTS.ADMIN_MUSICS, METHOD.POST, data, { 'Content-Type': 'multipart/form-data' });
}

export const updateMusic = async (musicId, data) => {
    return await apiService(ENDPOINTS.ADMIN_MUSIC(musicId), METHOD.PUT, data, {
        'Content-Type': 'multipart/form-data',
    });
}

export const deleteMusic = async (musicId) => {
    return await apiService(ENDPOINTS.ADMIN_MUSIC(musicId), METHOD.DELETE);
}

export const restoreMusic = async (musicId) => {
    return await apiService(ENDPOINTS.ADMIN_MUSIC_RESTORE(musicId), METHOD.PUT);
}