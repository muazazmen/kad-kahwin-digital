import { defineStore } from "pinia";

export const useAuthStore = defineStore('authStore', {
  state: () => {
    return {
      user: null,
      errors: {}
    }
  },
  actions: {
    async authenticate(apiRoute, formData) {
      const res = await fetch(`/api/v1/auth/${apiRoute}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(formData)
      })
      
      const data = await res.json()
      if (data.errors) {
        this.errors = data.errors
        return
      } else {
        console.log(data);
      }
    }
  }
})