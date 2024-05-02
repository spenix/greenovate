<script setup>
import ErrorMessage from '@/Components/ErrorMessage.vue';
import { usePage, useForm } from '@inertiajs/vue3';
import { onMounted, ref, watch} from "vue";
import http from "@/utils/https";
import notify from "@/common/notification.js";
import { commonFuntions } from "@/common/common-function.js";
const { isNumber, converToCurrencyFormat} = commonFuntions();
const emit = defineEmits(["reloadPageData"]);
const page = usePage()

const employees = ref(page?.props?.employees ?? []);

const form = useForm({
    id: "",
    payroll_records: []
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
                form.id = data?.id;
                form.payroll_records = [];
                var detail = {
                  'employee_id': data?.id,
                  'employee_no': data?.employee_no,
                  'employee_name': data?.employee_name,
                  'payment_rate': converToCurrencyFormat(data?.payment_rate),
                  'payment_start': data?.payment_start,
                  'payment_period': data?.payment_period,
                  'reg_hours': data?.reg_hours,
                  'reg_hour_rate': converToCurrencyFormat(data?.reg_hour_rate),
                  'ot_hours': data?.ot_hours,
                  'ot_hour_rate': converToCurrencyFormat(data?.ot_hour_rate),
                  'philhealth': converToCurrencyFormat(data?.philhealth),
                  'tin': converToCurrencyFormat(data?.tin),
                  'sss': converToCurrencyFormat(data?.sss),
                  'pag_ibig': converToCurrencyFormat(data?.pag_ibig),
                  'quarterly': converToCurrencyFormat(data?.quarterly),
                  'year_end': converToCurrencyFormat(data?.year_end),
                }
                form.payroll_records[0] = detail;
            }
        );
    } else {
        var list = employees.value?.map(d=> {
        return {
          'employee_id': d?.id,
          'employee_no': d?.employee_no,
          'employee_name': d?.employee_name,
          'payment_rate': '',
          'payment_start': '',
          'payment_period': '',
          'reg_hours': '',
          'reg_hour_rate': '',
          'ot_hours': '',
          'ot_hour_rate': '',
          'philhealth': '',
          'tin': '',
          'sss': '',
          'pag_ibig': '',
          'quarterly': '',
          'year_end': '',
        }
      });
      form.payroll_records = list;
    }
});

const converToCurrency = (evt, inputField, index) => {
  
    const inputValue = evt.target.value.replace(/,/g, "");
  
            var val = "0.00";
            if (inputValue) {
                var val = parseFloat(inputValue).toLocaleString("en-US", {
                    style: "decimal",
                    maximumFractionDigits: 2,
                    minimumFractionDigits: 2,
                });
            }
        form.payroll_records[index][inputField] = val
}

