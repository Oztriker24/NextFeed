<script setup lang="ts">
const emit = defineEmits(["update:closeDeleteModal", "success"]);

const props = defineProps({
  title: {
    type: String,
    required: true,
  },
  content: {
    type: String,
    default: "",
  },
});

const title = ref(props.title);
const content = ref(props.content);

const closeModal = () => emit("update:closeDeleteModal", false);
const successModal = () => {
  emit("success");
  closeModal();
};

</script>

<template>
  <div class="absolute z-50 flex h-screen w-screen items-center justify-center bg-black/50 top-0 left-0 ">
    <div class="h-max-[80%] w-3/3 md:w-2/3 lg:w-1/3 rounded bg-white p-4">
      <div class="flex justify-center">
        <Icon
          name="bi:exclamation-diamond-fill"
          class="w-16 h-16 text-red-500"
        />
      </div>
      <h1 class="mt-3 text-lg font-bold text-center">
        {{ title }}
      </h1>
      <p class="text-center">
        {{ content }}
      </p>
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
          class="relative rounded px-5 py-2.5 overflow-hidden group bg-blue-500 hover:bg-gradient-to-r hover:from-blue-500 hover:to-blue-400 text-white hover:ring-2 hover:ring-offset-2 hover:ring-blue-400 transition-all ease-out duration-300"
          @click="successModal()"
        >
          <span class="absolute right-0 w-8 h-32 -mt-12 transition-all duration-1000 transform translate-x-14 bg-white opacity-10 rotate-12 group-hover:-translate-x-40 ease" />
          <span class="relative">
            <Icon
              name="bi:check-circle-fill"
              class="mr-1"
            />
            Oui, je suis sûr !
          </span>
        </button>
      </div>
    </div>
  </div>
</template>