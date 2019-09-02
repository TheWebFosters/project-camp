<template>
    <div>
        <v-toolbar color="cyan" dark tabs height="28">
            <v-toolbar-title>{{ project.name }}</v-toolbar-title>
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
                    <v-tab
                        v-if="$can('project.' + projectId + '.overview')"
                        href="#tab-1"
                        @click="projectOverview"
                    >
                        {{ trans('messages.overview') }}
                        <v-icon>dvr</v-icon>
                    </v-tab>

                    <v-tab
                        v-if="$can('project.' + projectId + '.activities')"
                        href="#tab-2"
                        @click="projectActivities"
                    >
                        {{ trans('messages.activities') }}
                        <v-icon>timeline</v-icon>
                    </v-tab>

                    <v-tab
                        v-if="$can('project.' + projectId + '.task.create')"
                        href="#tab-3"
                        @click="tasks"
                    >
                        {{ trans('messages.tasks') }}
                        <v-icon>assignment</v-icon>
                    </v-tab>

                    <v-tab href="#tab-4" @click="documentAndNotes">
                        {{ trans('messages.documents_notes') }}
                        <v-icon>perm_media</v-icon>
                    </v-tab>

                    <v-tab
                        v-if="$can('project.' + projectId + '.milestone.create')"
                        href="#tab-5"
                        @click="milestones"
                    >
                        {{ trans('messages.milestones') }}
                        <v-icon>outlined_flag</v-icon>
                    </v-tab>

                    <v-tab
                        v-if="$can('project.' + projectId + '.invoice.view')"
                        href="#tab-6"
                        @click="invoice"
                    >
                        {{ trans('messages.invoices') }}
                        <v-icon>receipt</v-icon>
                    </v-tab>
                    <!-- /tab menu-->
                </v-tabs>
            </template>
        </v-toolbar>

        <!-- tab content -->
        <v-tabs-items v-model="tabs">
            <v-tab-item :key="1" :value="'tab-1'">
                <v-card flat>
                    <v-card-text>
                        <ProjectOverview ref="projectOverview"> </ProjectOverview>
                    </v-card-text>
                </v-card>
            </v-tab-item>

            <v-tab-item :key="2" :value="'tab-2'">
                <v-card flat>
                    <v-card-text>
                        <ProjectActivity ref="projectActivity"> </ProjectActivity>
                    </v-card-text>
                </v-card>
            </v-tab-item>

            <v-tab-item :key="3" :value="'tab-3'">
                <v-card flat>
                    <v-card-text> <TaskLists></TaskLists> </v-card-text>
                </v-card>
            </v-tab-item>

            <v-tab-item :key="4" :value="'tab-4'">
                <v-card flat>
                    <v-card-text>
                        <NotesAndDocumentsList ref="documentAndNote"> </NotesAndDocumentsList>
                    </v-card-text>
                </v-card>
            </v-tab-item>

            <v-tab-item :key="5" :value="'tab-5'">
                <v-card flat>
                    <v-card-text> <MilestonesLists ref="milestone"></MilestonesLists> </v-card-text>
                </v-card>
            </v-tab-item>

            <v-tab-item :key="6" :value="'tab-6'">
                <v-card flat>
                    <v-card-text> <InvoiceLists ref="invoice"></InvoiceLists> </v-card-text>
                </v-card>
            </v-tab-item>
        </v-tabs-items>
        <!-- /tab content -->
    </div>
</template>
<script>
import TaskLists from '../tasks/List';
import MilestonesLists from '../milestones/Lists';
import ProjectOverview from '../overview/ProjectOverview';
import ProjectActivity from '../activities/ProjectActivity';
import InvoiceLists from '../invoices/Lists';
import NotesAndDocumentsList from '../notes/Lists';
export default {
    components: {
        TaskLists,
        MilestonesLists,
        ProjectOverview,
        ProjectActivity,
        InvoiceLists,
        NotesAndDocumentsList,
    },
    data() {
        return {
            projectId: null,
            project: [],
            tabs: 'tab-1',
        };
    },
    created() {
        const self = this;
        self.projectId = self.$route.params.id;
        self.getProject(self.projectId);
        self.$store.commit('drawer', {
            drawer: false,
        });
    },
    methods: {
        projectOverview() {
            const self = this;
            self.$refs.projectOverview.getProjectOverviewFromApi(self.projectId);
        },
        projectActivities() {
            const self = this;
            self.$refs.projectActivity.getProjectActivities();
        },
        tasks() {
            const self = this;
            self.$eventBus.$emit('updateTaskTable');
        },
        documentAndNotes() {
            const self = this;
            self.$refs.documentAndNote.getProjectNotes();
        },
        milestones() {
            const self = this;
            self.$refs.milestone.getMilestoneList();
        },
        invoice() {
            const self = this;
            self.$refs.invoice.getInvoiceFromApi();
        },
        getProject(project_id) {
            const self = this;
            axios
                .get('/projects/' + project_id + '/customer')
                .then(function(response) {
                    self.project = response.data;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
    },
};
</script>
