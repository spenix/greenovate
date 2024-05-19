<script setup>
import html2pdf from 'html2pdf.js'
import HeaderLayout from '@/Layouts/HeaderLayout.vue';
import SidebarLayout from '@/Layouts/SidebarLayout.vue';
import FooterLayout from '@/Layouts/FooterLayout.vue';
import http from "@/utils/https";
import moment from "moment";
import { Link, Head, usePage, useForm } from '@inertiajs/vue3';
import { onMounted, ref} from "vue";
const page = usePage()
const employees = ref(page.props?.employees ?? []);
const emp_id_select = ref();
const props = defineProps({
    systemSetup: {
        type: Object,
    },
});
const employee_list = ref([])
const processing = ref(false)
const form = useForm({
    employee: "",
    employee_name: "",
    department: "",
    designation: "",
    period_start: "",
    period_end: ""
});

const submitData = () => {
    processing.value = true;
    http.post(`${page?.url}/get_employee_dtr_rec`, {
        employee: form.employee,
        period_start: form.period_start,
        period_end: form.period_end
    }).then(
        ({ data, status }) => {
           if (status == 200) {
            employee_list.value = data;
           }
           processing.value = false;
        }
    );
}

const printDTR = () => {
//   var printdata = document.getElementById('myform');
//   var newwin = window.open("");
//   newwin.document.write(printdata.outerHTML);
//   newwin.print();
//   newwin.close();
  html2pdf(document.getElementById('printDTR'), {
    margin: 0.5,
    filename: `Daily_Time_Record.pdf`,
    html2canvas: { dpi: 192, letterRendering: true, useCORS: true },
    jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
})
}

const getSelectedEmp = (selectedVal) => {
  var emp = employees.value.find((d) => {
       return d.id == selectedVal
    });
    form.employee = emp?.id;
    form.department = emp?.department;
    form.designation = emp?.designation;
    form.employee_name = emp?.employee_name;
    
};
onMounted(() => {
  emp_id_select.value = $("#employee").selectize({
        onChange: function(value) {
            getSelectedEmp(value);
        }
    })
})
</script>

<template>
    <Head title="Attendance Report" />
    <HeaderLayout :systemSetup="systemSetup"/>
    <SidebarLayout />
    <main id="main" class="main">
        <div class="pagetitle">
        <h1>Attendance Report</h1>
        <nav>
            <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <Link :href="route('daily-attendance')">Attendance</Link>
            </li>
            <li class="breadcrumb-item active">Attendance Report</li>
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
                    <label for="employee" class="form-label">employee</label>
                    <select 
                        id="employee" 
                        :class="form.errors.employee ? 'error-field' : ''"
                        v-model="form.employee"
                        :disabled="props?.modalAttrs?.action == 'VIEW'"
                    >
                        <option value="" hidden>
                            All employee
                        </option>
                        <option 
                        v-for="(
                            d, i
                        ) in employees"
                        :key="d.id"
                        :value="d.id"
                        :selected="
                            form.employee ==
                            d?.id
                        "
                        >
                        {{ d?.employee_name }}
                        </option>
                    </select>
                </div>
                    <div class="col-3">
                        <label for="period_start" class="form-label">Period Start</label>
                        <input 
                            type="date" 
                            class="form-control" 
                            name="period_start" 
                            id="period_start"
                            autocomplete="off"
                            :class="form.errors.period_start ? 'error-field' : ''"
                            v-model="form.period_start"
                            :disabled="props?.modalAttrs?.action == 'VIEW'"
                        >
                    </div>
                    <div class="col-3">
                        <label for="period_end" class="form-label">Period End</label>
                        <input 
                            type="date" 
                            class="form-control" 
                            name="period_end" 
                            id="period_end"
                            autocomplete="off"
                            :class="form.errors.period_end ? 'error-field' : ''"
                            v-model="form.period_end"
                            :disabled="props?.modalAttrs?.action == 'VIEW'"
                        >
                    </div>
                    <div class="col-3 position-relative">
                        <button type="submit" class="btn btn-primary btn-sm position-absolute top-70 start-50 translate-middle" style="width:100%;" :disabled="processing">
                            	<span><i class="bi bi-search"></i> Search Employees</span>
                        </button>
                    </div>
                </div>
                </form>
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
                    <div class="row mb-4" id="printDTR">
                        <div class="col-12 text-center mb-2"><h5>Daily Time Record</h5></div>
                        <div class="col-12 text-center mb-4">Period start from {{ moment(form.period_start).format('L') }} to {{ moment(form.period_end).format('L') }}</div>
                        <div class="col-4 text-start mb-4"><span><b>Employee: </b>{{ form.employee_name }}</span></div>
                        <div class="col-4 text-start mb-4"><span><b>Department: </b>{{ form.department }}</span></div>
                        <div class="col-4 text-start mb-4"><span><b>Position: </b>{{ form.designation }}</span></div>
                        <div class="col-12">
                            <table class="table table-sm">
                            <thead>
                            <tr>
                                <th scope="col" width="20%">Date</th>
                                <th scope="col" width="20%">Time-In</th>
                                <th scope="col" width="20%">Break-In</th>
                                <th scope="col" width="20%">Break-Out</th>
                                <th scope="col" width="20%">Time-Out</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(d, i) in employee_list" :key="i">
                                <th scope="row">{{ moment(d?.date_in).format('L') }}</th>
                                <td>{{ d?.clock_in }}</td>
                                <td>{{ d?.break_out }}</td>
                                <td>{{ d?.break_in }}</td>
                                <td>{{ d?.clock_out }}</td>
                            </tr>
                            </tbody>
                        </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 text-end">
                            <button type="button" class="btn btn-primary" @click="printDTR()"><i class="bi bi-printer"></i> Print</button>
                        </div>
                    </div>
                    </div>
                    <div class="col-12 mt-4 text-center" v-else>No Data Results.</div>
                </div>
              </div>
            </div>
        </section>
    </main>
    <FooterLayout :systemSetup="systemSetup"/>
</template>
