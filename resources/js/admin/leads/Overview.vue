<template>
    <v-container grid-list-md>
        <v-layout wrap>
            <v-flex xs12 sm5 md5>
                <v-card elevation="4">
                    <v-card-title primary-title>
                        <div>
                            <h3 class="headline mb-0">
                                {{ lead.company }}
                            </h3>

                            <p>
                                <v-tooltip top>
                                    <template slot="activator">
                                        <v-icon>mobile_friendly</v-icon>
                                        {{ lead.mobile }}
                                    </template>

                                    <span>{{ trans('messages.mobile') }}</span>
                                </v-tooltip>
                            </p>

                            <p>
                                <v-tooltip top>
                                    <template slot="activator">
                                        <v-icon>mobile_friendly</v-icon>
                                        {{ lead.alternate_contact_no }}
                                    </template>

                                    <span>{{ trans('messages.alternate_num') }}</span>
                                </v-tooltip>
                            </p>

                            <p>
                                <v-tooltip top>
                                    <template slot="activator">
                                        <v-icon>email</v-icon>
                                        {{ lead.email }}
                                    </template>

                                    <span>{{ trans('messages.email') }}</span>
                                </v-tooltip>
                            </p>

                            <p>
                                <v-tooltip top>
                                    <template slot="activator">
                                        <v-icon>http</v-icon>
                                        {{ lead.website }}
                                    </template>

                                    <span>{{ trans('messages.website') }}</span>
                                </v-tooltip>
                            </p>

                            <p>
                                <v-tooltip top>
                                    <template slot="activator">
                                        <v-icon>pin_drop</v-icon>
                                        {{ lead.city }}, {{ lead.state }},
                                        {{ lead.country }}
                                    </template>

                                    <span
                                        >{{ trans('messages.city') }},
                                        {{ trans('messages.state') }},
                                        {{ trans('messages.country') }}</span
                                    >
                                </v-tooltip>
                            </p>

                            <p>
                                <v-tooltip top>
                                    <template slot="activator">
                                        <v-icon>money</v-icon>
                                        {{ currency.iso_name }}
                                        ({{ currency.symbol }})
                                    </template>

                                    <span>{{ trans('messages.currency') }}</span>
                                </v-tooltip>
                            </p>

                            <p>
                                <v-tooltip top>
                                    <template slot="activator">
                                        <v-icon>store_mall_directory</v-icon>
                                        {{ lead.billing_address }}
                                    </template>

                                    <span>{{ trans('messages.billing_address') }}</span>
                                </v-tooltip>
                            </p>

                            <p>
                                <v-tooltip top>
                                    <template slot="activator">
                                        <v-icon>local_shipping</v-icon>
                                        {{ lead.shipping_address }}
                                    </template>

                                    <span>{{ trans('messages.shipping_address') }}</span>
                                </v-tooltip>
                            </p>
                        </div>
                    </v-card-title>
                </v-card>
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>
export default {
    data() {
        return {
            lead: [],
            lead_id: null,
            currency: [],
        };
    },
    created() {
        this.lead_id = this.$route.params.id;
        this.show(this.lead_id);
    },
    methods: {
        show(data) {
            const self = this;
            axios
                .get('/admin/customers/' + data)
                .then(function(response) {
                    self.lead = response.data;
                    self.currency = response.data.currency;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
    },
};
</script>
