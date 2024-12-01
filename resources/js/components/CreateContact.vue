<script>
export default {
    name: "CreateContact",
    props: {
        dialog: {
            type: Boolean,
            required: true
        },
        lead: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            form: {
                phone: null,
                comment: null,
                name: null,
            },
            error: false,
            loading: false,
        }
    },
    methods: {
        linkContact() {
            this.loading = true;
            let form = {...this.form, ...this.lead}
            axios.post('/api/contact/link', form)
                .then(res => {
                    this.error = false;
                    this.loading = false;
                    this.$emit('close');
                })
                .catch(err => {
                    this.error = true;
                    this.loading = false;
                })
        }
    }
}
</script>

<template>

    <v-row justify="center">
        <v-dialog
            v-model="dialog"
            persistent
            max-width="600px"
        >
            <v-card :loading="loading">
                <v-alert
                    v-model="error"
                    color="#C51162"
                    dark
                    icon="mdi-material-design"
                    border="right"
                    class="mt-3"
                >
                    Во время выполнения произошла ошибка, убедитесь, что вы заполнили все поля корректно!
                    Имя - текстовое поле.
                    Телефон - телефон
                    Комментарий - текстовое поле.
                    Все поля обязательны к заполнению
                </v-alert>
                <v-card-title>
                    <span class="text-h5">Создание контакта</span>
                </v-card-title>
                <v-card-text>
                    <v-container>
                        <v-row>
                            <v-col
                                cols="12"
                            >
                                <v-text-field
                                    label="Имя*"
                                    required
                                    color="teal"
                                    v-model="form.name"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12">
                                <v-text-field
                                    label="Телефон*"
                                    required
                                    color="teal"
                                    v-model="form.phone"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12">
                                <v-text-field
                                    label="Комментарий*"
                                    required
                                    color="teal"
                                    v-model="form.comment"
                                ></v-text-field>
                            </v-col>
                        </v-row>
                    </v-container>
                    <small>* отмечены обязательные поля</small>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        color="teal"
                        text
                        @click="$emit('close')"
                        :disabled="loading"
                    >
                        Отмена
                    </v-btn>
                    <v-btn
                        color="teal"
                        text
                        @click="linkContact"
                        :disabled="loading"
                    >
                        Привязать
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>

<style scoped lang="sass">

</style>
