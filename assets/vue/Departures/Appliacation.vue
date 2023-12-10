<template>
  <div class="bg-gray-600 static grid grid-cols-2 text-white py-3 px-2">
    <div class="grid">
      <h2 class="text-2xl font-black">{{title}}</h2>
    </div>
    <div class="flex flex-row content-end justify-end gap-4">
      <div class="flex flex-wrap content-center">
        Total players:
        <span class="font-black flex ml-1"
        >{{ store.players['hydra:totalItems']}}</span>
      </div>
    </div>
  </div>
  <div class="w-full md:px-4 px-1">
    <Table />
  </div>
  <div v-if="store.players['hydra:totalItems'] > 0">
    <Paginator />
  </div>
</template>

<script setup>
  import {departuresStore} from "./store";
  import {onBeforeMount, ref} from "vue";
  import Table from "./components/TableComponent.vue";
  import Paginator from "./components/Paginator.vue";

  const store = departuresStore();

  const props = defineProps(['players']);

  const title = ref('Die heutigen Spielerabgänge');

  onBeforeMount(() => {
    store.players = props.players
  });

</script>
