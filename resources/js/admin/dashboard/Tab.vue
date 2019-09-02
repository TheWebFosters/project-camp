<template>
    <v-tabs centered color="cyan" dark icons-and-text>
        <v-tabs-slider color="yellow"> </v-tabs-slider>

        <!-- tab menu -->
        <v-tab href="#tab-1" @click="tasks">
            {{ trans('messages.my_tasks') }}
            <v-icon>assignment</v-icon>
        </v-tab>
        <!-- /tab menu-->

        <!-- tab content -->
        <v-tabs-items>
            <v-tab-item :key="1" :value="'tab-1'">
                <v-card flat>
                    <v-card-text>
                        <TasksList
                            :id="project_id"
                            :task-in-dashboard="task_in_dashboard"
                        ></TasksList>
                    </v-card-text>
                </v-card>
            </v-tab-item>
        </v-tabs-items>
        <!-- /tab content -->
    </v-tabs>
</template>
<script>
import TasksList from '../../common/projects/tasks/List';
export default {
    components: {
        TasksList,
    },
    data() {
        return {
            project_id: null,
            task_in_dashboard: true,
        };
    },
    methods: {
        tasks() {
            const self = this;
            self.$eventBus.$emit('updateTaskTable');
        },
    },
};
</script>
