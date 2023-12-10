import { defineStore } from "pinia";
import axios from "axios";

export const recruitingStore= defineStore({
    id: 'recruiting',

    state: () => ({
        baseApiUrl: 'https://api2.fpcstat.cz/api',
        baseUrl: 'https://api2.fpcstat.cz',
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
        },
        getPlayersByUrl(url) {
            request(url, null)
                .then((response) => {
                    this.players =  response.data
                })
        },
    },

})

const apiURL = 'https://api2.fpcstat.cz';
async function request(url, params = null) {
    return await  axios.get(apiURL + url, {
        header: {'accept': 'application/ld+json'},
        params: params
    })
}
