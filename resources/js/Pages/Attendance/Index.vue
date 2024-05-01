<script setup>
import HeaderLayout from '@/Layouts/HeaderLayout.vue';
import SidebarLayout from '@/Layouts/SidebarLayout.vue';
import FooterLayout from '@/Layouts/FooterLayout.vue';
import { Link, Head } from '@inertiajs/vue3';

defineProps({
    systemSetup: {
        type: Object,
    },
});
</script>

<template>
    <Head title="Attendance" />
    <HeaderLayout :systemSetup="systemSetup"/>
    <SidebarLayout />
    <main id="main" class="main">
        <div class="pagetitle">
        <h1>Attendace</h1>
        <nav>
            <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <Link :href="route('dashboard')">Home</Link>
            </li>
            <li class="breadcrumb-item active">Attendace</li>
            </ol>
        </nav>
        </div>
        <section class="section">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title">Attendace List</h5>
                
              </div>
            </div>
        </section>
    </main>
    <FooterLayout :systemSetup="systemSetup"/>
</template>
