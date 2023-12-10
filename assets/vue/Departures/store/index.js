import { defineStore } from "pinia";
import axios from "axios";

export const departuresStore= defineStore({
    id: 'departures',

    state: () => ({
        baseUrl: 'https://api2.fpcstat.cz',
        baseApiUrl: 'https://api2.fpcstat.cz/api',
        players: {},
    }),

    actions: {
        // setPlayers(players) {
        //     this.plyers = players
        // },

        getPlayersByUrl(url) {
            request(url, null)
                .then((response) => {
                     this.players =  response.data
                })
        },

        // getPlayers() {
        //
        //     const params = new URLSearchParams()
        //
        //     if (this.filterWnFrom !== null) {
        //         params.append('wn8[gt]', this.filterWnFrom)
        //     }
        //
        //     if (this.filterWnTo !== null) {
        //         params.append('wn8[lt]', this.filterWnTo)
        //     }
        //
        //     if (this.filterNickName !== null) {
        //         params.append('nickname', this.filterNickName)
        //     }
        //
        //     if (this.tank_1) {
        //         params.append('tank_1', 'true')
        //     }
        //
        //     if (this.tank_2) {
        //         params.append('tank_2', 'true')
        //     }
        //
        //     if (this.tank_3) {
        //         params.append('tank_3', 'true')
        //     }
        //
        //     axios.get(this.baseApiUrl + '/recruiting-germany',
        //         {
        //             headers: {'accept': 'application/ld+json'},
        //             params: params
        //         }, {}).then(response => {
        //         this.players = response.data
        //     })
        //
        //
        // }
    },
})
const apiURL = 'https://api2.fpcstat.cz';
async function request(url, params = null) {
    return await  axios.get(apiURL + url, {
        header: {'accept': 'application/ld+json'},
        params: params
    })
}
