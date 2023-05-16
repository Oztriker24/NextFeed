<script setup lang="ts">
definePageMeta({
  layout: "admin",
});

const { newNotyf } = useNotyf();
const { convertDateFormat } = useDate();

const apiUrl = import.meta.env.VITE_API_URL;
const bearer = useCookie("token-session");

const isDeleteModalVisible = ref(false);
const isUpdateModalVisible = ref(false);
const currentPage = ref(1);
const pagesList: Ref<number[]> = ref([]);

const { data, refresh } = await useAsyncData<any>(() => $fetch(`${apiUrl}/user/paginate/${currentPage.value}`, {
  headers: {
    Authorization: `Bearer ${bearer.value}`,
  },
}));

const nbOfPages = ref(Math.ceil(data.value.totalCount / data.value.itemsNumberPerPage));

refreshPagination(currentPage.value);

function refreshPagination(pageNumber: number) {
  nbOfPages.value = Math.ceil(data.value.totalCount / data.value.itemsNumberPerPage);
  const prevPage: number = pageNumber - 1;
  const prevOfPrevPage: number = pageNumber - 2;
  const nextPage: number = pageNumber + 1;
  const nextOfNextPage: number = pageNumber + 2;

  switch (pageNumber) {
    case (1):
      if (nbOfPages.value >= 3) {
        pagesList.value = [pageNumber, nextPage, nextOfNextPage];
      } else if (nbOfPages.value === 2) {
        pagesList.value = [pageNumber, nextPage];
      }
      break;
    case (2):
      if (nbOfPages.value === 2) {
        pagesList.value = [prevPage, pageNumber];
      } else {
        pagesList.value = [prevPage, pageNumber, nextPage];
      }
      break;
    case (nbOfPages.value):
      pagesList.value = [prevOfPrevPage, prevPage, pageNumber];
      break;

    default:
      pagesList.value = [prevPage, pageNumber, nextPage];
      break;
  }
}

async function newPage(pageNumber: number) {
  if (pageNumber !== currentPage.value) {
    currentPage.value = pageNumber;
    await refresh();
    refreshPagination(pageNumber);
  }
}

async function updateUserStatus(user: { is_active: boolean, id: number }) {
  await fetch(`${apiUrl}/user/updateStatus/${user.id}`, {
    method: "PATCH",
    headers: {
      Authorization: `Bearer ${bearer.value}`,
    },
    body: JSON.stringify({
      isActive: user.is_active,
    }),
  })
    .then(() => {
      newNotyf(true, "Modification réussie !");
    })
    .catch(() => newNotyf(false, "Une Erreur est survenue"));
}
</script>

<template>
  <!-- MAIN PAGE -->
  <div class="relative flex flex-col justify-between h-screen overflow-x-auto p-7">
    <!-- TABLE CATEGORIES -->
    <div class="inline-block w-full shadow-lg mt-5">
      <table class="w-full text-sm text-left text-gray-500">
        <thead class="text-xs text-blue-600 uppercase bg-gray-100">
          <tr>
            <th
              scope="col"
              class="px-6 py-3"
            >
              E-mail
            </th>
            <th
              scope="col"
              class="px-6 py-3"
            >
              Nom
            </th>
            <th
              scope="col"
              class="px-6 py-3"
            >
              Rôles
            </th>
            <th
              scope="col"
              class="px-6 py-3"
            >
              Date d'ajout
            </th>
            <th
              scope="col"
              class="px-6 py-3"
            >
              Action
            </th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(user, index) in data.items"
            :key="index"
            class="font-medium bg-gray-400 border-t border-gray-600"
          >
            <td
              scope="row"
              class="px-6 py-4 text-white whitespace-nowrap"
            >
              {{ user.email }}
            </td>
            <td
              scope="row"
              class="px-6 py-4 text-white whitespace-nowrap"
            >
              {{ user.name }}
            </td>
            <td
              scope="row"
              class="px-6 py-4 text-white whitespace-nowrap"
            >
              <span
                :class="[user.roles[0] === 'ROLE_ADMIN'?'bg-red-600' : 'bg-blue-600', 'px-3 whitespace-nowrap  rounded-xl']"
              >

                {{ user.roles[0].replace('ROLE_', "") }}
              </span>
            </td>
            <td class="px-6 py-4 text-white">
              {{ convertDateFormat(user.created_at) }}
            </td>
            <td class="px-6 py-4 flex justify-end gap-x-3">
              <label class="relative inline-flex items-center cursor-pointer">
                <input
                  v-model="user.is_active"
                  type="checkbox"
                  class="sr-only peer"
                  @change="updateUserStatus(user)"
                >
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-500" />
              </label>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <!-- PAGINATE NAV -->
    <div
      v-if="nbOfPages > 1 "
      class="inline-block w-full"
    >
      <nav
        class="flex items-center justify-center"
      >
        <ul class="inline-flex items-center ">
          <li>
            <button
              :disabled="currentPage === 1"
              class="mr-3 disabled:text-gray-400 disabled:hover:scale-100 text-blue-500 transition duration-300 transform disabled:ng hover:scale-110"
              @click="newPage(currentPage - 1)"
            >
              <Icon
                name="bi:arrow-left-square-fill"
                class="w-8 h-8"
              />
            </button>
          </li>
          <li
            v-for="(pageNumber, index) in pagesList"
            :key="index"
          >
            <button
              :class="[pageNumber === currentPage ? ' text-blue-500 scale-110' : 'text-black scale-90', 'w-8 h-8 mx-1 transition duration-200 transform border hover:scale-110']"
              @click="newPage(pageNumber)"
            >
              {{ pageNumber }}
            </button>
          </li>
          <li>
            <button
              :disabled="currentPage === nbOfPages"
              class="ml-3 disabled:text-gray-400 disabled:hover:scale-100 text-blue-500 transition duration-300 transform hover:scale-110"
              @click="newPage(currentPage + 1)"
            >
              <Icon
                name="bi:arrow-right-square-fill"
                class="w-8 h-8"
              />
            </button>
          </li>
        </ul>
      </nav>
    </div>
  </div>
</template>