<script setup>
// import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const activeStyle = a => a
    ? "py-2.7 shadow-soft-xl text-sm ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap rounded-lg bg-white px-4 font-semibold text-slate-700 transition-colors"
    : "py-2.7 text-sm ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap px-4 transition-colors";

const activeStyle2 = a => a
    ? "bg-gradient-to-tl from-purple-700 to-pink-500 shadow-soft-2xl mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-white bg-center stroke-0 text-center xl:p-2.5"
    : "shadow-soft-2xl mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-white bg-center fill-current stroke-0 text-center xl:p-2.5";

const navlinks = [
    {route: 'admin.dashboard', name: 'Статистика', type: 'url', icon: 'fas fa-home'},
    {route: 'admin.dashboard', name: 'Персонажи online', type: 'url', icon: 'fas fa-home'},
    {route: 'admin.dashboard', name: 'Чат online', type: 'url', icon: 'fas fa-home'},
    {route: '', name: 'PW Edit', type: 'hr', icon: ''},
    {route: 'admin.accounts', name: 'Аккаунты', type: 'url', icon: 'fas fa-address-book'},
    {route: 'admin.persons', name: 'Персонажи', type: 'url', icon: 'fas fa-user-friends'},
    {route: 'admin.guilds', name: 'Гильдии', type: 'url', icon: 'fas fa-wheelchair'},
    {route: 'admin.locations', name: 'Локации', type: 'url', icon: 'fas fa-globe'},
]


</script>
<template>
    <aside class="max-w-62.5 ease-nav-brand z-990 fixed inset-y-0 my-4 ml-4 block w-full -translate-x-full flex-wrap items-center justify-between overflow-y-auto rounded-2xl border-0 bg-white p-0 antialiased shadow-none transition-transform duration-200 xl:left-0 xl:translate-x-0 xl:bg-transparent">
        <div class="h-19.5">
            <Link :href="route('admin.dashboard')" class="block px-8 py-6 m-0 text-sm whitespace-nowrap text-slate-700">
                <img src="@/../images/pwi-logo.webp" class="inline h-full max-w-full transition-all duration-200 ease-nav-brand max-h-8" />
                <span class="ml-1 font-semibold transition-all duration-200 ease-nav-brand">PW Admin</span>
            </Link>
        </div>

        <hr class="h-px mt-0 bg-transparent bg-gradient-to-r from-transparent via-black/40 to-transparent" />

        <div class="items-center block w-auto max-h-screen overflow-auto h-sidenav grow basis-full">
            <ul v-for="rlink in navlinks" class="flex flex-col pl-0 mb-0">
                <li class="mt-0.5 w-full" v-if="rlink.type==='url'">
                    <Link :href="route(rlink.route)" :class="activeStyle(route().current(rlink.route))">
                        <div :class="activeStyle2(route().current(rlink.route))">
                            <i :class="rlink.icon"></i>
                        </div>
                        <span class="ml-1 duration-300 opacity-100 pointer-events-none ease-soft">{{ rlink.name }}</span>
                    </Link>
                </li>
                <li class="w-full mt-4" v-else="0">
                    <h6 class="pl-6 ml-2 font-bold leading-tight uppercase text-xs opacity-60">{{ rlink.name }}</h6>
                </li>
            </ul>
        </div>

        <div class="mx-4">
        </div>
    </aside>
</template>
