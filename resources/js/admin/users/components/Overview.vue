<template>
    <v-container grid-list-md>
        <v-layout wrap>
            <v-flex xs12 sm7 md7>
                <v-card elevation="4">
                    <v-card-title primary-title>
                        <v-icon>person</v-icon>
                        <h4 class="headline mb-0">{{ employee_data.name }}</h4>
                        <v-spacer></v-spacer>
                        <v-tooltip top>
                            <template slot="activator">
                                <v-icon>radio_button_checked</v-icon>
                                {{ trans('messages.' + employee_data.gender) }}
                            </template>

                            <span>{{ trans('messages.gender') }}</span>
                        </v-tooltip>
                    </v-card-title>
                    <v-divider></v-divider>
                    <v-card-text>
                        <v-layout wrap>
                            <v-flex xs12 sm6 md6>
                                <v-tooltip top>
                                    <template slot="activator">
                                        <v-icon>mobile_friendly</v-icon>
                                        {{ employee_data.mobile }}
                                    </template>

                                    <span>{{ trans('messages.mobile') }}</span>
                                </v-tooltip>
                            </v-flex>
                            <v-flex xs12 sm6 md6>
                                <v-tooltip top>
                                    <template slot="activator">
                                        <v-icon>mobile_friendly</v-icon>
                                        {{ employee_data.alternate_num }}
                                    </template>

                                    <span>{{ trans('messages.alternate_num') }}</span>
                                </v-tooltip>
                            </v-flex>
                        </v-layout>
                        <v-layout wrap>
                            <v-flex xs12 sm6 md6>
                                <v-tooltip top>
                                    <template slot="activator">
                                        <v-icon>cake</v-icon>
                                        {{ employee_data.birth_date | formatDate }}
                                    </template>

                                    <span>{{ trans('messages.date_of_birth') }}</span>
                                </v-tooltip>
                            </v-flex>
                            <v-flex xs12 sm6 md6>
                                <v-tooltip top>
                                    <template slot="activator">
                                        <v-icon>email</v-icon>
                                        {{ employee_data.email }}
                                    </template>

                                    <span>{{ trans('messages.email') }}</span>
                                </v-tooltip>
                            </v-flex>
                        </v-layout>
                        <v-layout wrap>
                            <v-flex xs12 sm6 md6>
                                <v-tooltip top>
                                    <template slot="activator">
                                        <v-icon>web</v-icon>
                                        {{ employee_data.skype }}
                                    </template>

                                    <span>{{ trans('messages.skype') }}</span>
                                </v-tooltip>
                            </v-flex>
                            <v-flex xs12 sm6 md6>
                                <v-tooltip top>
                                    <template slot="activator">
                                        <v-icon>public</v-icon>
                                        {{ employee_data.twitter }}
                                    </template>

                                    <span>{{ trans('messages.twitter') }}</span>
                                </v-tooltip>
                            </v-flex>
                        </v-layout>
                        <v-layout wrap>
                            <v-flex xs12 sm6 md6>
                                <v-tooltip top>
                                    <template slot="activator">
                                        <v-icon>public</v-icon>
                                        {{ employee_data.linkedin }}
                                    </template>

                                    <span>{{ trans('messages.linkedin') }}</span>
                                </v-tooltip>
                            </v-flex>
                            <v-flex xs12 sm6 md6>
                                <v-tooltip top>
                                    <template slot="activator">
                                        <v-icon>public</v-icon>
                                        {{ employee_data.facebook }}
                                    </template>

                                    <span>{{ trans('messages.facebook') }}</span>
                                </v-tooltip>
                            </v-flex>
                        </v-layout>
                        <v-layout wrap>
                            <v-flex xs12 sm6 md6>
                                <v-tooltip top>
                                    <template slot="activator">
                                        <v-icon>person</v-icon>
                                        {{ employee_data.guardian_name }}
                                    </template>

                                    <span>{{ trans('messages.guardian_name') }}</span>
                                </v-tooltip>
                            </v-flex>
                            <v-flex xs12 sm6 md6>
                                <v-tooltip top>
                                    <template slot="activator">
                                        <v-icon>location_on</v-icon>
                                        {{ employee_data.current_address }}
                                    </template>

                                    <span>{{ trans('messages.current_address') }}</span>
                                </v-tooltip>
                            </v-flex>
                            <v-flex xs12 sm6 md6>
                                <v-tooltip top>
                                    <template slot="activator">
                                        <v-icon>location_on</v-icon>
                                        {{ employee_data.home_address }}
                                    </template>

                                    <span>{{ trans('messages.home_address') }}</span>
                                </v-tooltip>
                            </v-flex>
                            <v-flex xs12 sm6 md6>
                                <v-tooltip top>
                                    <template slot="activator">
                                        <v-icon>note</v-icon>
                                        {{ employee_data.note }}
                                    </template>

                                    <span>{{ trans('messages.note') }}</span>
                                </v-tooltip>
                            </v-flex>
                        </v-layout>
                        <v-divider></v-divider>
                        <v-layout>
                            <v-flex xs12 sm6 md6>
                                <v-tooltip top>
                                    <template slot="activator">
                                        <v-icon>account_circle</v-icon>
                                        {{ employee_data.account_holder_name }}
                                    </template>

                                    <span>{{ trans('messages.account_holder_name') }}</span>
                                </v-tooltip>
                            </v-flex>
                            <v-flex xs12 sm6 md6>
                                <v-tooltip top>
                                    <template slot="activator">
                                        <v-icon>account_balance_wallet</v-icon>
                                        {{ employee_data.account_no }}
                                    </template>

                                    <span>{{ trans('messages.account_no') }}</span>
                                </v-tooltip>
                            </v-flex>
                        </v-layout>
                        <v-layout>
                            <v-flex xs12 sm6 md6>
                                <v-tooltip top>
                                    <template slot="activator">
                                        <v-icon>account_balance</v-icon>
                                        {{ employee_data.bank_name }}
                                    </template>

                                    <span>{{ trans('messages.bank_name') }}</span>
                                </v-tooltip>
                            </v-flex>
                            <v-flex xs12 sm6 md6>
                                <v-tooltip top>
                                    <template slot="activator">
                                        <v-icon>account_balance</v-icon>
                                        {{ employee_data.bank_identifier_code }}
                                    </template>

                                    <span>{{ trans('messages.bank_identifier_code') }}</span>
                                </v-tooltip>
                            </v-flex>
                        </v-layout>
                        <v-layout>
                            <v-flex xs12 sm6 md6>
                                <v-tooltip top>
                                    <template slot="activator">
                                        <v-icon>location_on</v-icon>
                                        {{ employee_data.branch_location }}
                                    </template>

                                    <span>{{ trans('messages.branch_location') }}</span>
                                </v-tooltip>
                            </v-flex>
                            <v-flex xs12 sm6 md6>
                                <v-tooltip top>
                                    <template slot="activator">
                                        <v-icon>verified_user</v-icon>
                                        {{ employee_data.tax_payer_id }}
                                    </template>

                                    <span>{{ trans('messages.tax_payer_id') }}</span>
                                </v-tooltip>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn
                            v-if="$can('employee.edit')"
                            block
                            outline
                            color="accent"
                            @click="
                                $router.push({
                                    name: 'users.edit',
                                    params: { id: employee_data.id },
                                })
                            "
                        >
                            <v-icon>edit</v-icon>
                            {{ trans('messages.edit') }}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-flex>
        </v-layout>
    </v-container>
</template>
<script>
export default {
    data() {
        return {
            userId: null,
            employee_data: [],
        };
    },
    created() {
        const self = this;
        self.userId = self.$route.params.id;
        self.show(self.userId);
    },
    methods: {
        show(user_id) {
            const self = this;
            axios
                .get('/admin/users/' + user_id)
                .then(function(response) {
                    self.employee_data = response.data;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
    },
};
</script>
