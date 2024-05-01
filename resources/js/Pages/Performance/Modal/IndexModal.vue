<script setup>
import ErrorMessage from '@/Components/ErrorMessage.vue';
import { usePage, useForm } from '@inertiajs/vue3';
import { ref, watch, onMounted} from "vue";
import http from "@/utils/https";
import notify from "@/common/notification.js";
const emit = defineEmits(["reloadPageData"]);
const page = usePage()

const violationTypes = ref(page?.props?.violationTypes ?? []);
const attempLimits = ref(page?.props?.attempLimits ?? []);
const employees = ref(page?.props?.employees ?? []);
const emp_id_select = ref();
const form = useForm({
    id: "",
    employee_id: "",
    employee_name: "",
    department: "",
    employee_type: "",
    date_committed: "",
    attemps: "",
    violation_type: "",
    remark: "",
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
        form.put(route('performance.update', [form.id]), {
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
        form.post(route('performance.store'), {
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
        `Performance was ${
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

const getSelectedEmp = (selectedVal) => {
    var emp = employees.value.find((d) => {
       return d.id == selectedVal
    });
    
    form.employee_id = emp?.id
    form.employee_name = emp?.employee_name
    form.department = emp?.designation
    form.employee_type = emp?.employee_type
}

watch(props?.modalAttrs, (newValue) => {
    resetFormAction();
    emp_id_select.value[0].selectize.clear();
    emp_id_select.value[0].selectize.enable();
    if (newValue?.action != "ADD") {
        http.get(`${page?.url}/show/${newValue?.dataId}`).then(
            ({ data, status }) => {
                form.id = data.id;
                emp_id_select.value[0].selectize.setValue(data.employee_id);
                form.employee_id = data.employee_id;
                form.employee_name = data.employee_name;
                form.department = data.designation;
                form.employee_type = data.employee_type;
                form.date_committed = data.date_committed;
                form.attemps = data.attemps;
                form.violation_type = data.violation_type_id;
                form.remark = data.remark;
            }
        );
    }
    if(newValue?.action == "VIEW") {
        emp_id_select.value[0].selectize.disable();
    }
});
onMounted(() => {
    emp_id_select.value = $("#emp-id-no").selectize({
        onChange: function(value) {
            getSelectedEmp(value);
        }
    });
})
</script>

<template>
    <div class="modal fade" :id="modalAttrs.modalId" tabindex="-1" data-bs-backdrop="false" style="display: none;" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ modalAttrs.title }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form @submit.prevent="submitData">
            <div class="modal-body">
              <div class="row">
                <div class="col-6 mt-1">
                    <div class="row mb-3">
                        <label for="emp-id-no" class="col-sm-5 col-form-label">Employee ID No.:</label>
                        <div class="col-sm-7">
                            <select 
                                id="emp-id-no" 
                                aria-label="Select ID No."
                                :disabled="modalAttrs?.action == 'VIEW'"
                            >
                            <option value="" hidden>
                                Select ID No.
                            </option>
                            <option 
                            v-for="(
                                d, i
                            ) in employees"
                            :key="d.id"
                            :value="d.id"
                            :selected="
                                form.employee_id ==
                                d?.id
                            "
                            >
                            {{ d?.employee_no }}
                            </option>
                            </select>
                            <ErrorMessage :message="form.errors.employee_id"/>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="inputText" class="col-sm-5 col-form-label">Employee Name:</label>
                        <div class="col-sm-7">
                            <input 
                                type="text" 
                                class="form-control" 
                                placeholder="Employee Name" 
                                autocomplete="off"
                                v-model="form.employee_name"
                                :disabled="modalAttrs?.action == 'VIEW'"
                                readonly
                            >
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="inputText" class="col-sm-5 col-form-label">Designation:</label>
                        <div class="col-sm-7">
                            <input 
                                type="text" 
                                class="form-control" 
                                placeholder="Designation" 
                                autocomplete="off"
                                v-model="form.department"
                                :disabled="modalAttrs?.action == 'VIEW'"
                                readonly
                            >
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="inputText" class="col-sm-5 col-form-label">Status:</label>
                        <div class="col-sm-7">
                            <input 
                                type="text" 
                                class="form-control" 
                                placeholder="Status" 
                                autocomplete="off"
                                v-model="form.employee_type"
                                :disabled="modalAttrs?.action == 'VIEW'"
                                readonly
                            >
                        </div>
                    </div>   
                </div>
                <div class="col-6 mt-1">
                    <div class="row mb-3">
                        <label for="inputText" class="col-sm-5 col-form-label">Date Committed:</label>
                        <div class="col-sm-7">
                            <input 
                                type="date" 
                                class="form-control" 
                                placeholder="Date Committed" 
                                autocomplete="off"
                                :class="form.errors.date_committed ? 'error-field' : ''"
                                v-model="form.date_committed"
                                :disabled="modalAttrs?.action == 'VIEW'"
                            >
                            <ErrorMessage :message="form.errors.date_committed"/>
                        </div>
                    </div>   
                    <div class="row mb-3">
                        <label for="count-attempt" class="col-sm-5 col-form-label">Count Attempt:</label>
                        <div class="col-sm-7">
                            <select 
                                class="form-select" 
                                id="count-attempt" 
                                aria-label="Select Count Attempt"
                                :class="form.errors.attemps ? 'error-field' : ''"
                                v-model="form.attemps"
                                :disabled="modalAttrs?.action == 'VIEW'"
                            >
                                <option value="" hidden>
                                    Select Count Attempt
                                </option>
                                <option 
                                v-for="d in attempLimits"
                                    :key="d"
                                    :value="d"
                                    :selected="
                                        form.attemps ==
                                        d
                                    "
                                >{{ d }}</option>
                            </select>
                            <ErrorMessage :message="form.errors.attemps"/>
                        </div>
                    </div>   
                    <div class="row mb-3">
                        <label for="violation" class="col-sm-5 col-form-label">Violation:</label>
                        <div class="col-sm-7">
                            <select 
                                class="form-select" 
                                id="violation" 
                                aria-label="Select Violation"
                                :class="form.errors.violation_type ? 'error-field' : ''"
                                v-model="form.violation_type"
                                :disabled="modalAttrs?.action == 'VIEW'"
                            >
                                <option value="" hidden>
                                    Select Violation
                                </option>
                                <option 
                                v-for="(d, i) in violationTypes"
                                    :key="d.id"
                                    :value="d.id"
                                    :selected="
                                        form.violation_type ==
                                        d.id
                                    "
                                >{{ d.name }}</option>
                            </select>
                            <ErrorMessage :message="form.errors.violation_type"/>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="inputText" class="col-sm-5 col-form-label">Remark:</label>
                        <div class="col-sm-7">
                            <textarea 
                                class="form-control"  
                                rows="3" 
                                placeholder="Remark" 
                                :class="form.errors.remark ? 'error-field' : ''"
                                v-model="form.remark"
                                :disabled="modalAttrs?.action == 'VIEW'"
                            ></textarea>
                            <ErrorMessage :message="form.errors.remark"/>
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
