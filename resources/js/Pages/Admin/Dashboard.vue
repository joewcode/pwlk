<script setup>
import { ref, reactive } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, usePage } from '@inertiajs/vue3'

//
const page = usePage()
const serverStatus = ref(page.props.server)
const gameSers = reactive([
    {name: 'logservices', status: 0, title: '...', icon: 'fas fa-clipboard-list'},
    {name: 'glinkd', status: 0, title: '...', icon: 'fas fa-link'},
    {name: 'authd', status: 0, title: '...', icon: 'fas fa-user-shield'},
    {name: 'gdeliveryd', status: 0, title: '...', icon: 'fas fa-truck'},
    {name: 'gacd', status: 0, title: '...', icon: 'fas fa-shield-alt'},
    {name: 'gfactiond', status: 0, title: '...', icon: 'fas fa-users'},
    {name: 'uniquenamed', status: 0, title: '...', icon: 'fas fa-user-lock'},
    {name: 'gamedbd', status: 0, title: '...', icon: 'fas fa-database'},
])

console.log('Server status', serverStatus.value)
let cards = []

if ( serverStatus.value === true ) {
    for ( let i of gameSers ) {
        const p = page.props.server_processes[i.name] ? page.props.server_processes[i.name] : false;
        if ( p ) {
            i.status = p.id
            i.title = p.cpu+'% cpu, '+p.cpu+'% mem'
        }
    }
    cards = [
        {name: 'Сервер', text: page.props.server_rel, span: page.props.server_cpu[0][1], icon: 'fas fa-server'},
        {name: 'Память RAM', text: '... ... ...', span: page.props.server_ram['MemAvailable']+'/ '+page.props.server_ram['MemFree']+'mb', icon: 'fas fa-memory'},
        {name: 'Процессор CPU', text: page.props.server_cpu[11][1], span: 'x'+page.props.server_cpu[3][1], icon: 'fas fa-microchip'},
        {name: 'Диск HDD', text: '... ... ...', span: 'ssd', icon: 'fas fa-hdd'},
    ]
}

const toogleInstanse = (loc) => {
        if ( loc.status ) {
            console.log('Instance shutdown ID:', loc.id)
        } else {
            console.log('Instance inclusion ID:', loc.id)
        }
}

const toogleService = (name, id) => {
        if ( id > 0 ) {
            console.log('Service shutdown ID:', id, name)
        } else {
            console.log('Service inclusion ID:', name)
        }
}
</script>

<template>
    <Head title="Статистика" />
    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ствкаааа</h2>
        </template>
        <div v-if="serverStatus">
            <!-- cards -->
            <div  class="flex flex-wrap -mx-3">
                <div v-for="card in cards" class="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4 mt-2">
                    <div class="relative flex flex-col min-w-0 break-words bg-white shadow-soft-xl rounded-2xl bg-clip-border">
                        <div class="flex-auto p-4">
                            <div class="flex flex-row -mx-3">
                                <div class="flex-none w-2/3 max-w-full px-3">
                                    <div>
                                    <p class="mb-0 font-sans text-sm font-semibold leading-normal">{{ card.name }}</p>
                                    <h5 class="mb-0 font-bold">
                                        {{ card.text }}
                                        <span class="text-sm leading-normal font-weight-bolder text-lime-500">{{ card.span }}</span>
                                    </h5>
                                    </div>
                                </div>
                                <div class="px-3 text-right basis-1/3">
                                    <div class="inline-block w-12 h-12 text-center rounded-lg bg-gradient-to-tl from-purple-700 to-pink-500">
                                        <i :class="card.icon" class="leading-none text-lg relative top-3.5 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- /// -->
                
                <div class="w-full max-w-full px-3 xl:w-8/12 mt-3">
                    <div class="relative flex flex-col h-full min-w-0 break-words bg-white border-0 shadow-soft-xl rounded-2xl bg-clip-border">
                        <div class="p-4 pb-0 mb-0 bg-white border-b-0 rounded-t-2xl">
                            <h6 class="mb-0">Игровые локации</h6>
                        </div>
                        <div class="flex-auto p-4 h-48 overflow-y-auto">
                            <ul class="flex flex-col pl-0 mb-0 rounded-lg ">
                                <li v-for="loc in page.props.server_locations" class="relative block px-0 py-2 bg-white border-0 rounded-t-lg text-inherit">
                                    <div class="min-h-6 mb-0.5 block pl-0">
                                        <input @click="toogleInstanse(loc)" :checked="loc.status" :id="loc.id" class="mt-0.54 rounded-10 duration-250 ease-soft-in-out after:rounded-circle after:shadow-soft-2xl after:duration-250 checked:after:translate-x-5.25 h-5 relative float-left ml-auto w-10 cursor-pointer appearance-none border border-solid border-gray-200 bg-slate-800/10 bg-none bg-contain bg-left bg-no-repeat align-top transition-all after:absolute after:top-px after:h-4 after:w-4 after:translate-x-px after:bg-white after:content-[''] checked:border-slate-800/95 checked:bg-slate-800/95 checked:bg-none checked:bg-right" type="checkbox" />
                                        <label :for="loc.id" class="w-4/5 mb-0 ml-4 overflow-hidden font-normal cursor-pointer select-none text-sm text-ellipsis whitespace-nowrap text-slate-500">{{ loc.name }}</label>
                                        <i>{{ loc.status ? (loc.status.cpu+' cpu / '+loc.status.mem+' ram') : 'Offline' }}</i>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="w-full max-w-full px-3 lg-max:mt-6 xl:w-4/12 mt-3">
                    <div class="relative flex flex-col h-full min-w-0 break-words bg-white border-0 shadow-soft-xl rounded-2xl bg-clip-border">
                        <div class="p-4 pb-0 mb-0 bg-white border-b-0 rounded-t-2xl">
                            <h6 class="mb-0">Игровые сервисы</h6>
                        </div>
                        <div class="flex-auto p-4">
                            <ul class="flex flex-col pl-0 mb-0 rounded-lg">
                                <li v-for="sers in gameSers" class="relative flex items-center px-0 py-2 mb-2 bg-white border-0 rounded-t-lg text-inherit">
                                    <div class="inline-flex items-center justify-center w-12 h-12 mr-4 text-black transition-all duration-200 text-base ease-soft-in-out rounded-xl">
                                        <i :class="sers.icon"></i>
                                    </div>
                                    <div class="flex flex-col items-start justify-center">
                                        <h6 class="mb-0 leading-normal text-sm">{{ sers.name }}</h6>
                                        <p class="mb-0 leading-tight text-xs">{{ !sers.status ? 'Offline' : sers.title }}</p>
                                    </div>
                                    <a @click="toogleService(sers.name, sers.status)" class="inline-block py-3 pl-0 pr-4 mb-0 ml-auto font-bold text-center uppercase align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-pro text-xs ease-soft-in hover:scale-102 hover:active:scale-102 active:opacity-85 text-fuchsia-500 hover:text-fuchsia-800 hover:shadow-none active:scale-100">{{ !sers.status ? 'Включить' : 'Выключить' }}</a>
                                </li>

                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div v-else>
            Offline server
        </div>
    </AdminLayout>
</template>
