import { defineStore } from "pinia";

export const useAuthStore = defineStore('authStore', {
  state: () => {
    return {
      user: null,
      errors: {},
      message: {}
    }
  },
  actions: {
    /**************** get authenticated user API ******************/
    async fetchUser() {
      const accessToken = localStorage.getItem('accessToken')
      
      if (accessToken) {
        const res = await fetch('/api/v1/me', {
          headers: {
            Authorization: `Bearer ${accessToken}`
          }
        });
        const data = await res.json()
        if (res.ok) {
          this.user = data
        }
        console.log('ss', data);
      }
    },
    /******************** Login/Register API **********************/
    async authenticate(apiRoute, formData) {
      const res = await fetch(`/api/v1/auth/${apiRoute}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(formData)
      })
      
      const data = await res.json()
      if (!data.errors) {
        if (apiRoute === 'login') {
          localStorage.setItem('accessToken', data.accessToken);
          this.user = data.user;
          this.message = data.message;
          this.router.push({ name: 'dashboard' });
        } else {
          this.router.push({ name: 'landing' });
        }
      } else {
        this.message = data.message
        this.errors = data.errors;
        return;
      }
    }
  }
})