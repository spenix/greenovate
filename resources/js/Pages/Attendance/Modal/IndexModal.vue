<script setup>
import ErrorMessage from '@/Components/ErrorMessage.vue';
import { usePage, useForm } from '@inertiajs/vue3';
import { ref, watch, onMounted} from "vue";
import moment from "moment";
import http from "@/utils/https";
import notify from "@/common/notification.js";
const emit = defineEmits(["reloadPageData"]);
const page = usePage()

const employees = ref(page.props.employees);
const shift_codes = ref(page.props.shift_codes);
const emp_id_select = ref();

const form = useForm({
    id: "",
    employee_id: "",
    employee_name: "",
    position: "",
    employee_type: "",
    shift_code: ""
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
        form.put(route('employee-attendance.update', [form.id]), {
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
        form.post(route('employee-attendance.store'), {
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
        `Shift was ${
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
    emp_id_select.value[0].selectize.clear();
    emp_id_select.value[0].selectize.enable();
    if (newValue?.action != "ADD") {
        http.get(`${page?.url}/show/${newValue?.dataId}`).then(
            ({ data, status }) => {
                emp_id_select.value[0].selectize.setValue(data.employee_id)
                form.id = data.id
                form.employee_id = data.employee_id
                form.shift = data?.shift_id;
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
    form.position = emp?.designation;
    form.employee_type = emp?.employee_type;
    shift_codes.value = shift_codes.value.filter(d => {
      if (d.shift_id == 3 ) {
        if ((emp?.designation).toLowerCase() == 'security guard' ||  (emp?.employee_type).toLowerCase() != 'regular') {
          return d
        }
      } else {
        return d
      }
    })
};
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
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ modalAttrs.title }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form @submit.prevent="submitData">
            <div class="modal-body">
              <div class="row">
                <div class="col-12 mt-2">
                  <label for="select_employee_id" class="form-label">Employee ID</label>
                    <select 
                                id="select_employee_id" 
                                aria-label="Search Employee ID"
                                :disabled="modalAttrs?.action == 'VIEW'"
                            >
                            <option value="" hidden>
                                Search Employee ID
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
                <div class="col-12 mt-2">
                  <label for="employee_name" class="form-label">Employee Name</label>
                  <input 
                    type="text" 
                    class="form-control" 
                    name="employee_name" 
                    id="employee_name"
                    autocomplete="off"
                    placeholder="Employee Name"
                    :class="form.errors.employee_name ? 'error-field' : ''"
                    v-model="form.employee_name"
                    :disabled="props?.modalAttrs?.action == 'VIEW'"
                    readonly
                  >
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
                  <label for="employee_type" class="form-label">Employee Type</label>
                  <input 
                    type="text" 
                    class="form-control" 
                    name="employee_type" 
                    id="employee_type"
                    autocomplete="off"
                    placeholder="Employee Type"
                    :class="form.errors.employee_type ? 'error-field' : ''"
                    v-model="form.employee_type"
                    :disabled="props?.modalAttrs?.action == 'VIEW'"
                    readonly
                  >
                  <ErrorMessage :message="form.errors.employee_type"/>
                </div>
                <div class="col-12 mt-1">
                  <label for="shift_code" class="form-label">Shift Code</label>
                  <select 
                    id="shift_code" 
                    class="form-select"
                    :class="form.errors.shift_code ? 'error-field' : ''"
                    v-model="form.shift_code"
                    :disabled="props?.modalAttrs?.action == 'VIEW'"
                  >
                    <option value="" hidden>
                        Select Shift Code
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
                    {{ d?.shift }}
                    </option>
                  </select>
                  <ErrorMessage :message="form.errors.shift_code"/>
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
