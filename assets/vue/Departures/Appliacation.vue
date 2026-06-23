<template>
  <div class="departures-page">
    <div ref="toolbarRef" class="toolbar" :class="{ 'toolbar--sticky': isSticky }">
      <h1 class="toolbar-title">Die heutigen Spielerabgänge</h1>
      <div class="toolbar-stats">
        <span>DE Klane: <strong>{{ store.numberOfGermanClanCount }}</strong></span>
        <span>Spieler: <strong>{{ store.players['totalItems'] ?? '…' }}</strong></span>
      </div>
    </div>
    <div class="toolbar-spacer" :style="{ height: isSticky ? toolbarHeight + 'px' : '0' }"></div>
    <Table />
  </div>
</template>

<script setup>
  import {departuresStore} from "./store";
  import {onBeforeMount, onMounted, onUnmounted, ref} from "vue";
  import Table from "./components/TableComponent.vue";

  const store = departuresStore();
  const props = defineProps(['players']);

  const toolbarRef = ref(null);
  const isSticky = ref(false);
  const toolbarHeight = ref(0);
  let toolbarOffsetTop = 0;

  function handleScroll() {
    isSticky.value = window.scrollY >= toolbarOffsetTop - 64;
  }

  onBeforeMount(() => {
    store.players = props.players;
  });

  onMounted(() => {
    store.getClansCount();
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

main {
  margin-top: 0;
}

.departures-page {
  min-height: calc(100vh - 4rem);
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  padding: 1.5rem 1rem;
}

.toolbar {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  background: rgba(255, 255, 255, 0.75);
  border: 1px solid rgba(0, 0, 0, 0.08);
  border-radius: 12px;
  padding: 1rem 1.25rem;
  margin-bottom: 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  backdrop-filter: blur(8px);
}

.toolbar--sticky {
  position: fixed;
  top: 4rem;
  left: 0;
  right: 0;
  border-radius: 0;
  border-left: none;
  border-right: none;
  border-top: none;
  margin-bottom: 0;
  padding: 0.75rem 1.5rem;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
  z-index: 50;
  animation: toolbarDock 0.28s cubic-bezier(0.4, 0, 0.2, 1) forwards;
}

@keyframes toolbarDock {
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

.toolbar-title {
  font-size: 1.4rem;
  font-weight: 500;
  color: #636b6f;
  white-space: nowrap;
}

.toolbar-stats {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  font-size: 0.9rem;
  color: #8892a6;
}

.toolbar-stats strong {
  color: #636b6f;
}

@media (max-width: 640px) {
  .departures-page {
    padding: 1rem 0.5rem;
  }

  .toolbar {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }

  .toolbar--sticky {
    top: 3.5rem;
  }

  .toolbar-title {
    font-size: 1.1rem;
  }
}
</style>
