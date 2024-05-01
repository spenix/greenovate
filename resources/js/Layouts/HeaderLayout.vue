<script setup>
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
defineProps({
    systemSetup: {
        type: Object,
    },
});

const user = usePage().props.auth.user;

</script>

<template>
    <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between">
    <Link :href="route('dashboard')" class="logo d-flex align-items-center">
        <img :src="systemSetup?.appLogo1" alt="">
        <span class="d-none d-lg-block">{{ systemSetup?.nameShort }}</span>
    </Link>
    <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->


    <nav class="header-nav ms-auto">
    <ul class="d-flex align-items-center">

        <li class="nav-item dropdown pe-3">

        <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <img src="assets/icons/adminLogo.png" alt="Profile" class="rounded-circle">
            <span class="d-none d-md-block dropdown-toggle ps-2">{{ user?.name }}</span>
        </a><!-- End Profile Iamge Icon -->

        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
            <h6>{{ user?.name }}</h6>
            <span>{{ user?.role }}</span>
            </li>
            <li>
            <hr class="dropdown-divider">
            </li>

            <li>
                <ResponsiveNavLink :href="route('profile.edit')" class="dropdown-item d-flex align-items-center">  
                    <i class="bi bi-person"></i>
                    <span>My Profile</span> 
                </ResponsiveNavLink>
            </li>
            <li>
            <hr class="dropdown-divider">
            </li>
            <li>
                <ResponsiveNavLink :href="route('logout')" class="dropdown-item d-flex align-items-center" method="post" as="button">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Sign Out</span>
                </ResponsiveNavLink>
                <!-- <Link :href="route('logout')" class="dropdown-item d-flex align-items-center">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Sign Out</span>
                </Link> -->
            </li>

        </ul><!-- End Profile Dropdown Items -->
        </li><!-- End Profile Nav -->

    </ul>
    </nav><!-- End Icons Navigation -->

  </header><!-- End Header -->
</template>
