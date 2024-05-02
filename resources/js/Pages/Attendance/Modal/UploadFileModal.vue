<script setup>
import ErrorMessage from '@/Components/ErrorMessage.vue';
import { usePage, useForm } from '@inertiajs/vue3';
import { ref, watch, onMounted} from "vue";
import http from "@/utils/https";
import notify from "@/common/notification.js";
const emit = defineEmits(["reloadPageData"]);
const page = usePage()

const employees = ref(page.props.employees);
const emp_id_select = ref();
const file_upload = ref();

const form = useForm({
    id: "",
    file_upload: "",
    period_start: "",
    period_end: "",
});

const resetFormAction = () => {
    form.reset();
    form.clearErrors();
};

const props = defineProps({
    modalUploadAttrs: Object,
});

const submitData = () => {
    form.file_upload = file_upload.value.files[0]
  if (props?.modalUploadAttrs?.action == "UPLOAD") {
        form.post(route('upload-attendance'), {
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
        `DTR file was ${
            props?.modalUploadAttrs?.action == "EDIT" ? "updated" : "saved"
        } successfully.`,
        "success"
    );
    $(
        `#${
            props?.modalUploadAttrs?.modalId
                ? props?.modalUploadAttrs?.modalId
                : "page-modal"
        }`
    ).modal("hide");
    emit("reloadPageData");
};

watch(props?.modalUploadAttrs, (newValue) => {
  resetFormAction();
    emp_id_select.value[0].selectize.clear();
    emp_id_select.value[0].selectize.enable();
    if (newValue?.action != "ADD") {
        http.get(`${page?.url}/show/${newValue?.dataId}`).then(
            ({ data, status }) => {
                emp_id_select.value[0].selectize.setValue(data.employee_id)
                form.id = data.id
                form.employee_id = data.employee_id
               
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
    form.employee_id = emp?.id;
    form.employee_name = emp?.employee_name;
    form.department = emp?.designation;
    form.employee_type = emp?.employee_type;
};

const validateDate = (evt, inpt) => {
  var dt = evt.target.value;
  if (inpt == 'period_start') {
    $('#period_end').prop('min', dt);
  }
  if (inpt == 'period_end') {
    $('#period_start').prop('max', dt);
  }

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
    <div class="modal fade" :id="modalUploadAttrs.modalId" tabindex="-1" data-bs-backdrop="false" style="display: none;" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ modalUploadAttrs.title }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form @submit.prevent="submitData">
            <div class="modal-body">
              <div class="row">
               
                <div class="col-12 mt-2">
                  <label for="file_upload" class="form-label">
                    File Upload
                    <br>
                    <small>accept only (.pdf,.xlsx,.xlsm,.xls,.txt)</small>
                </label>
                  <input 
                    type="File" 
                    class="form-control" 
                    name="file_upload" 
                    id="file_upload"
                    ref="file_upload"
                    accept=".pdf,.xlsx,.xlsm,.xls,.xml,.txt"
                    autocomplete="off"
                    placeholder="File Upload"
                    :class="form.errors.file_upload ? 'error-field' : ''"
                    v-model="form.file_upload"
                    :disabled="props?.modalUploadAttrs?.action == 'VIEW'"
                    readonly
                  >
                  <ErrorMessage :message="form.errors.file_upload"/>
                </div>
                <div class="col-6 mt-2">
                  <label for="period_start" class="form-label">Period Start</label>
                  <input 
                    type="date" 
                    class="form-control" 
                    name="period_start" 
                    id="period_start"
                    @change="validateDate($event, 'period_start')"
                    autocomplete="off"
                    placeholder="Date Start"
                    :class="form.errors.period_start ? 'error-field' : ''"
                    v-model="form.period_start"
                    :disabled="props?.modalAttrs?.action == 'VIEW'"
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
                    @change="validateDate($event, 'period_end')"
                    autocomplete="off"
                    placeholder="Date End"
                    :class="form.errors.period_end ? 'error-field' : ''"
                    v-model="form.period_end"
                    :disabled="props?.modalAttrs?.action == 'VIEW'"
                  >
                  <ErrorMessage :message="form.errors.period_end"/>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button v-if="props?.modalUploadAttrs?.action != 'VIEW'" type="submit" class="btn btn-primary" :disabled="form.processing">Submit</button>
            </div>
          </form>
        </div>
      </div>
    </div>
</template>
