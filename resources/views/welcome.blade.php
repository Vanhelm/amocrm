<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>
        <link rel="stylesheet" href="{{mix('css/app.css')}}">
        <script src="{{ mix('js/app.js') }}" defer></script>
        <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/@mdi/font@6.x/css/materialdesignicons.min.css" rel="stylesheet">
    </head>
    <body>
        <div id="app">
            <v-app>
                <header-component></header-component>
                <v-main>
                    <router-view></router-view>
                </v-main>
            </v-app>
        </div>
    </body>
</html>
