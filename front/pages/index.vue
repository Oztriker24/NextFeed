<script setup lang="ts">

useSeoMeta({
  title: "Accueil",
});
definePageMeta({
  middleware: "connected",
});
const { convertDateFormat } = useDate();
const { escapeHtml } = useValidator();
const articles: Ref<{
  title: string | null | undefined;
  category: string | null | undefined;
  link: string | null | undefined;
  description: string | null | undefined;
  pubDate: string | null | undefined;
  img: string | null | undefined;
}[]> = ref([]);

const bearer = useCookie("token-session");
const apiUrl = import.meta.env.VITE_API_URL;
const selectCategoryId = ref("0");
const limitArticles = ref(6);

const { data: homeFluxRss } = await useAsyncData<any>(() => $fetch(`${apiUrl}/category/randoms/category`, {
  headers: {
    Authorization: `Bearer ${bearer.value}`,
  },
}));
const { data: allCategory } = await useAsyncData<any>(() => $fetch(`${apiUrl}/category/`, {
  headers: {
    Authorization: `Bearer ${bearer.value}`,
  },
}));

async function getHomeArticles(fluxRssList: any) {
  articles.value = [];
  for (let i = 0; i < fluxRssList.length; i += 1) {
    fetch(fluxRssList[i].url)
      .then((res) => res.text())
      .then((data) => {
        setRssFeed(data, fluxRssList[i].category.name, true);
      });
  }
}

function setRssFeed(dataFeed: string, category: string, limit: boolean) {
  limitArticles.value = 6;
  const parser = new DOMParser();
  const xml = parser.parseFromString(dataFeed, "application/xml");
  const items = xml.querySelectorAll("item");

  let limitLength;
  if (limit) {
    limitLength = 1;
  } else {
    limitLength = items.length;
  }
  for (let i = 0; i < limitLength; i += 1) {
    const title = items[i].querySelector("title")?.textContent;
    const link = items[i].querySelector("link")?.textContent;
    const description = items[i].querySelector("description")?.textContent;
    const pubDate = items[i].querySelector("pubDate")?.textContent;
    let img = "";

    if (items[i].querySelector("content")) {
      img = items[i].querySelector("content")?.getAttribute("url");
    } else if (items[i].querySelector("enclosure")) {
      img = items[i].querySelector("enclosure")?.getAttribute("url");
    }

    const article = {
      category,
      title,
      link,
      description,
      pubDate,
      img,
    };

    articles.value.push(article);
  }
}

function getCategoryAllArticles(categoryId: string) {
  articles.value = [];

  if (categoryId === "0") {
    getHomeArticles(homeFluxRss.value);
  } else {
    const index = allCategory.value.map((e: any) => e.id).indexOf(categoryId);
    const selectCategory = allCategory.value[index];

    for (let i = 0; i < selectCategory.flux_rsses.length; i += 1) {
      fetch(selectCategory.flux_rsses[i].url)
        .then((res) => res.text())
        .then((data) => {
          setRssFeed(data, selectCategory.name, false);
        });
    }
  }
}

function incrementLimitArticles() {
  limitArticles.value += 6;
}

function scrollToId(element: any) {
  // document.querySelector("#topSelectButton").scrollTo(1000);
}
onMounted(() => {
  getHomeArticles(homeFluxRss.value);
});

</script>

<template>
  <div class=" w-full h-screen p-6 ">
    <div class="h-full">
      <div class="mb-5">
        <select
          class="py-3 px-1 pr-9 mt-4 block border cursor-pointer rounded-lg shadow  focus:border-blue-500 focus:ring-blue-500 dark:text-gray-400"
          id="topButtonSelect"
          v-model="selectCategoryId"
          @change="getCategoryAllArticles(selectCategoryId)"
        >
          <option
            value="0"
            disabled
            selected
          >
            Selectionner une catégorie
          </option>
          <option
            value="0"
          >
            Tous
          </option>
          <option
            v-for="(category, index) in allCategory"
            :key="index"
            :value="category.id"
          >
            {{ category.name }}
          </option>
        </select>
      </div>
      <div class="grid md:grid-cols-2 gap-x-6 gap-y-10">
        <div
          v-for="(article , index) in articles.slice(0, limitArticles)"
          :key="index"
          class="flex h-full flex-col gap-4 rounded-lg bg-white p-4 shadow xl:flex-row"
        >
          <img
            v-if="article.img"
            :src="article.img"
            class="hidden h-40 w-full rounded-lg object-cover object-center md:block xl:w-1/4"
          >
          <img
            v-else
            src="~/assets/img/placeHolder.png"
            alt="logo nexton"
            class="hidden h-40 w-full rounded-lg object-cover object-center md:block xl:w-1/4"
          >
          <div class="flex flex-col justify-between gap-y-4">
            <div class="space-y-2">
              <div class="flex items-center justify-between gap-x-4">
                <p class="line-clamp-1 text-lg font-bold">
                  {{ article.title }}
                </p>
                <span class="rounded-full bg-green-200 px-2 py-1 text-xs w-fit">{{ article.category }}</span>
              </div>
              <p class="line-clamp-3">
                {{ escapeHtml(article.description) }}
              </p>
            </div>
            <div class="flex items-end justify-between">
              <p class="text-sm text-gray-500">
                {{ convertDateFormat(article.pubDate) }}
              </p>
              <a
                :href="article.link"
                target="_blank"
                class="float-right rounded-lg bg-blue-200 px-2 py-1 text-sm transition hover:bg-blue-300"
              >
                Lire la suite
              </a>
            </div>
          </div>
        </div>
      </div>
      <div class="w-full flex justify-end p-6">
        <button
          v-if="limitArticles < articles.length"
          @click="incrementLimitArticles()"
          class=" relative rounded px-5 py-2.5 overflow-hidden group bg-blue-500 hover:bg-gradient-to-r hover:from-blue-500 hover:to-blue-400 text-white hover:ring-2 hover:ring-offset-2 hover:ring-blue-400 transition-all ease-out duration-300"
        >
          <span class="absolute right-0 w-8 h-32 -mt-12 transition-all duration-1000 transform translate-x-12 bg-white opacity-10 rotate-12 group-hover:-translate-x-40 ease" />
          <span class="relative">
            Voir plus
          </span>
        </button>
        <button
          v-if="limitArticles >= articles.length"
          class=" relative rounded px-5 py-2.5 overflow-hidden group bg-green-500 hover:bg-gradient-to-r hover:from-green-500 hover:to-green-400 text-white hover:ring-2 hover:ring-offset-2 hover:ring-green-400 transition-all ease-out duration-300"
        >
          <span class="absolute right-0 w-8 h-32 -mt-12 transition-all duration-1000 transform translate-x-12 bg-white opacity-10 rotate-12 group-hover:-translate-x-40 ease" />
          <span class="relative">
            Haut de Page
          </span>
        </button>
      </div>
    </div>
  </div>
</template>
