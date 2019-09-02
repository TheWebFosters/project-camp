<template>
    <div>
        <v-toolbar color="cyan" tabs height="28" dark>
            <v-toolbar-title>
                {{ lead.company }}
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
                        {{ trans('messages.documents_notes') }}
                        <v-icon>perm_media</v-icon>
                    </v-tab>
                    <v-tab href="#tab-3">
                        {{ trans('messages.reminders') }}
                        <v-icon>alarm_on</v-icon>
                    </v-tab>
                </v-tabs>
            </template>
        </v-toolbar>
        <v-tabs-items v-model="model">
            <v-tab-item :key="'1'" :value="'tab-1'">
                <v-card flat>
                    <v-card-text> <LeadOverview></LeadOverview> </v-card-text>
                </v-card>
            </v-tab-item>
            <v-tab-item :key="'2'" :value="'tab-2'">
                <v-card flat>
                    <v-card-text> <NotesLists></NotesLists> </v-card-text>
                </v-card>
            </v-tab-item>
            <v-tab-item :key="'3'" :value="'tab-3'">
                <v-card flat>
                    <v-card-text> <ReminderLists></ReminderLists> </v-card-text>
                </v-card>
            </v-tab-item>
        </v-tabs-items>
    </div>
</template>
<script>
import LeadOverview from './Overview';
import NotesLists from './notes/List';
import ReminderLists from './reminders/List';
export default {
    components: {
        LeadOverview,
        NotesLists,
        ReminderLists,
    },
    data() {
        return {
            model: 'tab-1',
            lead: [],
            lead_id: null,
        };
    },
    created() {
        const self = this;
        self.lead_id = self.$route.params.id;
        self.getLead(self.lead_id);
    },
    methods: {
        getLead(lead_id) {
            const self = this;
            axios
                .get('/admin/customers/' + lead_id + '/customer-name')
                .then(function(response) {
                    self.lead = response.data;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
    },
};
</script>
