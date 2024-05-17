<script setup>
import ErrorMessage from '@/Components/ErrorMessage.vue';
import { usePage, useForm } from '@inertiajs/vue3';
import { ref, watch, onMounted} from "vue";
import moment from "moment";
import http from "@/utils/https";
import notify from "@/common/notification.js";
import { commonFuntions } from "@/common/common-function.js";
const { isNumber, converToCurrencyFormat } = commonFuntions();
const emit = defineEmits(["reloadPageData"]);
const page = usePage()

const employees = ref(page.props.employees);
const dtr_records = ref([]);
const dtr_attachment = ref("");
const emp_id_select = ref();
const form = useForm({
    id: "",
    employee: "",
    dtr_record: "",
    position: "",
    date_hired: "",
    basic_salary: "",
    default_basic_salary: "",
    period_start: "",
    period_end: "",
    working_hours: "",
    working_days: "",
    ot_hours: "",
    ot_compensation: "",
    compensation: [],
    deductions: [],
});

const resetFormAction = () => {
    form.reset();
    form.clearErrors();
};

const props = defineProps({
    modalAttrs: Object,
});

const submitData = () => {
  if (props?.modalAttrs?.action == "EDIT") {
        form.put(route('payroll.update', [form.id]), {
            preserveScroll: true,
            onSuccess: () => resetFormData(),
            onError: () => {
              if (form.errors.errorMessage) {
                  notify(
                      "Error",
                      form.errors.errorMessage,
                      "danger"
                  );
                }
            },
        });
    } else {
        form.post(route('payroll.store'), {
            preserveScroll: true,
            onSuccess: () => resetFormData(),
            onError: () => {
                if (form.errors.errorMessage) {
                  notify(
                      "Error",
                      form.errors.errorMessage,
                      "danger"
                  );
                }
            },
        });
    }
}

const resetFormData = () => {
    resetFormAction();
    notify(
        "Success",
        `Payroll was ${
            props?.modalAttrs?.action == "EDIT" ? "updated" : "saved"
        } successfully.`,
        "success"
    );
    $(
        `#${
            props?.modalAttrs?.modalId
                ? props?.modalAttrs?.modalId
                : "page-modal"
        }`
    ).modal("hide");
    emit("reloadPageData");
};

watch(props?.modalAttrs, (newValue) => {
  resetFormAction();
    if (newValue?.action != "ADD") {
        http.get(`${page?.url}/show/${newValue?.dataId}`).then(
            ({ data, status }) => {
                form.id = data.id
                form.employee = data.employee_id,
                emp_id_select.value[0].selectize.setValue(data.employee_id)
                form.basic_salary = converToCurrencyFormat(data.basic_salary),
                form.period_start = data.period_start
                form.period_end = data.period_end
                form.working_hours = data.working_hours
                form.working_days = data.working_days
                form.ot_hours = data.ot_hours
                form.ot_compensation = data.ot_compensation
                form.position = data?.designation
                form.date_hired = moment(data?.date_hired).format('L')
                form.compensation = data?.compensations?.map(d => { return { payroll_id:d.payroll_id, id: d?.benefit?.id, compensation: d?.benefit?.name, amount: converToCurrencyFormat(d?.amount)} }) 
                form.deductions = data?.deductions?.map(d => { return { payroll_id:d.payroll_id, id: d?.deduction_details?.id, deduction: d?.deduction_details?.name, amount: converToCurrencyFormat(d?.amount)} }) 
                var emp = employees.value.find((d) => {
                  return d.id == data.employee_id
                });
                form.default_basic_salary = converToCurrencyFormat(emp?.rate)
                dtr_records.value = emp?.attendance_records?.attachments?.map(d => {
                  return {id: d?.id, period: `${moment(d?.period_start).format('L')}-${moment(d?.period_end).format('L')}`, attachment: d?.attachment_path, period_start: d?.period_start, period_end: d?.period_end}
                })

                var dtr = emp?.attendance_records?.attachments?.find(d => {
                  if ((d?.period_start == data.period_start) && (data.period_end == data.period_end)) {
                    return {id: d?.id, period: `${moment(d?.period_start).format('L')}-${moment(d?.period_end).format('L')}`, attachment: d?.attachment_path, period_start: d?.period_start, period_end: d?.period_end}
                  }
               
                })
                form.dtr_record = dtr?.id
                dtr_attachment.value = dtr?.attachment_path
            }
        );
    }
    if(newValue?.action == "VIEW") {
        emp_id_select.value[0].selectize.disable();
    }
});

