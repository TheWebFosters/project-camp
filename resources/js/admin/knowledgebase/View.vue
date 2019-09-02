<template>
    <!-- card -->
    <v-card>
        <v-card-title class="headline">
            {{ data.title }}
            <v-spacer></v-spacer>
            <v-btn
                color="blue lighten-2"
                dark
                @click="$router.push({ name: 'Knowledgebase.list' })"
            >
                <v-icon small>arrow_back_ios</v-icon>
                {{ trans('messages.back') }}
            </v-btn>
        </v-card-title>
        <v-divider></v-divider>
        <v-card-text>
            <!-- container -->
            <v-container align-center>
                <v-layout row wrap>
                    <v-flex xs12 md12>
                        <span v-html="data.description"></span>
                    </v-flex>
                </v-layout>
            </v-container>

            <v-container v-show="data.media && data.media.length">
                <v-divider></v-divider>
                <v-list>
                    <v-subheader inset> {{ trans('messages.documents') }} </v-subheader>
                    <v-list-tile v-for="item in data.media" :key="item.id">
                        <v-list-tile-action>
                            <a :href="item.download_url"> <v-icon>archive</v-icon> </a>
                        </v-list-tile-action>

                        <v-list-tile-content>
                            <v-list-tile-title v-text="item.display_name"></v-list-tile-title>
                        </v-list-tile-content>
                    </v-list-tile>
                </v-list>
            </v-container>
            <!-- /container -->
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
            <div class="caption">
                <v-icon right>create</v-icon>
                {{ _.get(data, 'user.name', null) }}

                <v-icon right>calendar_today</v-icon>
                {{ data.created_at | formatDate }}
            </div>
        </v-card-actions>
    </v-card>
    <!-- /card -->
</template>

<script>
export default {
    data() {
        return {
            data: [],
            knowledgeBaseId: null,
        };
    },
    created() {
        this.knowledgeBaseId = this.$route.params.id;
        this.view(this.knowledgeBaseId);
    },
    methods: {
        view(id) {
            const self = this;

            axios
                .get('/admin/knowledge-bases/' + id)
                .then(function(response) {
                    self.data = response.data;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
    },
};
</script>
