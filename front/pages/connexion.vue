<script setup lang="ts">
// eslint-disable-next-line import/no-extraneous-dependencies
import Cookies from "js-cookie";

import { LockClosedIcon } from "@heroicons/vue/20/solid";

useSeoMeta({
  title: "Connexion",
});
definePageMeta({
  layout: false,
});
const formData = ref({
  email: "",
  password: "",
});
const { loginUser } = useLogin();

Cookies.remove("token-session");
</script>

<template>
  <div class="flex items-center justify-center h-screen min-h-full px-4 py-12 bg-gray-500 sm:px-6 lg:px-8">
    <div class="w-full max-w-md p-3 space-y-8 bg-white shadow-lg rounded-xl">
      <div>
        <div class="flex items-center justify-center ">
          <img
            class="w-auto h-8"
            src="~/assets/img/icon-nexton.png"
            alt="Your Company"
          >
          <h2 class="text-xl font-bold">
            | NextFeed
          </h2>
        </div>
        <h2 class="mt-6 text-3xl font-bold tracking-tight text-center text-blue-500">
          Se connecter
        </h2>
      </div>
      <form
        class="mt-8 space-y-6"
        @submit.prevent="loginUser(formData)"
      >
        <input
          type="hidden"
          name="remember"
          value="true"
        >
        <div class="-space-y-px rounded-md shadow-sm">
          <div>
            <label
              for="email"
              class="sr-only"
            >Adresse e-mail</label>
            <input
              v-model="formData.email"
              id="email"
              name="email"
              type="email"
              autocomplete="email"
              required
              class="relative block w-full rounded-t-md border-0 p-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:z-10 focus:ring-2 focus:ring-inset focus:ring-blue-500 sm:text-sm sm:leading-6"
              placeholder="E-mail"
            >
          </div>
          <div>
            <label
              for="password"
              class="sr-only"
            >Mot de passe</label>
            <input
              v-model="formData.password"
              id="password"
              name="password"
              type="password"
              autocomplete="current-password"
              required
              class="relative block w-full rounded-b-md border-0 p-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:z-10 focus:ring-2 focus:ring-inset focus:ring-blue-500 sm:text-sm sm:leading-6"
              placeholder="Mot de passe"
            >
          </div>
        </div>

        <div class="flex items-center">
          <div class="text-sm">
            <a class="font-medium text-blue-500 hover:text-blue-300">Mot de passe oublié ?</a>
          </div>
        </div>

        <div>
          <button
            type="submit"
            class="relative flex justify-center w-full px-3 py-2 text-sm font-semibold text-white bg-blue-500 rounded-md group hover:bg-blue-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
          >
            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
              <LockClosedIcon
                class="w-5 h-5 text-indigo-500 group-hover:text-indigo-400"
                aria-hidden="true"
              />
            </span>
            Connexion
          </button>
        </div>
        <p class="text-sm">
          Vous n'avez pas de compte ?
          <NuxtLink
            to="/inscription"
            class="font-medium text-blue-500 hover:text-blue-300"
          >
            Je m'inscris
          </NuxtLink>
        </p>
      </form>
    </div>
  </div>
</template>
