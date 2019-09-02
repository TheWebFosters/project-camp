<template>
    <v-layout row justify-center>
        <!-- category Add -->
        <CategoryAdd ref="categoryAdd"></CategoryAdd>
        <v-dialog v-model="dialog">
            <v-card>
                <v-card-title>
                    <v-icon medium>assessment</v-icon>
                    <span class="headline">
                        {{ trans('messages.create_project') }}
                    </span>
                    <v-spacer></v-spacer>
                    <v-icon @click="dialog = false">clear</v-icon><br />
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <v-container grid-list-md>
                        <v-layout row wrap>
                            <v-flex xs12 md6>
                                <v-text-field
                                    v-model="project.name"
                                    :label="trans('messages.name')"
                                    v-validate="'required'"
                                    data-vv-name="name"
                                    :data-vv-as="trans('messages.name')"
                                    :error-messages="errors.collect('name')"
                                    required
                                >
                                </v-text-field>
                            </v-flex>
                            <v-flex xs12 md5>
                                <v-autocomplete
                                    item-text="name"
                                    item-value="id"
                                    :items="categories"
                                    v-model="category_id"
                                    multiple
                                    :label="trans('messages.category')"
                                    v-validate="'required'"
                                    data-vv-name="category"
                                    :data-vv-as="trans('messages.category')"
                                    :error-messages="errors.collect('category')"
                                    required
                                ></v-autocomplete>
                            </v-flex>
                            <v-flex md1>
                                <v-btn @click="createCategory" small color="primary" fab dark>
                                    <v-icon>add</v-icon>
                                </v-btn>
                            </v-flex>
                        </v-layout>
                        <v-layout row wrap>
                            <v-flex xs12 sm12 md12>
                                {{ trans('messages.description') }}
                                <quill-editor v-model="project.description"> </quill-editor>
                            </v-flex>
                        </v-layout>
                        <v-layout wrap>
                            <v-flex xs12 md4>
                                <v-autocomplete
                                    item-text="company"
                                    item-value="id"
                                    :items="customers"
                                    v-model="project.customer_id"
                                    :label="trans('messages.customer')"
                                ></v-autocomplete>
                            </v-flex>
                            <!-- <v-flex xs12 md4>
                                <v-select
                                    item-text="value"
                                    item-value="key"
                                    :items="billing_types"
                                    v-model="project.billing_type"
                                    :label="trans('messages.billing_type')"
                                   
                                    v-validate="'required'"
                                    data-vv-name="billing_type"
                                    :data-vv-as="trans('messages.billing_type')"
                                    :error-messages="errors.collect('billing_type')"
                                    required
                                ></v-select>
                            </v-flex>

                            <v-flex xs12 md4>
                                <v-text-field
                                   
                                    v-model="project.total_rate"
                                    :label="trans('messages.total_rate')"
                                >
                                </v-text-field>
                            </v-flex>
                            
                            <v-flex xs12 md4>
                                <v-text-field
                                   
                                    v-model="project.price_per_hours"
                                    :label="trans('messages.price_per_hours')"
                                >
                                </v-text-field>
                            </v-flex>
                            <v-flex xs12 md4>
                                <v-text-field
                                   
                                    v-model="project.estimated_hours"
                                    :label="trans('messages.estimated_hours')"
                                >
                                </v-text-field>
                            </v-flex>

                            <v-flex xs12 md4>
                                <v-text-field
                                   
                                    v-model="project.estimated_cost"
                                    :label="trans('messages.estimated_cost')"
                                >
                                </v-text-field>
                            </v-flex> -->

                            <v-flex xs12 md4>
                                <v-select
                                    item-text="value"
                                    item-value="key"
                                    :items="status"
                                    v-model="project.status"
                                    :label="trans('messages.status')"
                                    v-validate="'required'"
                                    data-vv-name="status"
                                    :data-vv-as="trans('messages.status')"
                                    :error-messages="errors.collect('status')"
                                    required
                                ></v-select>
                            </v-flex>
                            <v-flex xs12 md4>
                                <v-autocomplete
                                    item-text="name"
                                    item-value="id"
                                    :items="users"
                                    v-model="project.lead_id"
                                    :label="trans('messages.lead')"
                                    v-validate="'required'"
                                    data-vv-name="lead"
                                    :data-vv-as="trans('messages.lead')"
                                    :error-messages="errors.collect('lead')"
                                    required
                                >
                                    <Popover
                                        slot="append"
                                        :helptext="trans('messages.project_lead_tooltip')"
                                    >
                                    </Popover>
                                </v-autocomplete>
                            </v-flex>

                            <v-flex xs12 md4>
                                <div class="v-input v-text-field theme--light">
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <div class="v-text-field__slot">
                                                <label
                                                    aria-hidden="true"
                                                    class="v-label v-label--active theme--light flat_picker_label"
                                                >
                                                    {{ trans('messages.start_date') }}
                                                </label>
                                                <flat-pickr
                                                    v-model="start_date"
                                                    v-validate="'required'"
                                                    name="start_date"
                                                    required
                                                    :config="flatPickerDate()"
                                                    :data-vv-as="trans('messages.start_date')"
                                                ></flat-pickr>
                                            </div>
                                        </div>
                                        <div class="v-messages theme--light error--text">
                                            {{ errors.first('start_date') }}
                                        </div>
                                    </div>
                                </div>
                            </v-flex>

                            <v-flex xs12 md4>
                                <div class="v-input v-text-field theme--light">
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <div class="v-text-field__slot">
                                                <label
                                                    aria-hidden="true"
                                                    class="v-label v-label--active theme--light flat_picker_label"
                                                >
                                                    {{ trans('messages.end_date') }}
                                                </label>
                                                <flat-pickr
                                                    v-model="end_date"
                                                    v-validate="'required'"
                                                    name="end_date"
                                                    required
                                                    :config="flatPickerDate()"
                                                    :data-vv-as="trans('messages.end_date')"
                                                ></flat-pickr>
                                            </div>
                                        </div>
                                        <div class="v-messages theme--light error--text">
                                            {{ errors.first('end_date') }}
                                        </div>
                                    </div>
                                </div>
                            </v-flex>
                            <v-flex xs12 md4>
                                <v-autocomplete
                                    item-text="name"
                                    item-value="id"
                                    :items="users"
                                    v-model="project.user_id"
                                    :label="trans('messages.members')"
                                    chips
                                    multiple
                                    v-validate="'required'"
                                    data-vv-name="members"
                                    :data-vv-as="trans('messages.members')"
                                    :error-messages="errors.collect('members')"
                                    required
                                >
                                    <Popover
                                        slot="append"
                                        :helptext="trans('messages.project_member_tooltip')"
                                    >
                                    </Popover>
                                </v-autocomplete>
                            </v-flex>
                        </v-layout>
                    </v-container>
                </v-card-text>
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
import CategoryAdd from '../category/Add';
import Popover from '../../../admin/popover/Popover';
export default {
    components: {
        CategoryAdd,
        Popover,
    },
    data() {
        return {
            customers: [],
            billing_types: [],
            status: [],
            users: [],
            project: [],
            dialog: false,
            start_date: null,
            end_date: null,
            categories: [],
            loading: false,
            category_id: [],
        };
    },
    mounted() {
        const self = this;
        self.$eventBus.$on('updateCategoryList', data => {
            self.categories.push(data);
            self.category_id.push(data.id);
        });
    },
    beforeDestroy() {
        const self = this;
        self.$eventBus.$off('updateCategoryList');
    },
    methods: {
        create() {
            const self = this;
            self.$validator.reset();
            self.project = [];
            self.category_id = [];
            (self.start_date = null),
                (self.end_date = null),
                axios
                    .get('/projects/create')
                    .then(function(response) {
                        self.customers = response.data.customers;
                        self.billing_types = response.data.billingTypes;
                        self.status = response.data.status;
                        self.users = response.data.users;
                        self.categories = response.data.categories;
                        self.dialog = true;
                    })
                    .catch(function(error) {
                        console.log(error);
                    });
        },
        store() {
            const self = this;
            let data = {
                name: self.project.name,
                category_id: self.category_id,
                customer_id: self.project.customer_id,
                billing_type: self.project.billing_type,
                total_rate: self.project.total_rate,
                price_per_hours: self.project.price_per_hours,
                estimated_hours: self.project.estimated_hours,
                estimated_cost: self.project.estimated_cost,
                status: self.project.status,
                lead_id: self.project.lead_id,
                description: self.project.description,
                user_id: self.project.user_id,
                start_date: self.start_date,
                end_date: self.end_date,
            };

            self.$validator.validateAll().then(result => {
                if (result == true) {
                    self.loading = true;
                    axios
                        .post('/projects', data)
                        .then(function(response) {
                            self.loading = false;
                            self.dialog = false;
                            self.$store.commit('showSnackbar', {
                                message: response.data.msg,
                                color: response.data.success,
                            });
                            if (response.data.success === true) {
                                self.$eventBus.$emit('updateProjectTable');
                            }
                        })
                        .catch(function(error) {
                            console.log(error);
                        });
                }
            });
        },
        createCategory() {
            const self = this;
            var data = { type: 'projects' };
            self.$refs.categoryAdd.create(data);
        },
    },
};
</script>
