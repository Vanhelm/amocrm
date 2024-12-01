<script>
import CreateContact from "./CreateContact.vue";

export default {
    name: "Index",
    components: {CreateContact},

    data() {
        return {
            loading: true,
            itemsPerPageArray: [4, 8, 12, 16, 20],
            search: '',
            filter: {},
            sortDesc: false,
            page: 1,
            itemsPerPage: 8,
            sortBy: 'name',
            keys: [
                'date',
                'id',
                'title',
                'contact',
                'action'
            ],
            items: [],
            modal: false,
            lead: {},
        }
    },

    mounted() {
        this.loadLeads();
    },

    computed: {
        numberOfPages () {
            return Math.ceil(this.items.length / this.itemsPerPage);
        },
        filteredKeys () {
            return this.keys.filter(key => key !== 'title');
        },
    },

    methods: {
        loadLeads() {
            this.loading = true;
            axios.get('/api/lead/list')
                .then(res => {
                    this.items = res.data;
                    this.loading = false;
                })
                .catch(res => console.log(res.data))
        },

        nextPage () {
            if (this.page + 1 <= this.numberOfPages) this.page += 1;
        },

        formerPage () {
            if (this.page - 1 >= 1) this.page -= 1;
        },

        updateItemsPerPage (number) {
            this.itemsPerPage = number;
        },

        replaceKey(key) {
            switch (key) {
                case 'title':
                    return 'Имя';
                case 'contact':
                    return 'Есть контакт?';
                case 'date':
                    return 'Дата';
                case 'action':
                    return '';
            }
            return key;
        },

        openModal(lead) {
            this.lead = {
                leadId: lead.id,
                title: lead.title,
                date: lead.date,
            };
            this.modal = true;
        },

        closeModal() {
            this.modal = false;
            this.loadLeads()
        }
    }
}
</script>

<template>
    <div v-if="loading">
        <v-progress-linear
            indeterminate
            color="teal"
        ></v-progress-linear>
    </div>
    <div v-else>
        <v-container>
            <create-contact @close="closeModal"  :dialog="modal" :lead="lead"></create-contact>
            <v-data-iterator
                :items="items"
                :items-per-page.sync="itemsPerPage"
                :page.sync="page"
                :sort-by="sortBy.toLowerCase()"
                :sort-desc="sortDesc"
                hide-default-footer
            >
                <template v-slot:header>
                    <v-toolbar
                        dark
                        color="teal"
                        class="mb-1"
                    >
                    </v-toolbar>
                </template>

                <template v-slot:default="props">
                    <v-row>
                        <v-col
                            v-for="item in props.items"
                            :key="item.name"
                            cols="12"
                            sm="6"
                            md="4"
                            lg="3"
                        >
                            <v-card>
                                <v-card-title class="subheading font-weight-bold">
                                    {{ item.title }}
                                </v-card-title>

                                <v-divider></v-divider>

                                <v-list dense>
                                    <v-list-item
                                        v-for="(key, index) in filteredKeys"
                                        :key="index"
                                    >
                                        <v-list-item-content :class="{ 'blue--text': sortBy === key }">
                                            {{ replaceKey(key) }}
                                        </v-list-item-content>
                                        <v-list-item-content
                                            class="align-end"
                                        >
                                            <span v-if="key === 'contact'">
                                                {{ item[key.toLowerCase()] !== null ? 'Да' : 'Нет' }}
                                            </span>
                                            <span v-else-if="key === 'action'">
                                                <v-btn @click="openModal(item)"
                                                    :dark="item['contact'] !== null ? false : true"
                                                    color="teal"
                                                    :disabled="item['contact'] !== null ? true : false">
                                                        Привязать
                                                </v-btn>
                                            </span>
                                            <span v-else>
                                                {{ item[key.toLowerCase()] }}
                                            </span>
                                        </v-list-item-content>
                                    </v-list-item>
                                </v-list>
                            </v-card>
                        </v-col>
                    </v-row>
                </template>

                <template v-slot:footer>
                    <v-row
                        class="mt-2"
                        align="center"
                        justify="center"
                    >
                        <span class="grey--text">Items per page</span>
                        <v-menu offset-y>
                            <template v-slot:activator="{ on, attrs }">
                                <v-btn
                                    dark
                                    text
                                    color="teal"
                                    class="ml-2"
                                    v-bind="attrs"
                                    v-on="on"
                                >
                                    {{ itemsPerPage }}
                                    <v-icon>mdi-chevron-down</v-icon>
                                </v-btn>
                            </template>
                            <v-list>
                                <v-list-item
                                    v-for="(number, index) in itemsPerPageArray"
                                    :key="index"
                                    @click="updateItemsPerPage(number)"
                                >
                                    <v-list-item-title>{{ number }}</v-list-item-title>
                                </v-list-item>
                            </v-list>
                        </v-menu>

                        <v-spacer></v-spacer>

                        <span
                            class="mr-4
            grey--text"
                        >
            Page {{ page }} of {{ numberOfPages }}
          </span>
                        <v-btn
                            fab
                            dark
                            color="teal"
                            class="mr-1"
                            @click="formerPage"
                        >
                            <v-icon>mdi-chevron-left</v-icon>
                        </v-btn>
                        <v-btn
                            fab
                            dark
                            color="teal"
                            class="ml-1"
                            @click="nextPage"
                        >
                            <v-icon>mdi-chevron-right</v-icon>
                        </v-btn>
                    </v-row>
                </template>
            </v-data-iterator>
        </v-container>
    </div>
</template>

<style scoped lang="sass">

</style>