const getSelectedEmp = (selectedVal) => {
  var emp = employees.value.find((d) => {
       return d.id == selectedVal
    });
    // 
    form.compensation = emp?.compensations?.map(d => { return { id: d?.benefit?.id, compensation: d?.benefit?.name, amount: converToCurrencyFormat(d?.benefit?.amount)} }) 
    form.deductions = emp?.deductions?.map(d => { return { id: d?.deduction_details?.id, deduction: d?.deduction_details?.name, amount: converToCurrencyFormat(d?.deduction_details?.amount)} }) 
    form.employee = emp?.id
    form.position = emp?.designation
    form.date_hired = moment(emp?.date_hired).format('L')
    form.basic_salary = converToCurrencyFormat(emp?.rate)
    form.default_basic_salary = converToCurrencyFormat(emp?.rate)
   dtr_records.value = emp?.attendance_records?.attachments?.map(d => {
    return {id: d?.id, period: `${moment(d?.period_start).format('L')}-${moment(d?.period_end).format('L')}`, attachment: d?.attachment_path, period_start: d?.period_start, period_end: d?.period_end}
   })
};

const getDtrAttachment = (evt) => {
  var dtr = evt?.target?.value;
  var data = dtr_records.value.find((d) => {
       return d.id == dtr
    });
    dtr_attachment.value = data?.attachment
    form.period_start = data?.period_start
    form.period_end = data?.period_end
}

const converToCurrency = (evt, col_name) => {
    const inputValue = evt.target.value.replace(/,/g, "");
            var val = "0.00";
            if (inputValue) {
                var val = parseFloat(inputValue).toLocaleString("en-US", {
                    style: "decimal",
                    maximumFractionDigits: 2,
                    minimumFractionDigits: 2,
                });
            }
    form[col_name] = val
}

const converToCurrencyV2 = (evt, col_name, index, name) => {
    const inputValue = evt.target.value.replace(/,/g, "");
            var val = "0.00";
            if (inputValue) {
                var val = parseFloat(inputValue).toLocaleString("en-US", {
                    style: "decimal",
                    maximumFractionDigits: 2,
                    minimumFractionDigits: 2,
                });
            }
    form[col_name][index][name] = val
}

onMounted(() => {
    emp_id_select.value = $("#select_employee_id").selectize({
        onChange: function(value) {
            getSelectedEmp(value);
        }
    });
})
</script>

