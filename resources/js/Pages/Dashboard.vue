<script setup>
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import HeaderLayout from '@/Layouts/HeaderLayout.vue';
import SidebarLayout from '@/Layouts/SidebarLayout.vue';
import FooterLayout from '@/Layouts/FooterLayout.vue';
import { Link, Head, usePage } from '@inertiajs/vue3';
import { ref} from "vue";
const page = usePage()
const totalEmployee = ref(page.props.totalEmployee);
const regularEmployee = ref(page.props.regularEmployee);
const terminatedEmployee = ref(page.props.terminatedEmployee);
const onCallEmployee = ref(page.props.onCallEmployee);
const onLeaveEmployee = ref(page.props.onLeaveEmployee);
const event_today = ref(page.props.event_today);
const projects = ref(page.props.projects);
const systemSetup = ref(page.props.systemSetup);

const calendarOptions = ref({
        plugins: [dayGridPlugin],
        initialView: 'dayGridMonth',
        weekends: true,
        events: page.props.events.map(d => { return {title: d?.name, start: d?.start_date, end: d?.end_date}})
      });
console.log('event_today', event_today.value);

</script>

<template>
    <Head title="Dashboard" />
    <HeaderLayout :systemSetup="systemSetup"/>
    <SidebarLayout />
    <main id="main" class="main">
        <div class="pagetitle">
        <h1>Dashboard</h1>
        <nav>
            <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <Link :href="route('dashboard')">Home</Link>
            </li>
            <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </nav>
        </div>
        <section class="section dashboard">
            <div class="row">
                <div class="col-lg-12 mb-2">
                    <div class="alert alert-primary alert-dismissible fade show" v-for="(d, i) in event_today" :key="i" role="alert">
                        <i class="bi bi-star me-1"></i>
                        {{ `${d.name} - ${d.description}` }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="row">
                        <!-- Sales Card -->
                        <div class="col-xxl-3 col-md-4">
                        <div class="card info-card total-emp-card">
                            <div class="card-body">
                            <h5 class="card-title">Total Employees</h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="ri-user-2-line"></i>
                                </div>
                                <div class="ps-3">
                                <h6>{{ totalEmployee }}</h6>
                                </div>
                            </div>
                            </div>

                        </div>
                        </div><!-- End Sales Card -->

                        <!-- Revenue Card -->
                        <div class="col-xxl-3 col-md-4">
                        <div class="card info-card regular-card">
                            <div class="card-body">
                            <h5 class="card-title">Regular</h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="ri-shield-user-line"></i>
                                </div>
                                <div class="ps-3">
                                <h6>{{ regularEmployee }}</h6>
                                </div>
                            </div>
                            </div>
                        </div>
                        </div><!-- End Revenue Card -->

                        <!-- Revenue Card -->
                        <div class="col-xxl-3 col-md-4">
                        <div class="card info-card terminated-card">
                            <div class="card-body">
                            <h5 class="card-title">Terminated</h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="ri-user-unfollow-fill"></i>
                                </div>
                                <div class="ps-3">
                                <h6>{{ terminatedEmployee }}</h6>
                                </div>
                            </div>
                            </div>
                        </div>
                        </div><!-- End Revenue Card -->
                        <!-- Sales Card -->
                        <div class="col-xxl-3 col-md-4">
                        <div class="card info-card on-call-card">
                            <div class="card-body">
                            <h5 class="card-title">On-call</h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="ri-cellphone-line"></i>
                                </div>
                                <div class="ps-3">
                                <h6>{{ onCallEmployee }}</h6>
                                </div>
                            </div>
                            </div>

                        </div>
                        </div><!-- End Sales Card -->

                        <!-- Revenue Card -->
                        <div class="col-xxl-3 col-md-4">
                        <div class="card info-card leave-card">
                            <div class="card-body">
                            <h5 class="card-title">Today's Leave</h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-envelope"></i>
                                </div>
                                <div class="ps-3">
                                <h6>{{ onLeaveEmployee }}</h6>
                                </div>
                            </div>
                            </div>
                        </div>
                        </div><!-- End Revenue Card -->

                        <!-- Revenue Card -->
                        <div class="col-xxl-3 col-md-4">
                        <div class="card info-card attendance-card">
                            <div class="card-body">
                            <h5 class="card-title">Ongoing Projects</h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-building"></i>
                                </div>
                                <div class="ps-3">
                                    <h6>{{projects}}</h6>
                                </div>
                            </div>
                            </div>
                        </div>
                        </div><!-- End Revenue Card -->
                </div>
            </div>
                <div class="col-lg-12 mt-4">
                    <div class="card">
                        <div class="card-body p-4"><FullCalendar :options='calendarOptions' /></div>
                    </div>
                    
                </div>
            </div>
        </section>
    </main>
    <FooterLayout :systemSetup="systemSetup"/>
</template>
