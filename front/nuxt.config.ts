// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  modules: [
    "@nuxtjs/tailwindcss",
    "@pinia/nuxt",
    "@nuxtjs/eslint-module",
    "@vueuse/nuxt",
    "nuxt-icon",
  ],
  css: [
    "@/assets/css/style.css",
  ],
});
