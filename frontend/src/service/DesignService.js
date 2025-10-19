import { ENDPOINTS } from "@/constants/api.constant";
import { METHOD } from "@/constants/method.constant";
import { apiService } from "@/utils/api.util";

export const getDesigns = async () => { 
  return await apiService(ENDPOINTS.DESIGNS);
}

export const getDesignsWithTrashed = async (page = 1, perPage = 10) => {
  const params = {
    page: page,
    per_page: perPage
  };
  return await apiService(ENDPOINTS.ADMIN_DESIGNS, METHOD.GET, null, {}, params);
}

export const getDesignById = async (designId) => {
  return await apiService(ENDPOINTS.ADMIN_DESIGN(designId), METHOD.GET);
}

export const addDesign = async (data) => {
  return await apiService(ENDPOINTS.ADMIN_DESIGNS, METHOD.POST, data, { 'Content-Type': 'multipart/form-data' });
}

export const updateDesign = async (designId, data) => {
  return await apiService(ENDPOINTS.ADMIN_DESIGN(designId), METHOD.PUT, data, { 'Content-Type': 'multipart/form-data' });
}

export const deleteDesign = async (designId) => {
  return await apiService(ENDPOINTS.ADMIN_DESIGN(designId), METHOD.DELETE);
}

export const restoreDesign = async (designId) => {
  return await apiService(ENDPOINTS.ADMIN_DESIGN_RESTORE(designId), METHOD.PUT);
}