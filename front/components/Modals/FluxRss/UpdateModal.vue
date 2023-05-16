<script setup lang="ts">
const emit = defineEmits(["update:closeUpdateModal", "success"]);

const props = defineProps({
  title: {
    type: String,
    required: true,
  },
  content: {
    type: String,
    default: "",
  },
  category: {
    type: Number,
    default: 0,
  },
});

const { urlRegex } = useValidator();
const apiUrl = import.meta.env.VITE_API_URL;
const content = ref(props.content);
const category = ref(props.category);
const errorsMessages = ref({
  url: { message: "Veuillez saisir une URL valide !", isDisplay: false },
  category: { message: "Veuillez selectionner une catégorie !", isDisplay: false },
});

const closeModal = () => emit("update:closeUpdateModal", false);

function isFormValid() {
  let isValid = true;

  if (!urlRegex(content.value)) {
    isValid = false;
    errorsMessages.value.url.isDisplay = true;
  }
  if (!category.value) {
    isValid = false;
    errorsMessages.value.category.isDisplay = true;
  }
  return isValid;
}
const successModal = () => {
  if (isFormValid()) {
    emit("success", content.value, category.value);
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
          name="bi:gear-fill"
          class="w-16 h-16 text-blue-500"
        />
      </div>
      <h1 class="mt-3 text-lg font-bold text-center">
        Modification d'un Flux RSS
      </h1>
      <form>
        <input
          v-model="content"
          type="text"
          class="w-full border py-3 px-1 mt-3 rounded-md border-black"
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
          @focusin="errorsMessages.category.isDisplay = false"
          v-model="category"
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
          type="submit"
          @click="successModal()"
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