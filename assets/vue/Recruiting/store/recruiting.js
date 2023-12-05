import { defineStore } from "pinia";
import axios from "axios";

export const recruitingStore= defineStore({
    id: 'recruiting',

    state: () => ({
        baseApiUrl: 'http://api2-fpcstat.dock/api',
        counter: 42,
        players: {},
        filterWnFrom: 1800,
        filterWnTo: 2500,
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
        },

        getPlayers() {

            const params = new URLSearchParams()

            if (this.filterWnFrom !== null) {
                params.append('wn8[gt]', this.filterWnFrom)
            }

            if (this.filterWnTo !== null) {
                params.append('wn8[lt]', this.filterWnTo)
            }

            if (this.filterNickName !== null) {
                params.append('nickname', this.filterNickName)
            }

            if (this.tank_1) {
                params.append('tank_1', 'true')
            }

            if (this.tank_2) {
                params.append('tank_2', 'true')
            }

            if (this.tank_3) {
                params.append('tank_3', 'true')
            }

            axios.get(this.baseApiUrl + '/recruiting-germany',
                {
                    headers: {'accept': 'application/ld+json'},
                    params: params
                }, {}).then(response => {
               this.players = response.data
            })
        }
    },
})
