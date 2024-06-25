<template>
  <div class="flex flex-wrap justify-around text-xl font-black">
    <div>
      <a :href="'https://lab-vole.cz/Player?name='+player.nickname" target="_blank"
      >LAB-VOLE</a>
    </div>
    <div>
      <a :href="'https://wot-life.com/eu/player/' + player.nickname" target="_blank"
      >
        WOT-LIFE
      </a>
    </div>
    <div>
      <a :href="'https://www.tomato.gg/stats/EU/' + player.nickname+'='+player.account_id" target="_blank">
        TOMATO.GG
      </a>
    </div>
  </div>

</template>

<script setup>
const props = defineProps(['player'])

</script>
