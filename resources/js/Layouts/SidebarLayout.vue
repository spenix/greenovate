<script setup>
import { Link, usePage } from '@inertiajs/vue3';
const page = usePage()
const user = usePage().props.auth.user;

console.log('user', user);
</script>

<template>
    <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">

    <li class="nav-item">
        <Link :href="route('dashboard')" class="nav-link" :class="page?.url != '/dashboard' ? 'collapsed' : ''">
            <i class="bi bi-grid"></i>
            <span>Dashboard</span>
        </Link>
    </li><!-- End Dashboard Nav -->

    <li class="nav-item">
        <Link :href="route('employees')" class="nav-link" :class="page?.url != '/employees' ? 'collapsed' : ''">
            <i class="ri-user-settings-line"></i>
            <span>Employees</span>
        </Link>
    </li><!-- End Dashboard Nav -->

    <li class="nav-item">
        <Link :href="route('employee-attendance')" class="nav-link" :class="page?.url != '/employee-attendance' ? 'collapsed' : ''">
            <i class="ri-calendar-check-line"></i>
            <span>Attendance</span>
        </Link>
    </li><!-- End Dashboard Nav -->

    <li class="nav-item">
        <a class="nav-link" :class="['/employee-leaves', '/leave-types', '/leave-credits'].includes(page?.url) ? '' : 'collapsed'" data-bs-target="#leave-nav" data-bs-toggle="collapse" href="#">
        <i class="ri-sailboat-line"></i><span>Leave</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="leave-nav" class="nav-content collapse " :class="['/employee-leaves', '/leave-types', '/leave-credits'].includes(page?.url) ? 'show' : ''" data-bs-parent="#sidebar-nav">
        <li>
            <Link :href="route('employee-leaves')" class="nav-link" :class="page?.url == '/employee-leaves' ? 'active' : ''">
                <i class="bi bi-circle"></i><span>Employee Leave Credits</span>
            </Link>
        </li>
        <li>
            <Link :href="route('leave-types')" class="nav-link" :class="page?.url == '/leave-types' ? 'active' : ''">
                <i class="bi bi-circle"></i><span>Leave Type</span>
            </Link>
        </li>
        <li>
            <Link :href="route('leave-credits')" class="nav-link" :class="page?.url == '/leave-credits' ? 'active' : ''">
                <i class="bi bi-circle"></i><span>Leave Creadit</span>
            </Link>
        </li>
        <!-- <li>
            <a href="components-accordion.html">
            <i class="bi bi-circle"></i><span>Terminated</span>
            </a>
        </li> -->
        </ul>
    </li><!-- End Components Nav -->

    <li class="nav-item">
        <a class="nav-link" :class="['/payroll', '/payslip-report'].includes(page?.url) ? '' : 'collapsed'" data-bs-target="#payroll-nav" data-bs-toggle="collapse" href="#">
        <i class="ri-calculator-line"></i><span>Payroll</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="payroll-nav" class="nav-content collapse " :class="['/payroll', '/payslip-report'].includes(page?.url) ? 'show' : ''" data-bs-parent="#sidebar-nav">
        <li>
            <Link :href="route('payroll')" class="nav-link" :class="page?.url == '/payroll' ? 'active' : ''">
                <i class="bi bi-circle"></i><span>Payroll List</span>
            </Link>
        </li>
        <li>
            <Link :href="route('payslip-report')" class="nav-link" :class="page?.url == '/payslip-report' ? 'active' : ''">
                <i class="bi bi-circle"></i><span>Payslip Report</span>
            </Link>
        </li>
        <!-- <li>
            <a href="components-accordion.html">
            <i class="bi bi-circle"></i><span>Terminated</span>
            </a>
        </li> -->
        </ul>
    </li><!-- End Components Nav -->

    <li class="nav-item">
        <a class="nav-link " :class="['/performance', '/violation-types'].includes(page?.url) ? '' : 'collapsed'" data-bs-target="#performance-monitoring-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-menu-button-wide"></i><span>Performance Monitoring</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="performance-monitoring-nav" class="nav-content collapse " :class="['/performance', '/violation-types'].includes(page?.url) ? 'show' : ''" data-bs-parent="#sidebar-nav">
        <li>
            <Link :href="route('performance')" class="nav-link" :class="page?.url == '/performance' ? 'active' : ''">
                <i class="bi bi-circle"></i><span>Performance</span>
            </Link>
        </li>
        <li>
            <Link :href="route('violation-types')" class="nav-link" :class="page?.url == '/violation-types' ? 'active' : ''">
                <i class="bi bi-circle"></i><span>Violation Type</span>
            </Link>
        </li>
        <!-- <li>
            <a href="components-accordion.html">
            <i class="bi bi-circle"></i><span>Terminated</span>
            </a>
        </li> -->
        </ul>
    </li><!-- End Components Nav -->
    </ul>
  </aside><!-- End Sidebar-->
</template>
