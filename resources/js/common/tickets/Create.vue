<template>
    <v-layout row justify-center>
        <CategoryAdd ref="categoryAdd"></CategoryAdd>
        <v-dialog v-model="dialog">
            <v-card>
                <v-card-title>
                    <v-icon>live_help</v-icon>
                    <span class="headline">
                        {{ trans('messages.create_ticket') }}
                    </span>
                    <v-spacer></v-spacer>
                    <v-btn flat icon @click="dialog = false">
                        <v-icon>clear</v-icon>
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <v-container grid-list-md>
                        <v-layout row wrap>
                            <v-flex xs12 sm6 md6>
                                <v-text-field
                                    v-model="form_fields.title"
                                    :label="trans('messages.title')"
                                    v-validate="'required'"
                                    data-vv-name="title"
                                    :data-vv-as="trans('messages.title')"
                                    :error-messages="errors.collect('title')"
                                    required
                                ></v-text-field>
                            </v-flex>
                            <v-flex xs11 sm5 md5>
                                <v-autocomplete
                                    item-text="name"
                                    item-value="id"
                                    :items="ticket_types"
                                    v-model="form_fields.category_id"
                                    :label="trans('messages.ticket_type')"
                                    v-validate="'required'"
                                    data-vv-name="ticket_type"
                                    :data-vv-as="trans('messages.ticket_type')"
                                    :error-messages="errors.collect('ticket_type')"
                                    required
                                ></v-autocomplete>
                            </v-flex>
                            <v-flex xs1 sm1 md1>
                                <v-btn
                                    @click="createCategory"
                                    small
                                    color="primary"
                                    fab
                                    dark
                                    v-if="$hasRole('employee')"
                                >
                                    <v-icon>add</v-icon>
                                </v-btn>
                            </v-flex>
                        </v-layout>
                        <v-layout row>
                            <v-flex xs12 sm12 md12>
                                <v-textarea
                                    rows="4"
                                    v-model="form_fields.description"
                                    :label="trans('messages.description')"
                                    v-validate="'required'"
                                    data-vv-name="description"
                                    :data-vv-as="trans('messages.description')"
                                    :error-messages="errors.collect('description')"
                                    required
                                ></v-textarea>
                            </v-flex>
                        </v-layout>
                        <v-layout row>
                            <v-flex xs12 sm6 md6 v-if="$hasRole('employee')">
                                <v-autocomplete
                                    item-text="value"
                                    item-value="key"
                                    :items="statuses"
                                    v-model="form_fields.status"
                                    :label="trans('messages.status')"
                                    v-validate="'required'"
                                    data-vv-name="status"
                                    :data-vv-as="trans('messages.status')"
                                    :error-messages="errors.collect('status')"
                                    required
                                ></v-autocomplete>
                            </v-flex>
                            <v-flex xs12 sm6 md6 v-if="$hasRole('employee')">
                                <v-autocomplete
                                    item-text="value"
                                    item-value="key"
                                    :items="priorities"
                                    v-model="form_fields.priority"
                                    :label="trans('messages.priority')"
                                ></v-autocomplete>
                            </v-flex>
                        </v-layout>
                        <v-layout row wrap>
                            <v-flex xs12 sm6 md6 v-if="$hasRole('employee')">
                                <v-autocomplete
                                    item-text="name"
                                    item-value="id"
                                    :items="employees"
                                    v-model="form_fields.assigned_to"
                                    :label="trans('messages.assigned_to')"
                                ></v-autocomplete>
                            </v-flex>
                            <v-flex xs12 sm6 md6 v-if="$hasRole('employee')">
                                <v-autocomplete
                                    item-text="company"
                                    item-value="id"
                                    :items="customers"
                                    v-model="form_fields.customer_id"
                                    :label="trans('messages.customer')"
                                    v-validate="'required'"
                                    data-vv-name="customer"
                                    :data-vv-as="trans('messages.customer')"
                                    :error-messages="errors.collect('customer')"
                                    required
                                ></v-autocomplete>
                            </v-flex>
                        </v-layout>
                    </v-container>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="green darken-1" flat @click="dialog = false">
                        {{ trans('messages.close') }}
                    </v-btn>
                    <v-btn color="success" @click="store" :loading="loading" :disabled="loading">
                        {{ trans('messages.save') }}
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-layout>
</template>
<script>
import CategoryAdd from '../../common/projects/category/Add';
export default {
    components: {
        CategoryAdd,
    },
    data() {
        return {
            dialog: false,
            loading: false,
            form_fields: [],
            statuses: [],
            employees: [],
            ticket_types: [],
            customers: [],
            priorities: [],
        };
    },
    created() {
        const self = this;
        self.$eventBus.$on('updateCategoryList', data => {
            self.ticket_types.push(data);
            self.form_fields.category_id = data.id;
        });
    },
    beforeDestroy() {
        const self = this;
        self.$eventBus.$off('updateCategoryList');
    },
    methods: {
        create() {
            const self = this;
            self.form_fields = [];
            self.$validator.reset();
            axios
                .get('/tickets/create')
                .then(function(response) {
                    self.statuses = response.data.statuses;
                    self.ticket_types = response.data.ticket_types;
                    self.priorities = response.data.priority;
                    self.employees = response.data.employees;
                    self.customers = response.data.customers;
                    self.dialog = true;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        createCategory() {
            var data = { type: 'tickets' };
            this.$refs.categoryAdd.create(data);
        },
        store() {
            const self = this;
            let data = _.pick(self.form_fields, [
                'title',
                'category_id',
                'priority',
                'description',
                'status',
                'assigned_to',
                'customer_id',
            ]);

            self.$validator.validateAll().then(result => {
                if (result == true) {
                    self.loading = true;
                    axios
                        .post('/tickets', data)
                        .then(function(response) {
                            self.loading = false;
                            self.$store.commit('showSnackbar', {
                                message: response.data.msg,
                                color: response.data.success,
                            });

                            if (response.data.success === true) {
                                self.dialog = false;
                                self.$eventBus.$emit('updateTicketsTable');
                            }
                        })
                        .catch(function(error) {
                            console.log(error);
                        });
                }
            });
        },
    },
};
</script>
