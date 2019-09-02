<template>
    <div class="component-wrap">
        <NoteAdd ref="noteAdd"></NoteAdd>
        <NoteEdit ref="noteEdit"></NoteEdit>
        <NoteView ref="noteView"></NoteView>
        <!-- v-card -->
        <v-card>
            <v-card-title>
                <div>
                    <div class="headline">
                        {{ trans('messages.documents_notes') }}
                    </div>
                </div>
                <v-spacer></v-spacer>
                <v-btn
                    v-if="$can('leadNote.create')"
                    class="primary lighten-1"
                    dark
                    @click="create"
                >
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
                                <v-list-tile
                                    v-if="$can('leadNote.view')"
                                    @click="view(props.item.id)"
                                >
                                    <v-list-tile-title>
                                        <v-icon small class="mr-2"> visibility </v-icon>
                                        {{ trans('messages.view') }}
                                    </v-list-tile-title>
                                </v-list-tile>

                                <v-list-tile
                                    v-if="$can('leadNote.edit')"
                                    @click="edit(props.item.id)"
                                >
                                    <v-list-tile-title>
                                        <v-icon small class="mr-2"> edit </v-icon>
                                        {{ trans('messages.edit') }}
                                    </v-list-tile-title>
                                </v-list-tile>

                                <v-list-tile
                                    v-if="$can('leadNote.delete')"
                                    @click="deleteNote(props.item)"
                                >
                                    <v-list-tile-title>
                                        <v-icon small class="mr-2"> delete_forever </v-icon>
                                        {{ trans('messages.delete') }}
                                    </v-list-tile-title>
                                </v-list-tile>
                            </v-list>
                        </v-menu>
                    </td>
                    <td>{{ props.item.heading }}</td>
                    <td>{{ props.item.user.name }}</td>
                    <td>{{ props.item.created_at | formatDateTime }}</td>
                    <td>{{ props.item.updated_at | formatDateTime }}</td>
                </template>
            </v-data-table>
            <!-- /datatable -->
        </v-card>
    </div>
</template>
<script>
import NoteAdd from './Add';
import NoteEdit from './Edit';
import NoteView from './View';
export default {
    components: {
        NoteAdd,
        NoteEdit,
        NoteView,
    },
    data() {
        const self = this;
        return {
            lead_id: null,
            total_items: 0,
            loading: true,
            pagination: { totalItems: 0, sortBy: 'created_at', descending: true },
            headers: [
                {
                    text: self.trans('messages.action'),
                    value: false,
                    align: 'left',
                    sortable: false,
                },
                {
                    text: self.trans('messages.heading'),
                    value: 'heading',
                    align: 'left',
                    sortable: true,
                },
                {
                    text: self.trans('messages.added_by'),
                    value: 'user',
                    align: 'left',
                    sortable: false,
                },
                {
                    text: self.trans('messages.created_at'),
                    value: 'created_at',
                    align: 'left',
                    sortable: true,
                },
                {
                    text: self.trans('messages.updated_at'),
                    value: 'updated_at',
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
                this.getLeadNotesFromApi();
            },
        },
    },
    mounted() {
        const self = this;
        self.lead_id = self.$route.params.id;
        self.$eventBus.$on('updateLeadNotesTable', data => {
            self.getLeadNotesFromApi();
        });
    },
    beforeDestroy() {
        const self = this;
        self.$eventBus.$off('updateLeadNotesTable');
    },
    methods: {
        getLeadNotesFromApi() {
            const self = this;
            const { sortBy, descending, page, rowsPerPage } = self.pagination;
            axios
                .get('/admin/lead-notes', {
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
        create() {
            var data = { lead_id: this.lead_id };
            this.$refs.noteAdd.create(data);
        },
        view(id) {
            var data = { id: id, lead_id: this.lead_id };
            this.$refs.noteView.view(data);
        },
        edit(id) {
            var data = { id: id, lead_id: this.lead_id };
            this.$refs.noteEdit.edit(data);
        },
        deleteNote(item) {
            const self = this;
            self.$store.commit('showDialog', {
                type: 'confirm',
                icon: 'warning',
                title: self.trans('messages.are_you_sure'),
                message: self.trans('messages.you_cant_restore_it'),
                okCb: () => {
                    axios
                        .delete('/admin/lead-notes/' + item.id, {
                            params: { lead_id: self.lead_id },
                        })
                        .then(function(response) {
                            self.$store.commit('showSnackbar', {
                                message: response.data.msg,
                                color: response.data.success,
                            });

                            if (response.data.success === true) {
                                self.getLeadNotesFromApi();
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
    },
};
</script>
