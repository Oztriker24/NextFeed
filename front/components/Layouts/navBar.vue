<script setup lang="ts">
const { disconnect } = useLogin();
</script>

<template>
  <ul class="flex justify-between">
    <li>
      <NuxtLink to="/">
        <button class="flex items-center justify-center">
          <img
            class="w-auto h-8"
            src="~/assets/img/icon-nexton.png"
            alt="Your Company"
          >
          <h2 class="text-xl font-bold hidden sm:block">
            | NextFeed
          </h2>
        </button>
      </NuxtLink>
    </li>
    <li>
      <button
        class="box-items"
        @click="disconnect()"
      >
        <Icon
          name="bi:box-arrow-left"
          class="icon-items"
        />
        <span
          class="text-items"
        >
          Déconnexion
        </span>
      </button>
    </li>
  </ul>
</template>