<template>
    <v-chip label small :color="color(status)">
        <small>{{ trans('messages.' + status) }}</small>
    </v-chip>
</template>
<script>
export default {
    props: ['status'],
    methods: {
        color(status) {
            if (status === 'approved' || status === 'paid' || status === 'closed') {
                return 'green lighten-1';
            } else if (
                status === 'cancelled' ||
                status === 'due' ||
                status === 'new' ||
                status === 'urgent'
            ) {
                return 'red accent-2';
            } else if (status === 'pending' || status === 'partial') {
                return 'cyan lighten-2';
            } else if (status === 'open') {
                return 'pink lighten-1';
            } else if (status === 'low') {
                return 'yellow darken-4';
            } else if (status === 'medium') {
                return 'deep-orange lighten-2';
            } else if (status === 'high') {
                return 'red accent-1';
            }
        },
    },
};
</script>
