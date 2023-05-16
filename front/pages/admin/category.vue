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
const isAddModalVisible = ref(false);
const currentPage = ref(1);
const pagesList: Ref<number[]> = ref([]);

let selectCategory: { id: string, name: string } = {
  id: "",
  name: "",
};
const { data, refresh } = await useAsyncData<any>(() => $fetch(`${apiUrl}/category/paginate/${currentPage.value}`, {
  headers: {
    Authorization: `Bearer ${bearer.value}`,
  },
}));

const nbOfPages = ref(Math.ceil(data.value.totalCount / data.value.itemsNumberPerPage));

refreshPagination(currentPage.value);

async function deleteCategory() {
  await fetch(`${apiUrl}/category/delete/${selectCategory.id}`, {
    method: "DELETE",
    headers: {
      Authorization: `Bearer ${bearer.value}`,
    },
  })
    .then(() => {
      newNotyf(true, "Supression réussie !");
      refresh();
    })
    .catch(() => {
      newNotyf(false, "Une erreur est survenue ");
    });
}

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

function showDeleteModal(category: { id: string, name: string }) {
  isDeleteModalVisible.value = true;
  selectCategory = category;
}

function showUpdateModal(category: { id: string, name: string }) {
  isUpdateModalVisible.value = true;
  selectCategory = category;
}
function showAddModal() {
  isAddModalVisible.value = true;
}
async function updateCategory(categoryName: string) {
  await fetch(`${apiUrl}/category/update/${selectCategory.id}`, {
    method: "PATCH",
    headers: {
      Authorization: `Bearer ${bearer.value}`,
    },
    body: JSON.stringify({
      name: categoryName,
    }),
  })
    .then((res) => {
      if (res.ok) {
        newNotyf(true, "Modification réussie !");
        refresh();
      } else {
        newNotyf(false, "Cette catégorie existe déjà !");
      }
    })
    .catch(() => newNotyf(false, "Une Erreur est survenue"));
}
async function addCategory(categoryName: string) {
  await fetch(`${apiUrl}/category/add`, {
    method: "POST",
    headers: {
      Authorization: `Bearer ${bearer.value}`,
    },
    body: JSON.stringify({
      name: categoryName,
    }),
  })
    .then((res) => {
      if (res.ok) {
        newNotyf(true, "Ajout réussi !");
        refresh();
      } else {
        newNotyf(false, "Cette catégorie existe déjà ! ");
      }
    })
    .catch(() => newNotyf(false, "Une Erreur est survenue"));
}
</script>

<template>
  <!-- MODAL WINDOW -->
  <ModalsDeleteModal
    v-if="isDeleteModalVisible"
    :title="'Êtes vous sûr de vouloir supprimer la catégorie suivante ?'"
    :content="selectCategory.name"
    v-model:closeDeleteModal="isDeleteModalVisible"
    @success="deleteCategory()"
  />
  <ModalsCategoryUpdateModal
    v-if="isUpdateModalVisible"
    :content="selectCategory.name"
    v-model:closeUpdateModal="isUpdateModalVisible"
    @success="updateCategory"
  />
  <ModalsCategoryAddModal
    v-if="isAddModalVisible"
    v-model:closeAddModal="isAddModalVisible"
    @success="addCategory"
  />
  <!-- MAIN PAGE -->
  <div class="relative flex flex-col justify-between h-screen overflow-x-auto p-7">
    <!-- BUTTON ADD CATEGORY -->
    <div class="flex justify-end">
      <button
        @click="showAddModal()"
        class="relative rounded px-5 py-2.5 overflow-hidden group bg-blue-500 hover:bg-gradient-to-r hover:from-blue-500 hover:to-blue-400 text-white hover:ring-2 hover:ring-offset-2 hover:ring-blue-400 transition-all ease-out duration-300">
        <span class="absolute right-0 w-8 h-32 -mt-12 transition-all duration-1000 transform translate-x-12 bg-white opacity-10 rotate-12 group-hover:-translate-x-40 ease" />
        <span class="relative">
          <Icon
            name="bi:plus-circle-fill"
            class="mr-1"
          />
          Ajouter une Catégorie
        </span>
      </button>
    </div>
    <!-- TABLE CATEGORIES -->
    <div class="inline-block w-full shadow-lg">
      <table class="w-full text-sm text-left text-gray-500">
        <thead class="text-xs text-blue-600 uppercase bg-gray-100">
          <tr>
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
            v-for="(category, index) in data.items"
            :key="index"
            class="font-medium bg-gray-400 border-t border-gray-600"
          >
            <td
              scope="row"
              class="px-6 py-4 text-white whitespace-nowrap"
            >
              {{ category.name }}
            </td>
            <td class="px-6 py-4 text-white">
              {{ convertDateFormat(category.created_at) }}
            </td>
            <td class="px-6 py-4 flex justify-end gap-x-3">
              <button
                class="text-blue-600 transition duration-300 transform hover:scale-110"
                @click="showUpdateModal(category)"
              >
                <Icon
                  name="bi:gear-fill"
                  class="w-6 h-6"
                />
              </button>
              <button
                class="text-red-500 transition duration-300 transform hover:scale-110"
                @click="showDeleteModal(category)"
              >
                <Icon
                  name="bi:trash3-fill"
                  class="w-6 h-6"
                />
              </button>
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