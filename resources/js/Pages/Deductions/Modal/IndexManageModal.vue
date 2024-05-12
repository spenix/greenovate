<script setup>
import ErrorMessage from '@/Components/ErrorMessage.vue';
import PageModal from '@/Pages/Deductions/Modal/IndexDeductionSetupModal.vue';
import { usePage, useForm, router } from '@inertiajs/vue3';
import { ref, watch, onMounted} from "vue";
import moment from "moment";
import http from "@/utils/https";
import notify from "@/common/notification.js";
import { commonFuntions } from "@/common/common-function.js";
const { converToCurrencyFormat } = commonFuntions();
const emit = defineEmits(["reloadPageData"]);
const page = usePage()
const personnelManageTable = ref({});
const modalPersonnelAttrs = ref({
    modalId: "compensation-setup-modal",
    title: "EMPLOYEE COMPENSATION/BENEFIT",
    action: "",
});
const form = useForm({
    id: "",
    compensation: "",
    short_code: "",
    amount: "",
    status: "Y",
});

const resetFormAction = () => {
    form.reset();
    form.clearErrors();
};

const props = defineProps({
    modalAttrs: Object,
});

const reloadDatatable = () => {
    personnelManageTable.value = $("#manage-personnel-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: `${page.url}/show_deduction_manage_table`,
        columns: [
            { data: "employee_name", title: "Employee Name." },
            { data: "department", title: "Department" },
            { data: "designation", title: "Position" },
            { 
                data: "start_date", 
                title: "Start Date",
                render(h) {
                    return moment(h).format('L');
                },  
             },
            { 
                data: "end_date", 
                title: "End Date",
                render(h) {
                    return h ? moment(h).format('L') : 'Present';
                },  
             },
            {
                data: "id",
                title: "Action",
                className: "text-center",
                orderable: false,
                searchable: false,
                render(h, type, row) {
                    var view = `<li class="view p-1" title="View"> <a href="#" class="view-btn-option blue"><i class="bi bi-eye" data-id="${h}"></i></a></li>`;
                    var del = `<li class="delete p-1" title="Delete"><a href="#" class="delete-btn-option red"><i class="bi bi-trash" data-id="${h}"></i></a></li>`;
                    var edit = `<li class="edit p-1" title="Edit"> <a href="#" class="edit-btn-option green"><i class="bi bi-pencil" data-id="${h}"></i></a></li>`;
                    return `<ul class="action text-center">
                                ${view}
                                ${edit}
                                ${del}
                              </ul>`;
                },
            },
        ],
    });

    setTableBtnAction();
};
const setTableBtnAction = () => {
    $("#manage-personnel-table").on("click", ".view-btn-option", (e) => {
        e.stopImmediatePropagation();
        var id = $(e.target).data("id");
        btnActionFunc(id, "VIEW");
    });
    $("#manage-personnel-table").on("click", ".delete-btn-option", (e) => {
        e.stopImmediatePropagation();
        var id = $(e.target).data("id");
        btnActionFunc(id, "DELETE");
    });
    $("#manage-personnel-table").on("click", ".edit-btn-option", (e) => {
        e.stopImmediatePropagation();
        var id = $(e.target).data("id");
        btnActionFunc(id, "EDIT");
    });
};
const btnActionFunc = (id, action) => {
    modalPersonnelAttrs.value.dataId = id;
    modalPersonnelAttrs.value.title = `${action} EMPLOYEE COMPENSATION/BENEFIT`;
    modalPersonnelAttrs.value.action = action;
    if (["ADD", "EDIT", "VIEW"].includes(action)) {
        $(
            `#${
                modalPersonnelAttrs.value?.modalId
                    ? modalPersonnelAttrs.value?.modalId
                    : "page-modal"
            }`
        ).modal("show");
    } else {
        swal("Are you sure you want to delete data?", {
            buttons: {
                cancel: "Cancel",
                catch: {
                    text: "Yes",
                    value: "catch",
                },
                // defeat: true,
            },
        }).then((value) => {
            switch (value) {
                case "catch":
                    router.delete(`${page?.url}/destroy_deduction/${id}`, {
                        onSuccess: () => {
                            swal(
                                "Success!",
                                "Compensation record was deleted successfully.",
                                "success"
                            ),
                            reloadDatatableAjax();
                        },
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
                    break;
                default:
                    swal.close();
            }
        });
    }
}

const reloadDatatableAjax = () => {
    modalPersonnelAttrs.value.action = "";
    personnelManageTable.value.ajax.reload();
};

watch(props?.modalAttrs, (newValue) => {
  resetFormAction();
    if (newValue?.action != "ADD") {
        http.get(`${page?.url}/show/${newValue?.dataId}`).then(
            ({ data, status }) => {
                form.id = data?.id;
                form.compensation = data?.name;
                form.short_code = data?.short_code;
                form.amount = converToCurrencyFormat(data?.amount);
                form.status = data?.status == 'Y' ? 'Active' : 'Inactive';
            }
        );
    }
});

onMounted(() => {
    reloadDatatable();
})
</script>

<template>
    <div class="modal fade" :id="modalAttrs.modalId" tabindex="-1" data-bs-backdrop="false" style="display: none;" aria-hidden="true">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ modalAttrs.title }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
            <div class="modal-body" style="min-height:450px">
                <div class="row">
                    <div class="col-4 mt-1">
                        <div class="row">
                            <div class="col-12 mt-1">
                            <label for="compensation" class="text-muted">Deduction:</label>
                            <input 
                                type="text" 
                                class="form-control" 
                                name="compensation" 
                                id="compensation"
                                autocomplete="off"
                                v-model="form.compensation"
                                readonly
                            >
                            </div>
                            <div class="col-12 mt-1">
                            <label for="short_code" class="text-muted">Short Code:</label>
                            <input 
                                type="text" 
                                class="form-control" 
                                name="short_code" 
                                id="short_code"
                                autocomplete="off"
                                v-model="form.short_code"
                                readonly
                            >
                            </div>
                            <div class="col-12 mt-1">
                            <label for="amount" class="text-muted">Amount:</label>
                            <input 
                                type="text" 
                                class="form-control text-end" 
                                name="amount" 
                                id="amount"
                                autocomplete="off"
                                v-model="form.amount"
                                readonly
                            >
                            </div>
                            <div class="col-12 mt-1">
                            <label class="text-muted">Status:</label>
                            <input 
                                type="text" 
                                class="form-control" 
                                name="short_code" 
                                id="short_code"
                                autocomplete="off"
                                v-model="form.status"
                                readonly
                            >
                            </div>
                        </div>
                    </div>
                    <div class="col-8 mt-1">
                        <ul class="list-unstyled">
                            <li class="mb-2 d-flex justify-content-end">
                                <button type="button" class="btn btn-primary btn-sm" @click="btnActionFunc('', 'ADD')">Add Employee</button>
                            </li>
                            <li class="mb-2">
                                <div class="table-responsive">
                                    <table
                                        class="table table-bordered table-sm display"
                                        id="manage-personnel-table"
                                        style="width: 150% !important"
                                    ></table>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
               
            </div>
        </div>
      </div>
    </div>
    <PageModal 
                    :modalAttrs="modalPersonnelAttrs"
                    @reloadPageData="reloadDatatableAjax()"
                />
</template>
