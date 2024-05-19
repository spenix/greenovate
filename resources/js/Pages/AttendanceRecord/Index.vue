<script setup>
import HeaderLayout from '@/Layouts/HeaderLayout.vue';
import SidebarLayout from '@/Layouts/SidebarLayout.vue';
import FooterLayout from '@/Layouts/FooterLayout.vue';
import PageModal from '@/Pages/AttendanceRecord/Modal/IndexModal.vue';
import notify from "@/common/notification.js";
import http from "@/utils/https";
import { Link, Head, usePage, router, useForm } from '@inertiajs/vue3';
import { onMounted, ref} from "vue";
const page = usePage()

const props = defineProps({
    systemSetup: {
        type: Object,
    },
    departments: {
        type: Array,
    },
    shift_codes: {
        type: Array,
    },
    date_today: {
        type: String,
    },
});
const modalAttrs = ref({
    modalId: "employee-schedule-modal",
    title: "EMPLOYEE SCHEDULE",
    action: "",
});
const employee_list = ref([])
const processing = ref(false)
const form = useForm({
    department: "",
    shift_code: "",
    filter_date: props.date_today
});

const btnActionFunc = (id, action) => {
    modalAttrs.value.dataId = id;
    modalAttrs.value.title = `${action} EMPLOYEE SCHEDULE`;
    modalAttrs.value.action = action;
    $(
         `#${
            modalAttrs.value?.modalId
                ? modalAttrs.value?.modalId
                : "page-modal"
        }`
    ).modal("show");
};
const submitData = () => {
    processing.value = true;
    http.post(`${page?.url}/get_employees_with_shift`, {
        department: form.department,
        shift_code: form.shift_code,
        filter_date: form.filter_date
    }).then(
        ({ data, status }) => {
           if (status == 200) {
            employee_list.value = data;
           }
           processing.value = false;
        }
    );
}

const log_process = (emp_id = null, log_action = '') => {
    if (['TI', 'BO', 'BI', 'TO'].includes(log_action)) {
        http.post(`${page?.url}/log_process`, {
            emp_id,
            log_action,
            filter_date: form.filter_date
        }).then(
            ({ data, status }) => {
                if (status == 200) {
                    notify(
                        "Success",
                        data?.msg,
                        "success"
                    );
                    submitData();
                }
            }
        );
    } else {
        notify(
            "Info",
            "Oops, incorrect log code...",
            "info"
        );
    }
}

const reloadDatatableAjax = () => {
    modalAttrs.value.action = "";
};
onMounted(() => {
  submitData();
})
</script>

