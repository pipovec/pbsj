import { createApp } from 'vue'
import Application from './Application.vue'
import { createPinia } from "pinia";

const app = createApp(Application)
app.use(createPinia())
app.mount('#recruiting')


