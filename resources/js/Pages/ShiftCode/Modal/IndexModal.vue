<script setup>
import ErrorMessage from '@/Components/ErrorMessage.vue';
import { usePage, useForm } from '@inertiajs/vue3';
import { ref, watch} from "vue";
import http from "@/utils/https";
import notify from "@/common/notification.js";
const emit = defineEmits(["reloadPageData"]);
const page = usePage()

const shifts = ref(page.props.shifts);
const days_list = ref(page.props.days_list);
const form = useForm({
    id: "",
    shift: "",
    time_in: "",
    break_out: "",
    break_in: "",
    time_out: "",
    days: [],
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
        form.put(route('shift-code.update', [form.id]), {
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
        form.post(route('shift-code.store'), {
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
        `Shift code was ${
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
              form.id = data?.id
              form.shift = data?.shift_id
              form.time_in = data?.clock_in
              form.break_out = data?.break_out
              form.break_in = data?.break_in
              form.time_out = data?.clock_out
              form.days = data?.days?.split("|")
              form.status = data?.status
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
                  <label for="shift" class="form-label">Shift</label>
                  <select 
                    id="shift" 
                    class="form-select"
                    :class="form.errors.shift ? 'error-field' : ''"
                    v-model="form.shift"
                    :disabled="props?.modalAttrs?.action == 'VIEW'"
                  >
                    <option value="" hidden>
                        Select Shift
                    </option>
                    <option 
                    v-for="(
                          d, i
                      ) in shifts"
                      :key="d.id"
                      :value="d.id"
                      :selected="
                          form.shift ==
                          d?.id
                      "
                    >
                    {{ d?.name }}
                    </option>
                  </select>
                  <ErrorMessage :message="form.errors.shift"/>
                </div>
                <div class="col-12 mt-1">
                  <label for="time_in" class="form-label">Time In</label>
                  <input 
                    type="time" 
                    class="form-control" 
                    name="time_in" 
                    id="time_in"
                    autocomplete="off"
                    :class="form.errors.time_in ? 'error-field' : ''"
                    v-model="form.time_in"
                    :disabled="props?.modalAttrs?.action == 'VIEW'"
                  >
                  <ErrorMessage :message="form.errors.time_in"/>
                </div>
                <div class="col-12 mt-1">
                  <label for="break_out" class="form-label">Break Out</label>
                  <input 
                    type="time" 
                    class="form-control" 
                    name="break_out" 
                    id="break_out"
                    autocomplete="off"
                    :class="form.errors.break_out ? 'error-field' : ''"
                    v-model="form.break_out"
                    :disabled="props?.modalAttrs?.action == 'VIEW'"
                  >
                  <ErrorMessage :message="form.errors.break_out"/>
                </div>
                <div class="col-12 mt-1">
                  <label for="break_in" class="form-label">Break In</label>
                  <input 
                    type="time" 
                    class="form-control" 
                    name="break_in" 
                    id="break_in"
                    autocomplete="off"
                    :class="form.errors.break_in ? 'error-field' : ''"
                    v-model="form.break_in"
                    :disabled="props?.modalAttrs?.action == 'VIEW'"
                  >
                  <ErrorMessage :message="form.errors.break_in"/>
                </div>
                <div class="col-12 mt-1">
                  <label for="time_out" class="form-label">Time Out</label>
                  <input 
                    type="time" 
                    class="form-control" 
                    name="time_out" 
                    id="time_out"
                    autocomplete="off"
                    :class="form.errors.time_out ? 'error-field' : ''"
                    v-model="form.time_out"
                    :disabled="props?.modalAttrs?.action == 'VIEW'"
                  >
                  <ErrorMessage :message="form.errors.time_out"/>
                </div>
                <div class="col-12 mt-1">
                  <label for="days" class="form-label">Days <span v-if="form.days.length">{{ form.days }}</span></label>
                  <div class="form-check" v-for="(d, i) in days_list" :key="i">
                      <input class="form-check-input" type="checkbox" :id="`day_${i}`" :value="d" v-model="form.days">
                      <label class="form-check-label" :for="`day_${i}`">
                        {{ d }}
                      </label>
                    </div>
                  <ErrorMessage :message="form.errors.days"/>
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
