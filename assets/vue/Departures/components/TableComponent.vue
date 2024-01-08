<template>
  <div v-if="openPlayerDetailModal" class="absolute lg:top-1/4 lg:left-1/3 lg:w-1/3">
    <player-detail-modal :playerDetail="playerDetail"/>
  </div>
  <div class="w-full">
    <div class="table-component">
      <div class="w-full grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-6 mt-4 mx-auto overflow-y-auto">
        <div
            class="px-2 py-1 grid grid-flow-row mr-2 mb-2 rounded"
            v-for="(player, index) in store.players['hydra:member']" :key="index"
            :style="backgroundColor(player.wn8)"
            @click="openPlayerDetail(player)"
        >
          <div class="grid grid-cols-[70px,1fr] font-bold text-white">
            <div class="grid max-w-fit">
              <img :src="player.emblems_large" :alt="player.name" :title="player.name" width="68" height="68" class="rounded-full">
              <div class="text-white text-center font-bold">{{ player.abbreviation }}</div>
            </div>
            <div class="grid text-lg text-center content-center">{{player.nickname}}</div>
          </div>
          <hr />
          <div class="grid grid-cols-3 text-sm text-white">
            <div class="mr-2 grid content-end">battles: {{player.battles}}</div>
            <div class="mr-2 grid content-end">wn8: {{player.wn8}}</div>
            <div class="grid content-end">winrate: {{winrate(player.battles, player.wins)}}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { departuresStore } from "../store";
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

function wn8color(wn8) {
  let color;
  if (wn8 < 450) {
    color = 'grey';
  } else if (wn8 >= 450 && wn8 < 650) {
    color = '#e50000';
  } else if (wn8 >= 650 && wn8 < 850) {
    color = '#cd3333';
  } else if (wn8 >= 850 && wn8 < 1050) {
    color = '#d77900';
  } else if (wn8 >= 1050 && wn8 < 1250) {
    color = '#d7B600';
  } else if (wn8 >= 1250 && wn8 < 1400) {
    color = '#6d9251';
  } else if (wn8 >= 1400 && wn8 < 1600) {
    color = '#4c762e';
  } else if (wn8 >= 1600 && wn8 < 2000) {
    color = '#46a892';
  } else if (wn8 >= 2000 && wn8 < 2450) {
    color = '#4a92b7';
  } else if (wn8 >= 2450 && wn8 < 2850) {
    color = '#83579d';
  } else if (wn8 >= 2850) {
    color = '#5a3175';
  }

  return color;
}

</script>

<style>
.table-component {
  display: grid;
  grid-template-columns: 1fr auto;
}
</style>