<template>
    <Head title="Daily Attendance" />
    <HeaderLayout :systemSetup="systemSetup"/>
    <SidebarLayout />
    <main id="main" class="main">
        <div class="pagetitle">
        <h1>Daily Attendance</h1>
        <nav>
            <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <Link :href="route('daily-attendance')">Attendance</Link>
            </li>
            <li class="breadcrumb-item active">Daily Attendance</li>
            </ol>
        </nav>
        </div>
        <section class="section">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title">Filters</h5>
                <form @submit.prevent="submitData">
                <div class="row">
                    <div class="col-3 mt-1">
                    <label for="department" class="form-label">Department</label>
                    <select 
                        id="department" 
                        class="form-select"
                        :class="form.errors.department ? 'error-field' : ''"
                        v-model="form.department"
                        :disabled="props?.modalAttrs?.action == 'VIEW'"
                    >
                        <option value="" hidden>
                            All Department
                        </option>
                        <option 
                        v-for="(
                            d, i
                        ) in departments"
                        :key="d.id"
                        :value="d.id"
                        :selected="
                            form.department ==
                            d?.id
                        "
                        >
                        {{ d?.name }}
                        </option>
                    </select>
                    <ErrorMessage :message="form.errors.department"/>
                </div>
                    <div class="col-3">
                        <label for="shift_code" class="form-label">Shift Code</label>
                        <select 
                            id="shift_code" 
                            class="form-select"
                            :class="form.errors.shift_code ? 'error-field' : ''"
                            v-model="form.shift_code"
                            :disabled="props?.modalAttrs?.action == 'VIEW'"
                        >
                            <option value="" hidden>
                                All Shift Code
                            </option>
                            <option 
                            v-for="(
                                d, i
                            ) in shift_codes"
                            :key="d.id"
                            :value="d.id"
                            :selected="
                                form.shift_code ==
                                d?.id
                            "
                            >
                            {{ d?.name }}
                            </option>
                        </select>
                        <ErrorMessage :message="form.errors.shift_code"/>
                    </div>
                    <div class="col-3">
                        <label for="filter_date" class="form-label">Filter Date</label>
                        <input 
                            type="date" 
                            class="form-control" 
                            name="filter_date" 
                            id="filter_date"
                            autocomplete="off"
                            :class="form.errors.filter_date ? 'error-field' : ''"
                            v-model="form.filter_date"
                            :disabled="props?.modalAttrs?.action == 'VIEW'"
                        >
                        <ErrorMessage :message="form.errors.filter_date"/>
                    </div>
                    <div class="col-3 position-relative">
                        <button type="submit" class="btn btn-primary btn-sm position-absolute top-70 start-50 translate-middle" style="width:100%;" :disabled="processing">
                            	<span><i class="bi bi-search"></i> Search Employees</span>
                        </button>
                    </div>
                </div>
                </form>
                <div class="row mt-4">
                    <div class="col-12">
                        <small><b>Legend: </b>[ T-In = Time-In, B-Out = Break-Out, B-In = Break-In, T-Out = Time-Out]</small>
                    </div>
                </div>
              </div>
            </div>
            <div class="card">
              <div class="card-body">
                <div class="row" v-if="processing">
                    <div class="col-12 mt-4 text-center">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Loading...
                    </div>
                </div>
                <div class="row" v-else>
                    <div class="col-12 mt-4 text-center" v-if="employee_list.length">
                        <table class="table table-sm">
                            <thead>
                            <tr>
                                <th scope="col" width="10%">Emp. #</th>
                                <th scope="col" width="20%">Employee Name</th>
                                <th scope="col" width="20%">Department</th>
                                <th scope="col" width="35%">Log Records</th>
                                <th scope="col" width="15%">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(d, i) in employee_list" :key="i">
                                <th scope="row">{{ d?.employee_no }}</th>
                                <td>{{ d?.employee_name }}</td>
                                <td>{{ d?.department }}</td>
                                <td>
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th><small>T-In</small></th>
                                                <th><small>B-Out</small></th>
                                                <th><small>B-In</small></th>
                                                <th><small>T-Out</small></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <span v-if="d?.log_record?.clock_in">{{ d?.log_record?.clock_in }}</span>
                                                    <span v-else>
                                                        <span class="badge bg-primary" role="button" @click="log_process(d?.employee_id, 'TI')" >T-In</span>
                                                    </span>
                                                    
                                                </td>
                                                <td>
                                                    <span v-if="d?.log_record?.break_out">{{ d?.log_record?.break_out }}</span>
                                                    <span v-else>
                                                        <span class="badge bg-primary" role="button" @click="log_process(d?.employee_id, 'BO')" >B-Out</span>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span v-if="d?.log_record?.break_in">{{ d?.log_record?.break_in }}</span>
                                                    <span v-else>
                                                        <span class="badge bg-primary" role="button" @click="log_process(d?.employee_id, 'BI')" >B-In</span>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span v-if="d?.log_record?.clock_out">{{ d?.log_record?.clock_out }}</span>
                                                    <span v-else>
                                                        <span class="badge bg-primary" role="button" @click="log_process(d?.employee_id, 'TO')" >T-Out</span>
                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                
                                </td>
                                <td>
                                    <button type="button" class="btn btn-secondary btn-sm" @click="btnActionFunc(d?.employee_id, 'VIEW')">VIEW SCHEDULE</button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-12 mt-4 text-center" v-else>No Data Results.</div>
                </div>
              </div>
            </div>
        </section>
        <PageModal 
            :modalAttrs="modalAttrs"
            @reloadPageData="reloadDatatableAjax()"
        />
    </main>
    <FooterLayout :systemSetup="systemSetup"/>
</template>
