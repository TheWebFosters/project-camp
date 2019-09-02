<template>
    <div class="component-wrap" v-if="maxCount">
        <span v-for="(member, key) in members" :key="member.id">
            <v-tooltip top :disabled="tooltip" v-if="maxCount > key">
                <v-avatar size="26px" slot="activator">
                    <v-img v-if="member.avatar_url" :src="member.avatar_url"></v-img>
                    <v-img v-else :src="'https://ui-avatars.com/api/?name=' + member.name"></v-img>
                </v-avatar>
                <span>
                    {{ member.name }}
                </span>
            </v-tooltip>
            <v-tooltip top :disabled="tooltip" v-if="maxCount == key">
                <v-avatar size="26px" slot="activator">
                    <v-img :src="'https://ui-avatars.com/api/?name=...'"></v-img>
                </v-avatar>
                <span>
                    ....
                </span>
            </v-tooltip>
        </span>
    </div>
    <div class="component-wrap" v-else>
        <span v-for="(member, key) in members" :key="member.id">
            <v-tooltip top :disabled="tooltip">
                <v-avatar size="26px" slot="activator">
                    <v-img v-if="member.avatar_url" :src="member.avatar_url"></v-img>
                    <v-img v-else :src="'https://ui-avatars.com/api/?name=' + member.name"></v-img>
                </v-avatar>
                <span>
                    {{ member.name }}
                </span>
            </v-tooltip>
        </span>
    </div>
</template>
<script>
export default {
    props: ['members', 'tooltip', 'maxCount'],
};
</script>
