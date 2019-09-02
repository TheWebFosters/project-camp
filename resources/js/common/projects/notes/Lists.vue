<template>
    <div class="component-wrap">
        <!-- Add notes -->
        <FormAdd ref="noteAdd"></FormAdd>
        <!-- view notes -->
        <NoteShow ref="noteShow"></NoteShow>
        <!-- edit contact -->
        <NoteEdit ref="noteEdit"></NoteEdit>

        <v-card>
            <v-card-title>
                <div>
                    <div class="headline">
                        {{ trans('messages.documents_notes') }}
                    </div>
                </div>
                <v-spacer></v-spacer>
                <v-btn class="primary lighten-1" dark @click="create">
                    {{ trans('messages.add') }}
                    <v-icon right dark>add</v-icon>
                </v-btn>
            </v-card-title>
            <v-divider></v-divider>
            <!-- v-datatable -->
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
                                <v-list-tile @click="view(props.item.id)">
                                    <v-list-tile-title>
                                        <v-icon small class="mr-2"> visibility </v-icon>
                                        {{ trans('messages.view') }}
                                    </v-list-tile-title>
                                </v-list-tile>

                                <v-list-tile @click="edit(props.item.id)">
                                    <v-list-tile-title>
                                        <v-icon small class="mr-2"> edit </v-icon>
                                        {{ trans('messages.edit') }}
                                    </v-list-tile-title>
                                </v-list-tile>

                                <v-list-tile @click="deleteNote(props.item)">
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
import FormAdd from './Add';
import NoteShow from './View';
import NoteEdit from './Edit';
export default {
    components: {
        FormAdd,
        NoteShow,
        NoteEdit,
    },
    data() {
        const self = this;
        return {
            projectId: null,
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
                self.getProjectNotes();
            },
        },
    },
    created() {
        const self = this;
        self.projectId = self.$route.params.id;
        self.$eventBus.$on('updateProjectNotesTable', data => {
            self.getProjectNotes();
        });
    },
    beforeDestroy() {
        const self = this;
        self.$eventBus.$off('updateProjectNotesTable');
    },
    methods: {
        getProjectNotes() {
            const self = this;

            self.loading = true;

            const { sortBy, descending, page, rowsPerPage } = self.pagination;

            axios
                .get('/project-notes', {
                    params: {
                        sort_by: sortBy,
                        descending: descending,
                        page: page,
                        rowsPerPage: rowsPerPage,
                        project_id: self.projectId,
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
            var data = { projectId: this.projectId };
            this.$refs.noteAdd.add(data);
        },
        view(id) {
            var data = { id: id, projectId: this.projectId };
            this.$refs.noteShow.view(data);
        },
        edit(id) {
            var data = { id: id, projectId: this.projectId };
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
                        .delete('/project-notes/' + item.id, {
                            params: { project_id: self.projectId },
                        })
                        .then(function(response) {
                            self.$store.commit('showSnackbar', {
                                message: response.data.msg,
                                color: response.data.success,
                            });

                            if (response.data.success === true) {
                                self.getProjectNotes();
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
