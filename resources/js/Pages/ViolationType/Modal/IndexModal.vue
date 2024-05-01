<script setup>
import ErrorMessage from '@/Components/ErrorMessage.vue';
import { usePage, useForm } from '@inertiajs/vue3';
import { ref, watch} from "vue";
import http from "@/utils/https";
import notify from "@/common/notification.js";
const emit = defineEmits(["reloadPageData"]);
const page = usePage()

const violationCategories = ref(page.props.violationCategories);

const form = useForm({
    id: "",
    violation_type: "",
    violation_category: "",
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
        form.put(route('violation-types.update', [form.id]), {
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
        form.post(route('violation-types.store'), {
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
        `Violation type was ${
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
    if (newValue?.action != "ADD") {
        http.get(`${page?.url}/show/${newValue?.dataId}`).then(
            ({ data, status }) => {
                form.id = data?.id;
                form.violation_type = data?.name;
                form.violation_category = data?.violation_category_id;
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
                  <label for="violation_type" class="form-label">Violation Type</label>
                  <input 
                    type="text" 
                    class="form-control" 
                    name="violation_type" 
                    id="violation_type"
                    :class="form.errors.violation_type ? 'error-field' : ''"
                    v-model="form.violation_type"
                    autocomplete="off"
                    :disabled="props?.modalAttrs?.action == 'VIEW'"
                  >
                  <ErrorMessage :message="form.errors.violation_type"/>
                </div>
                <div class="col-12 mt-1">
                  <label for="violation_category" class="form-label">Violation Category</label>
                  <select 
                    id="violation_category" 
                    class="form-select"
                    :class="form.errors.violation_category ? 'error-field' : ''"
                    v-model="form.violation_category"
                    :disabled="props?.modalAttrs?.action == 'VIEW'"
                  >
                    <option value="" hidden>
                        Select Violation Category
                    </option>
                    <option 
                    v-for="(
                          d, i
                      ) in violationCategories"
                      :key="d.id"
                      :value="d.id"
                      :selected="
                          form.violation_category ==
                          d?.id
                      "
                    >
                    {{ d?.name }}
                    </option>
                  </select>
                  <ErrorMessage :message="form.errors.violation_category"/>
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
