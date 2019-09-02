<template>
    <div>
        <v-toolbar color="cyan" dark tabs height="28">
            <v-toolbar-title>{{ employee.name }}</v-toolbar-title>
            <template slot="extension">
                <v-tabs
                    centered
                    color="cyan"
                    slider-color="yellow"
                    v-model="tabs"
                    dark
                    icons-and-text
                    height="47"
                >
                    <!-- tab menu -->
                    <v-tab href="#tab-1">
                        {{ trans('messages.overview') }}
                        <v-icon>dvr</v-icon>
                    </v-tab>

                    <v-tab href="#tab-2">
                        {{ trans('messages.documents_notes') }}
                        <v-icon>perm_media</v-icon>
                    </v-tab>
                    <!-- /tab menu-->
                </v-tabs>
            </template>
        </v-toolbar>
        <!-- tab content -->
        <v-tabs-items v-model="tabs">
            <v-tab-item :key="1" :value="'tab-1'">
                <v-card flat>
                    <v-card-text> <EmployeeOverview></EmployeeOverview> </v-card-text>
                </v-card>
            </v-tab-item>
            <v-tab-item :key="2" :value="'tab-2'">
                <v-card flat>
                    <v-card-text> <NoteAndDocumentList></NoteAndDocumentList> </v-card-text>
                </v-card>
            </v-tab-item>
        </v-tabs-items>
        <!-- /tab content -->
    </div>
</template>
<script>
import EmployeeOverview from '../components/Overview';
import NoteAndDocumentList from '../notes/List';
export default {
    components: {
        EmployeeOverview,
        NoteAndDocumentList,
    },
    data() {
        return {
            user_id: null,
            employee: [],
            tabs: 'tab-1',
        };
    },
    created() {
        const self = this;
        self.user_id = self.$route.params.id;
        self.getEmployee(self.user_id);
    },
    methods: {
        getEmployee(user_id) {
            const self = this;
            axios
                .get('/admin/users/' + user_id + '/name')
                .then(function(response) {
                    self.employee = response.data;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
    },
};
</script>
