<template>
    <div class="component-wrap">
        <v-card>
            <v-card-title>
                <v-icon medium>control_point</v-icon>
                <span class="headline">
                    {{ trans('messages.edit_role') }}
                </span>
            </v-card-title>
            <v-divider></v-divider>
            <v-card-text>
                <v-layout row wrap>
                    <v-flex xs12 sm6 md6>
                        <v-text-field
                            v-model="role_name"
                            :label="trans('messages.role_name')"
                            v-validate="'required'"
                            data-vv-name="role_name"
                            :data-vv-as="trans('messages.role_name')"
                            :error-messages="errors.collect('role_name')"
                            required
                        ></v-text-field>
                    </v-flex>
                </v-layout>
                <v-container grid-list-md>
                    <v-layout row>
                        <v-flex xs12 sm12 md12>
                            <v-icon small>
                                control_point_duplicate
                            </v-icon>
                            <span class="subheading">
                                {{ trans('messages.permissions') }}
                            </span>
                        </v-flex>
                    </v-layout>
                    <v-divider class="mt-1"></v-divider>

                    <v-layout row wrap class="mt-3">
                        <v-flex xs12 sm3 md3>
                            <h4>{{ trans('messages.employee') }}</h4>
                        </v-flex>
                        <v-flex xs12 sm5 md5>
                            <v-checkbox
                                v-model="permissions"
                                :label="trans('messages.add_employee')"
                                value="employee.create"
                            >
                            </v-checkbox>
                            <v-checkbox
                                v-model="permissions"
                                :label="trans('messages.view_employee')"
                                value="employee.view"
                            >
                            </v-checkbox>
                            <v-checkbox
                                v-model="permissions"
                                :label="trans('messages.edit_employee')"
                                value="employee.edit"
                            >
                            </v-checkbox>
                            <v-checkbox
                                v-model="permissions"
                                :label="trans('messages.delete_employee')"
                                value="employee.delete"
                            >
                            </v-checkbox>
                        </v-flex>
                        <v-flex xs12 sm4 md4>
                            <v-checkbox
                                v-model="permissions"
                                :label="trans('messages.add_employee_note')"
                                value="employeeNote.create"
                            >
                            </v-checkbox>
                            <v-checkbox
                                v-model="permissions"
                                :label="trans('messages.view_employee_note')"
                                value="employeeNote.view"
                            >
                            </v-checkbox>
                            <v-checkbox
                                v-model="permissions"
                                :label="trans('messages.edit_employee_note')"
                                value="employeeNote.edit"
                            >
                            </v-checkbox>
                            <v-checkbox
                                v-model="permissions"
                                :label="trans('messages.delete_employee_note')"
                                value="employeeNote.delete"
                            >
                            </v-checkbox>
                        </v-flex>
                    </v-layout>
                    <v-divider></v-divider>

                    <v-layout row wrap class="mt-2">
                        <v-flex xs12 sm3 md3>
                            <h4>{{ trans('messages.customer') }}</h4>
                        </v-flex>
                        <v-flex xs12 sm4 md3>
                            <v-checkbox
                                v-model="permissions"
                                :label="trans('messages.add_customer')"
                                value="customer.create"
                            >
                            </v-checkbox>
                            <v-checkbox
                                v-model="permissions"
                                :label="trans('messages.view_customer')"
                                value="customer.view"
                            >
                            </v-checkbox>
                            <v-checkbox
                                v-model="permissions"
                                :label="trans('messages.edit_customer')"
                                value="customer.edit"
                            >
                            </v-checkbox>
                            <v-checkbox
                                v-model="permissions"
                                :label="trans('messages.delete_customer')"
                                value="customer.delete"
                            >
                            </v-checkbox>
                        </v-flex>
                        <v-flex xs12 sm3 md3>
                            <v-checkbox
                                v-model="permissions"
                                :label="trans('messages.add_contact')"
                                value="contact.create"
                            >
                            </v-checkbox>
                            <v-checkbox
                                v-model="permissions"
                                :label="trans('messages.view_contact')"
                                value="contact.view"
                            >
                            </v-checkbox>
                            <v-checkbox
                                v-model="permissions"
                                :label="trans('messages.edit_contact')"
                                value="contact.edit"
                            >
                            </v-checkbox>
                            <v-checkbox
                                v-model="permissions"
                                :label="trans('messages.delete_contact')"
                                value="contact.delete"
                            >
                            </v-checkbox>
                        </v-flex>
                        <v-flex xs12 sm3 md3>
                            <v-checkbox
                                v-model="permissions"
                                :label="trans('messages.add_customer_note')"
                                value="customerNote.create"
                            >
                            </v-checkbox>
                            <v-checkbox
                                v-model="permissions"
                                :label="trans('messages.view_customer_note')"
                                value="customerNote.view"
                            >
                            </v-checkbox>
                            <v-checkbox
                                v-model="permissions"
                                :label="trans('messages.edit_customer_note')"
                                value="customerNote.edit"
                            >
                            </v-checkbox>
                            <v-checkbox
                                v-model="permissions"
                                :label="trans('messages.delete_customer_note')"
                                value="customerNote.delete"
                            >
                            </v-checkbox>
                        </v-flex>
                    </v-layout>
                    <v-divider></v-divider>

                    <v-layout row wrap class="mt-2">
                        <v-flex xs12 sm3 md3>
                            <h4>{{ trans('messages.knowladge_base') }}</h4>
                        </v-flex>
                        <v-flex xs12 sm3 md3>
                            <v-checkbox
                                v-model="permissions"
                                :label="trans('messages.add_knowledge_base')"
                                value="knowledge_base.create"
                            >
                            </v-checkbox>
                            <v-checkbox
                                v-model="permissions"
                                :label="trans('messages.view_knowledge_base')"
                                value="knowledge_base.view"
                            >
                            </v-checkbox>
                            <v-checkbox
                                v-model="permissions"
                                :label="trans('messages.edit_knowledge_base')"
                                value="knowledge_base.edit"
                            >
                            </v-checkbox>
                            <v-checkbox
                                v-model="permissions"
                                :label="trans('messages.delete_knowledge_base')"
                                value="knowledge_base.delete"
                            >
                            </v-checkbox>
                        </v-flex>
                    </v-layout>
                    <v-divider></v-divider>

                    <v-layout row wrap class="mt-2">
                        <v-flex xs12 sm3 md3>
                            <h4>{{ trans('messages.expense') }}</h4>
                        </v-flex>
                        <v-flex xs12 sm3 md3>
                            <v-checkbox
                                v-model="permissions"
                                :label="trans('messages.add_expense')"
                                value="expense.create"
                            >
                            </v-checkbox>
                            <v-checkbox
                                v-model="permissions"
                                :label="trans('messages.edit_expense')"
                                value="expense.edit"
                            >
                            </v-checkbox>
                            <v-checkbox
                                v-model="permissions"
                                :label="trans('messages.delete_expense')"
                                value="expense.delete"
                            >
                            </v-checkbox>
                        </v-flex>
                    </v-layout>
                    <v-divider></v-divider>

                    <v-layout row wrap class="mt-2">
                        <v-flex xs12 sm3 md3>
                            <h4>{{ trans('messages.leaves') }}</h4>
                        </v-flex>
                        <v-flex xs12 sm3 md3>
                            <v-checkbox
                                v-model="permissions"
                                :label="trans('messages.add_leaves')"
                                value="leaves.create"
                            >
                            </v-checkbox>
                            <v-checkbox
                                v-model="permissions"
                                :label="trans('messages.edit_leaves')"
                                value="leaves.edit"
                            >
                            </v-checkbox>
                            <v-checkbox
                                v-model="permissions"
                                :label="trans('messages.delete_leaves')"
                                value="leaves.delete"
                            >
                            </v-checkbox>
                        </v-flex>
                    </v-layout>
                    <v-divider></v-divider>

                    <v-layout row wrap class="mt-2">
                        <v-flex xs12 sm3 md3>
                            <h4>{{ trans('messages.tickets') }}</h4>
                        </v-flex>
                        <v-flex xs12 sm3 md3>
                            <v-checkbox
                                v-model="permissions"
                                :label="trans('messages.add_ticket')"
                                value="tickets.create"
                            >
                            </v-checkbox>
                            <v-checkbox
                                v-model="permissions"
                                :label="trans('messages.view_ticket')"
                                value="tickets.view"
                            >
                            </v-checkbox>
                            <v-checkbox
                                v-model="permissions"
                                :label="trans('messages.edit_ticket')"
                                value="tickets.edit"
                            >
                            </v-checkbox>
                            <v-checkbox
                                v-model="permissions"
                                :label="trans('messages.delete_ticket')"
                                value="tickets.delete"
                            >
                            </v-checkbox>
                        </v-flex>
                    </v-layout>
                    <v-divider></v-divider>

                    <v-layout row wrap class="mt-2">
                        <v-flex xs12 sm3 md3>
                            <h4>{{ trans('messages.other') }}</h4>
                        </v-flex>
                        <v-flex xs12 sm3 md3>
                            <v-checkbox
                                v-model="permissions"
                                :label="trans('messages.add_project')"
                                value="project.create"
                            >
                            </v-checkbox>
                            <v-checkbox
                                v-model="permissions"
                                :label="trans('messages.system_setting')"
                                value="setting"
                            >
                            </v-checkbox>
                            <v-checkbox
                                v-model="permissions"
                                :label="trans('messages.edit_profile')"
                                value="profile.edit"
                            >
                            </v-checkbox>
                            <v-checkbox
                                v-model="permissions"
                                :label="trans('messages.view_sales')"
                                value="sales.invoices"
                            >
                            </v-checkbox>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="success" @click="store" :loading="loading" :disabled="loading">
                    {{ trans('messages.update') }}
                </v-btn>
            </v-card-actions>
        </v-card>
    </div>
</template>
<script>
export default {
    data() {
        return {
            role_name: '',
            loading: false,
            permissions: [],
            roleId: null,
        };
    },
    created() {
        const self = this;
        self.roleId = self.$route.params.id;
        self.getRole(self.roleId);
    },
    methods: {
        getRole(role_id) {
            const self = this;
            axios
                .get('/admin/roles/' + role_id + '/edit')
                .then(function(response) {
                    self.role_name = response.data.role;
                    self.permissions = response.data.permissions;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        store() {
            const self = this;
            let data = {
                name: self.role_name,
                permissions: self.permissions,
            };
            self.$validator.validateAll().then(result => {
                if (result == true) {
                    self.loading = true;
                    axios
                        .put('/admin/roles/' + self.roleId, data)
                        .then(function(response) {
                            self.loading = false;
                            self.$validator.reset();
                            self.$store.commit('showSnackbar', {
                                message: response.data.msg,
                                color: response.data.success,
                            });

                            if (response.data.success === true) {
                                self.goBack();
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
