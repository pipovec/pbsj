import { defineStore } from "pinia";
export const recruitingStore= defineStore({
    id: 'recruiting',

    state: () => ({
        baseApiUrl: 'https://api2.fpcstat.cz/api',
        counter: 42,
        players: {},
        filterWnFrom: null,
        filterWnTO: null,
        filterNickName: null,
        tank_1: false,
        tank_2: false,
        tank_3: false,
    }),

    actions: {
        increment() {
            this.counter++
        },
        reset() {
            this.counter = 42
        },

        setPlayers(players) {
            this.plyers = players
        }
    },
})
