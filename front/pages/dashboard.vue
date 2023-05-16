<script setup lang="ts">

const userInfos = useAuthUser();

const tokenSession = useCookie("token-session");
</script>

<template>
  <div>
    <h1 class="text-center">
      Page d'index
    </h1>
    <div>
      <p>Bienvenue <span class="text-red-500">{{ userInfos.username }}</span> !</p>
      <p>Vos Rôles sont :</p>
      <ul>
        <li
          v-for="rôle in userInfos.roles"
          class="text-red-500 "
          :key="rôle.id"
        >
          - {{ rôle }}
        </li>
      </ul>
      <p>Token Bearer: <span class="text-xs text-red-500 "> {{ tokenSession }}</span> </p>
      <button
        class="px-4 py-2 font-bold text-white bg-blue-500 border border-blue-700 rounded hover:bg-blue-700"
        @click="useDisconnect()"
      >
        Déconnexion
      </button>
    </div>
  </div>
</template>
