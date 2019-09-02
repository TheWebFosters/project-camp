<template>
    <div class="text-xs-center" v-if="$hasRole('employee')">
        <ProjectFormAdd ref="projectAdd"></ProjectFormAdd>
        <LeaveAdd ref="applyLeave"></LeaveAdd>
        <ExpenseAdd ref="expenseAdd"></ExpenseAdd>
        <CustomerFormAdd ref="customerAdd"></CustomerFormAdd>
        <TaskFormAdd ref="taskAdd"></TaskFormAdd>
        <ticket-create ref="ticketCreate"></ticket-create>
        <v-menu left nudge-bottom="30" transition="slide-x-transition">
            <template slot="activator">
                <v-btn flat icon>
                    <v-icon dark medium>add_circle</v-icon>
                </v-btn>
            </template>
            <v-list>
                <v-list-tile v-if="$can('customer.create')" @click="createCustomer">
                    <v-list-tile-title>
                        <v-icon>business_center</v-icon>
                        {{ trans('messages.new_customer') }}
                    </v-list-tile-title>
                </v-list-tile>
                <v-list-tile v-if="$can('customer.create')" @click="createLead">
                    <v-list-tile-title>
                        <v-icon>verified_user</v-icon>
                        {{ trans('messages.new_lead') }}
                    </v-list-tile-title>
                </v-list-tile>
                <v-list-tile v-if="$can('project.create')" @click="createProject">
                    <v-list-tile-title>
                        <v-icon>assessment</v-icon>
                        {{ trans('messages.new_project') }}
                    </v-list-tile-title>
                </v-list-tile>
                <v-list-tile
                    v-if="$can('project.' + projectId + '.task.create')"
                    @click="createTask"
                >
                    <v-list-tile-title>
                        <v-icon>assignment</v-icon>
                        {{ trans('messages.new_task') }}
                    </v-list-tile-title>
                </v-list-tile>
                <v-list-tile
                    v-if="$can('project.' + projectId + '.invoice.create')"
                    @click="$router.push({ name: 'sales.invoices.create' })"
                >
                    <v-list-tile-title>
                        <v-icon>receipt</v-icon>
                        {{ trans('messages.new_invoice') }}
                    </v-list-tile-title>
                </v-list-tile>
                <v-list-tile v-if="$can('tickets.create')" @click="$refs.ticketCreate.create()">
                    <v-list-tile-title>
                        <v-icon>live_help</v-icon>
                        {{ trans('messages.new_ticket') }}
                    </v-list-tile-title>
                </v-list-tile>
                <v-list-tile v-if="$can('expense.create')" @click="$refs.expenseAdd.create()">
                    <v-list-tile-title>
                        <v-icon>remove_circle_outline</v-icon>
                        {{ trans('messages.new_expense') }}
                    </v-list-tile-title>
                </v-list-tile>
                <v-list-tile v-if="$can('leaves.create')" @click="$refs.applyLeave.create()">
                    <v-list-tile-title>
                        <v-icon>work_off</v-icon>
                        {{ trans('messages.apply_leave') }}
                    </v-list-tile-title>
                </v-list-tile>
                <v-list-tile
                    v-if="$can('knowledge_base.create')"
                    @click="$router.push({ name: 'Knowledgebase.create' })"
                >
                    <v-list-tile-title>
                        <v-icon>ballot</v-icon>
                        {{ trans('messages.create_knowledgebase') }}
                    </v-list-tile-title>
                </v-list-tile>
            </v-list>
        </v-menu>
    </div>
</template>
<script>
import ProjectFormAdd from '../../common/projects/components/Add';
import LeaveAdd from '../leaves/Add';
import ExpenseAdd from '../expenses/Add';
import CustomerFormAdd from '../customers/components/Add';
import TaskFormAdd from '../../common/projects/tasks/Add';
import TicketCreate from '../../common/tickets/Create';
export default {
    components: {
        ProjectFormAdd,
        LeaveAdd,
        ExpenseAdd,
        CustomerFormAdd,
        TaskFormAdd,
        TicketCreate,
    },
    data: () => ({
        projectId: null,
    }),
    methods: {
        createProject() {
            this.$refs.projectAdd.create();
        },
        createCustomer() {
            var templateType = { template: 'customer' };
            this.$refs.customerAdd.create(templateType);
        },
        createLead() {
            var templateType = { template: 'lead' };
            this.$refs.customerAdd.create(templateType);
        },
        createTask() {
            this.$refs.taskAdd.create(this.projectId);
        },
    },
};
</script>
