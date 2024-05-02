<script setup>
import HeaderLayout from '@/Layouts/HeaderLayout.vue';
import SidebarLayout from '@/Layouts/SidebarLayout.vue';
import FooterLayout from '@/Layouts/FooterLayout.vue';
import PageModal from '@/Pages/Attendance/Modal/IndexModal.vue';
import UploadModal from '@/Pages/Attendance/Modal/UploadFileModal.vue';
import DTRModal from '@/Pages/Attendance/Modal/DTRModal.vue';
import notify from "@/common/notification.js";
import { Link, Head, usePage, router } from '@inertiajs/vue3';
import { onMounted, ref} from "vue";

const page = usePage()
const table = ref({});
const modalAttrs = ref({
    modalId: "attendance-modal",
    title: "SHIFT",
    action: "",
});
const modalDTRAttrs = ref({
    modalId: "dtr-modal",
    title: "Daily Time Record",
    action: "",
});
const modalUploadAttrs = ref({
    modalId: "upload-file-modal",
    title: "UPLOAD FILE",
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
const reloadDatatable = () => {
    table.value = $("#attendance-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: `${page.url}/show_table_data`,
        columns: [
            { data: "employee_no", title: "Employee ID" },
            { data: "employee_name", title: "Employee Name" },
            { data: "shift", title: "shift" },
            { 
                data: "Upload File", 
                title: "Upload File",
                orderable: false,
                searchable: false,
                className: "text-center",
                render(h, type, row) {
                    return `<button type="button" class="btn btn-primary btn-sm upload-btn-option" data-id="${row?.id}">Upload File</button> <br>Uploaded Files: ${row?.attachments?.length}`;
                }
            },
            {
                data: "id",
                title: "Action",
                className: "text-center",
                orderable: false,
                searchable: false,
                render(h, type, row) {
                    var dtr = `<li class="view p-1" title="DTR"> <a href="#" class="dtr-btn-option grey"><i class="ri-file-list-line" data-id="${h}"></i></a></li>`;
                    var view = `<li class="view p-1" title="View"> <a href="#" class="view-btn-option blue"><i class="bi bi-eye" data-id="${h}"></i></a></li>`;
                    var del = `<li class="delete p-1" title="Delete"><a href="#" class="delete-btn-option red"><i class="bi bi-trash" data-id="${h}"></i></a></li>`;
                    var edit = `<li class="edit p-1" title="Edit"> <a href="#" class="edit-btn-option green"><i class="bi bi-pencil" data-id="${h}"></i></a></li>`;

                    return `<ul class="action text-center">
                                ${dtr}
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
    $("#attendance-table").on("click", ".upload-btn-option", (e) => {
        e.stopImmediatePropagation();
        var id = $(e.target).data("id");
        btnActionFunc(id, "UPLOAD");
    });
    $("#attendance-table").on("click", ".dtr-btn-option", (e) => {
        e.stopImmediatePropagation();
        var id = $(e.target).data("id");
        btnActionFunc(id, "DTR");
    });
    $("#attendance-table").on("click", ".view-btn-option", (e) => {
        e.stopImmediatePropagation();
        var id = $(e.target).data("id");
        btnActionFunc(id, "VIEW");
    });
    $("#attendance-table").on("click", ".delete-btn-option", (e) => {
        e.stopImmediatePropagation();
        var id = $(e.target).data("id");
        btnActionFunc(id, "DELETE");
    });
    $("#attendance-table").on("click", ".edit-btn-option", (e) => {
        e.stopImmediatePropagation();
        var id = $(e.target).data("id");
        btnActionFunc(id, "EDIT");
    });
};

const btnActionFunc = (id, action) => {
    modalAttrs.value.dataId = id;
    modalAttrs.value.title = `${action} SHIFT`;
    modalAttrs.value.action = action;
    if (["ADD", "EDIT", "VIEW"].includes(action)) {
        $(
            `#${
                modalAttrs.value?.modalId
                    ? modalAttrs.value?.modalId
                    : "page-modal"
            }`
        ).modal("show");
    } 
    else if (action == "DTR") {
        modalDTRAttrs.value.dataId = id;
        modalDTRAttrs.value.action = action;
        $(
            `#${
                modalDTRAttrs.value?.modalId
                    ? modalDTRAttrs.value?.modalId
                    : "page-modal"
            }`
        ).modal("show");
    }
    else if (action == "UPLOAD") {
        modalUploadAttrs.value.dataId = id;
        modalUploadAttrs.value.title = `${action} FILE`;
        modalUploadAttrs.value.action = action;
        $(
            `#${
                modalUploadAttrs.value?.modalId
                    ? modalUploadAttrs.value?.modalId
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
                                "Shift was deleted successfully.",
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
};

onMounted(() => {
    reloadDatatable();
})
</script>

<template>
    <Head title="Attendance" />
    <HeaderLayout :systemSetup="systemSetup"/>
    <SidebarLayout />
    <main id="main" class="main">
        <div class="pagetitle">
        <h1>Attendance</h1>
        <nav>
            <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <Link :href="route('dashboard')">Home</Link>
            </li>
            <li class="breadcrumb-item active">Attendance</li>
            </ol>
        </nav>
        </div>
        <section class="section">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title">Attendance List</h5>
                <div class="row">
                    <div class="col-12 p-2 text-end">
                        <button type="button" class="btn btn-primary btn-sm" @click="btnActionFunc('', 'ADD')">Add Shift</button>
                    </div>
                    <div class="col-12 p-2">
                        <div class="table-responsive">
                            <table
                                class="table table-bordered table-sm display"
                                id="attendance-table"
                                style="width: 100% !important"
                            ></table>
                        </div>
                    </div>
                </div>
              </div>
            </div>
        </section>
        <PageModal 
            :modalAttrs="modalAttrs"
            @reloadPageData="reloadDatatableAjax()"
        />
        <UploadModal 
            :modalUploadAttrs="modalUploadAttrs"
            @reloadPageData="reloadDatatableAjax()"
        />
        <DTRModal 
            :modalDTRAttrs="modalDTRAttrs"
            @reloadPageData="reloadDatatableAjax()"
        />
    </main>
    <FooterLayout :systemSetup="systemSetup"/>
</template>
