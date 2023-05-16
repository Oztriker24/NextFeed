<script setup lang="ts">
import { LockClosedIcon } from "@heroicons/vue/20/solid";

useSeoMeta({
  title: "Inscription",
});
definePageMeta({
  layout: false,
});

const { newNotyf } = useNotyf();
const { urlRegex, passwordRegex, emailRegex } = useValidator();

const apiUrl = import.meta.env.VITE_API_URL;
const formData = ref({
  email: "",
  password: "",
  confirmPassword: "",
  name: "",
  roles: ["ROLE_USER"],
  isActive: true,
});

const errorsMessages = ref({
  email: { message: "Veuillez saisir une adresse e-mail valide !", isDisplay: false },
  password: { message: "Veuillez Saisir un mot de passe valide (12 caractères, 1 majuscule et un caractère spécial) !", isDisplay: false },
  confirmPassword: { message: "Les mots de passe doivent être identiques !", isDisplay: false },
  name: { message: "Veuillez saisir un Nom d'utilisateur! ", isDisplay: false },
});

function isValidForm() {
  let isValid = true;

  if (!emailRegex(formData.value.email)) {
    console.log(urlRegex(formData.value.email));
    isValid = false;
    errorsMessages.value.email.isDisplay = true;
  }

  if (!passwordRegex(formData.value.password)) {
    isValid = false;
    errorsMessages.value.password.isDisplay = true;
  }

  if (formData.value.password !== formData.value.confirmPassword) {
    isValid = false;
    errorsMessages.value.confirmPassword.isDisplay = true;
  }

  if (!formData.value.name) {
    isValid = false;
    errorsMessages.value.name.isDisplay = true;
  }
  return isValid;
}

async function signInUser(user: any) {
  if (isValidForm()) {
    await fetch(`${apiUrl}/user/add`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        email: user.email,
        password: user.password,
        name: user.name,
        roles: user.roles,
        isActive: user.isActive,
      }),
    })
      .then((res) => {
        if (res.ok) {
          navigateTo({ path: "/connexion" });
          newNotyf(true, "Inscription réussie !");
        } else {
          newNotyf(false, "Oups, cette adresse e-mail est déjà utilisée ! ");
        }
      })
      .catch(() => {
        newNotyf(false, "Une Erreur est survenue");
      });
  }
}

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
          S'inscrire
        </h2>
      </div>
      <form
        class="mt-8 "
        @submit.prevent="signInUser(formData)"
      >
        <div>
          <label
            for="email"
            class="sr-only"
          >Adresse e-mail</label>
          <input
            v-model="formData.email"
            id="email"
            type="email"
            :class="[errorsMessages.email.isDisplay ? 'border-red-600' : 'border' , 'w-full border rounded-md p-1.5 text-gray-900 placeholder:text-gray-400']"
            placeholder="Adresse e-mail"
            @focusin="errorsMessages.email.isDisplay = false"
          >
          <div class="mt-1">
            <p
              v-if="errorsMessages.email.isDisplay"
              class=" text-red-500 text-sm"
            >
              {{ errorsMessages.email.message }}
            </p>
          </div>
        </div>
        <div
          :class="[errorsMessages.password.isDisplay || errorsMessages.confirmPassword.isDisplay ? 'border-red-600 ' : 'border', 'mt-5 border rounded-md']"
          @focusin="errorsMessages.password.isDisplay = false, errorsMessages.confirmPassword.isDisplay = false"
        >
          <div>
            <label
              for="password"
              class="sr-only"
            >Mot de passe</label>
            <input
              v-model="formData.password"
              id="password"
              type="password"
              autocomplete="current-password"
              class="w-full p-1.5 text-gray-900  placeholder:text-gray-400"
              placeholder="12 caractères, 1 majuscule et un caractère spécial"
            >
          </div>
          <div class="border-t">
            <label
              for="confirm_password"
              class="sr-only"
            >Confirmer votre mot de passe</label>
            <input
              v-model="formData.confirmPassword"
              id="confirm_password"
              type="password"
              class="w-full p-1.5 text-gray-900  placeholder:text-gray-400"
              placeholder="Confirmer votre mot de passe"
            >
          </div>
        </div>
        <p
          v-if="errorsMessages.password.isDisplay"
          class=" text-red-500 text-sm "
        >
          {{ errorsMessages.password.message }}
        </p>
        <p
          v-if="errorsMessages.confirmPassword.isDisplay && !errorsMessages.password.isDisplay"
          class=" text-red-500 text-sm "
        >
          {{ errorsMessages.confirmPassword.message }}
        </p>
        <div class="mb-5 mt-5">
          <label
            for="name"
            class="sr-only"
          >Nom d'utilisateur</label>
          <input
            v-model="formData.name"
            @focusin="errorsMessages.name.isDisplay = false"
            id="name"
            type="text"
            :class="[errorsMessages.name.isDisplay ? 'border-red-600' : 'border' , 'w-full border rounded-md p-1.5 text-gray-900 placeholder:text-gray-400']"
            placeholder="Nom d'utilisateur"
          >
          <div class="mt-1">
            <p
              v-if="errorsMessages.name.isDisplay"
              class=" text-red-500 text-sm"
            >
              {{ errorsMessages.name.message }}
            </p>
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
            Inscription
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
