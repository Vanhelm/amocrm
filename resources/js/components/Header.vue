<script>
import router from "../router";

export default {
    name: "Header",

    data() {
        return {
            page: null
        }
    },

    mounted() {
        let route = this.$route.path;
        this.setPage(route);
    },

    methods: {
        switchPage(page) {
            this.$router.push(page);
            this.setPage(page)
        },

        setPage(path) {
            if (path === '/') {
                this.page = 0;
            } else if (path === '/history') {
                this.page = 1;
            } else {
                this.page = null;
            }
        }
     }
}
</script>

<template>
    <v-bottom-navigation
        :value="page"
        :color="page === null ? 'grey darken-1' : 'teal'"
        grow
    >
        <v-btn @click="switchPage('/')">
            <span>Лиды</span>

            <v-icon>mdi-account</v-icon>
        </v-btn>

        <v-btn @click="switchPage('/history')">
            <span>История</span>

            <v-icon>mdi-history</v-icon>
        </v-btn>
    </v-bottom-navigation>
</template>

<style scoped lang="sass">

</style>
