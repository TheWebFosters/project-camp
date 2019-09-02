<template>
    <div class="component-wrap">
        <!-- create contact -->
        <ContactFormAdd ref="contactAdd"></ContactFormAdd>
        <!-- view contact -->
        <ContactShow ref="contactShow"></ContactShow>
        <!-- edit contact -->
        <ContactFormEdit ref="contactEdit"></ContactFormEdit>
        <v-card>
            <v-card-title row wrap>
                <div>
                    <div class="headline">
                        {{ trans('messages.all_contacts') }}
                    </div>
                </div>
                <v-spacer></v-spacer>
                <v-btn v-if="$can('contact.create')" class="primary lighten-1" dark @click="create">
                    {{ trans('messages.new_contact') }}
                    <v-icon right dark>add</v-icon>
                </v-btn>
            </v-card-title>
            <v-divider></v-divider>
            <!-- datatable -->
            <v-data-table
                :headers="headers"
                :pagination.sync="pagination"
                :total-items="total_items"
                :loading="loading"
                :items="items"
                class="elevation-3"
            >
                <template slot="headerCell" slot-scope="props">
                    <span v-if="props.header.value == 'name'">
                        <v-icon>person</v-icon> {{ props.header.text }}
                    </span>
                    <span v-else-if="props.header.value == 'email'">
                        <v-icon>email</v-icon> {{ props.header.text }}
                    </span>
                    <span v-else>{{ props.header.text }}</span>
                </template>

                <template slot="items" slot-scope="props">
                    <td>
                        <v-menu>
                            <v-btn icon slot="activator"> <v-icon>more_vert</v-icon> </v-btn>
                            <v-list>
                                <v-list-tile
                                    v-if="$can('contact.view')"
                                    @click="view(props.item.id)"
                                >
                                    <v-list-tile-title>
                                        <v-icon small class="mr-2"> visibility </v-icon>
                                        {{ trans('messages.view') }}
                                    </v-list-tile-title>
                                </v-list-tile>

                                <v-list-tile
                                    v-if="$can('contact.edit')"
                                    @click="edit(props.item.id)"
                                >
                                    <v-list-tile-title>
                                        <v-icon small class="mr-2"> edit </v-icon>
                                        {{ trans('messages.edit') }}
                                    </v-list-tile-title>
                                </v-list-tile>

                                <v-list-tile
                                    v-if="$can('contact.delete')"
                                    @click="deleteContact(props.item)"
                                >
                                    <v-list-tile-title>
                                        <v-icon small class="mr-2"> delete_forever </v-icon>
                                        {{ trans('messages.delete') }}
                                    </v-list-tile-title>
                                </v-list-tile>
                            </v-list>
                        </v-menu>
                    </td>
                    <td>{{ props.item.name }}</td>
                    <td>{{ props.item.email }}</td>
                </template>
            </v-data-table>
            <!-- /datatable -->
        </v-card>
    </div>
</template>
<script>
import ContactFormAdd from './Add';
import ContactShow from './Show';
import ContactFormEdit from './Edit';
export default {
    components: {
        ContactFormAdd,
        ContactShow,
        ContactFormEdit,
    },
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
                { text: self.trans('messages.name'), value: 'name', align: 'left', sortable: true },
                {
                    text: self.trans('messages.email'),
                    value: 'email',
                    align: 'left',
                    sortable: true,
                },
            ],
            items: [],
            customer_id: '',
        };
    },
    watch: {
        pagination: {
            handler() {
                this.getDataFromApi();
            },
        },
    },
    mounted() {
        const self = this;
        self.customer_id = self.$route.params.id;
        self.$eventBus.$on('updateContactTable', data => {
            self.getDataFromApi();
        });
    },
    beforeDestroy() {
        const self = this;
        self.$eventBus.$off('updateContactTable');
    },
    methods: {
        create() {
            const self = this;
            self.$refs.contactAdd.create();
        },
        getDataFromApi() {
            const self = this;
            self.loading = true;

            const { sortBy, descending, page, rowsPerPage } = self.pagination;

            axios
                .get('/admin/contacts', {
                    params: {
                        sort_by: sortBy,
                        descending: descending,
                        page: page,
                        rowsPerPage: rowsPerPage,
                        customer_id: self.customer_id,
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
            var data = { id: id, customer_id: self.customer_id };
            self.$refs.contactShow.view(data);
        },
        edit(id) {
            const self = this;
            var data = { id: id, customer_id: self.customer_id };
            self.$refs.contactEdit.edit(data);
        },
        deleteContact(item) {
            const self = this;
            self.$store.commit('showDialog', {
                type: 'confirm',
                icon: 'warning',
                title: self.trans('messages.are_you_sure'),
                message: self.trans('messages.you_cant_restore_it'),
                okCb: () => {
                    axios
                        .delete('/admin/contacts/' + item.id, {
                            params: { customer_id: self.customer_id },
                        })
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
