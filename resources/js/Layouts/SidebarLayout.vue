<script setup>
import { Link, usePage } from '@inertiajs/vue3';
const page = usePage()
const user = usePage().props.auth.user;
</script>

<template>
    <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
    <!-- Dashboard -->
    <li class="nav-item">
        <Link :href="route('dashboard')" class="nav-link" :class="page?.url != '/dashboard' ? 'collapsed' : ''">
            <i class="bi bi-grid"></i>
            <span>Dashboard</span>
        </Link>
    </li>
     <!-- End Dashboard -->
     <!-- Calendar -->
     <li class="nav-item">
        <Link :href="route('system-calendar')" class="nav-link" :class="page?.url != '/system-calendar' ? 'collapsed' : ''">
            <i class="bi bi-calendar-event"></i>
            <span>Event</span>
        </Link>
    </li>
    <!-- End Calendar -->
     <!-- Employees -->
    <li class="nav-item">
        <Link :href="route('employees')" class="nav-link" :class="page?.url != '/employees' ? 'collapsed' : ''">
            <i class="ri-user-settings-line"></i>
            <span>Employees</span>
        </Link>
    </li>
    <!-- End Employees -->
    <!-- Projects -->
    <li class="nav-item">
        <Link :href="route('project')" class="nav-link" :class="page?.url != '/project' ? 'collapsed' : ''">
            <i class="bi bi-building"></i>
            <span>Project</span>
        </Link>
    </li>
    <!-- End Projects -->
    <!-- Attendance -->
    <li class="nav-item">
        <a class="nav-link" :class="['/daily-attendance', '/attendance-report'].includes(page?.url) ? '' : 'collapsed'" data-bs-target="#attendance-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-calendar-check"></i><span>Attendance</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="attendance-nav" class="nav-content collapse " :class="['/daily-attendance', '/attendance-report'].includes(page?.url) ? 'show' : ''" data-bs-parent="#sidebar-nav">
        <li>
            <Link :href="route('daily-attendance')" class="nav-link" :class="page?.url == '/daily-attendance' ? 'active' : ''">
                <i class="bi bi-circle"></i><span>Daily Attendance</span>
            </Link>
        </li>
        <li>
            <Link :href="route('attendance-report')" class="nav-link" :class="page?.url == '/attendance-report' ? 'active' : ''">
                <i class="bi bi-circle"></i><span>Attendance Report</span>
            </Link>
        </li>
        </ul>
    </li>
    
    <!-- End Attendance -->
    <!-- Shift Management -->
    <li class="nav-item">
        <a class="nav-link" :class="['/employee-shift', '/shift-code'].includes(page?.url) ? '' : 'collapsed'" data-bs-target="#shift-management-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-calendar-plus"></i><span>Shift Management</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="shift-management-nav" class="nav-content collapse " :class="['/employee-shift', '/shift-code'].includes(page?.url) ? 'show' : ''" data-bs-parent="#sidebar-nav">
        <li>
            <Link :href="route('employee-shift')" class="nav-link" :class="page?.url == '/employee-shift' ? 'active' : ''">
                <i class="bi bi-circle"></i><span>Employee Shift Setup</span>
            </Link>
        </li>
        <li>
            <Link :href="route('shift-code')" class="nav-link" :class="page?.url == '/shift-code' ? 'active' : ''">
                <i class="bi bi-circle"></i><span>Shift Code</span>
            </Link>
        </li>
        </ul>
    </li>
    
    <!-- End Shift Management -->

    <!-- Leave -->
    <li class="nav-item">
        <a class="nav-link" :class="['/employee-leaves', '/leave-types', '/leave-credits', '/leave-history'].includes(page?.url) ? '' : 'collapsed'" data-bs-target="#leave-nav" data-bs-toggle="collapse" href="#">
        <i class="ri-sailboat-line"></i><span>Leave</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="leave-nav" class="nav-content collapse " :class="['/employee-leaves', '/leave-types', '/leave-credits', '/leave-history'].includes(page?.url) ? 'show' : ''" data-bs-parent="#sidebar-nav">
        <li>
            <Link :href="route('leave-history')" class="nav-link" :class="page?.url == '/leave-history' ? 'active' : ''">
                <i class="bi bi-circle"></i><span>Leave History</span>
            </Link>
        </li>
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
    </li>
     <!-- End Leave -->

     <!-- Payroll -->
    <li class="nav-item">
        <a class="nav-link" :class="['/payroll', '/payslip-report', '/compensations', '/deductions', '/basic-salary'].includes(page?.url) ? '' : 'collapsed'" data-bs-target="#payroll-nav" data-bs-toggle="collapse" href="#">
        <i class="ri-calculator-line"></i><span>Payroll</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="payroll-nav" class="nav-content collapse " :class="['/payroll', '/payslip-report', '/compensations', '/deductions', '/basic-salary'].includes(page?.url) ? 'show' : ''" data-bs-parent="#sidebar-nav">
        <li>
            <Link :href="route('payroll')" class="nav-link" :class="page?.url == '/payroll' ? 'active' : ''">
                <i class="bi bi-circle"></i><span>Payroll List</span>
            </Link>
        </li>
        <!-- <li>
            <Link :href="route('payslip-report')" class="nav-link" :class="page?.url == '/payslip-report' ? 'active' : ''">
                <i class="bi bi-circle"></i><span>Payslip Report</span>
            </Link>
        </li> -->
        <li>
            <Link :href="route('compensations')" class="nav-link" :class="page?.url == '/compensations' ? 'active' : ''">
                <i class="bi bi-circle"></i><span>Compensations</span>
            </Link>
        </li>
        <li>
            <Link :href="route('deductions')" class="nav-link" :class="page?.url == '/deductions' ? 'active' : ''">
                <i class="bi bi-circle"></i><span>Deductions</span>
            </Link>
        </li>
        <li>
            <Link :href="route('basic-salary')" class="nav-link" :class="page?.url == '/basic-salary' ? 'active' : ''">
                <i class="bi bi-circle"></i><span>Basic Salary</span>
            </Link>
        </li>
        <!-- <li>
            <a href="components-accordion.html">
            <i class="bi bi-circle"></i><span>Terminated</span>
            </a>
        </li> -->
        </ul>
    </li>
    <!-- End Payroll -->

    <!-- Performance Monitoring -->
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
    </li>
    <!-- End Performance Monitoring -->

    <!-- Code Library -->
    <li class="nav-item">
        <a class="nav-link " :class="['/departments', '/designations'].includes(page?.url) ? '' : 'collapsed'" data-bs-target="#code-library-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-gear"></i><span>Code Library</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="code-library-nav" class="nav-content collapse " :class="['/departments', '/designations'].includes(page?.url) ? 'show' : ''" data-bs-parent="#sidebar-nav">
        <li>
            <Link :href="route('departments')" class="nav-link" :class="page?.url == '/departments' ? 'active' : ''">
                <i class="bi bi-circle"></i><span>Departments</span>
            </Link>
        </li>
        <li>
            <Link :href="route('positions')" class="nav-link" :class="page?.url == '/positions' ? 'active' : ''">
                <i class="bi bi-circle"></i><span>Positions</span>
            </Link>
        </li>
        <!-- <li>
            <a href="components-accordion.html">
            <i class="bi bi-circle"></i><span>Terminated</span>
            </a>
        </li> -->
        </ul>
    </li>
    <!-- End Performance Monitoring -->
    </ul>
  </aside><!-- End Sidebar-->
</template>
