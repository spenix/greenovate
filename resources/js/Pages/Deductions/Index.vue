<script setup>
import HeaderLayout from '@/Layouts/HeaderLayout.vue';
import SidebarLayout from '@/Layouts/SidebarLayout.vue';
import FooterLayout from '@/Layouts/FooterLayout.vue';
import PageModal from '@/Pages/Deductions/Modal/IndexModal.vue';
import PersonnelModal from '@/Pages/Deductions/Modal/IndexPersonnelModal.vue';
import ManageModal from '@/Pages/Deductions/Modal/IndexManageModal.vue';
import notify from "@/common/notification.js";
import { Link, Head, usePage, router } from '@inertiajs/vue3';
import { onMounted, ref} from "vue";

const page = usePage()
const table = ref({});
const personnelTable = ref({});
const modalAttrs = ref({
    modalId: "deductions-modal",
    title: "DEDUCTION",
    action: "",
});
const modalPersonnelAttrs = ref({
    modalId: "personnel-modal",
    title: "DEDUCTION",
    action: "",
});

const modalManageAttrs = ref({
    modalId: "manage-deduction-modal",
    title: "DEDUCTION",
    action: "",
});
const props = defineProps({
    systemSetup: {
        type: Object,
    },
});

const reloadDatatableAjax = () => {
    modalAttrs.value.action = "";
    table.value.ajax.reload();
};
const reloadPersonnelDatatableAjax = () => {
    modalPersonnelAttrs.value.action = "";
    personnelTable.value.ajax.reload();
};
const reloadDatatable = () => {
    table.value = $("#deductions-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: `${page.url}/show_table_data`,
        columns: [
            { data: "name", title: "Deduction" },
            { data: "short_code", title: "Short Code" },
            { data: "status", title: "Status", className: "text-center", },
            {
                data: "id",
                title: "Action",
                className: "text-center",
                orderable: false,
                searchable: false,
                render(h, type, row) {
                    var manage = `<li class="manage p-1" title="Manage"> <a href="#" class="manage-btn-option grey"><i class="bi bi-folder2" data-id="${h}"></i></a></li>`;
                    var view = `<li class="view p-1" title="View"> <a href="#" class="view-btn-option blue"><i class="bi bi-eye" data-id="${h}"></i></a></li>`;
                    var del = `<li class="delete p-1" title="Delete"><a href="#" class="delete-btn-option red"><i class="bi bi-trash" data-id="${h}"></i></a></li>`;
                    var edit = `<li class="edit p-1" title="Edit"> <a href="#" class="edit-btn-option green"><i class="bi bi-pencil" data-id="${h}"></i></a></li>`;
                    return `<ul class="action text-center">
                                ${manage}
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

const reloadPersonnelDatatable = () => {
    personnelTable.value = $("#personnel-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: `${page.url}/show_personnel_table_data`,
        columns: [
            { data: "employee_no", title: "Employee No." },
            { data: "employee_name", title: "Employee Name." },
            { data: "department", title: "Department" },
            { data: "designation", title: "Position" },
            { data: "employee_type", title: "Employment Type" },
            {
                data: "id",
                title: "Action",
                className: "text-center",
                orderable: false,
                searchable: false,
                render(h, type, row) {
                    var manage = `<li class="manage p-1" title="Manage"> <a href="#" class="manage-btn-option grey"><i class="bi bi-folder2" data-id="${h}"></i></a></li>`;
                    return `<ul class="action text-center">
                                ${manage}
                            </ul>`;
                },
            },
        ],
    });

    setPersonnelTableBtnAction();
};
const setTableBtnAction = () => {
    $("#deductions-table").on("click", ".manage-btn-option", (e) => {
        e.stopImmediatePropagation();
        var id = $(e.target).data("id");
        btnActionFunc(id, "MANAGE");
    });
    $("#deductions-table").on("click", ".view-btn-option", (e) => {
        e.stopImmediatePropagation();
        var id = $(e.target).data("id");
        btnActionFunc(id, "VIEW");
    });
    $("#deductions-table").on("click", ".delete-btn-option", (e) => {
        e.stopImmediatePropagation();
        var id = $(e.target).data("id");
        btnActionFunc(id, "DELETE");
    });
    $("#deductions-table").on("click", ".edit-btn-option", (e) => {
        e.stopImmediatePropagation();
        var id = $(e.target).data("id");
        btnActionFunc(id, "EDIT");
    });
};

const setPersonnelTableBtnAction = () => {
    $("#personnel-table").on("click", ".manage-btn-option", (e) => {
        e.stopImmediatePropagation();
        var id = $(e.target).data("id");
        btnPersonnelActionFunc(id, "MANAGE");
    });
};

const btnPersonnelActionFunc = (id, action) => {
    modalPersonnelAttrs.value.dataId = id;
    modalPersonnelAttrs.value.title = `${action} EMPLOYEE COMPENSATION/BENEFITS & DEDUCTIONS`;
    modalPersonnelAttrs.value.action = action;
    if (["MANAGE"].includes(action)) {
        $(
            `#${
                modalPersonnelAttrs.value?.modalId
                    ? modalPersonnelAttrs.value?.modalId
                    : "page-modal"
            }`
        ).modal("show");
    } 
};

const btnActionFunc = (id, action) => {
    modalAttrs.value.dataId = id;
    modalAttrs.value.title = `${action} DEDUCTION`;
    modalAttrs.value.action = action;
    if (["ADD", "EDIT", "VIEW"].includes(action)) {
        $(
            `#${
                modalAttrs.value?.modalId
                    ? modalAttrs.value?.modalId
                    : "page-modal"
            }`
        ).modal("show");
    } else if (action == 'MANAGE') {
        modalManageAttrs.value.dataId = id;
        modalManageAttrs.value.title = `${action} DEDUCTION`;
        modalManageAttrs.value.action = action;
        $(
            `#${
                modalManageAttrs.value?.modalId
                    ? modalManageAttrs.value?.modalId
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
                    router.delete(`${page?.url}/destroy/${id}`, {
                        onSuccess: () => {
                            swal(
                                "Success!",
                                "Deduction record was deleted successfully.",
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

onMounted(() => {
    reloadDatatable();
    reloadPersonnelDatatable();
})
</script>

<template>
    <Head title="Deductions" />
    <HeaderLayout :systemSetup="systemSetup"/>
    <SidebarLayout />
    <main id="main" class="main">
        <div class="pagetitle">
        <h1>Deductions</h1>
        <nav>
            <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <Link :href="route('payroll')">Payroll</Link>
            </li>
            <li class="breadcrumb-item active">Deductions</li>
            </ol>
        </nav>
        </div>
        <section class="section">
            <div class="card">
            <div class="card-body">
             <h5 class="card-title">Deduction Records</h5>

              <!-- Bordered Tabs Justified -->
              <ul class="nav nav-tabs nav-tabs-bordered d-flex" id="borderedTabJustified" role="tablist">
                <li class="nav-item flex-fill" role="presentation">
                  <button class="nav-link w-100 active" id="home-tab" data-bs-toggle="tab" data-bs-target="#deduction-personel-tab" type="button" role="tab" aria-controls="personnel" aria-selected="true">EMPLOYEE LIST</button>
                </li>
                <li class="nav-item flex-fill" role="presentation">
                  <button class="nav-link w-100" id="profile-tab" data-bs-toggle="tab" data-bs-target="#deduction-list-tab" type="button" role="tab" aria-controls="deduction" aria-selected="false" tabindex="-1">DEDUCTION LIST</button>
                </li>
                
              </ul>
              <div class="tab-content pt-2" id="borderedTabJustifiedContent">
                <div class="tab-pane fade show active" id="deduction-personel-tab" role="tabpanel" aria-labelledby="home-tab">
                    <div class="row">
                    <div class="col-12 p-2">
                        <div class="table-responsive">
                            <table
                                class="table table-bordered table-sm display"
                                id="personnel-table"
                                style="width: 100% !important"
                            ></table>
                        </div>
                    </div>
                </div>
                </div>
                <div class="tab-pane fade" id="deduction-list-tab" role="tabpanel" aria-labelledby="profile-tab">
                    <div class="row">
                    <div class="col-12 p-2 text-end">
                        <button type="button" class="btn btn-primary btn-sm" @click="btnActionFunc('', 'ADD')">Add Deduction</button>
                    </div>
                    <div class="col-12 p-2">
                        <div class="table-responsive">
                            <table
                                class="table table-bordered table-sm display"
                                id="deductions-table"
                                style="width: 100% !important"
                            ></table>
                        </div>
                    </div>
                </div>
                </div>
                
              </div><!-- End Bordered Tabs Justified -->

            </div>
          </div>
            
        </section>
        <PageModal 
            :modalAttrs="modalAttrs"
            @reloadPageData="reloadDatatableAjax()"
        />
        <ManageModal 
            :modalAttrs="modalManageAttrs"
            @reloadPageData="reloadDatatableAjax()"
        />
        <PersonnelModal 
            :modalAttrs="modalPersonnelAttrs"
            @reloadPersonnelPageData="reloadPersonnelDatatableAjax()"
        />
    </main>
    <FooterLayout :systemSetup="systemSetup"/>
</template>