<template>
    <div class="modal fade" :id="modalAttrs.modalId" tabindex="-1" data-bs-backdrop="false" style="display: none;" aria-hidden="true">
      <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ modalAttrs.title }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form @submit.prevent="submitData">
            <div class="modal-body" style="min-height: 450px; max-height: 450px;">
              <div class="row">
                <div class="col-3 mt-2">
                  <div class="row">
                  <div class="col-12 mt-2">
                  <label for="select_employee_id" class="form-label">Employee Name</label>
                    <select 
                                id="select_employee_id" 
                                aria-label="Select Employee Name"
                                :disabled="modalAttrs?.action == 'VIEW'"
                            >
                            <option value="" hidden>
                                Select Employee Name
                            </option>
                            <option 
                            v-for="(
                                d, i
                            ) in employees"
                            :key="d.id"
                            :value="d.id"
                            :selected="
                                form.employee_name ==
                                d?.id
                            "
                            >
                            {{ d?.employee_name }}
                            </option>
                            </select>
                  <ErrorMessage :message="form.errors.employee_name"/>
                </div>
                <div class="col-12 mt-2">
                  <label for="position" class="form-label">Position</label>
                  <input 
                    type="text" 
                    class="form-control" 
                    name="position" 
                    id="position"
                    autocomplete="off"
                    placeholder="Position"
                    :class="form.errors.position ? 'error-field' : ''"
                    v-model="form.position"
                    :disabled="props?.modalAttrs?.action == 'VIEW'"
                    readonly
                  >
                  <ErrorMessage :message="form.errors.position"/>
                </div>
                <div class="col-12 mt-2">
                  <label for="date_hired" class="form-label">Date Hired</label>
                  <input 
                    type="text" 
                    class="form-control" 
                    name="date_hired" 
                    id="date_hired"
                    autocomplete="off"
                    placeholder="Date Hired"
                    :class="form.errors.date_hired ? 'error-field' : ''"
                    v-model="form.date_hired"
                    :disabled="props?.modalAttrs?.action == 'VIEW'"
                    readonly
                  >
                  <ErrorMessage :message="form.errors.date_hired"/>
                </div>
                <div class="col-12 mt-2">
                  <label for="default_basic_salary" class="form-label">Basic Salary</label>
                  <input 
                    type="text" 
                    class="form-control" 
                    name="default_basic_salary" 
                    id="default_basic_salary"
                    autocomplete="off"
                    placeholder="Basic Salary"
                    :class="form.errors.default_basic_salary ? 'error-field' : ''"
                    v-model="form.default_basic_salary"
                    :disabled="props?.modalAttrs?.action == 'VIEW'"
                    readonly
                  >
                  <ErrorMessage :message="form.errors.default_basic_salary"/>
                </div>
                <div class="col-12 mt-1">
                  <label for="dtr_record" class="form-label">DTR Period</label>
                  <select 
                    id="dtr_record" 
                    class="form-select"
                    :class="form.errors.dtr_record ? 'error-field' : ''"
                    v-model="form.dtr_record"
                    @change="getDtrAttachment($event)"
                    :disabled="props?.modalAttrs?.action == 'VIEW'"
                  >
                    <option value="" hidden>
                        Select DTR Period
                    </option>
                    <option 
                    v-for="(
                          d, i
                      ) in dtr_records"
                      :key="d.id"
                      :value="d.id"
                      :selected="
                          form.dtr_record ==
                          d?.id
                      "
                    >
                    {{ d?.period }}
                    </option>
                  </select>
                  <ErrorMessage :message="form.errors.dtr_record"/>
                </div>
                <div class="col-12 mt-1" v-if="dtr_attachment != ''">
                  <a :href="dtr_attachment" target="_blank">View DTR</a>
                </div>
                </div>
                
                </div>
                <div class="col-9">
                  <ul class="nav nav-tabs nav-tabs-bordered d-flex" id="borderedTabJustified" role="tablist">
                    <li class="nav-item flex-fill" role="presentation">
                      <button class="nav-link w-100 active" id="home-tab" data-bs-toggle="tab" data-bs-target="#bordered-justified-payroll" type="button" role="tab" aria-controls="Ppayroll" aria-selected="true">Payroll</button>
                    </li>
                    <li class="nav-item flex-fill" role="presentation">
                      <button class="nav-link w-100" id="profile-tab" data-bs-toggle="tab" data-bs-target="#bordered-justified-compensation" type="button" role="tab" aria-controls="compensation" aria-selected="false" tabindex="-1">Compensation</button>
                    </li>
                    <li class="nav-item flex-fill" role="presentation">
                      <button class="nav-link w-100" id="contact-tab" data-bs-toggle="tab" data-bs-target="#bordered-justified-deduction" type="button" role="tab" aria-controls="deduction" aria-selected="false" tabindex="-1">Deduction</button>
                    </li>
                  </ul>
                  <div class="tab-content pt-2" id="borderedTabJustifiedContent">
                    <div class="tab-pane fade show active" id="bordered-justified-payroll" role="tabpanel" aria-labelledby="home-tab">
                      <div class="row">
                        <div class="col-6 mt-2">
                          <label for="period_start" class="form-label">Period Start</label>
                          <input 
                            type="date" 
                            class="form-control" 
                            name="period_start" 
                            id="period_start"
                            autocomplete="off"
                            placeholder="Period Start"
                            :class="form.errors.period_start ? 'error-field' : ''"
                            v-model="form.period_start"
                            :disabled="props?.modalAttrs?.action == 'VIEW'"
                            readonly
                          >
                          <ErrorMessage :message="form.errors.period_start"/>
                        </div>
                        <div class="col-6 mt-2">
                          <label for="period_end" class="form-label">Period End</label>
                          <input 
                            type="date" 
                            class="form-control" 
                            name="period_end" 
                            id="period_end"
                            autocomplete="off"
                            placeholder="Period End"
                            :class="form.errors.period_end ? 'error-field' : ''"
                            v-model="form.period_end"
                            :disabled="props?.modalAttrs?.action == 'VIEW'"
                            readonly
                          >
                          <ErrorMessage :message="form.errors.period_end"/>
                        </div>
                        <div class="col-6 mt-2">
                          <label for="working_days" class="form-label">Working Days</label>
                          <input 
                            type="text" 
                            class="form-control" 
                            name="working_days" 
                            id="working_days"
                            autocomplete="off"
                            placeholder="Working Days"
                            @keypress="isNumber($event)" 
                            :class="form.errors.working_days ? 'error-field' : ''"
                            v-model="form.working_days"
                            :disabled="props?.modalAttrs?.action == 'VIEW'"
                          >
                          <ErrorMessage :message="form.errors.working_days"/>
                        </div>
                        <div class="col-6 mt-2">
                          <label for="working_hours" class="form-label">Working Hours</label>
                          <input 
                            type="text" 
                            class="form-control" 
                            name="working_hours" 
                            id="working_hours"
                            autocomplete="off"
                            placeholder="Working Hours"
                            @keypress="isNumber($event)" 
                            :class="form.errors.working_hours ? 'error-field' : ''"
                            v-model="form.working_hours"
                            :disabled="props?.modalAttrs?.action == 'VIEW'"
                          >
                          <ErrorMessage :message="form.errors.working_hours"/>
                        </div>
                        <div class="col-6 mt-2">
                          <label for="ot_hours" class="form-label">OT Hours</label>
                          <input 
                            type="text" 
                            class="form-control" 
                            name="ot_hours" 
                            id="ot_hours"
                            autocomplete="off"
                            placeholder="OT Hours"
                            @keypress="isNumber($event)" 
                            :class="form.errors.ot_hours ? 'error-field' : ''"
                            v-model="form.ot_hours"
                            :disabled="props?.modalAttrs?.action == 'VIEW'"
                          >
                          <ErrorMessage :message="form.errors.ot_hours"/>
                        </div>
                        <div class="col-6 mt-2">
                          <label for="ot_compensation" class="form-label">OT Compensation</label>
                          <input 
                            type="text" 
                            class="form-control text-end" 
                            name="ot_compensation" 
                            id="ot_compensation"
                            autocomplete="off"
                            placeholder="OT Compensation"
                            @keypress="isNumber($event)" 
                            @blur="
                                converToCurrency(
                                    $event, 'ot_compensation'
                                )
                            "
                            :class="form.errors.ot_compensation ? 'error-field' : ''"
                            v-model="form.ot_compensation"
                            :disabled="props?.modalAttrs?.action == 'VIEW'"
                          >
                          <ErrorMessage :message="form.errors.ot_compensation"/>
                        </div>
                        <div class="col-6 mt-2">
                          <label for="basic_salary" class="form-label">Basic Salary</label>
                          <input 
                            type="text" 
                            class="form-control text-end" 
                            name="basic_salary" 
                            id="basic_salary"
                            autocomplete="off"
                            placeholder="Basic Salary"
                            @keypress="isNumber($event)" 
                            @blur="
                                converToCurrency(
                                    $event, 'basic_salary'
                                )
                            "
                            :class="form.errors.basic_salary ? 'error-field' : ''"
                            v-model="form.basic_salary"
                            :disabled="props?.modalAttrs?.action == 'VIEW'"
                          >
                          <ErrorMessage :message="form.errors.basic_salary"/>
                        </div>
                      </div>
                    </div>
                    <div class="tab-pane fade" id="bordered-justified-compensation" role="tabpanel" aria-labelledby="profile-tab">
                      <table class="table table-bordered" width="100%">
                        <thead>
                          <tr>
                            <th>Compensation</th>
                            <th>Amount</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-for="(d, i) in form.compensation" :key="d.id">
                            <td>{{ d?.compensation }}</td>
                            <td>
                              <input 
                                type="text" 
                                class="form-control text-end" 
                                placeholder="Amount" 
                                autocomplete="off"
                                v-model="d.amount"
                                @keypress="isNumber($event)" 
                                @blur="
                                    converToCurrencyV2(
                                        $event, 'compensation', i, 'amount'
                                    )
                                "
                                :disabled="props?.modalAttrs?.action == 'VIEW'"
                            >
                            <ErrorMessage :message="form.errors[`compensation.${i}.amount`]?.replace(`compensation.${i}.amount`, 'Amount')"/>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                    <div class="tab-pane fade" id="bordered-justified-deduction" role="tabpanel" aria-labelledby="contact-tab">
                      <table class="table table-bordered" width="100%">
                        <thead>
                          <tr>
                            <th>Deduction</th>
                            <th>Amount</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-for="(d, i) in form.deductions" :key="d.id">
                            <td>{{ d?.deduction }}</td>
                            <td>
                              <input 
                                type="text" 
                                class="form-control text-end" 
                                placeholder="Amount" 
                                autocomplete="off"
                                v-model="d.amount"
                                @keypress="isNumber($event)" 
                                @blur="
                                    converToCurrencyV2(
                                        $event, 'deductions', i, 'amount'
                                    )
                                "
                                :disabled="props?.modalAttrs?.action == 'VIEW'"
                            >
                            <ErrorMessage :message="form.errors[`deductions.${i}.amount`]?.replace(`deductions.${i}.amount`, 'Amount')"/>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button v-if="props?.modalAttrs?.action != 'VIEW'" type="submit" class="btn btn-primary" :disabled="form.processing">Submit</button>
            </div>
          </form>
        </div>
      </div>
    </div>
</template>
