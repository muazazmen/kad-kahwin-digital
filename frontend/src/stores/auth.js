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
      if (this.user) return;

      const accessToken = localStorage.getItem('accessToken');
      if (!accessToken) return;

      try {
          const res = await fetch('/api/v1/me', {
              headers: {
                  Authorization: `Bearer ${accessToken}`
              }
          });

          if (!res.ok) {
              // Token is invalid or expired
              console.warn('Token expired or invalid, logging out...');
              localStorage.removeItem('accessToken');
              this.user = null;
              return;
          }

          const data = await res.json();
          this.user = data;
      } catch (error) {
          console.error('Error fetching user:', error);
          // localStorage.removeItem('accessToken');
          // this.user = null;
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
          localStorage.setItem('accessToken', data.accessToken);
          this.user = data.user;
          this.message = data.message;
        return;
      } else {
        this.errors = data.errors;
        this.message = data.message;
      }
    },
    /******************** Forgot/Reset Password API **********************/
    async handlePassword(apiRoute, formData) {
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
        this.message = data.message;
      } else {
        this.errors = data.errors;
        this.message = data.message;
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