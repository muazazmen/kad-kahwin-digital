import { ENDPOINTS } from '@/constants/api.constant';
import { METHOD } from '@/constants/method.constant';
import { apiService } from '@/utils/api.util';

export const getFrames = async (page = 1, perPage = 10) => {
    const params = {
        page: page,
        per_page: perPage
    };
    return await apiService(ENDPOINTS.ADMIN_FRAMES, METHOD.GET, null, {}, params);
};

export const getFrameById = async (frameId) => {
  return await apiService(ENDPOINTS.ADMIN_FRAME(frameId), METHOD.GET);
}

export const addFrame = async (data) => {
  return await apiService(ENDPOINTS.ADMIN_FRAMES, METHOD.POST, data, { 'Content-Type': 'multipart/form-data' });
}

export const updateFrame = async (frameId, data) => {
  return await apiService(ENDPOINTS.ADMIN_FRAME(frameId), METHOD.POST, data, { 'Content-Type': 'multipart/form-data' });
}

export const deleteFrame = async (frameId) => {
  return await apiService(ENDPOINTS.ADMIN_FRAME(frameId), METHOD.DELETE);
}

export const restoreFrame = async (frameId) => {
  return await apiService(ENDPOINTS.ADMIN_FRAME_RESTORE(frameId), METHOD.PUT);
}