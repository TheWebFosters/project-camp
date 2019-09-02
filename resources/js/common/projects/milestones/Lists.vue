<template>
    <div class="component-wrap">
        <!-- create milestone -->
        <MilestoneFormAdd></MilestoneFormAdd>
        <!-- view milestone -->
        <MilestoneShow ref="milestoneShow"></MilestoneShow>
        <!-- edit milestone -->
        <MilestoneFormEdit ref="milestoneEdit"></MilestoneFormEdit>
        <v-layout row wrap>
            <v-spacer></v-spacer>
            <v-btn
                v-if="$can('project.' + projectId + '.milestone.create')"
                class="primary lighten-1"
                dark
                @click="create"
            >
                {{ trans('messages.new_milestone') }}
                <v-icon right>add</v-icon>
            </v-btn>
        </v-layout>
        <v-divider></v-divider>
        <!-- milestone List -->
        <v-list subheader three-line v-if="milestones.length">
            <v-subheader>
                {{ trans('messages.milestone_list') }}
            </v-subheader>
            <template @click="" v-for="milestone in milestones">
                <v-list-tile :key="milestone.id" @click="">
                    <v-list-tile-content>
                        <v-list-tile-title>
                            <span> {{ milestone.name }} </span>
                        </v-list-tile-title>
                        <v-list-tile-sub-title>
                            <v-chip :color="dueDateColor(milestone)" small>
                                {{ milestone.due_date | formatDateTime }}
                            </v-chip>
                        </v-list-tile-sub-title>
                        <v-list-tile-sub-title>
                            <v-btn
                                flat
                                icon
                                v-if="$can('project.' + projectId + '.milestone.view')"
                                @click="view(milestone.id)"
                            >
                                <v-icon small> visibility </v-icon>
                            </v-btn>
                            <v-btn
                                flat
                                icon
                                v-if="$can('project.' + projectId + '.milestone.edit')"
                                @click="edit(milestone.id)"
                            >
                                <v-icon small> edit </v-icon>
                            </v-btn>
                            <v-btn
                                v-if="$can('project.' + projectId + '.milestone.delete')"
                                flat
                                icon
                                @click="deleteMilestone(milestone.id)"
                            >
                                <v-icon small> delete_outline </v-icon>
                            </v-btn>
                        </v-list-tile-sub-title>
                    </v-list-tile-content>
                </v-list-tile>
                <v-divider></v-divider>
            </template>
        </v-list>
        <v-layout row wrap v-else>
            <v-flex xs12 sm12>
                <div>
                    <v-alert :value="true" color="info" outline>
                        {{ trans('messages.no_records_found') }}
                    </v-alert>
                </div>
            </v-flex>
        </v-layout>
        <!-- /milestone List -->
    </div>
</template>

<script>
import MilestoneFormAdd from '../../projects/milestones/Add';
import MilestoneShow from '../../projects/milestones/Show';
import MilestoneFormEdit from '../../projects/milestones/Edit';
export default {
    components: {
        MilestoneFormAdd,
        MilestoneShow,
        MilestoneFormEdit,
    },
    data() {
        return {
            projectId: null,
            milestones: [],
        };
    },
    created() {
        const self = this;
        self.projectId = self.$route.params.id;
        self.getMilestoneList();
        self.$eventBus.$on('updateMilestoneList', data => {
            self.getMilestoneList();
        });
    },
    beforeDestroy() {
        const self = this;
        self.$eventBus.$off('updateMilestoneList');
    },
    methods: {
        create() {
            const self = this;
            self.$eventBus.$emit('createMilestoneAdd', self.projectId);
        },
        getMilestoneList() {
            const self = this;
            if (self.$can('project.' + self.projectId + '.milestone.view')) {
                axios
                    .get('/project-milestones', { params: { project_id: self.projectId } })
                    .then(function(response) {
                        self.milestones = response.data;
                    })
                    .catch(function(error) {
                        console.log(error);
                    });
            }
        },
        view(id) {
            const self = this;
            var data = { id: id, project_id: self.projectId };
            self.$refs.milestoneShow.view(data);
        },
        edit(id) {
            const self = this;
            var data = { id: id, project_id: self.projectId };
            self.$refs.milestoneEdit.edit(data);
        },
        dueDateColor(milestone) {
            var date_passed = moment().isAfter(milestone.due_date);
            if (date_passed) {
                return 'red';
            } else {
                return 'primary';
            }
        },
        deleteMilestone(id) {
            const self = this;
            self.$store.commit('showDialog', {
                type: 'confirm',
                icon: 'warning',
                title: self.trans('messages.are_you_sure'),
                message: self.trans('messages.you_cant_restore_it'),
                okCb: () => {
                    axios
                        .delete('/project-milestones/' + id, {
                            params: { project_id: self.projectId },
                        })
                        .then(function(response) {
                            self.$store.commit('showSnackbar', {
                                message: response.data.msg,
                                color: response.data.success,
                            });

                            if (response.data.success === true) {
                                self.getMilestoneList();
                            }
                        })
                        .catch(function(error) {
                            console.log(error);
                        });
                },
                cancelCb: () => {
                    console.log('CANCEL');
                },
            });
        },
    },
};
</script>
