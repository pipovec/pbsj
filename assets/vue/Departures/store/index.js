import { defineStore } from "pinia";
import axios from "axios";

export const departuresStore= defineStore({
    id: 'departures',

    state: () => ({
        baseUrl: 'https://api2.fpcstat.cz',
        baseApiUrl: 'https://api2.fpcstat.cz/api',
        players: {},
        numberOfGermanClanCount: 0,
    }),

    actions: {
        getPlayersByUrl(url) {
            request(url, null)
                .then((response) => {
                     this.players =  response.data
                })
        },
        async getClansCount() {
            const response = await fetch(this.baseApiUrl + '/clans/de/count', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }

            const data = await response.json();

            this.numberOfGermanClanCount = data.clans_count;
        }
    },
})
const apiURL = 'https://api2.fpcstat.cz';
async function request(url, params = null) {
    return await  axios.get(apiURL + url, {
        header: {'accept': 'application/ld+json'},
        params: params
    })
}
