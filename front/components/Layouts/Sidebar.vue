<script setup>
const open = ref(true);
const { disconnect } = useLogin();

const itemsSidebar = [
  { icon: "ri:account-circle-fill", text: "Utilisateurs", path: "/admin/user" },
  { icon: "ri:article-fill", text: "Catégories", path: "/admin/category" },
  { icon: "ri:align-justify", text: "Flux RSS", path: "/admin/flux-rss" },
];
</script>
<template>
  <div :class="[open ? 'lg:w-[250px]' : 'lg:w-[80px]', 'relative h-screen text-white bg-white w-[80px] p-3 duration-150 border-r ']">
    <button
      @click="open = !open"
      class="absolute hidden -right-3 top-9 lg:block"
    >
      <Icon
        name="material-symbols:arrow-back-rounded"
        size="24"
        :class="[!open ? 'rotate-180': 'rotate-0', 'text-blue-500 bg-white border border-gray-900 rounded-full duration-300']"
      />
    </button>
    <div class="flex flex-col justify-between h-full">
      <div>
        <div :class="[open ? 'lgg:justify-start' : 'lg:justify-center', 'flex items-center gap-3 text-xl font-bold py-6 border-b duration-100 justify-center']">
          <img
            src="~/assets/img/icon-nexton.png"
            alt="icon-nexton"
            class="w-6 h-6 duration-150"
          >
          <div
            v-if="open"
            class="hidden lg:block"
          >
            <span class="text-blue-500 ">
              Next
            </span>
            <span class="text-blue-500 ">|</span>
            <span class="text-gray-900 ">
              Feed
            </span>
          </div>
        </div>
        <ul>
          <li
            v-for="(item, index) in itemsSidebar"
            :key="index"
            class="sidebar-items"
          >
            <NuxtLink
              :to="item.path"
              :class="[open ? 'sidebar-button-open' : 'sidebar-button-close', 'sidebar-button']"
            >
              <div class="box-items">
                <Icon
                  :name="item.icon"
                  class="icon-items"
                />
                <span
                  v-if="open"
                  class="text-items"
                >
                  {{ item.text }}
                </span>
              </div>
              <span
                v-if="open"
                class="arrow-items"
              >
                <Icon name="gridicons:chevron-right" />
              </span>
            </NuxtLink>
          </li>
        </ul>
      </div>
      <!-- Déconnexion  -->
      <button
        :class="[open ? 'sidebar-button-open' : 'sidebar-button-close', 'sidebar-button']"
        @click="disconnect()"
      >
        <div class="box-items">
          <Icon
            name="bi:box-arrow-left"
            class="icon-items"
          />
          <span
            v-if="open"
            class="text-items"
          >
            Déconnexion
          </span>
        </div>
        <span
          v-if="open"
          class="arrow-items"
        >
          <Icon name="gridicons:chevron-right" />
        </span>
      </button>
    </div>
  </div>
</template>