<template>
    <TasksList :id="project_id" :show-all-task="show_all_task"></TasksList>
</template>
<script>
import TasksList from '../projects/tasks/List';
export default {
    components: {
        TasksList,
    },
    data() {
        return {
            project_id: null,
            show_all_task: true,
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
