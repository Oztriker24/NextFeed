<script setup lang="ts">

const emit = defineEmits(["update:closeAddModal", "success"]);

const { urlRegex } = useValidator();
const categoryId = ref("");
const content = ref("");
const descriptionFlux = ref("");
const apiUrl = import.meta.env.VITE_API_URL;
const errorsMessages = ref({
  url: { message: "Veuillez saisir une URL valide !", isDisplay: false },
  category: { message: "Veuillez selectionner une catégorie !", isDisplay: false },
  description: { message: "Veuillez saisir une description !", isDisplay: false },
});

const closeModal = () => emit("update:closeAddModal", false);
function changeCategory(event: Event) {
  const { value } = event.target as HTMLInputElement;
  categoryId.value = value;
}
function isFormValid() {
  let isValid = true;

  if (!urlRegex(content.value)) {
    isValid = false;
    errorsMessages.value.url.isDisplay = true;
  }
  if (!categoryId.value) {
    isValid = false;
    errorsMessages.value.category.isDisplay = true;
  }
  if (!descriptionFlux.value) {
    isValid = false;
    errorsMessages.value.description.isDisplay = true;
  }
  return isValid;
}

const successModal = () => {
  if (isFormValid()) {
    emit("success", content.value, descriptionFlux.value, categoryId.value);
    closeModal();
  }
};

const { data } = await useAsyncData<any>(() => $fetch(`${apiUrl}/category`, {
}));
</script>

<template>
  <div class="absolute z-50 flex h-screen w-screen items-center justify-center bg-black/50 top-0 left-0 ">
    <div class="h-max-[80%] w-3/3 md:w-2/3 lg:w-1/3 rounded bg-white p-4">
      <div class="flex justify-center">
        <Icon
          name="bi:plus-circle-fill"
          class="w-16 h-16 text-blue-500"
        />
      </div>
      <h1 class="mt-3 text-lg font-bold text-center">
        Ajouter un Flux RSS :
      </h1>
      <form class="mt-5">
        <input
          v-model="content"
          type="text"
          class="w-full border py-3 px-1  rounded-md border-black"
          placeholder="Entrez le lien du flux Url"
          @focusin="errorsMessages.url.isDisplay = false"
        >
        <div class="mt-1">
          <p
            v-if="errorsMessages.url.isDisplay"
            class=" text-red-500 text-sm"
          >
            {{ errorsMessages.url.message }}
          </p>
        </div>
        <select
          class="py-3 px-1 pr-9 mt-4 block w-full border cursor-pointer border-black rounded-md  focus:border-blue-500 focus:ring-blue-500 dark:text-gray-400"
          v-model="categoryId"
          @focusin="errorsMessages.category.isDisplay = false"
        >
          <option
            value=""
            disabled
            selected
          >
            Selectionner une catégorie
          </option>
          <option
            v-for="(category, index) in data"
            :key="index"
            :value="category.id"
          >
            {{ category.name }}
          </option>
        </select>
        <div class="mt-1">
          <p
            v-if="errorsMessages.category.isDisplay"
            class=" text-red-500 text-sm"
          >
            {{ errorsMessages.category.message }}
          </p>
        </div>
        <div class="mt-4">
          <textarea
            v-model="descriptionFlux"
            class="border w-full rounded-lg border-black py-3 px-1 "
            placeholder="Saisir une description"
          />
        </div>
        <div class="mt-">
          <p
            v-if="errorsMessages.description.isDisplay"
            class=" text-red-500 text-sm"
          >
            {{ errorsMessages.description.message }}
          </p>
        </div>
      </form>
      <div class="flex mt-7 justify-center gap-x-2">
        <button
          class="relative rounded px-5 py-2.5 overflow-hidden group bg-gray-500 hover:bg-gradient-to-r hover:from-gray-500 hover:to-gray-400 text-white hover:ring-2 hover:ring-offset-2 hover:ring-gray-400 transition-all ease-out duration-300"
          @click="closeModal"
        >
          <span class="absolute right-0 w-8 h-32 -mt-12 transition-all duration-1000 transform translate-x-12 bg-white opacity-10 rotate-12 group-hover:-translate-x-40 ease" />
          <span class="relative">
            <Icon
              name="bi:arrow-left-circle-fill"
              class="mr-1"
            />
            Non, revenir.
          </span>
        </button>
        <button
          @click="successModal"
          class="relative rounded px-5 py-2.5 overflow-hidden group bg-blue-500 hover:bg-gradient-to-r hover:from-blue-500 hover:to-blue-400 text-white hover:ring-2 hover:ring-offset-2 hover:ring-blue-400 transition-all ease-out duration-300"
        >
          <span class="absolute right-0 w-8 h-32 -mt-12 transition-all duration-1000 transform translate-x-14 bg-white opacity-10 rotate-12 group-hover:-translate-x-40 ease" />
          <span class="relative">
            <Icon
              name="bi:check-circle-fill"
              class="mr-1"
            />
            Je valide !
          </span>
        </button>
      </div>
    </div>
  </div>
</template>