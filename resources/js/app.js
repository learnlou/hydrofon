import axios from 'axios';
import debounce from 'lodash/debounce';
import Events from './modules/events';

import flashMessages from './modules/flashMessages';
import impersonation from './modules/impersonation';

const app = new Vue({
    el: '#app',

    data: {
        date: null,
        categories: [],
        resources: [],
        bookings: [],
        updatedResources: new Map()
    },

    computed: {
        selectedResources: function() {
            return this.resources.filter(resources => resources.selected)
        }
    },

    methods: {
        initialData: function() {
            this.date = window.HYDROFON.date || new Date().setHours(0, 0, 0, 0) / 1000;

            this.categories = window.HYDROFON.categories || [];
            this.resources = window.HYDROFON.resources || [];
            this.bookings = [];

            this.categories.forEach(category => category.expanded = false);
        },

        fetchBookings: function() {
            // Only make HTTP request if there are selected resources.
            if (this.selectedResources.length > 0) {
                axios.get("api/bookings", {
                    params: {
                        "resource_id": this.selectedResources.map(resource => resource.id),
                        "filter[between]": this.date + "," + (this.date + 86400),
                    }
                })
                    .then(response => {
                        this.bookings = response.data.data;
                    })
                    .catch(error => {
                        console.log(error);
                    });
            }
        },

        handleCreateBooking: function(booking) {
            let newID = Math.random().toString(36).substring(2);

            this.bookings.push(Object.assign({
                id: newID,
                status: 'updating'
            }, booking));

            axios.post("api/bookings", booking, {withCredentials: true})
                .then(response => {
                    // Find index of created booking.
                    let index = this.bookings.findIndex(function(stored) {
                        return stored.id === newID;
                    });

                    // Replace object with copy of object with new status.
                    this.bookings.splice(index, 1, response.data);
                })
                .catch(error => {
                    // Find index of created booking.
                    let index = this.bookings.findIndex(function(stored) {
                        return stored.id === newID;
                    });

                    // Replace object with copy of object with new status.
                    this.bookings.splice(index, 1);

                    // Log error.
                    console.log(error);
                });
        },

        handleUpdateBooking: function(booking) {
            // Find index of updated booking.
            let index = this.bookings.findIndex(function(stored) {
                return stored.id === booking.id;
            });

            this.$set(this.bookings, index, Object.assign({
                status: 'updating'
            }, booking));

            axios.put("api/bookings/" + booking.id, booking, {withCredentials: true})
                .then(response => {
                    // Replace object with copy of object with new status.
                    this.bookings.splice(index, 1, response.data);
                })
                .catch(error => {
                    // Log error.
                    console.log(error);
                });
        },

        handleDeleteBooking: function(booking) {
            axios.delete("api/bookings/" + booking.id, {withCredentials: true})
                .then(response => {
                    // Find index of deleted booking.
                    let index = this.bookings.findIndex(function(stored) {
                        return stored.id === booking.id;
                    });

                    // Remove object.
                    this.bookings.splice(index, 1);
                })
                .catch(error => {
                    // Log error.
                    console.log(error);
                });
        },

        updateSelectedResources: debounce(function() {
            this.updatedResources.forEach((value, key) => {
                // Find index of updated booking.
                let index = this.resources.findIndex(function(stored) {
                    return stored.id === key;
                });

                // Replace object with copy of object with new status.
                this.$set(this.resources, index, Object.assign(this.resources[index], {
                    selected: value
                }));
            });

            // Clear map when all resources have been updated.
            this.updatedResources.clear();
        }, 1250)
    },

    watch: {
        resources: function () {
            // Update bookings whenever resources our updated.
            this.fetchBookings();
        },
        date: function () {
            // Update bookings whenever date is changed.
            this.fetchBookings();
        }
    },

    components: {
        'calendar-header': require('./components/CalendarHeader').default,
        'resourcelist-root': require('./components/ResourceList').default,
    },

    created: function() {
        this.initialData();

        Events.$on('resources-selected', event => {
            this.updatedResources.set(event.id, event.selected);
            this.updateSelectedResources();
        });

        Events.$on('date-changed', newDate => {
            this.date = newDate;
        });
    }
});