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
