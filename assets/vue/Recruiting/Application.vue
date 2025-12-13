<template>
  <div class="recruiting-header-fixed bg-gray-600 text-white py-3 px-4">
    <div class="flex flex-col gap-3">
      <h2 class="text-2xl font-black">{{msg}}</h2>
      <div class="flex flex-row flex-wrap gap-6 text-sm md:text-base items-center">
        <Filter />
        <div class="flex items-center">
          Total players: <span class="font-black ml-1">{{ totalPlayers }}</span>
        </div>
      </div>
    </div>
  </div>
  <div class="recruiting-content w-full">
    <Table />
  </div>
  <template v-if="totalPlayers > 0">
    <Paginator />
  </template>
</template>

<script setup>
import {onBeforeMount, ref, computed} from "vue";
import { recruitingStore } from "./store";
import Filter from "./components/filters.vue";
import Table from "./components/table.vue";
import Paginator from "./components/Paginator.vue";

  const store = recruitingStore()

  const msg = ref('Deutsche Freispieler')

  const totalPlayers = computed(() => {
    return store.players.totalItems || 0;
  })

  onBeforeMount(() => {
    store.getPlayers()
  })
</script>
