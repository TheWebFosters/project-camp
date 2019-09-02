<template>
    <div class="component-wrap">
        <v-tooltip top>
            <template slot="activator">
                <v-icon small>info</v-icon>
            </template>
            <span v-html="helptext"></span>
        </v-tooltip>
    </div>
</template>
<script>
export default {
    props: ['helptext'],
};
</script>
