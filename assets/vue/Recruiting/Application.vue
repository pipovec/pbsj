<template>
  <div class="rec-page" :style="{ '--thead-top': theadTop }">
    <div ref="toolbarRef" class="rec-toolbar" :class="{ 'rec-toolbar--sticky': isSticky }">
      <div class="rec-toolbar-left">
        <h1 class="rec-toolbar-title">Deutsche Freispieler</h1>
      </div>
      <Filter />
    </div>
    <div class="rec-toolbar-spacer" :style="{ height: isSticky ? toolbarHeight + 'px' : '0' }"></div>
    <div class="rec-content">
      <Table />
    </div>
    <Paginator v-if="totalPlayers > 0" />
  </div>
</template>

<script setup>
import { onBeforeMount, onMounted, onUnmounted, ref, computed } from "vue";
import { recruitingStore } from "./store";
import Filter from "./components/filters.vue";
import Table from "./components/table.vue";
import Paginator from "./components/Paginator.vue";

const store = recruitingStore();

const totalPlayers = computed(() => store.players.totalItems || 0);

const toolbarRef = ref(null);
const isSticky = ref(false);
const toolbarHeight = ref(0);
let toolbarOffsetTop = 0;

const theadTop = computed(() => `${64 + toolbarHeight.value}px`);

function handleScroll() {
  isSticky.value = window.scrollY >= toolbarOffsetTop - 64;
}

onBeforeMount(() => {
  store.getPlayers();
});

onMounted(() => {
  if (toolbarRef.value) {
    toolbarHeight.value = toolbarRef.value.offsetHeight;
    toolbarOffsetTop = toolbarRef.value.offsetTop;
  }
  window.addEventListener('scroll', handleScroll, { passive: true });
});

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll);
});
</script>

<style>
:root {
  --card-bg: #2d3748;
  --text-primary: #f8fafc;
  --text-secondary: #cbd5e1;
  --primary-color: #2563eb;
}

.rec-page {
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  min-height: calc(100vh - 4rem);
  padding: 1.5rem 0 0;
}

.rec-toolbar {
  position: relative;
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  background: rgba(255, 255, 255, 0.75);
  border: 1px solid rgba(0, 0, 0, 0.08);
  border-radius: 12px;
  padding: 0.875rem 1.25rem;
  margin: 0 1rem 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  backdrop-filter: blur(8px);
}

.rec-toolbar--sticky {
  position: fixed;
  top: 4rem;
  left: 0;
  right: 0;
  border-radius: 0;
  border-left: none;
  border-right: none;
  border-top: none;
  margin: 0;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
  z-index: 50;
  animation: recToolbarDock 0.28s cubic-bezier(0.4, 0, 0.2, 1) forwards;
}

@keyframes recToolbarDock {
  from {
    transform: scaleX(0.94) translateY(-6px);
    opacity: 0.5;
    border-radius: 12px;
  }
  to {
    transform: scaleX(1) translateY(0);
    opacity: 1;
    border-radius: 0;
  }
}

.rec-toolbar-left {
  display: flex;
  align-items: baseline;
  gap: 1rem;
  flex-wrap: wrap;
}

.rec-toolbar-title {
  font-size: 1.4rem;
  font-weight: 500;
  color: #636b6f;
  white-space: nowrap;
}

.rec-toolbar-count {
  font-size: 0.9rem;
  color: #8892a6;
}

.rec-content {
  padding-top: 0.5rem;
  padding-bottom: 6rem;
  overflow-x: auto;
  width: 100%;
}

@media (max-width: 640px) {
  .rec-page {
    padding: 1rem 0 0;
  }

  .rec-toolbar {
    margin: 0 0.5rem 1rem;
  }

  .rec-toolbar--sticky {
    top: 3.5rem;
  }

  .rec-toolbar-title {
    font-size: 1.1rem;
  }
}
</style>
