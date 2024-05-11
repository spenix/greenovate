<script setup>
import ErrorMessage from '@/Components/ErrorMessage.vue';
import { usePage, useForm } from '@inertiajs/vue3';
import { ref, watch} from "vue";
import http from "@/utils/https";
import notify from "@/common/notification.js";
const emit = defineEmits(["reloadPageData"]);
const page = usePage()

const departments = ref(page.props.departments);

const form = useForm({
    id: "",
    position: "",
    department: "",
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
        form.put(route('positions.update', [form.id]), {
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
        form.post(route('positions.store'), {
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
        `Position was ${
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
                form.position = data?.name;
                form.department = data?.department_id;
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
                  <label for="position" class="form-label">Position</label>
                  <input 
                    type="text" 
                    class="form-control" 
                    name="position" 
                    id="position"
                    autocomplete="off"
                    :class="form.errors.position ? 'error-field' : ''"
                    v-model="form.position"
                    :disabled="props?.modalAttrs?.action == 'VIEW'"
                  >
                  <ErrorMessage :message="form.errors.position"/>
                </div>
                <div class="col-12 mt-1">
                  <label for="department" class="form-label">Department</label>
                  <select 
                    id="department" 
                    class="form-select"
                    :class="form.errors.department ? 'error-field' : ''"
                    v-model="form.department"
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
                  <label class="form-label">Status</label>
                  <div class="form-check">
                      <input class="form-check-input" type="radio" name="status" v-model="form.status" id="status_active_position" value="Y" :checked="form.status == 'Y'" :disabled="props?.modalAttrs?.action == 'VIEW'">
                      <label class="form-check-label" for="status_active_position">
                        Active
                      </label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="status" v-model="form.status" id="status_inactive_position" value="N" :checked="form.status == 'N'" :disabled="props?.modalAttrs?.action == 'VIEW'">
                      <label class="form-check-label" for="status_inactive_position">
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
