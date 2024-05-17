<script setup>
import ErrorMessage from '@/Components/ErrorMessage.vue';
import { usePage, useForm } from '@inertiajs/vue3';
import { ref, watch} from "vue";
import http from "@/utils/https";
import notify from "@/common/notification.js";
const emit = defineEmits(["reloadPageData"]);
const page = usePage()

const form = useForm({
    id: "",
    title: "",
    description: "",
    start_date: "",
    end_date: "",
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
        form.put(route('system-calendar.update', [form.id]), {
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
        form.post(route('system-calendar.store'), {
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
        `Event was ${
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
                form.id = data?.id,
                form.title = data?.name
                form.description = data?.description
                form.start_date = data?.start_date
                form.end_date = data?.end_date
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
                  <label for="title" class="form-label">Title</label>
                  <input 
                    type="text" 
                    class="form-control" 
                    name="title" 
                    id="title"
                    placeholder="Title"
                    autocomplete="off"
                    :class="form.errors.title ? 'error-field' : ''"
                    v-model="form.title"
                    :disabled="props?.modalAttrs?.action == 'VIEW'"
                  >
                  <ErrorMessage :message="form.errors.title"/>
                </div>
                <div class="col-12 mt-1">
                  <label for="client" class="form-label">Description</label>
                  <textarea 
                    class="form-control"
                    name="description" 
                    id="description"
                    rows="5"
                    placeholder="Description"
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
