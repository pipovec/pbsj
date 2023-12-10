<template>
  <div class="w-full flex flex-row flex-wrap mt-4 mx-auto">
    <div
        class="px-2 py-1 grid grid-flow-col mr-2 mb-2 rounded"
        v-for="(player, index) in store.players['hydra:member']" :key="index"
        :style="backgroundColor(player.wn8)"
    >
      <div style="width: 4rem;" class="grid text-center mr-2">
        <img :src="player.emblems_large" :alt="player.name" :title="player.name" width="96" class="rounded-full">
        <span class="text-white font-bold">{{ player.abbreviation }}</span>
      </div>
      <div class="grid font-bold text-white">
        <div class="grid text-lg text-center content-center">{{player.nickname}}</div>
        <div class="grid grid-cols-2 text-sm">
          <div class="mr-2 grid content-end">wn8: {{player.wn8}}</div>
          <div class="grid content-end">winrate: {{winrate(player.battles, player.wins)}}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { departuresStore } from "../store";
import {wn8color} from "wn8-color";
const store = departuresStore();

function backgroundColor(wn8) {
  return'background-color: ' + wn8color(wn8)
}

function winrate(battles, wins) {
  return ((wins/battles) * 100).toFixed(2)
}

</script>
