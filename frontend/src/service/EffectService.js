import { ENDPOINTS } from '@/constants/api.constant';
import { METHOD } from '@/constants/method.constant';
import { apiService } from '@/utils/api.util';

export const getEffects = async () => {
  return await apiService(ENDPOINTS.EFFECTS);
}

export const getEffectsWithTrashed = (page = 1, perPage = 10) => {
  const params = {
    page: page,
    per_page: perPage
  }

  return apiService(ENDPOINTS.SUPER_ADMIN_EFFECTS, METHOD.GET, null, {}, params);
}

export const getEffectById = (effectId) => {
  return apiService(ENDPOINTS.EFFECT(effectId), METHOD.GET);
}

export const addEffect = (data) => {
  return apiService(ENDPOINTS.SUPER_ADMIN_EFFECTS, METHOD.POST, data);
}

export const updateEffect = (effectId, data) => {
  return apiService(ENDPOINTS.SUPER_ADMIN_EFFECT(effectId), METHOD.PUT, data);
}

export const deleteEffect = (effectId) => {
  return apiService(ENDPOINTS.SUPER_ADMIN_EFFECT(effectId), METHOD.DELETE);
}

export const restoreEffect = (effectId) => {
  return apiService(ENDPOINTS.SUPER_ADMIN_EFFECT_RESTORE(effectId), METHOD.PUT);
}