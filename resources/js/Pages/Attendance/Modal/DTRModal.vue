<script setup>
import ErrorMessage from '@/Components/ErrorMessage.vue';
import { usePage, useForm } from '@inertiajs/vue3';
import { ref, watch, onMounted} from "vue";
import moment from "moment";
import http from "@/utils/https";
import notify from "@/common/notification.js";
const emit = defineEmits(["reloadPageData"]);
const page = usePage()
const table = ref(null);
const employees = ref(page.props.employees);
const shifts = ref(page.props.shifts);
const emp_id_select = ref();

const form = useForm({
    id: "",
    employee_id: "",
    employee_name: "",
    department: "",
    employee_type: "",
    shift: ""
});

const props = defineProps({
    modalDTRAttrs: Object,
});

const reloadDatatable = () => {
    table.value = $("#attendance-dtr-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: `${page.url}/show_dtr_table_data?attendance_id=${form.id}`,
        columns: [
            { 
                data: "period_start", 
                title: "Period Start",
                render(h) {
                    return moment(h).format('L');
                },      
            },
            { 
                data: "period_end", 
                title: "Period End",
                render(h) {
                    return moment(h).format('L');
                },      
            },
            { 
                data: "attachment_path", 
                title: "DTR",
                render(h) {
                    return `<a href="${h}" target="_blank">Download</a>`;
                },  
            }
        ],
    });

};

watch(props?.modalDTRAttrs, (newValue) => {
//   resetFormAction();
    if (table.value) {
        table.value.destroy();
    }
    emp_id_select.value[0].selectize.clear();
    emp_id_select.value[0].selectize.enable();
    if (newValue?.action != "ADD") {
        http.get(`${page?.url}/show/${newValue?.dataId}`).then(
            ({ data, status }) => {
                emp_id_select.value[0].selectize.setValue(data.employee_id)
                form.id = data.id
                form.employee_id = data.employee_id
                form.shift = data?.shift_id;
                reloadDatatable();
            }
        );
    }
    if(newValue?.action == "DTR") {
        emp_id_select.value[0].selectize.disable();
    }
  
    
});


const getSelectedEmp = (selectedVal) => {
  var emp = employees.value.find((d) => {
       return d.id == selectedVal
    });
    form.employee_id = emp?.id;
    form.employee_name = emp?.employee_name;
    form.department = emp?.designation;
    form.employee_type = emp?.employee_type;
};
onMounted(() => {
    emp_id_select.value = $("#select_dtr_employee_id").selectize({
        onChange: function(value) {
            getSelectedEmp(value);
        }
    });
})
</script>

<template>
    <div class="modal fade" :id="modalDTRAttrs.modalId" tabindex="-1" data-bs-backdrop="false" style="display: none;" aria-hidden="true">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ modalDTRAttrs.title }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form>
            <div class="modal-body">
              <div class="row">
                <div class="col-4">
                    <div class="row">
                    <div class="col-12 mt-2">
                    <label for="select_dtr_employee_id" class="form-label">Employee ID</label>
                        <select 
                                    id="select_dtr_employee_id" 
                                    aria-label="Search Employee ID"
                                    :disabled="modalDTRAttrs?.action == 'DTR'"
                                >
                                <option value="" hidden>
                                    Search Employee ID
                                </option>
                                <option 
                                v-for="(
                                    d, i
                                ) in employees"
                                :key="d.id"
                                :value="d.id"
                                :selected="
                                    form.employee_id ==
                                    d?.id
                                "
                                >
                                {{ d?.employee_no }}
                                </option>
                                </select>
                    <ErrorMessage :message="form.errors.employee_id"/>
                    </div>
                    <div class="col-12 mt-2">
                    <label for="employee_name" class="form-label">Employee Name</label>
                    <input 
                        type="text" 
                        class="form-control" 
                        name="employee_name" 
                        id="employee_name"
                        autocomplete="off"
                        placeholder="Employee Name"
                        :class="form.errors.employee_name ? 'error-field' : ''"
                        v-model="form.employee_name"
                        :disabled="props?.modalDTRAttrs?.action == 'DTR'"
                        readonly
                    >
                    <ErrorMessage :message="form.errors.employee_name"/>
                    </div>
                    <div class="col-12 mt-2">
                    <label for="department" class="form-label">Department</label>
                    <input 
                        type="text" 
                        class="form-control" 
                        name="department" 
                        id="department"
                        autocomplete="off"
                        placeholder="Department"
                        :class="form.errors.department ? 'error-field' : ''"
                        v-model="form.department"
                        :disabled="props?.modalDTRAttrs?.action == 'DTR'"
                        readonly
                    >
                    <ErrorMessage :message="form.errors.department"/>
                    </div>
                    <div class="col-12 mt-2">
                    <label for="employee_type" class="form-label">Employee Type</label>
                    <input 
                        type="text" 
                        class="form-control" 
                        name="employee_type" 
                        id="employee_type"
                        autocomplete="off"
                        placeholder="Employee Type"
                        :class="form.errors.employee_type ? 'error-field' : ''"
                        v-model="form.employee_type"
                        :disabled="props?.modalDTRAttrs?.action == 'DTR'"
                        readonly
                    >
                    <ErrorMessage :message="form.errors.employee_type"/>
                    </div>
                    <div class="col-12 mt-1">
                    <label for="shift" class="form-label">Shift</label>
                    <select 
                        id="shift" 
                        class="form-select"
                        :class="form.errors.shift ? 'error-field' : ''"
                        v-model="form.shift"
                        :disabled="props?.modalDTRAttrs?.action == 'DTR'"
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
                </div>
                </div>
                <div class="col-8">
                    <div class="table-responsive">
                            <table
                                class="table table-bordered table-sm display"
                                id="attendance-dtr-table"
                                style="width: 100% !important"
                            ></table>
                        </div>
                </div>
              </div>
             
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button v-if="props?.modalDTRAttrs?.action != 'DTR'" type="submit" class="btn btn-primary" :disabled="form.processing">Submit</button>
            </div>
          </form>
        </div>
      </div>
    </div>
</template>
