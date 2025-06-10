<template>
  <table class="recruiting-table">
    <thead>
      <tr>
        <template v-for="header in headName">
        <th>{{ header }}</th>
        </template>
      </tr>
    </thead>
    <tbody>
      <tr v-for="player in store.players['member']">
        <td>{{ player.nickname }}</td>
        <td></td>
        <td></td>
        <td>{{ roundWN(player.wn8) }}</td>
        <td>{{ player.global_rating }}</td>
        <td></td>
        <td>{{ player.tier_10 }}</td>
        <td>{{ player.tier_8 }}</td>
        <td>{{ player.tier_6 }}</td>
        <td></td>
        <td></td>
        <td></td>
        <td v-html="isTrue(player.combat_officer)"></td>
        <td v-html="isTrue(player.personnel_officer)"></td>
        <td v-html="isTrue(player.tank_1)"></td>
        <td v-html="isTrue(player.tank_2)"></td>
        <td v-html="isTrue(player.tank_3)"></td>
      </tr>
    </tbody>
  </table>
</template>


<script setup>
  import { recruitingStore } from "../store";
  import {computed, ref} from "vue";
  const store = recruitingStore()

  const headName = ref({
    nickname: 'Nickname',
    battlesAll: 'Battles All',
    winrate: 'Winrate',
    wn8: 'WN8',
    globalRating: 'Global rating',
    avgXp: 'Avg XP',
    tierX: 'Tier X',
    tier8: 'Tier 8',
    tier6: 'Tier 6',
    bat7: 'batt. 7 days',
    bat14: 'batt. 14 days',
    bat30: 'batt. 30 days',
    combat: 'combat Officer',
    personnel: 'personnel Officer',
    tank_1: 'Obj.279r',
    tank_2: 'Obj.907',
    tank_3: 'T95/FV4201 CHIEFTAIN'
  })

  function roundWN(wn8) {
    console.log(parseFloat(wn8).toFixed(2))
    return parseFloat(wn8).toFixed(2);
  }

  function isTrue(value) {

    return value ? '<img src="/success-green-check-mark-icon.svg" width="16" alt="success" class="tank"/>'
        : '<img src="/checkbox-cross-red-icon.svg" width="16" alt="cross mark" class="tank"/>';
  }

</script>
