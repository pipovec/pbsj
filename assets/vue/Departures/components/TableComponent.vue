<template>
  <div v-if="openPlayerDetailModal" class="absolute lg:top-1/4 lg:left-1/3 lg:w-1/3">
    <player-detail-modal :playerDetail="playerDetail"/>
  </div>
  <div class="w-full grid grid-cols-5 mt-4 mx-auto overflow-y-auto max-h-screen" style="max-height: 45rem;">
    <div
        class="px-2 py-1 grid grid-flow-row mr-2 mb-2 rounded"
        v-for="(player, index) in store.players['hydra:member']" :key="index"
        :style="backgroundColor(player.wn8)"
        @click="openPlayerDetail(player)"
    >
      <div class="grid grid-flow-col font-bold text-white">
        <div class="grid">
          <img :src="player.emblems_large" :alt="player.name" :title="player.name" width="64" class="rounded-full">
          <div class="text-white font-bold">{{ player.abbreviation }}</div>
        </div>
        <div class="grid text-lg content-center">{{player.nickname}}</div>
      </div>
      <hr />
      <div class="grid grid-cols-3 text-sm text-white">
        <div class="mr-2 grid content-end">battles: {{player.battles}}</div>
        <div class="mr-2 grid content-end">wn8: {{player.wn8}}</div>
        <div class="grid content-end">winrate: {{winrate(player.battles, player.wins)}}</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { departuresStore } from "../store";
import {wn8color} from "wn8-color";
import {ref} from "vue";
import PlayerDetailModal from "./playerDetailModal.vue";
const store = departuresStore();

let openPlayerDetailModal = ref(false)
let playerDetail = ref({})

function backgroundColor(wn8) {
  return'background-color: ' + wn8color(wn8)
}

function winrate(battles, wins) {
  return ((wins/battles) * 100).toFixed(2)
}

function openPlayerDetail(player) {
  this.openPlayerDetailModal = !this.openPlayerDetailModal;
  this.playerDetail = player;
}

</script>
