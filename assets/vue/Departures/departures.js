import { createApp } from "vue";
import Application from "./Appliacation.vue";
import { createPinia } from "pinia";

const appContainer = document.querySelector('#departures')
let players = JSON.parse(appContainer.dataset.players)


const app = createApp(Application, {players})
app.use( createPinia() )

app.mount('#departures')