const btnActionDelRow = (i, d) => {
    if (form.payroll_records.length >= 1) {
      form.payroll_records[i]['payment_rate'] = "";
      form.payroll_records[i]['payment_start'] = "";
      form.payroll_records[i]['payment_period'] = "";
      form.payroll_records[i]['reg_hours'] = "";
      form.payroll_records[i]['reg_hour_rate'] = "";
      form.payroll_records[i]['ot_hours'] = "";
      form.payroll_records[i]['ot_hour_rate'] = "";
      form.payroll_records[i]['philhealth'] = "";
      form.payroll_records[i]['tin'] = "";
      form.payroll_records[i]['sss'] = "";
      form.payroll_records[i]['pag_ibig'] = "";
      form.payroll_records[i]['quarterly'] = "";
      form.payroll_records[i]['year_end'] = "";
        
    } else {
        notify(
                      "Info",
                      "Oops, unable to execute action.",
                      "info"
                  );
    }
}
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
            <div class="modal-body">
              <div class="table-responsive" style="min-height: 400px; max-height: 400px; overflow: auto;">
                <table
                    class="table table-bordered table-sm display"
                    style="width: 180% !important"
                >
                <thead>
                  <tr>
                  <th colspan="2" class="text-center">Details</th>
                  <th colspan="3" class="text-center">Payment Schedule</th>
                  <th colspan="4" class="text-center">Details</th>
                  <th colspan="4" class="text-center">Deduction</th>
                  <th colspan="2" class="text-center">Bunoses</th>
                  <th v-if="modalAttrs?.action != 'VIEW'"></th>
                </tr>
                <tr>
                  <th class="text-center">ID No.</th>
                  <th class="text-center">Employee Name</th>
                  <th class="text-center">Payment Rate</th>
                  <th class="text-center">Payment Start</th>
                  <th class="text-center">Payment Period</th>
                  <th class="text-center">Reg. Hours</th>
                  <th class="text-center">Reg. Hours Rate</th>
                  <th class="text-center">OT Hours</th>
                  <th class="text-center">OT Hours Rate</th>
                  <th class="text-center">Philhealth</th>
                  <th class="text-center">TIN</th>
                  <th class="text-center">SSS</th>
                  <th class="text-center">Pag-ibig</th>
                  <th class="text-center">Quarterly</th>
                  <th class="text-center">Year End</th>
                  <th class="text-center" v-if="modalAttrs?.action != 'VIEW'">Action</th>
                </tr>
                </thead>
                <tbody>
                  <tr  
                    v-for="(
                        d, i
                    ) in form.payroll_records"
                    :key="i"
                    v-if="form.payroll_records.length">
                    <td>
                      <input 
                          type="text" 
                          class="form-control" 
                          placeholder="ID No." 
                          autocomplete="off"
                          v-model="d.employee_no"
                          disabled
                      >
                      <ErrorMessage :message="form.errors[`payroll_records.${i}.employee_no`]?.replace(`payroll_records.${i}.employee_no`, 'ID No.')"/>
                    </td>
                    <td>
                      <input 
                          type="text" 
                          class="form-control" 
                          placeholder="Employee Name" 
                          autocomplete="off"
                          v-model="d.employee_name"
                          disabled
                      >
                      <ErrorMessage :message="form.errors[`payroll_records.${i}.employee_name`]?.replace(`payroll_records.${i}.employee_name`, 'employee name')"/>
                    </td>
                    <td>
                      <input 
                          type="text" 
                          class="form-control text-end" 
                          placeholder="Payment Rate" 
                          autocomplete="off"
                          @keypress="isNumber($event)" 
                          @blur="
                              converToCurrency(
                                  $event,
                                  'payment_rate',
                                  i
                              )
                          "
                          v-model="d.payment_rate"
                          :disabled="modalAttrs?.action == 'VIEW'"
                      >
                      <ErrorMessage :message="form.errors[`payroll_records.${i}.payment_rate`]?.replace(`payroll_records.${i}.payment_rate`, 'payment rate')"/>
                    </td>
                    <td>
                      <input 
                          type="date" 
                          class="form-control" 
                          placeholder="Payment Start" 
                          autocomplete="off"
                          v-model="d.payment_start"
                          :disabled="modalAttrs?.action == 'VIEW'"
                      >
                      <ErrorMessage :message="form.errors[`payroll_records.${i}.payment_start`]?.replace(`payroll_records.${i}.payment_start`, 'payment start')"/>
                    </td>
                    <td>
                      <input 
                          type="date" 
                          class="form-control" 
                          placeholder="Payment Period" 
                          autocomplete="off"
                          v-model="d.payment_period"
                          :disabled="modalAttrs?.action == 'VIEW'"
                      >
                      <ErrorMessage :message="form.errors[`payroll_records.${i}.payment_period`]?.replace(`payroll_records.${i}.payment_period`, 'payment period')"/>
                    </td>
                    <td>
                      <input 
                          type="text" 
                          class="form-control" 
                          placeholder="Reg. Hours" 
                          autocomplete="off"
                          @keypress="isNumber($event)" 
                          v-model="d.reg_hours"
                          :disabled="modalAttrs?.action == 'VIEW'"
                      >
                      <ErrorMessage :message="form.errors[`payroll_records.${i}.reg_hours`]?.replace(`payroll_records.${i}.reg_hours`, 'reg. hours')"/>
                    </td>
                    <td>
                      <input 
                          type="text" 
                          class="form-control  text-end" 
                          placeholder="Reg. Hours Rate" 
                          autocomplete="off"
                          @keypress="isNumber($event)" 
                          @blur="
                              converToCurrency(
                                  $event,
                                  'reg_hour_rate',
                                  i
                              )
                          "
                          v-model="d.reg_hour_rate"
                          :disabled="modalAttrs?.action == 'VIEW'"
                      >
                      <ErrorMessage :message="form.errors[`payroll_records.${i}.reg_hour_rate`]?.replace(`payroll_records.${i}.reg_hour_rate`, 'reg. hour rate')"/>
                    </td>
                    <td>
                      <input 
                          type="text" 
                          class="form-control" 
                          placeholder="OT Hours" 
                          autocomplete="off"
                          @keypress="isNumber($event)" 
                          v-model="d.ot_hours"
                          :disabled="modalAttrs?.action == 'VIEW'"
                      >
                      <ErrorMessage :message="form.errors[`payroll_records.${i}.ot_hours`]?.replace(`payroll_records.${i}.ot_hours`, 'OT hours')"/>
                    </td>
                    <td>
                      <input 
                          type="text" 
                          class="form-control text-end" 
                          placeholder="OT Hours Rate" 
                          autocomplete="off"
                          @keypress="isNumber($event)" 
                          @blur="
                              converToCurrency(
                                  $event,
                                  'ot_hour_rate',
                                  i
                              )
                          "
                          v-model="d.ot_hour_rate"
                          :disabled="modalAttrs?.action == 'VIEW'"
                      >
                      <ErrorMessage :message="form.errors[`payroll_records.${i}.ot_hour_rate`]?.replace(`payroll_records.${i}.ot_hour_rate`, 'OT hour rate')"/>
                    </td>
                    <td>
                      <input 
                          type="text" 
                          class="form-control text-end" 
                          placeholder="Philhealth" 
                          autocomplete="off"
                          @keypress="isNumber($event)" 
                          @blur="
                              converToCurrency(
                                  $event,
                                  'philhealth',
                                  i
                              )
                          "
                          v-model="d.philhealth"
                          :disabled="modalAttrs?.action == 'VIEW'"
                      >
                      <ErrorMessage :message="form.errors[`payroll_records.${i}.philhealth`]?.replace(`payroll_records.${i}.philhealth`, 'Philhealth')"/>
                    </td>
                    <td>
                      <input 
                          type="text" 
                          class="form-control text-end" 
                          placeholder="TIN" 
                          autocomplete="off"
                          @keypress="isNumber($event)" 
                          @blur="
                              converToCurrency(
                                  $event,
                                  'tin',
                                  i
                              )
                          "
                          v-model="d.tin"
                          :disabled="modalAttrs?.action == 'VIEW'"
                      >
                      <ErrorMessage :message="form.errors[`payroll_records.${i}.tin`]?.replace(`payroll_records.${i}.tin`, 'TIN')"/>
                    </td>
                    <td>
                    <input 
                          type="text" 
                          class="form-control text-end" 
                          placeholder="SSS" 
                          autocomplete="off"
                          @keypress="isNumber($event)" 
                          @blur="
                              converToCurrency(
                                  $event,
                                  'sss',
                                  i
                              )
                          "
                          v-model="d.sss"
                          :disabled="modalAttrs?.action == 'VIEW'"
                      >
                      <ErrorMessage :message="form.errors[`payroll_records.${i}.sss`]?.replace(`payroll_records.${i}.sss`, 'SSS')"/>
                    </td>
                    <td>
                      <input 
                          type="text" 
                          class="form-control text-end" 
                          placeholder="Pag-ibig" 
                          autocomplete="off"
                          @keypress="isNumber($event)" 
                          @blur="
                              converToCurrency(
                                  $event,
                                  'pag_ibig',
                                  i
                              )
                          "
                          v-model="d.pag_ibig"
                          :disabled="modalAttrs?.action == 'VIEW'"
                      >
                      <ErrorMessage :message="form.errors[`payroll_records.${i}.pag_ibig`]?.replace(`payroll_records.${i}.Pag_ibig`, 'pag-ibig')"/>
                    </td>
                    <td>
                      <input 
                          type="text" 
                          class="form-control text-end" 
                          placeholder="Quarterly" 
                          autocomplete="off"
                          @keypress="isNumber($event)" 
                          @blur="
                              converToCurrency(
                                  $event,
                                  'quarterly',
                                  i
                              )
                          "
                          v-model="d.quarterly"
                          :disabled="modalAttrs?.action == 'VIEW'"
                      >
                      <ErrorMessage :message="form.errors[`payroll_records.${i}.quarterly`]?.replace(`payroll_records.${i}.quarterly`, 'quarterly')"/>
                    </td>
                    <td>
                      <input 
                          type="text" 
                          class="form-control text-end" 
                          placeholder="Year End" 
                          autocomplete="off"
                          @keypress="isNumber($event)" 
                          @blur="
                              converToCurrency(
                                  $event,
                                  'year_end',
                                  i
                              )
                          "
                          v-model="d.year_end"
                          :disabled="modalAttrs?.action == 'VIEW'"
                      >
                      <ErrorMessage :message="form.errors[`payroll_records.${i}.year_end`]?.replace(`payroll_records.${i}.year_end`, 'year_end')"/>
                    </td>
                    <td class="text-center" v-if="modalAttrs?.action != 'VIEW'">
                      <a 
                          href="javascript:;" 
                          class="delete-btn-option red" 
                          title="Erase" 
                          @click="btnActionDelRow(i, d)"
                      ><i class="bi bi-eraser"></i></a>
                    </td>
                  </tr>
                </tbody>
                </table>
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
