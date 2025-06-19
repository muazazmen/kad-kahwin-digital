import { ENDPOINTS } from '@/constants/api.constant';
import { METHOD } from '@/constants/method.constant';
import { apiService } from '@/utils/api.util';

export const getAnimations = (page = 1, perPage = 10) => {
  const params = {
    page: page,
    per_page: perPage
  }

  return apiService(ENDPOINTS.SUPER_ADMIN_ANIMATIONS, METHOD.GET, null, {}, params);
};

export const getAnimationById = (animationId) => {
  return apiService(ENDPOINTS.SUPER_ADMIN_ANIMATION(animationId), METHOD.GET)
};

export const addAnimation = (data) => {
  return apiService(ENDPOINTS.SUPER_ADMIN_ANIMATIONS, METHOD.POST, data)
};

export const updateAnimation = (animationId, data) => {
  return apiService(ENDPOINTS.SUPER_ADMIN_ANIMATION(animationId), METHOD.PUT, data)
};

export const deleteAnimation = (animationId) => apiService(ENDPOINTS.SUPER_ADMIN_ANIMATION(animationId), METHOD.DELETE);

export const restoreAnimation = (animationId) => apiService(ENDPOINTS.SUPER_ADMIN_ANIMATION_RESTORE(animationId), METHOD.PUT);