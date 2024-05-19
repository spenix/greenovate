<script setup>
import html2pdf from 'html2pdf.js'
import ErrorMessage from '@/Components/ErrorMessage.vue';
import { usePage, useForm } from '@inertiajs/vue3';
import { ref, watch} from "vue";
import http from "@/utils/https";
import { commonFuntions } from "@/common/common-function.js";
const { isNumber, converToCurrencyFormat } = commonFuntions();
import moment from "moment";
const emit = defineEmits(["reloadPageData"]);
const page = usePage()

const systemSetup = ref(page.props.systemSetup);
const gross_earnings = ref(0);

const form = useForm({
    detail: {}
});

const resetFormAction = () => {
    form.reset();
    form.clearErrors();
};

const props = defineProps({
    modalAttrs: Object,
});

const submitData = () => {
//   var printdata = document.getElementById('myform');
//   var newwin = window.open("");
//   newwin.document.write(printdata.outerHTML);
//   newwin.print();
//   newwin.close();
  html2pdf(document.getElementById('myform'), {
    margin: 0.1,
    filename: 'payslip.pdf',
    html2canvas: { dpi: 192, letterRendering: true, useCORS: true },
    jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
})
}

watch(props?.modalAttrs, (newValue) => {
  resetFormAction();
    if (newValue?.action != "ADD") {
        http.get(`${page?.url}/show/${newValue?.dataId}`).then(
            ({ data, status }) => {
                form.detail = data
                
                var basic_salary = parseFloat(data?.basic_salary)
                var compensation = 0;
                data.compensations.forEach(d => {
                    compensation += parseFloat(d?.amount)
                });
                var deduction = 0;
                data.deductions.forEach(d => {
                    deduction += parseFloat(d?.amount)
                });
                gross_earnings.value = (basic_salary + compensation) - deduction;
            }
        );
    }
});
</script>

<template>
    <div class="modal fade" :id="modalAttrs.modalId" tabindex="-1" data-bs-backdrop="false" style="display: none;" aria-hidden="true">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ modalAttrs.title }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form @submit.prevent="submitData" >
            <div class="modal-body" id="myform">
              <div class="row">
                <div class="col-12 text-center"><h4>Payslip</h4></div>
                <div class="col-12 text-center">{{ systemSetup.appFullName }}</div>
                <div class="col-12 text-center">{{ systemSetup.appAddress }}</div>
                <div class="col-1" style="padding: 10px 30px;"></div>
                <div class="col-10 mt-4">
                    <div class="row">
                        <div class="col-3 mt-2 text-end">Date Hired:</div>
                        <div class="col-3 mt-2" style="border-bottom: 1px solid black;">{{ moment(form.detail?.date_hired).format('L') }}</div>
                        <div class="col-3 mt-2 text-end">Employee name:</div>
                        <div class="col-3 mt-2" style="border-bottom: 1px solid black;">{{ form.detail?.employee_name }}</div>
                    </div>
                    <div class="row">
                        <div class="col-3 mt-2 text-end">Pay Period:</div>
                        <div class="col-3 mt-2" style="border-bottom: 1px solid black; font-size: 14px;">{{  `${moment(form.detail?.period_start).format('L')}-${moment(form.detail?.period_end).format('L')}` }}</div>
                        <div class="col-3 mt-2 text-end">Position:</div>
                        <div class="col-3 mt-2" style="border-bottom: 1px solid black;">{{ form.detail?.designation }}</div>
                    </div>
                    <div class="row">
                        <div class="col-3 mt-2 text-end">Worked Days:</div>
                        <div class="col-3 mt-2" style="border-bottom: 1px solid black;">{{ form.detail?.working_days }}</div>
                        <div class="col-3 mt-2"></div>
                        <div class="col-3 mt-2"></div>
                    </div>
                </div>
                <div class="col-1" style="padding: 10px 30px;"></div>
                <div class="col-1" style="padding: 10px 30px;"></div>
                <div class="col-10" style="padding: 10px 30px;">
                    <table class="table" width="100%">
                        <tbody>
                            <tr>
                                <td>Basic Salary</td>
                                <td class="text-end">{{ converToCurrencyFormat(form.detail.basic_salary) }}</td>
                            </tr>
                            <tr>
                                <td colspan="2">Compensation</td>
                            </tr>
                            <tr v-for="(d, i) in form.detail.compensations" :key="i">
                                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ d.benefit?.name }}</td>
                                <td class="text-end">{{ converToCurrencyFormat(d.amount) }}</td>
                            </tr>
                            <tr>
                                <td colspan="2">Deductions</td>
                            </tr>
                            <tr v-for="(d, i) in form.detail.deductions" :key="i">
                                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ d.deduction_details?.name }}</td>
                                <td class="text-end">{{ converToCurrencyFormat(d.amount) }}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold;">Take Home Pay</td>
                                <td class="text-end" style="font-weight: bold;">{{ converToCurrencyFormat(gross_earnings) }}</td>
                            </tr>
                        </tbody>
                    </table>
                
                </div>
                <div class="col-1" style="padding: 10px 30px;"></div>
                <div class="col-1" style="padding: 10px 30px;"></div>
                <div class="col-10" style="padding: 10px 30px; margin-top:100px;">
                    <div class="row">
                        <div class="col-4 text-center" style="border-top: 1px solid black;">
                            Employee Signature
                        </div>
                        <div class="col-4">
                        
                        </div>
                        <div class="col-4 text-center" style="border-top: 1px solid black;">
                            Employee Signature
                        </div>
                    </div>
                </div>
                <div class="col-1" style="padding: 10px 30px;"></div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button v-if="props?.modalAttrs?.action != 'VIEW'" type="submit" class="btn btn-primary" :disabled="form.processing"><i class="bi bi-printer"></i> Print</button>
            </div>
          </form>
        </div>
      </div>
    </div>
</template>
