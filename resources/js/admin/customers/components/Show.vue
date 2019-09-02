<template>
    <div>
        <v-toolbar color="cyan" tabs dark height="28">
            <v-toolbar-title>
                {{ customer.company }}
            </v-toolbar-title>
            <template slot="extension">
                <v-tabs
                    centered
                    color="cyan"
                    slider-color="yellow"
                    icons-and-text
                    v-model="model"
                    height="47"
                >
                    <v-tab href="#tab-1">
                        {{ trans('messages.overview') }}
                        <v-icon>dvr</v-icon>
                    </v-tab>
                    <v-tab href="#tab-2">
                        {{ trans('messages.contacts') }}
                        <v-icon>contacts</v-icon>
                    </v-tab>
                    <v-tab href="#tab-3">
                        {{ trans('messages.documents_notes') }}
                        <v-icon>perm_media</v-icon>
                    </v-tab>
                    <v-tab href="#tab-4">
                        {{ trans('messages.customer_account_ledger') }}
                        <v-icon>receipt</v-icon>
                    </v-tab>
                </v-tabs>
            </template>
        </v-toolbar>
        <v-tabs-items v-model="model">
            <v-tab-item :key="'1'" :value="'tab-1'">
                <v-card flat>
                    <v-card-text> <CustomerOverview></CustomerOverview> </v-card-text>
                </v-card>
            </v-tab-item>

            <v-tab-item :key="'2'" :value="'tab-2'">
                <v-card flat>
                    <v-card-text> <ContactLists></ContactLists> </v-card-text>
                </v-card>
            </v-tab-item>

            <v-tab-item :key="'3'" :value="'tab-3'">
                <v-card flat>
                    <v-card-text> <NotesLists></NotesLists> </v-card-text>
                </v-card>
            </v-tab-item>

            <v-tab-item :key="'4'" :value="'tab-4'">
                <v-card flat>
                    <v-card-text>
                        <customer-account-ledger></customer-account-ledger>
                    </v-card-text>
                </v-card>
            </v-tab-item>
        </v-tabs-items>
    </div>
</template>

<script>
import CustomerOverview from '../components/Overview';
import ContactLists from './contacts/List';
import NotesLists from '../components/notes/List';
import CustomerAccountLedger from '../components/account_ledger/AccountLedger';

export default {
    components: { CustomerOverview, ContactLists, NotesLists, CustomerAccountLedger },
    data() {
        return {
            model: 'tab-1',
            customer_id: null,
            customer: [],
        };
    },
    created() {
        const self = this;
        self.customer_id = self.$route.params.id;
        self.getCustomer(self.customer_id);
    },
    methods: {
        getCustomer(customer_id) {
            const self = this;
            axios
                .get('/admin/customers/' + customer_id + '/customer-name')
                .then(function(response) {
                    self.customer = response.data;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
    },
};
</script>
