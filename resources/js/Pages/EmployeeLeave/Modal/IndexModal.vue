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
const leaveTypes = ref(page.props.leaveTypes);
const emp_id_select = ref();

const form = useForm({
    id: "",
    employee_name: "",
    leave_type: "",
    leave_entitlement_id: "",
    leave_entitlement: "",
    date_start: "",
    date_end: "",
    leave_days: "",
});

const getSelectedLeaveType = (evt) => {
    var data = leaveTypes.value.find((d) => {
       return d.id == evt.target.value
    });
    
    form.leave_entitlement = data?.leave_entitlement
    form.leave_entitlement_id = data?.leave_entitlement_id
}

const resetFormAction = () => {
    form.reset();
    form.clearErrors();
};

const props = defineProps({
    modalAttrs: Object,
});

const submitData = () => {
  if (props?.modalAttrs?.action == "EDIT") {
        form.put(route('employee-leaves.update', [form.id]), {
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
        form.post(route('employee-leaves.store'), {
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
        `Employee leave was ${
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
                form.employee_name = data.employee_id
                form.leave_type = data.leave_type_id
                form.leave_entitlement_id = data.leave_entitlement_id
                form.leave_entitlement = data.leave_entitlement
                form.date_start = data.date_start
                form.date_end = data.date_end
                form.leave_days = data.leave_days
            }
        );
    }
    if(newValue?.action == "VIEW") {
        emp_id_select.value[0].selectize.disable();
    }
});

const treatAsUTC = (date) => {
    var result = new Date(date);
    result.setMinutes(result.getMinutes() - result.getTimezoneOffset());
    return result;
}

const daysBetween = (startDate, endDate) => {
    var millisecondsPerDay = 24 * 60 * 60 * 1000;
    return (treatAsUTC(endDate) - treatAsUTC(startDate)) / millisecondsPerDay;
}

const validateDate = (evt, inpt) => {
  var dt = evt.target.value;
  if (inpt == 'date_start') {
    $('#date_end').prop('min', dt);
  }
  if (inpt == 'date_end') {
    $('#date_start').prop('max', dt);
  }

  if (form.date_start && form.date_end) {
    form.leave_days = daysBetween(form.date_start, form.date_end) + 1;
  }
}
const getSelectedEmp = (selectedVal) => {
  var emp = employees.value.find((d) => {
       return d.id == selectedVal
    });

    form.employee_name = emp?.id;
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
                  <label for="leave_type" class="form-label">Leave Type</label>
                  <select 
                    id="leave_type" 
                    class="form-select"
                    :class="form.errors.leave_type ? 'error-field' : ''"
                    v-model="form.leave_type"
                    @change="getSelectedLeaveType($event)"
                    :disabled="props?.modalAttrs?.action == 'VIEW'"
                  >
                    <option value="" hidden>
                        Select Leave Type
                    </option>
                    <option 
                    v-for="(
                          d, i
                      ) in leaveTypes"
                      :key="d.id"
                      :value="d.id"
                      :selected="
                          form.leave_type ==
                          d?.id
                      "
                    >
                    {{ d?.name }}
                    </option>
                  </select>
                  <ErrorMessage :message="form.errors.leave_type"/>
                </div>
                <div class="col-12 mt-2">
                  <label for="leave_entitlement" class="form-label">Leave Entitlement</label>
                  <input 
                    type="text" 
                    class="form-control" 
                    name="leave_entitlement" 
                    id="leave_entitlement"
                    autocomplete="off"
                    placeholder="Leave Entitlement"
                    :class="form.errors.leave_entitlement ? 'error-field' : ''"
                    v-model="form.leave_entitlement"
                    :disabled="props?.modalAttrs?.action == 'VIEW'"
                    readonly
                  >
                  <ErrorMessage :message="form.errors.leave_entitlement"/>
                </div>
                <div class="col-6 mt-2">
                  <label for="date_start" class="form-label">Date Start</label>
                  <input 
                    type="date" 
                    class="form-control" 
                    name="date_start" 
                    id="date_start"
                    @change="validateDate($event, 'date_start')"
                    autocomplete="off"
                    placeholder="Date Start"
                    :class="form.errors.date_start ? 'error-field' : ''"
                    v-model="form.date_start"
                    :disabled="props?.modalAttrs?.action == 'VIEW'"
                  >
                  <ErrorMessage :message="form.errors.date_start"/>
                </div>
                <div class="col-6 mt-2">
                  <label for="date_end" class="form-label">Date End</label>
                  <input 
                    type="date" 
                    class="form-control" 
                    name="date_end" 
                    id="date_end"
                    @change="validateDate($event, 'date_end')"
                    autocomplete="off"
                    placeholder="Date End"
                    :class="form.errors.date_end ? 'error-field' : ''"
                    v-model="form.date_end"
                    :disabled="props?.modalAttrs?.action == 'VIEW'"
                  >
                  <ErrorMessage :message="form.errors.date_end"/>
                </div>
                 <div class="col-12 mt-2">
                  <label for="leave_days" class="form-label">Leave Day(s)</label>
                  <input 
                    type="text" 
                    class="form-control" 
                    name="leave_days" 
                    id="leave_days"
                    autocomplete="off"
                    placeholder="Leave Day(s)"
                    :class="form.errors.leave_days ? 'error-field' : ''"
                    v-model="form.leave_days"
                    :disabled="props?.modalAttrs?.action == 'VIEW'"
                    readonly
                  >
                  <ErrorMessage :message="form.errors.leave_days"/>
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
