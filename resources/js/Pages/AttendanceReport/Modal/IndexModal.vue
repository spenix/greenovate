<script setup>
import ErrorMessage from '@/Components/ErrorMessage.vue';
import { usePage, useForm } from '@inertiajs/vue3';
import { ref, watch} from "vue";
import http from "@/utils/https";
import notify from "@/common/notification.js";
const emit = defineEmits(["reloadPageData"]);
const page = usePage()

const leaveEntitlements = ref(page.props.leaveEntitlements);

const form = useForm({
    id: "",
    leave_type: "",
    leave_entitlement: "",
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
        form.put(route('leave-types.update', [form.id]), {
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
        form.post(route('leave-types.store'), {
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

watch(props?.modalAttrs, (newValue) => {
  resetFormAction();
    if (newValue?.action != "ADD") {
        http.get(`${page?.url}/show/${newValue?.dataId}`).then(
            ({ data, status }) => {
                form.id = data?.id;
                form.leave_type = data?.name;
                form.leave_entitlement = data?.leave_entitlement_id;
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
                  <label for="leave_type" class="form-label">Leave Type</label>
                  <input 
                    type="text" 
                    class="form-control" 
                    name="leave_type" 
                    id="leave_type"
                    autocomplete="off"
                    :class="form.errors.leave_type ? 'error-field' : ''"
                    v-model="form.leave_type"
                    :disabled="props?.modalAttrs?.action == 'VIEW'"
                  >
                  <ErrorMessage :message="form.errors.leave_type"/>
                </div>
                <div class="col-12 mt-1">
                  <label for="leave_entitlement" class="form-label">Leave Entitlement</label>
                  <select 
                    id="leave_entitlement" 
                    class="form-select"
                    :class="form.errors.leave_entitlement ? 'error-field' : ''"
                    v-model="form.leave_entitlement"
                    :disabled="props?.modalAttrs?.action == 'VIEW'"
                  >
                    <option value="" hidden>
                        Select Leave Entitlement
                    </option>
                    <option 
                    v-for="(
                          d, i
                      ) in leaveEntitlements"
                      :key="d.id"
                      :value="d.id"
                      :selected="
                          form.leave_entitlement ==
                          d?.id
                      "
                    >
                    {{ d?.name }}
                    </option>
                  </select>
                  <ErrorMessage :message="form.errors.leave_entitlement"/>
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
