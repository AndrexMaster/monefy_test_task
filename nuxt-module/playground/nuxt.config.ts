export default defineNuxtConfig({
  css: ['~/assets/css/tailwind.css'],
  devtools: { enabled: true },
  compatibilityDate: 'latest',
  myModule: {},
  modules: [
    '../src/module',
    '@nuxtjs/tailwindcss'
  ],
  runtimeConfig: {
    public: {
      apiBase: 'http://localhost:8000/api'
    }
  },
  vite: {
    server: {
      proxy: {
        '/api': {
          target: 'http://laravel-api:8000',
          changeOrigin: true
        }
      }
    }
  }
})
