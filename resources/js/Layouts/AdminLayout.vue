<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link, usePage } from '@inertiajs/vue3';
import Sidebar from './Admin/sidebar.vue';
import Header from './Admin/header.vue';
import Footer from './Admin/footer.vue';

const showingNavigationDropdown = ref(false);

const page = usePage()
// console.log(page.props.server)

</script>

<template>
    <Sidebar/>
    <main class="ease-soft-in-out xl:ml-68.5 relative h-full max-h-screen rounded-xl transition-all duration-200">
        <Header />
        <div class="w-full px-6 py-6 mx-auto">
            <div class="appmain">
                <slot />
            </div>
            <Footer />
        </div>
    </main>
</template>

<style>
.appmain {
    min-height: 82vh;
}
</style>