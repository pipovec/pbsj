import { defineStore } from "pinia";
import axios from "axios";

export const recruitingStore= defineStore({
    id: 'recruiting',

    state: () => ({
        baseApiUrl: 'https://api2.fpcstat.cz/api',
        baseUrl: 'https://api2.fpcstat.cz',
        counter: 42,
        players: {},
        filterWnFrom: null,
        filterWnTo: null,
        filterNickName: null,
        tank_1: false,
        tank_2: false,
        tank_3: false,
        currentPage: 1,
        itemsPerPage: 10,
        sortBy: 'wn8',
        sortOrder: 'desc',
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

            // Pagination
            params.append('page', this.currentPage)
            params.append('itemsPerPage', this.itemsPerPage)

            // Sorting
            if (this.sortBy && this.sortOrder) {
                params.append(`order[${this.sortBy}]`, this.sortOrder)
            }

            // Filters
            if (this.filterWnFrom !== null && this.filterWnFrom !== '') {
                params.append('wn8[gt]', this.filterWnFrom)
            }

            if (this.filterWnTo !== null && this.filterWnTo !== '') {
                params.append('wn8[lt]', this.filterWnTo)
            }

            if (this.filterNickName !== null && this.filterNickName !== '') {
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
                }).then(response => {
               this.players = response.data
            }).catch(error => {
                console.error('Error fetching players:', error)
            })
        },

        setPage(page) {
            this.currentPage = page
            this.getPlayers()
        },

        setSorting(field, order) {
            this.sortBy = field
            this.sortOrder = order
            this.currentPage = 1
            this.getPlayers()
        },

        resetFilters() {
            this.filterWnFrom = null
            this.filterWnTo = null
            this.filterNickName = null
            this.tank_1 = false
            this.tank_2 = false
            this.tank_3 = false
            this.currentPage = 1
            this.getPlayers()
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
