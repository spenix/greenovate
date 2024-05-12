<script setup>
import ErrorMessage from '@/Components/ErrorMessage.vue';
import { usePage, useForm } from '@inertiajs/vue3';
import { ref, watch} from "vue";
import moment from "moment";
import http from "@/utils/https";
import { commonFuntions } from "@/common/common-function.js";
const { converToCurrencyFormat } = commonFuntions();
const emit = defineEmits(["reloadPersonnelPageData"]);
const page = usePage()
const activeTab = ref('compensation');
const form = useForm({
    emp_details: ""
});

const resetFormAction = () => {
    form.reset();
    form.clearErrors();
};

const activeTabAction = (tab) => {
  activeTab.value = tab
};
const props = defineProps({
    modalAttrs: Object,
});

watch(props?.modalAttrs, (newValue) => {
  resetFormAction();
    if (newValue?.action != "ADD") {
        http.get(`${page?.url}/show_employee_details/${newValue?.dataId}`).then(
            ({ data, status }) => {
                form.emp_details = data;
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
            <div class="modal-body" style="min-height: 450px">
              <div class="row">
                <div class="col-4 mt-1">
                  <div class="card">
                    <div class="card-body">
                      <h5 class="card-title">Employee Information</h5>
                      <div class="row">
                        <div class="col-12 mt-1">
                          <ul class="list-unstyled">
                            <li class="mt-2"><span class="text-muted">Employee Fullname: </span>{{ form.emp_details?.employee_name }}</li>
                            <li class="mt-2"><span class="text-muted">Department: </span>{{ form.emp_details?.department }}</li>
                            <li class="mt-2"><span class="text-muted">Position: </span>{{ form.emp_details?.designation }}</li>
                            <li class="mt-2"><span class="text-muted">Employment Type: </span>{{ form.emp_details?.employee_type }}</li>
                            <li class="mt-2"><span class="text-muted">Date Hired: </span>{{ moment(form.emp_details?.date_hired).format('LL') }}</li>
                          </ul>
                        </div>
                    </div>
                    </div>
                  </div>
                </div>
                <div class="col-8 mt-1">
                  <div class="card">
                    <div class="card-body" style="min-height: 400px;">
                      <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                          <button class="nav-link" :class="activeTab == 'compensation' ? 'active' : ''" id="compensation-tab" @click="activeTabAction('compensation')" data-bs-toggle="tab" data-bs-target="#compensation" type="button" role="tab" aria-controls="compensation" aria-selected="true">Compensation/Benefits</button>
                        </li>
                        <li class="nav-item" role="presentation">
                          <button class="nav-link" :class="activeTab == 'deduction' ? 'active' : ''" id="deduction-tab" @click="activeTabAction('deduction')" data-bs-toggle="tab" data-bs-target="#deduction" type="button" role="tab" aria-controls="deduction" aria-selected="false" tabindex="-1">Deductions</button>
                        </li>
                      </ul>
                      <div class="tab-content pt-2" id="myTabContent">
                        <div class="tab-pane fade" :class="activeTab == 'compensation' ? 'active show' : ''" id="compensation" role="tabpanel" aria-labelledby="compensation-tab">
                          <table class="table table-bordered" width="100%">
                            <thead>
                              <tr>
                                <th>Compensation/Benefit</th>
                                <th class="text-center" width="30%">Amount</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr v-for="(
                                  d, i
                              ) in form.emp_details?.compensations"
                              :key="d.id"
                              >
                                <td>{{ `${d?.benefit?.short_code} ${d?.benefit?.classification}` }}</td>
                                <td class="text-end" width="30%">
                                {{ converToCurrencyFormat(d?.benefit?.amount) }}
                                </td>
                              </tr>
                              <tr v-if="form.emp_details?.compensations?.length == 0">
                                <td colspan="2" class="text-center">No data results</td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                        <div class="tab-pane fade" :class="activeTab == 'deduction' ? 'active show' : ''" id="deduction" role="tabpanel" aria-labelledby="deduction-tab">
                          <table class="table table-bordered" width="100%">
                            <thead>
                              <tr>
                                <th>Deduction</th>
                                <th class="text-center" width="30%">Amount</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr v-for="(
                                  d, i
                              ) in form.emp_details?.deductions"
                              :key="d.id"
                              >
                                <td>{{ `${d?.deduction_details?.short_code} ${d?.deduction_details?.classification}` }}</td>
                                <td class="text-end" width="30%">
                                {{ converToCurrencyFormat(d?.deduction_details?.amount) }}
                                </td>
                              </tr>
                              <tr v-if="form.emp_details?.deductions?.length == 0">
                                <td colspan="2" class="text-center">No data results</td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
        </div>
      </div>
    </div>
</template>
