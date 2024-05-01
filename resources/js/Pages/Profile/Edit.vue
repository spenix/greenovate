<script setup>
import HeaderLayout from '@/Layouts/HeaderLayout.vue';
import SidebarLayout from '@/Layouts/SidebarLayout.vue';
import FooterLayout from '@/Layouts/FooterLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Link, Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    systemSetup: {
        type: Object,
    },
});
</script>

<template>
    <Head title="Profile" />
    <HeaderLayout :systemSetup="systemSetup"/>
    <SidebarLayout />
    <main id="main" class="main">
        <div class="pagetitle">
        <h1>Profile</h1>
        <nav>
            <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <Link :href="route('dashboard')">Home</Link>
            </li>
            <li class="breadcrumb-item active">Profile</li>
            </ol>
        </nav>
        </div>
        <UpdateProfileInformationForm
            :must-verify-email="mustVerifyEmail"
            :status="status"
        />
        <UpdatePasswordForm />
        <DeleteUserForm />
    </main>
    <FooterLayout :systemSetup="systemSetup"/>
</template>
