import { ENDPOINTS } from '@/constants/api.constant';
import { METHOD } from '@/constants/method.constant';
import { apiService } from '@/utils/api.util';

export const getOpeningsWithTrashed = (page = 1, perPage = 10) => {
  const params = {
    page: page,
    per_page: perPage
  }

  return apiService(ENDPOINTS.SUPER_ADMIN_OPENING_ANIMATIONS, METHOD.GET, null, {}, params);
};

export const getOpeningById = (openingId) => {
  return apiService(ENDPOINTS.SUPER_ADMIN_OPENING_ANIMATION(openingId), METHOD.GET)
};

export const addOpening = (data) => {
  return apiService(ENDPOINTS.SUPER_ADMIN_OPENING_ANIMATIONS, METHOD.POST, data)
};

export const updateOpening = (openingId, data) => {
  return apiService(ENDPOINTS.SUPER_ADMIN_OPENING_ANIMATION(openingId), METHOD.PUT, data)
};

export const deleteOpening = (openingId) => apiService(ENDPOINTS.SUPER_ADMIN_OPENING_ANIMATION(openingId), METHOD.DELETE);

export const restoreOpening = (openingId) => apiService(ENDPOINTS.SUPER_ADMIN_OPENING_ANIMATION_RESTORE(openingId), METHOD.PUT);