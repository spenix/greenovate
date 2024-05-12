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

const form = useForm({
    id: "",
    compensation: "",
    short_code: "",
    amount: "",
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
        form.put(route('compensations.update', [form.id]), {
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
        form.post(route('compensations.store'), {
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
        `Compensation was ${
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

watch(props?.modalAttrs, (newValue) => {
  resetFormAction();
    if (newValue?.action != "ADD") {
        http.get(`${page?.url}/show/${newValue?.dataId}`).then(
            ({ data, status }) => {
                form.id = data?.id;
                form.compensation = data?.name;
                form.short_code = data?.short_code;
                form.amount = converToCurrencyFormat(data?.amount);
                form.status = data?.status;
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
                  <label for="compensation" class="form-label">Compensation/Benefit</label>
                  <input 
                    type="text" 
                    class="form-control" 
                    name="compensation" 
                    id="compensation"
                    autocomplete="off"
                    :class="form.errors.compensation ? 'error-field' : ''"
                    v-model="form.compensation"
                    :disabled="props?.modalAttrs?.action == 'VIEW'"
                  >
                  <ErrorMessage :message="form.errors.compensation"/>
                </div>
                <div class="col-12 mt-1">
                  <label for="short_code" class="form-label">Short Code</label>
                  <input 
                    type="text" 
                    class="form-control" 
                    name="short_code" 
                    id="short_code"
                    autocomplete="off"
                    :class="form.errors.short_code ? 'error-field' : ''"
                    v-model="form.short_code"
                    :disabled="props?.modalAttrs?.action == 'VIEW'"
                  >
                  <ErrorMessage :message="form.errors.short_code"/>
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
                    :disabled="props?.modalAttrs?.action == 'VIEW'"
                  >
                  <ErrorMessage :message="form.errors.amount"/>
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
