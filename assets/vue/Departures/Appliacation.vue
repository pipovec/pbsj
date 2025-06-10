<template>
  <div class="fixed bg-gray-600 grid grid-cols-2 text-white py-3 px-2 w-full" style="top: 2.5rem">
    <div class="grid">
      <h2 class="text-2xl font-black">{{title}}</h2>
    </div>
    <div class="flex flex-row content-end justify-end gap-4">
      <div class="flex flex-wrap content-center">
        Total players:
        <span class="font-black flex ml-1"
        >{{ store.players['totalItems']}}</span>
      </div>
    </div>
  </div>
  <div class="w-full md:px-4 px-1 mt-20">
    <Table />
  </div>
</template>

<script setup>
  import {departuresStore} from "./store";
  import {onBeforeMount, ref} from "vue";
  import Table from "./components/TableComponent.vue";

  const store = departuresStore();

  const props = defineProps(['players']);

  const title = ref('Die heutigen Spielerabgänge');

  onBeforeMount(() => {
    store.players = props.players
  });

</script>
