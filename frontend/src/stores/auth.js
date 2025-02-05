import { defineStore } from "pinia";

export const useAuthStore = defineStore('authStore', {
  state: () => {
    return {
      user: null,
      errors: {},
      message: {},
      response: {}
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
      
      this.response = res
      const data = await res.json()

      if (!data.errors) {
        if (apiRoute === 'login') {
          localStorage.setItem('accessToken', data.accessToken);
          this.user = data.user;
          this.message = data.message;
        }
      } else {
        this.message = data.message
        this.errors = data.errors;
        return;
      }
    },
    /********************* Logout API ***************************/
    async logout() {
      const res = await fetch('/api/v1/auth/logout', {
        method: 'POST',
        headers: {
          Authorization: `Bearer ${localStorage.getItem('accessToken')}`
        }
      });

      const data = await res.json()

      if (res.ok) {
        localStorage.removeItem('accessToken');
        this.user = null;
        this.message = data.message;
      }
    }
  }
})