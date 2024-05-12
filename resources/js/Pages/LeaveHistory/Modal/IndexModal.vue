<script setup>
import ErrorMessage from '@/Components/ErrorMessage.vue';
import { usePage, useForm } from '@inertiajs/vue3';
import { ref, watch} from "vue";
import moment from "moment";
import http from "@/utils/https";
const page = usePage()
const activeTab = ref('compensation');
const form = useForm({
    emp_details: ""
});

const resetFormAction = () => {
    form.reset();
    form.clearErrors();
};

const props = defineProps({
    modalAttrs: Object,
});

watch(props?.modalAttrs, (newValue) => {
  resetFormAction();
    if (newValue?.action != "ADD") {
        http.get(`${page?.url}/show/${newValue?.dataId}`).then(
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
                    <div class="card-body" style="min-height: 400px; min-height: 420px; overflow:auto;">
                      <h5 class="card-title">Leave History</h5>
                      <div class="table-responsive mt-3 mb-3">
                        <table class="table table-bordered">
                          <thead>
                            <tr>
                              <th>Leave Type</th>
                              <th>Leave Entitlement</th>
                              <th>Date Start</th>
                              <th>Date End</th>
                              <th>No. of Day(s)</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr v-for="(
                                  d, i
                              ) in form.emp_details?.leave_records"
                              :key="d.id"
                              >
                              <td>{{ d?.leave_type?.name }}</td>
                              <td>{{ d?.leave_entitlement?.name }}</td>
                              <td>{{  moment(d?.date_start).format('LL') }}</td>
                              <td>{{  moment(d?.date_end).format('LL') }}</td>
                              <td>{{ d?.leave_days }}</td>
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
</template>
