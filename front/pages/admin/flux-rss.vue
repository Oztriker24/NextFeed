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
let selectFluxRss: { id: number, url: string, description: string, category: {id: number} } = {
  id: 0,
  url: "",
  description: "",
  category: {
    id: 0,
  },
};

const { data, refresh } = await useAsyncData<any>(() => $fetch(`${apiUrl}/fluxrss/paginate/${currentPage.value}`, {
  headers: {
    Authorization: `Bearer ${bearer.value}`,
  },
}));

const nbOfPages = ref(Math.ceil(data.value.totalCount / data.value.itemsNumberPerPage));

refreshPagination(currentPage.value);

async function deleteFluxRss() {
  await fetch(`${apiUrl}/fluxrss/delete/${selectFluxRss.id}`, {
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
function showAddModal() {
  isAddModalVisible.value = true;
}
function showDeleteModal(fluxrss: { id: number, url: string, description: string, category: {id: number} }) {
  isDeleteModalVisible.value = true;
  selectFluxRss = fluxrss;
}

function showUpdateModal(fluxrss: { id: number, url: string, description: string, category: {id: number}}) {
  isUpdateModalVisible.value = true;
  selectFluxRss = fluxrss;
}

async function updateFluxRss(fluxRssUrl: string, categoryIdSelected: number) {
  console.log(categoryIdSelected);
  await fetch(`${apiUrl}/fluxrss/update/${selectFluxRss.id}`, {
    method: "PATCH",
    headers: {
      Authorization: `Bearer ${bearer.value}`,
    },
    body: JSON.stringify({
      url: fluxRssUrl,
      categoryId: categoryIdSelected,
      description: selectFluxRss.description,
    }),
  })
    .then((res) => {
      if (res.ok) {
        newNotyf(true, "Modification réussi !");
        refresh();
      } else {
        newNotyf(false, "Ce flux Rss existe déjà ! ");
      }
    })
    .catch(() => newNotyf(false, "Une Erreur est survenue"));
}
async function addFluxRss(fluxRssUrl: string, fluxRssDescription: string, categoryIdSelected: number) {
  console.log(categoryIdSelected);
  await fetch(`${apiUrl}/fluxrss/add`, {
    method: "POST",
    headers: {
      Authorization: `Bearer ${bearer.value}`,
    },
    body: JSON.stringify({
      url: fluxRssUrl,
      categoryId: categoryIdSelected,
      description: fluxRssDescription,
    }),
  })
    .then((res) => {
      if (res.ok) {
        newNotyf(true, "Ajout réussi !");
        refresh();
      } else {
        newNotyf(false, "Ce flux Rss existe déjà ! ");
      }
    })
    .catch(() => newNotyf(false, "Une Erreur est survenue"));
}
</script>

<template>
  <!-- MODAL WINDOW -->
  <ModalsDeleteModal
    v-if="isDeleteModalVisible"
    :title="'Êtes vous sûr de vouloir supprimer le flux suivant ?'"
    :content="selectFluxRss.url"
    v-model:closeDeleteModal="isDeleteModalVisible"
    @success="deleteFluxRss()"
  />
  <ModalsFluxRssUpdateModal
    v-if="isUpdateModalVisible"
    :title="'Mise à jour du flux Rss :'"
    :content="selectFluxRss.url"
    :category="selectFluxRss.category.id"
    v-model:closeUpdateModal="isUpdateModalVisible"
    @success="updateFluxRss"
  />
  <ModalsFluxRssAddModal
    v-if="isAddModalVisible"
    v-model:closeAddModal="isAddModalVisible"
    @success="addFluxRss"
  />
  <!-- MAIN PAGE -->
  <div class="relative flex flex-col justify-between h-screen overflow-x-auto p-7">
    <!-- BUTTON ADD FLUX RSS -->
    <div class="flex justify-end">
      <button
        class="relative rounded px-5 py-2.5 overflow-hidden group bg-blue-500 hover:bg-gradient-to-r hover:from-blue-500 hover:to-blue-400 text-white hover:ring-2 hover:ring-offset-2 hover:ring-blue-400 transition-all ease-out duration-300"
        @click="showAddModal()"
      >
        <span class="absolute right-0 w-8 h-32 -mt-12 transition-all duration-1000 transform translate-x-12 bg-white opacity-10 rotate-12 group-hover:-translate-x-40 ease" />
        <span class="relative">
          <Icon
            name="bi:plus-circle-fill"
            class="mr-1"
          />
          Ajouter un flux Rss
        </span>
      </button>
    </div>
    <!-- TABLE FLUX RSS -->
    <div class="inline-block w-full shadow-lg">
      <table class="w-full text-sm text-left text-gray-500">
        <Head class="text-xs text-blue-600 uppercase bg-gray-100">
          <tr>
            <th
              scope="col"
              class="px-6 py-3"
            >
              Url
            </th>
            <th
              scope="col"
              class="px-6 py-3"
            >
              Catégorie
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
        </Head>
        <Body>
          <tr
            v-for="(fluxInfo, index) in data.items"
            :key="index"
            class="font-medium bg-gray-400 border-t border-gray-600"
          >
            <td
              scope="row"
              class="px-6 py-4 text-white whitespace-nowrap"
            >
              <a
                :href="fluxInfo.url"
                class="text-blue-700 hover:border-b hover:border-blue-700"
              >{{ fluxInfo.url }}</a>
            </td>
            <td class="px-6 py-4 text-white">
              {{ fluxInfo.category.name }}
            </td>
            <td class="px-6 py-4 text-white">
              {{ convertDateFormat(fluxInfo.created_at) }}
            </td>
            <td class="px-6 py-4 flex justify-between">
              <button
                class="text-blue-600 transition duration-300 transform hover:scale-110"
                @click="showUpdateModal(fluxInfo)"
              >
                <Icon
                  name="bi:gear-fill"
                  class="w-6 h-6"
                />
              </button>
              <button
                class="text-red-500 transition duration-300 transform hover:scale-110"
                @click="showDeleteModal(fluxInfo)"
              >
                <Icon
                  name="bi:trash3-fill"
                  class="w-6 h-6"
                />
              </button>
            </td>
          </tr>
        </Body>
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