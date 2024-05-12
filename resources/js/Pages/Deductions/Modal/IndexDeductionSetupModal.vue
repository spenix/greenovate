<script setup>
import ErrorMessage from '@/Components/ErrorMessage.vue';
import { usePage, useForm } from '@inertiajs/vue3';
import { ref, watch, onMounted } from "vue";
import moment from "moment";
import http from "@/utils/https";
import notify from "@/common/notification.js";
import { commonFuntions } from "@/common/common-function.js";
const { isNumber, converToCurrencyFormat } = commonFuntions();
const emit = defineEmits(["reloadPageData"]);
const page = usePage()

const deductions = ref(page.props?.deductions ?? []);
const employees = ref(page.props?.employees ?? []);
const emp_id_select = ref();
const form = useForm({
    id: "",
    deduction: "",
    amount: "",
    employee: "",
    start_date: "",
    end_date: "",
    isPresent: true
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
        form.put(route('deductions.update-emp-setup', [form.id]), {
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
        form.post(route('deductions.store-emp-setup'), {
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
        `Deduction setup was ${
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
    form.amount = val
}
const getDeductionDetail = (evt) => {
 
  var result = deductions.value.find(d => {
    return d.id = evt.target.value
  });

  form.amount = converToCurrencyFormat(result.amount)
}

const validateDate = (evt, inpt) => {
  var dt = evt.target.value;
  if (inpt == 'start_date') {
    $('#end_date').prop('min', dt);
  }
  if (inpt == 'end_date') {
    $('#start_date').prop('max', dt);
  }
}

watch(props?.modalAttrs, (newValue) => {
  resetFormAction();
    emp_id_select.value[0].selectize.clear();
    emp_id_select.value[0].selectize.enable();
    if (newValue?.action != "ADD") {
        http.get(`${page?.url}/show_employee_setup/${newValue?.dataId}`).then(
            ({ data, status }) => {
              form.id = data?.id
              form.deduction = data?.param_deduction_id
              emp_id_select.value[0].selectize.setValue(data.employee_id)
              form.employee = data?.employee_id
              form.start_date = moment(data.date_start).format("YYYY-MM-DD");
              form.end_date = data?.end_date ? moment(data.date_end).format("YYYY-MM-DD"): "";
              form.isPresent = data?.end_date ? false : true
              var result = deductions.value.find(d => {
                return d.id = data?.param_deduction_id
              });

              form.amount = converToCurrencyFormat(result.amount)
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

    form.employee = emp?.id;
};
onMounted(() => {
  emp_id_select.value = $("#employee").selectize({
        onChange: function(value) {
            getSelectedEmp(value);
        }
    })
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
                <div class="col-12 mt-1">
                  <label for="employee" class="form-label">Employee</label>
                  <select 
                    id="employee" 
                    :class="form.errors.employee ? 'error-field' : ''"
                    v-model="form.employee"
                    :disabled="props?.modalAttrs?.action == 'VIEW'"
                  >
                    <option value="" hidden>
                        Select Employee
                    </option>
                    <option 
                    v-for="(
                          d, i
                      ) in employees"
                      :key="d.id"
                      :value="d.id"
                      :selected="
                          form.employee ==
                          d?.id
                      "
                    >
                    {{ d?.employee_name }}
                    </option>
                  </select>
                  <ErrorMessage :message="form.errors.employee"/>
                </div>
                <div class="col-12 mt-1">
                  <label for="deduction" class="form-label">Deduction</label>
                  <select 
                    id="deduction" 
                    class="form-select"
                    :class="form.errors.deduction ? 'error-field' : ''"
                    v-model="form.deduction"
                    @change="getDeductionDetail($event)"
                    :disabled="props?.modalAttrs?.action == 'VIEW'"
                  >
                    <option value="" hidden>
                        Select Deduction
                    </option>
                    <option 
                    v-for="(
                          d, i
                      ) in deductions"
                      :key="d.id"
                      :value="d.id"
                      :selected="
                          form.deduction ==
                          d?.id
                      "
                    >
                    {{ `[${d?.short_code}] ${d?.name}` }}
                    </option>
                  </select>
                  <ErrorMessage :message="form.errors.deduction"/>
                </div>
                <div class="col-12 mt-1">
                  <label for="amount" class="form-label">Amount</label>
                  <input 
                    type="text" 
                    class="form-control text-end" 
                    name="amount" 
                    id="amount"
                    @keypress="isNumber($event)" 
                    @blur="
                        converToCurrency(
                            $event
                        )
                    "
                    autocomplete="off"
                    :class="form.errors.amount ? 'error-field' : ''"
                    v-model="form.amount"
                    readonly
                    :disabled="props?.modalAttrs?.action == 'VIEW'"
                  >
                  <ErrorMessage :message="form.errors.amount"/>
                </div>
                <div class="col-5 mt-2">
                  <label for="start_date" class="form-label">Start Date</label>
                  <input 
                    type="date" 
                    class="form-control" 
                    name="start_date" 
                    id="start_date"
                    @change="validateDate($event, 'start_date')"
                    autocomplete="off"
                    placeholder="Start Date"
                    :class="form.errors.start_date ? 'error-field' : ''"
                    v-model="form.start_date"
                    :disabled="props?.modalAttrs?.action == 'VIEW'"
                  >
                  <ErrorMessage :message="form.errors.start_date"/>
                </div>
                <div class="col-5 mt-2" v-if="!form.isPresent">
                  <label for="end_date" class="form-label">End Date</label>
                  <input 
                    type="date" 
                    class="form-control" 
                    name="end_date" 
                    id="end_date"
                    @change="validateDate($event, 'end_date')"
                    autocomplete="off"
                    placeholder="End Date"
                    :class="form.errors.end_date ? 'error-field' : ''"
                    v-model="form.end_date"
                    :disabled="props?.modalAttrs?.action == 'VIEW'"
                  >
                  <ErrorMessage :message="form.errors.end_date"/>
                </div>
                <div class="col-2 mt-2">
                  <label for="end_date" class="form-label">&nbsp;</label>
                  <div class="form-check">
                      <input class="form-check-input" type="checkbox" id="useInPresent" v-model="form.isPresent" :disabled="props?.modalAttrs?.action == 'VIEW'">
                      <label class="form-check-label" for="useInPresent">
                        Present
                      </label>
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
