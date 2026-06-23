<template>
  <div class="rec-filters">
    <div class="rec-filter-group">
      <label class="rec-filter-label">WN8</label>
      <input
        type="number"
        class="rec-filter-input rec-filter-input--sm"
        v-model="store.filterWnFrom"
        @change="reload"
        placeholder="von"
      />
      <span class="rec-filter-sep">–</span>
      <input
        type="number"
        class="rec-filter-input rec-filter-input--sm"
        v-model="store.filterWnTo"
        @change="reload"
        placeholder="bis"
      />
    </div>

    <div class="rec-filter-group">
      <input
        type="text"
        class="rec-filter-input rec-filter-input--name"
        v-model="store.filterNickName"
        @input="reload"
        placeholder="Spielername…"
      />
    </div>

    <div class="rec-filter-group rec-filter-group--checks">
      <label class="rec-filter-check">
        <input type="checkbox" v-model="store.tank_1" @change="reload" />
        <span>279r</span>
      </label>
      <label class="rec-filter-check">
        <input type="checkbox" v-model="store.tank_2" @change="reload" />
        <span>907</span>
      </label>
      <label class="rec-filter-check">
        <input type="checkbox" v-model="store.tank_3" @change="reload" />
        <span>Chief.</span>
      </label>
    </div>

    <button class="rec-filter-reset" @click="reset" title="Filter zurücksetzen">↻</button>
  </div>
</template>

<script setup>
import { recruitingStore } from "../store";

const store = recruitingStore();

function reload() {
  store.getPlayers();
}

function reset() {
  store.resetFilters();
}
</script>
