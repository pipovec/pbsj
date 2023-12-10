<template>
  <div class="bg-gray-600 static grid grid-cols-2 text-white py-3 px-2">
    <div class="grid">
      <h2 class="text-2xl font-black">{{msg}}</h2>
    </div>
    <div class="flex flex-row content-end justify-end gap-4">
     <Filter />
     <div class="flex flex-wrap content-center">
       Total players:
       <span class="font-black flex ml-1"
       >{{ store.players['hydra:totalItems']}}</span>
       </div>
    </div>
  </div>
  <div>
    <Table />
  </div>
  <template v-if="store.players['hydra:totalItems'] > 0">
    <Paginator />
  </template>
</template>

<script setup>
import {onBeforeMount, ref} from "vue";
import { recruitingStore } from "./store";
import Filter from "./components/filters.vue";
import Table from "./components/table.vue";
import Paginator from "./components/Paginator.vue";

  const store = recruitingStore()

  const msg = ref('Deutsche Freispieler')

  onBeforeMount(() => {
    store.getPlayers()
  })
</script>
