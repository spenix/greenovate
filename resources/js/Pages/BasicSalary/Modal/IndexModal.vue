<script setup>
import ErrorMessage from '@/Components/ErrorMessage.vue';
import { usePage, useForm } from '@inertiajs/vue3';
import { ref, watch} from "vue";
import http from "@/utils/https";
import notify from "@/common/notification.js";
import { commonFuntions } from "@/common/common-function.js";
const { isNumber, converToCurrencyFormat } = commonFuntions();
const emit = defineEmits(["reloadPageData"]);
const page = usePage()

const departments = ref(page.props.departments);
const designations = ref(page.props.designations);

const form = useForm({
    id: "",
    department: "",
    position: "",
    basic_salary: "",
    status: "Y"
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
        form.put(route('basic-salary.update', [form.id]), {
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
        form.post(route('basic-salary.store'), {
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
        `Basic salary was ${
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

const getPositionBasedOnDepartment = (evt) => {
    designations.value = page.props?.designations.filter(d => {
        return d.department_id == evt.target.value
    })
}

const converToCurrency = (evt) => {
    const inputValue = evt.target.value.replace(/,/g, "");
            var val = "0.00";
            if (inputValue) {
                var val = parseFloat(inputValue).toLocaleString("en-US", {
                    style: "decimal",
                    maximumFractionDigits: 2,
                    minimumFractionDigits: 2,
                });
            }
    form.basic_salary = val
}

watch(props?.modalAttrs, (newValue) => {
  resetFormAction();
    if (newValue?.action != "ADD") {
        http.get(`${page?.url}/show/${newValue?.dataId}`).then(
            ({ data, status }) => {
              if(status == 200) {
                form.id = data?.id;
                form.position = data?.designation_id;
                form.basic_salary = converToCurrencyFormat(data?.basic_salary);
                form.status = data?.status;
                var position = page.props?.designations.find(d => {
                     return d.id == data?.designation_id
                 })
                form.department = position ? position?.department_id : "";
                designations.value = page.props?.designations.filter(d => {
                    return d.department_id == position?.department_id
                })
              }
            }
        );
    }
});
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
                <div class="col-12 mt-1">
                  <label for="department" class="form-label">Department</label>
                  <select 
                    id="department" 
                    class="form-select"
                    :class="form.errors.department ? 'error-field' : ''"
                    v-model="form.department"
                    @change="getPositionBasedOnDepartment($event)"
                    :disabled="props?.modalAttrs?.action == 'VIEW'"
                  >
                    <option value="" hidden>
                        Select Department
                    </option>
                    <option 
                    v-for="(
                          d, i
                      ) in departments"
                      :key="d.id"
                      :value="d.id"
                      :selected="
                          form.department ==
                          d?.id
                      "
                    >
                    {{ d?.name }}
                    </option>
                  </select>
                  <ErrorMessage :message="form.errors.department"/>
                </div>
                <div class="col-12 mt-1">
                  <label for="position" class="form-label">Position</label>
                  <select 
                    id="position" 
                    class="form-select"
                    :class="form.errors.position ? 'error-field' : ''"
                    v-model="form.position"
                    :disabled="props?.modalAttrs?.action == 'VIEW'"
                  >
                    <option value="" hidden>
                        Select Position
                    </option>
                    <option 
                    v-for="(
                          d, i
                      ) in designations"
                      :key="d.id"
                      :value="d.id"
                      :selected="
                          form.position ==
                          d?.id
                      "
                    >
                    {{ d?.name }}
                    </option>
                  </select>
                  <ErrorMessage :message="form.errors.position"/>
                </div>
                <div class="col-12 mt-1">
                  <label for="basic_salary" class="form-label">Basic Salary</label>
                  <input 
                    type="text" 
                    class="form-control text-end" 
                    name="basic_salary" 
                    id="basic_salary"
                    @keypress="isNumber($event)" 
                    @blur="
                        converToCurrency(
                            $event
                        )
                    "
                    autocomplete="off"
                    :class="form.errors.basic_salary ? 'error-field' : ''"
                    v-model="form.basic_salary"
                    :disabled="props?.modalAttrs?.action == 'VIEW'"
                  >
                  <ErrorMessage :message="form.errors.basic_salary"/>
                </div>
                <div class="col-12 mt-1">
                  <label class="form-label">Status</label>
                  <div class="form-check">
                      <input class="form-check-input" type="radio" name="status" v-model="form.status" id="status_active_department" value="Y" :checked="form.status == 'Y'" :disabled="props?.modalAttrs?.action == 'VIEW'">
                      <label class="form-check-label" for="status_active_department">
                        Active
                      </label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="status" v-model="form.status" id="status_inactive_department" value="N" :checked="form.status == 'N'" :disabled="props?.modalAttrs?.action == 'VIEW'">
                      <label class="form-check-label" for="status_inactive_department">
                        Inactive
                      </label>
                    </div>
                  <ErrorMessage :message="form.errors.status"/>
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
