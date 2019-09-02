<template>
    <div class="component-wrap">
        <v-card>
            <v-card-title>
                <div>
                    <div class="headline">
                        {{ trans('messages.all_knowledgebases') }}
                    </div>
                </div>
                <v-spacer></v-spacer>
                <v-btn
                    v-if="$can('knowledge_base.create')"
                    class="primary lighten-1"
                    @click="$router.push({ name: 'Knowledgebase.create' })"
                    dark
                >
                    {{ trans('messages.add') }}
                    <v-icon right dark>add</v-icon>
                </v-btn>
            </v-card-title>
            <v-divider></v-divider>
            <!-- Data table -->
            <v-data-table
                :headers="headers"
                :pagination.sync="pagination"
                :total-items="total_items"
                :loading="loading"
                :items="items"
                class="elevation-3"
            >
                <template slot="headerCell" slot-scope="props">
                    <span v-if="props.header.value == 'title'">
                        <v-icon>title</v-icon> {{ props.header.text }}
                    </span>
                    <span v-else-if="props.header.value == 'is_active'">
                        <v-icon>vpn_key</v-icon> {{ props.header.text }}
                    </span>
                    <span v-else-if="props.header.value == 'knowledge_base_creator'">
                        <v-icon>person</v-icon> {{ props.header.text }}
                    </span>
                    <span v-else-if="props.header.value == 'created_at'">
                        <v-icon>date_range</v-icon> {{ props.header.text }}
                    </span>
                    <span v-else>{{ props.header.text }}</span>
                </template>

                <template slot="items" slot-scope="props">
                    <td>
                        <v-menu>
                            <v-btn icon slot="activator"> <v-icon>more_vert</v-icon> </v-btn>
                            <v-list>
                                <v-list-tile
                                    v-if="$can('knowledge_base.view')"
                                    @click="
                                        $router.push({
                                            name: 'Knowledgebase.view',
                                            params: { id: props.item.id },
                                        })
                                    "
                                >
                                    <v-list-tile-title>
                                        <v-icon small class="mr-2"> visibility </v-icon>
                                        {{ trans('messages.view') }}
                                    </v-list-tile-title>
                                </v-list-tile>

                                <v-list-tile
                                    v-if="$can('knowledge_base.edit')"
                                    @click="
                                        $router.push({
                                            name: 'Knowledgebase.edit',
                                            params: { id: props.item.id },
                                        })
                                    "
                                >
                                    <v-list-tile-title>
                                        <v-icon small class="mr-2"> edit </v-icon>
                                        {{ trans('messages.edit') }}
                                    </v-list-tile-title>
                                </v-list-tile>

                                <v-list-tile
                                    v-if="$can('knowledge_base.delete')"
                                    @click="deleteKnowladgeBase(props.item)"
                                >
                                    <v-list-tile-title>
                                        <v-icon small class="mr-2"> delete_forever </v-icon>
                                        {{ trans('messages.delete') }}
                                    </v-list-tile-title>
                                </v-list-tile>
                            </v-list>
                        </v-menu>
                    </td>
                    <td>{{ props.item.title }}</td>
                    <td>
                        <v-avatar outline>
                            <v-icon v-if="props.item.is_active != 0" class="green--text"
                                >check_circle</v-icon
                            >
                            <v-icon class="grey--text" v-else>error_outline</v-icon>
                        </v-avatar>
                    </td>
                    <td>{{ props.item.knowledge_base_creator }}</td>
                    <td>{{ props.item.created_at | formatDate }}</td>
                </template>
            </v-data-table>
            <!-- /Data table-->
        </v-card>
    </div>
</template>
<script>
export default {
    data() {
        const self = this;
        return {
            total_items: 0,
            loading: true,
            pagination: { totalItems: 0 },
            headers: [
                {
                    text: self.trans('messages.action'),
                    value: false,
                    align: 'left',
                    sortable: false,
                },
                {
                    text: self.trans('messages.title'),
                    value: 'title',
                    align: 'left',
                    sortable: true,
                },
                {
                    text: self.trans('messages.active'),
                    value: 'is_active',
                    align: 'left',
                    sortable: true,
                },
                {
                    text: self.trans('messages.added_by'),
                    value: 'knowledge_base_creator',
                    align: 'left',
                    sortable: true,
                },
                {
                    text: self.trans('messages.created_at'),
                    value: 'created_at',
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
                this.getDataFromApi();
            },
        },
    },
    methods: {
        getDataFromApi() {
            this.loading = true;

            const { sortBy, descending, page, rowsPerPage } = this.pagination;
            const self = this;
            var params = {
                sort_by: sortBy,
                descending: descending,
                page: page,
                rowsPerPage: rowsPerPage,
            };

            axios
                .get('/admin/knowledge-bases', {
                    params: params,
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
        deleteKnowladgeBase(item) {
            const self = this;
            self.$store.commit('showDialog', {
                type: 'confirm',
                icon: 'warning',
                title: self.trans('messages.are_you_sure'),
                message: self.trans('messages.you_cant_restore_it'),
                okCb: () => {
                    axios
                        .delete('/admin/knowledge-bases/' + item.id)
                        .then(function(response) {
                            self.$store.commit('showSnackbar', {
                                message: response.data.msg,
                                color: response.data.success,
                            });

                            if (response.data.success === true) {
                                self.getDataFromApi();
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
