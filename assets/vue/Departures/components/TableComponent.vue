<template>
  <div class="w-full">
    <div class="table-component">
      <div class="w-full grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-6 gap-2">
        <div
            class="px-2 py-1 grid grid-flow-row rounded cursor-pointer transition-transform hover:scale-105"
            v-for="(player, index) in store.players['member']" :key="index"
            :style="backgroundColor(player.wn8)"
            @click="openPlayerDetail(player)"
            data-umami-event="Click on player"
        >
          <div class="grid grid-cols-[70px,1fr] font-bold text-white">
            <div class="grid max-w-fit">
              <picture>
                <source :srcset="replaceFpcCdn(player.emblems_large)" type="image/webp">
                <source :srcset="player.emblems_large" type="image/png">
              <img
                  :src="replaceFpcCdn(player.emblems_large)"
                  loading="lazy"
                  :alt="player.name"
                  :title="player.name"
                  width="68" height="68" class="rounded-full"
                  sizes="(max-width: 1080) 68px, 68px"
              >
              </picture>
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

    <user-detail-modal
        v-if="showModal"
        @close-user-detail="showModal = false"
        :user="selectedUser"
    />
  </div>
</template>

<script setup>
import { departuresStore } from "../store";
import {ref, defineAsyncComponent} from "vue";

const userDetailModal = defineAsyncComponent(() => import('../../Recruiting/components/userDetailModal.vue'))

const store = departuresStore();

let showModal = ref(false)
let selectedUser = ref({name: '', account_id: null})
const cdnUrl = ref('https://cdn.fpcstat.cz')

function backgroundColor(wn8) {
  return'background-color: ' + wn8color(wn8)
}

function replaceFpcCdn(original) {
  const updatedUrl = original.replace('https://eu.wargaming.net/clans/media/clans/emblems', cdnUrl.value);
  return updatedUrl.replace('.png', '.webp')
}

function winrate(battles, wins) {
  return ((wins/battles) * 100).toFixed(2)
}

function openPlayerDetail(player) {
  if (!showModal.value) {
    sendClick(player)
  }
  selectedUser.value = {
    name: player.nickname,
    account_id: player.account_id
  }
  showModal.value = true
}

async function sendClick(player) {
  const body = {
    "accountId": player.account_id,
    "domain": "pbsj",
    "site": "departures",
    "date": getTodayDate(),
    "data": {
      nickname: player.nickname
    }
  }


  await fetch('https://api3.fpcstat.cz/api/site-click', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(body)
  })
}

function getTodayDate() {
  const date = new Date();
  const month = date.getMonth() + 1;
  const day = date.getDate();

  const formattedMonth = month.toString().padStart(2, '0');
  const formattedDay = day.toString().padStart(2, '0');

  return date.getFullYear() + '-' + formattedMonth + '-' + formattedDay;
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
  width: 100%;
  max-width: 100vw;
  overflow-x: hidden;
}
</style>
