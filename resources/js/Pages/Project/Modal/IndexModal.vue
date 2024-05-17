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
const statuses = ref(page.props.statuses);
const form = useForm({
    id: "",
    project: "",
    location: "",
    client: "",
    description: "",
    project_cost: "",
    start_date: "",
    end_date: "",
    status: "",
});

const resetFormAction = () => {
    form.reset();
    form.clearErrors();
};

const props = defineProps({
    modalAttrs: Object,
});

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
    form.project_cost = val
}

const submitData = () => {
  if (props?.modalAttrs?.action == "EDIT") {
        form.put(route('project.update', [form.id]), {
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
        form.post(route('project.store'), {
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
        `Leave type was ${
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
    if (newValue?.action != "ADD") {
        http.get(`${page?.url}/show/${newValue?.dataId}`).then(
            ({ data, status }) => {
              form.id = data.id
              form.project = data.name
              form.location = data.location
              form.client = data.client_name
              form.description = data.description
              form.project_cost = converToCurrencyFormat(data.project_cost)
              form.start_date = data.start_date
              form.end_date = data.end_date
              form.status = data.status
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
                  <label for="project" class="form-label">Project</label>
                  <input 
                    type="text" 
                    class="form-control" 
                    name="project" 
                    id="project"
                    autocomplete="off"
                    :class="form.errors.project ? 'error-field' : ''"
                    v-model="form.project"
                    :disabled="props?.modalAttrs?.action == 'VIEW'"
                  >
                  <ErrorMessage :message="form.errors.project"/>
                </div>
                <div class="col-12 mt-1">
                  <label for="location" class="form-label">Location</label>
                  <input 
                    type="text" 
                    class="form-control" 
                    name="location" 
                    id="location"
                    autocomplete="off"
                    :class="form.errors.location ? 'error-field' : ''"
                    v-model="form.location"
                    :disabled="props?.modalAttrs?.action == 'VIEW'"
                  >
                  <ErrorMessage :message="form.errors.location"/>
                </div>
                <div class="col-12 mt-1">
                  <label for="client" class="form-label">Client</label>
                  <input 
                    type="text" 
                    class="form-control" 
                    name="client" 
                    id="client"
                    autocomplete="off"
                    :class="form.errors.client ? 'error-field' : ''"
                    v-model="form.client"
                    :disabled="props?.modalAttrs?.action == 'VIEW'"
                  >
                  <ErrorMessage :message="form.errors.client"/>
                </div>
                <div class="col-12 mt-1">
                  <label for="client" class="form-label">Description</label>
                  <textarea 
                    class="form-control"
                    name="description" 
                    id="description"
                    rows="5"
                    :class="form.errors.description ? 'error-field' : ''"
                    v-model="form.description"
                    :disabled="props?.modalAttrs?.action == 'VIEW'"
                  ></textarea>
                  <ErrorMessage :message="form.errors.description"/>
                </div>
                <div class="col-6 mt-1">
                  <label for="start_date" class="form-label">Start Date</label>
                  <input 
                    type="date" 
                    class="form-control" 
                    name="start_date" 
                    id="start_date"
                    @change="validateDate($event, 'start_date')"
                    autocomplete="off"
                    :class="form.errors.start_date ? 'error-field' : ''"
                    v-model="form.start_date"
                    :disabled="props?.modalAttrs?.action == 'VIEW'"
                  >
                  <ErrorMessage :message="form.errors.start_date"/>
                </div>
                <div class="col-6 mt-1">
                  <label for="end_date" class="form-label">End Date</label>
                  <input 
                    type="date" 
                    class="form-control" 
                    name="end_date" 
                    id="end_date"
                    @change="validateDate($event, 'end_date')"
                    autocomplete="off"
                    :class="form.errors.end_date ? 'error-field' : ''"
                    v-model="form.end_date"
                    :disabled="props?.modalAttrs?.action == 'VIEW'"
                  >
                  <ErrorMessage :message="form.errors.end_date"/>
                </div>
                <div class="col-12 mt-1">
                  <label for="project_cost" class="form-label">Project Cost</label>
                  <input 
                    type="text" 
                    class="form-control text-end" 
                    name="project_cost" 
                    id="project_cost"
                    autocomplete="off"
                    @keypress="isNumber($event)" 
                    @blur="
                        converToCurrency(
                            $event
                        )
                    "
                    :class="form.errors.project_cost ? 'error-field' : ''"
                    v-model="form.project_cost"
                    :disabled="props?.modalAttrs?.action == 'VIEW'"
                  >
                  <ErrorMessage :message="form.errors.project_cost"/>
                </div>
                <div class="col-12 mt-1">
                  <label for="status" class="form-label">Status</label>
                  <select 
                    id="status" 
                    class="form-select"
                    :class="form.errors.status ? 'error-field' : ''"
                    v-model="form.status"
                    :disabled="props?.modalAttrs?.action == 'VIEW'"
                  >
                    <option value="" hidden>
                        Select Status
                    </option>
                    <option 
                    v-for="(
                          d, i
                      ) in statuses"
                      :key="d"
                      :value="d"
                      :selected="
                          form.status ==
                          d
                      "
                    >
                    {{ d }}
                    </option>
                  </select>
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
