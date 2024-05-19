<script setup>
import ErrorMessage from '@/Components/ErrorMessage.vue';
import { usePage, useForm } from '@inertiajs/vue3';
import { ref, watch} from "vue";
import http from "@/utils/https";
const emit = defineEmits(["reloadPageData"]);
const page = usePage()

const emp_data = ref({})

const props = defineProps({
    modalAttrs: Object,
});


watch(props?.modalAttrs, (newValue) => {
    if (newValue?.action != "ADD") {
        http.get(`${page?.url}/show/${newValue?.dataId}`).then(
            ({ data, status }) => {
              if(status == 200) {
                if (data?.days) {
                  data['days'] = data?.days.split("|")
                }
                emp_data.value = data;
              }
            }
        );
    }
});
</script>

<template>
    <div class="modal fade" :id="modalAttrs.modalId" tabindex="-1" data-bs-backdrop="false" style="display: none;" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ modalAttrs.title }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
            <div class="modal-body">
             <div class="row">
              <div class="col-6"><span><b>Emp. #: </b>{{ emp_data?.employee_no }}</span></div>
              <div class="col-6"><span><b>Emp. Name: </b>{{ emp_data?.employee_name }}</span></div>
              <div class="col-6"><span><b>Department: </b>{{ emp_data?.department }}</span></div>
              <div class="col-6"><span><b>Shift: </b>{{ emp_data?.shift }}</span></div>
             </div>
             <div class="row mt-4">
              <table class="table table-sm">
                <thead>
                  <tr>
                    <th width="20%">Day</th>
                    <th width="20%">Time-In</th>
                    <th width="20%">Break-Out</th>
                    <th width="20%">Break-In</th>
                    <th width="20%">Time-Out</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(d, i) in emp_data.days" :key="i">
                    <td>{{ d }}</td>
                    <td>{{ emp_data?.clock_in }}</td>
                    <td>{{ emp_data?.break_out }}</td>
                    <td>{{ emp_data?.break_in }}</td>
                    <td>{{ emp_data?.clock_out }}</td>
                  </tr>
                </tbody>
              </table>
             </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
      </div>
    </div>
</template>
