<template>
    <v-layout row justify-center>
        <!-- category Add -->
        <CategoryAdd ref="categoryAdd"></CategoryAdd>
        <v-dialog v-model="dialog" width="950">
            <v-card>
                <v-card-title>
                    <v-icon medium>remove_circle_outline</v-icon>
                    <span class="headline">
                        {{ trans('messages.edit_expense') }}
                    </span>
                    <v-spacer></v-spacer>
                    <v-btn flat @click="dialog = false" icon>
                        <v-icon>clear</v-icon>
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <v-container grid-list-md>
                        <v-layout wrap>
                            <v-flex xs12 md3>
                                <v-autocomplete
                                    item-text="name"
                                    item-value="id"
                                    :items="categories"
                                    v-model="category_id"
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
                            <v-flex xs6 md4>
                                <v-text-field
                                    v-model="expense.ref_no"
                                    :label="trans('messages.ref_no')"
                                >
                                </v-text-field>
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
                                                    {{ trans('messages.date') }}
                                                </label>
                                                <flat-pickr
                                                    v-model="transaction_date"
                                                    v-validate="'required'"
                                                    name="date"
                                                    required
                                                    :config="flatPickerDate()"
                                                    :data-vv-as="trans('messages.date')"
                                                ></flat-pickr>
                                            </div>
                                        </div>
                                        <div class="v-messages theme--light error--text">
                                            {{ errors.first('date') }}
                                        </div>
                                    </div>
                                </div>
                            </v-flex>
                            <v-flex xs12 md3>
                                <div class="v-input v-text-field theme--light">
                                    <div class="v-input__control">
                                        <div class="v-input__slot">
                                            <div class="v-text-field__slot">
                                                <label
                                                    aria-hidden="true"
                                                    class="v-label v-label--active theme--light flat_picker_label"
                                                >
                                                    {{ trans('messages.due_date') }}
                                                </label>
                                                <flat-pickr
                                                    v-model="due_date"
                                                    name="due_date"
                                                    :config="flatPickerDate()"
                                                ></flat-pickr>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </v-flex>
                            <v-flex xs12 md3>
                                <v-text-field
                                    type="number"
                                    v-model="expense.total"
                                    :label="trans('messages.total')"
                                    v-validate="'required'"
                                    data-vv-name="total"
                                    :data-vv-as="trans('messages.total')"
                                    :error-messages="errors.collect('total')"
                                    required
                                ></v-text-field>
                            </v-flex>
                            <v-flex xs12 md3>
                                <v-autocomplete
                                    item-text="name"
                                    item-value="id"
                                    :items="employees"
                                    v-model="expense.expense_for"
                                    :label="trans('messages.expense_for')"
                                ></v-autocomplete>
                            </v-flex>
                            <v-flex xs12 md3>
                                <v-autocomplete
                                    item-text="name"
                                    item-value="id"
                                    :items="projects"
                                    v-model="expense.project_id"
                                    :label="trans('messages.project')"
                                ></v-autocomplete>
                            </v-flex>
                            <v-flex xs12 sm12 md12>
                                <v-textarea
                                    rows="3"
                                    v-model="expense.notes"
                                    :label="trans('messages.notes')"
                                ></v-textarea>
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
                        {{ trans('messages.update') }}
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
            categories: [],
            employees: [],
            projects: [],
            category_id: null,
            expense: [],
            transaction_date: '',
            due_date: '',
            loading: false,
            expense_id: null,
        };
    },
    mounted() {
        const self = this;
        self.$eventBus.$on('updateCategoryList', data => {
            self.categories.push(data);
            self.category_id = data.id;
        });
    },
    beforeDestroy() {
        const self = this;
        self.$eventBus.$off('updateCategoryList');
    },
    methods: {
        edit(expense_id) {
            const self = this;
            self.$validator.reset();
            self.employees = [];
            self.categories = [];
            self.projects = [];
            self.expense = [];
            self.expense_id = expense_id;
            axios
                .get('/expenses/' + expense_id + '/edit')
                .then(function(response) {
                    self.expense = response.data.transaction;
                    self.category_id = response.data.transaction.category_id;
                    self.transaction_date = response.data.transaction.transaction_date;
                    self.due_date = response.data.transaction.due_date;
                    self.employees = response.data.employees;
                    self.categories = response.data.categories;
                    self.projects = response.data.projects;
                    self.dialog = true;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        createCategory() {
            const self = this;
            var data = { type: 'expenses' };
            self.$refs.categoryAdd.create(data);
        },
        store() {
            const self = this;
            let data = _.pick(self.expense, [
                'ref_no',
                'total',
                'expense_for',
                'project_id',
                'notes',
            ]);
            data.category_id = self.category_id;
            data.transaction_date = self.transaction_date;
            data.due_date = self.due_date;

            self.$validator.validateAll().then(result => {
                if (result == true) {
                    self.loading = true;
                    axios
                        .put('/expenses/' + self.expense_id, data)
                        .then(function(response) {
                            self.loading = false;
                            self.$store.commit('showSnackbar', {
                                message: response.data.msg,
                                color: response.data.success,
                            });

                            if (response.data.success === true) {
                                self.dialog = false;
                                self.$eventBus.$emit('updateExpenseTable');
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
