<template>
    <div class="component-wrap">
        <!-- create note & document -->
        <NoteAndDocumentAdd ref="noteAndDocumentAdd"></NoteAndDocumentAdd>
        <!-- view note & document -->
        <NoteAndDocumentView ref="noteAndDocumentView"></NoteAndDocumentView>
        <!-- edit note & document -->
        <NoteAndDocumentEdit ref="noteAndDocumentEdit"></NoteAndDocumentEdit>
        <!-- v-datatable -->
        <v-card>
            <v-card-title>
                <div>
                    <div class="headline">
                        {{ trans('messages.documents_notes') }}
                    </div>
                </div>
                <v-spacer></v-spacer>
                <v-btn
                    v-if="$can('employeeNote.create')"
                    class="primary lighten-1"
                    dark
                    @click="create"
                >
                    {{ trans('messages.add') }}
                    <v-icon right dark>add</v-icon>
                </v-btn>
            </v-card-title>
            <v-divider></v-divider>
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
                                    v-if="$can('employeeNote.view')"
                                    @click="view(props.item.id)"
                                >
                                    <v-list-tile-title>
                                        <v-icon small class="mr-2"> visibility </v-icon>
                                        {{ trans('messages.view') }}
                                    </v-list-tile-title>
                                </v-list-tile>

                                <v-list-tile
                                    v-if="$can('employeeNote.edit')"
                                    @click="edit(props.item.id)"
                                >
                                    <v-list-tile-title>
                                        <v-icon small class="mr-2"> edit </v-icon>
                                        {{ trans('messages.edit') }}
                                    </v-list-tile-title>
                                </v-list-tile>

                                <v-list-tile
                                    v-if="$can('employeeNote.delete')"
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
                    <td>{{ props.item.created_at | formatDate }}</td>
                    <td>{{ props.item.updated_at | formatDate }}</td>
                </template>
            </v-data-table>
        </v-card>
    </div>
</template>
<script>
import NoteAndDocumentAdd from '../notes/Add';
import NoteAndDocumentView from '../notes/View';
import NoteAndDocumentEdit from '../notes/Edit';
export default {
    components: {
        NoteAndDocumentAdd,
        NoteAndDocumentView,
        NoteAndDocumentEdit,
    },
    data() {
        const self = this;
        return {
            employeeId: null,
            total_items: 0,
            loading: true,
            pagination: { totalItems: 0, sortBy: 'created_at', descending: true },
            items: [],
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
        };
    },
    watch: {
        pagination: {
            handler() {
                const self = this;
                self.getEmployeeNotes();
            },
        },
    },
    created() {
        const self = this;
        self.employeeId = self.$route.params.id;
        self.$eventBus.$on('updateEmployeeNotesTable', data => {
            self.getEmployeeNotes();
        });
    },
    beforeDestroy() {
        const self = this;
        self.$eventBus.$off('updateEmployeeNotesTable');
    },
    methods: {
        create() {
            const self = this;
            self.$refs.noteAndDocumentAdd.create(self.employeeId);
        },
        getEmployeeNotes() {
            const self = this;

            self.loading = true;

            const { sortBy, descending, page, rowsPerPage } = self.pagination;

            axios
                .get('/admin/employee-notes', {
                    params: {
                        sort_by: sortBy,
                        descending: descending,
                        page: page,
                        rowsPerPage: rowsPerPage,
                        user_id: self.employeeId,
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
        view(id) {
            const self = this;
            var data = { user_id: self.employeeId, note_id: id };
            self.$refs.noteAndDocumentView.show(data);
        },
        edit(id) {
            const self = this;
            var data = { user_id: self.employeeId, note_id: id };
            self.$refs.noteAndDocumentEdit.edit(data);
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
                        .delete('/admin/employee-notes/' + item.id, {
                            params: { user_id: self.employeeId },
                        })
                        .then(function(response) {
                            self.$store.commit('showSnackbar', {
                                message: response.data.msg,
                                color: response.data.success,
                            });

                            if (response.data.success === true) {
                                self.getEmployeeNotes();
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
