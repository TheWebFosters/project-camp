<template>
    <div class="component-wrap">
        <!-- reminder add -->
        <ReminderAdd ref="reminderAdd"></ReminderAdd>
        <ReminderEdit ref="reminderEdit"></ReminderEdit>
        <ReminderView ref="reminderView"></ReminderView>
        <!-- v-card -->
        <v-card>
            <v-card-title>
                <div>
                    <div class="headline">
                        {{ trans('messages.all_reminders') }}
                    </div>
                </div>
                <v-spacer></v-spacer>
                <v-btn class="primary lighten-1" dark @click="create">
                    {{ trans('messages.add') }}
                    <v-icon right dark>add</v-icon>
                </v-btn>
            </v-card-title>
            <v-divider></v-divider>
            <!-- /v-card -->
            <!-- datatable -->
            <v-data-table
                :headers="headers"
                :pagination.sync="pagination"
                :total-items="total_items"
                :loading="loading"
                :items="items"
                class="elevation-3"
            >
                <template slot="items" slot-scope="props">
                    <td>
                        <v-menu>
                            <v-btn icon slot="activator"> <v-icon>more_vert</v-icon> </v-btn>
                            <v-list>
                                <v-list-tile @click="viewReminder(props.item)">
                                    <v-list-tile-title>
                                        <v-icon small class="mr-2"> visibility </v-icon>
                                        {{ trans('messages.view') }}
                                    </v-list-tile-title>
                                </v-list-tile>

                                <v-list-tile @click="edit(props.item)">
                                    <v-list-tile-title>
                                        <v-icon small class="mr-2"> edit </v-icon>
                                        {{ trans('messages.edit') }}
                                    </v-list-tile-title>
                                </v-list-tile>

                                <v-list-tile @click="deleteReminder(props.item)">
                                    <v-list-tile-title>
                                        <v-icon small class="mr-2"> delete_forever </v-icon>
                                        {{ trans('messages.delete') }}
                                    </v-list-tile-title>
                                </v-list-tile>
                            </v-list>
                        </v-menu>
                    </td>
                    <td>{{ props.item.remind_for }}</td>
                    <td>{{ props.item.remind_on | formatDateTime }}</td>
                    <td>{{ props.item.remind_to }}</td>
                </template>
            </v-data-table>
            <!-- /datatable -->
        </v-card>
    </div>
</template>
<script>
import ReminderAdd from './Add';
import ReminderEdit from './Edit';
import ReminderView from './View';
export default {
    components: {
        ReminderAdd,
        ReminderEdit,
        ReminderView,
    },
    data() {
        const self = this;
        return {
            lead_id: null,
            total_items: 0,
            loading: true,
            pagination: { totalItems: 0, sortBy: 'reminders.created_at', descending: true },
            headers: [
                {
                    text: self.trans('messages.action'),
                    value: false,
                    align: 'left',
                    sortable: false,
                },
                {
                    text: self.trans('messages.remind_for'),
                    value: 'remind_for',
                    align: 'left',
                    sortable: true,
                },
                {
                    text: self.trans('messages.remind_on'),
                    value: 'remind_on',
                    align: 'left',
                    sortable: false,
                },
                {
                    text: self.trans('messages.remind_to'),
                    value: 'remind_to',
                    align: 'left',
                    sortable: true,
                },
            ],
            items: [],
        };
    },
    watch: {
        pagination: {
            handler() {
                this.getLeadRemindersFromApi();
            },
        },
    },
    mounted() {
        const self = this;
        self.lead_id = self.$route.params.id;
        self.$eventBus.$on('updateReminderTable', data => {
            self.getLeadRemindersFromApi();
        });
    },
    beforeDestroy() {
        const self = this;
        self.$eventBus.$off('updateReminderTable');
    },
    methods: {
        create() {
            var data = { lead_id: this.lead_id };
            this.$refs.reminderAdd.create(data);
        },
        getLeadRemindersFromApi() {
            const self = this;
            const { sortBy, descending, page, rowsPerPage } = self.pagination;
            axios
                .get('/admin/reminders', {
                    params: {
                        sort_by: sortBy,
                        descending: descending,
                        page: page,
                        rowsPerPage: rowsPerPage,
                        lead_id: self.lead_id,
                    },
                })
                .then(function(response) {
                    self.total_items = response.data.total;
                    self.items = response.data.data;
                    self.loading = false;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        edit(item) {
            var data = { lead_id: this.lead_id, reminder_id: item.id };
            this.$refs.reminderEdit.edit(data);
        },
        deleteReminder(item) {
            const self = this;
            self.$store.commit('showDialog', {
                type: 'confirm',
                icon: 'warning',
                title: self.trans('messages.are_you_sure'),
                message: self.trans('messages.you_cant_restore_it'),
                okCb: () => {
                    axios
                        .delete('/admin/reminders/' + item.id, {
                            params: { lead_id: self.lead_id },
                        })
                        .then(function(response) {
                            self.$store.commit('showSnackbar', {
                                message: response.data.msg,
                                color: response.data.success,
                            });

                            if (response.data.success === true) {
                                self.getLeadRemindersFromApi();
                            }
                        })
                        .catch(function(error) {
                            console.log(error);
                        });
                },
                cancelCb: () => {
                    console.log('CANCEL');
                },
            });
        },
        viewReminder(item) {
            var data = { lead_id: this.lead_id, reminder_id: item.id };
            this.$refs.reminderView.show(data);
        },
    },
};
</script>
