<template>
  <div class="departures-header fixed bg-gray-600 text-white py-3 px-4 w-full z-10">
    <div class="flex flex-col gap-3">
      <h2 class="text-2xl font-black">{{title}}</h2>
      <div class="flex flex-row flex-wrap gap-6 text-sm md:text-base">
        <div class="flex items-center">
          Anzahl deutscher clans: <span class="font-black ml-1">{{ store.numberOfGermanClanCount }}</span>
        </div>
        <div class="flex items-center">
          Total players: <span class="font-black ml-1">{{ store.players['totalItems']}}</span>
        </div>
      </div>
    </div>
  </div>
  <div class="departures-content w-full">
    <Table />
  </div>
</template>

<script setup>
  import {departuresStore} from "./store";
  import {onBeforeMount, onMounted, ref} from "vue";
  import Table from "./components/TableComponent.vue";

  const store = departuresStore();

  const props = defineProps(['players']);

  const title = ref('Die heutigen Spielerabgänge');

  onBeforeMount(() => {
    store.players = props.players
  });

  onMounted(() => {
    store.getClansCount()
  })

</script>
