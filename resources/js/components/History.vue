<script>
export default {
    name: "History",
    data() {
        return {
            headers: [
                { text: 'Дата и время', value: 'date' },
                { text: 'Сообщение', value: 'message' },
                { text: 'Действие', value: 'action' },
                { text: 'Статус', value: 'status' },
            ],
            logs: [],
            loading: true,
            search: null,
        };
    },

    mounted() {
        this.getLogs();
    },

    methods: {
        getLogs() {
            axios.get('/api/log/list')
                .then(res => {
                    this.logs = res.data;
                    this.loading = false;
                })
                .catch(res => {
                    console.log(res.data.error);
                })
        }
    }
}
</script>

<template>
    <v-container>
        <v-card>
            <v-card-title>
                История
                <v-spacer></v-spacer>
                <v-text-field
                    v-model="search"
                    append-icon="mdi-magnify"
                    label="Поиск по логам"
                    single-line
                    hide-details
                ></v-text-field>
            </v-card-title>
        </v-card>
        <v-data-table
            :search="search"
            :headers="headers"
            :items="logs"
            class="elevation-1"
            :loading="loading"
            :sort-by="['date']"
            :sort-desc="[true]"
            :items-per-page="15"
            loading-text="Загрузка... Пожалуйста, подождите"
        >
            <template v-slot:[`item.status`]="{ item }">
                <span :class="{'success-status': item.status === 0, 'error-status': item.status === 1}">
                  {{ item.status === 0 ? 'Успешно' : 'Ошибка' }}
                </span>
            </template>
        </v-data-table>
    </v-container>
</template>

<style scoped lang="css">

.success-status {
    background-color: green;
    color: white;
    font-weight: bold;
    padding: 4px 12px;
    border-radius: 20px;
}

.error-status {
    background-color: red;
    color: white;
    font-weight: bold;
    padding: 4px 12px;
    border-radius: 20px;
}
</style>
