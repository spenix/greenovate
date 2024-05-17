<script setup>
import HeaderLayout from '@/Layouts/HeaderLayout.vue';
import SidebarLayout from '@/Layouts/SidebarLayout.vue';
import FooterLayout from '@/Layouts/FooterLayout.vue';
import PageModal from '@/Pages/ShiftCode/Modal/IndexModal.vue';
import notify from "@/common/notification.js";
import { Link, Head, usePage, router } from '@inertiajs/vue3';
import { onMounted, ref} from "vue";

const page = usePage()
const table = ref({});
const modalAttrs = ref({
    modalId: "shift-code-modal",
    title: "SHIFT CODE",
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
    table.value = $("#shift-code-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: `${page.url}/show_table_data`,
        columns: [
            { data: "shift", title: "Shift Code" },
            { data: "status", title: "Status" },
            {
                data: "id",
                title: "Action",
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
    $("#shift-code-table").on("click", ".view-btn-option", (e) => {
        e.stopImmediatePropagation();
        var id = $(e.target).data("id");
        btnActionFunc(id, "VIEW");
    });
    $("#shift-code-table").on("click", ".delete-btn-option", (e) => {
        e.stopImmediatePropagation();
        var id = $(e.target).data("id");
        btnActionFunc(id, "DELETE");
    });
    $("#shift-code-table").on("click", ".edit-btn-option", (e) => {
        e.stopImmediatePropagation();
        var id = $(e.target).data("id");
        btnActionFunc(id, "EDIT");
    });
};

const btnActionFunc = (id, action) => {
    modalAttrs.value.dataId = id;
    modalAttrs.value.title = `${action} SHIFT CODE`;
    modalAttrs.value.action = action;
    if (["ADD", "EDIT", "VIEW"].includes(action)) {
        $(
            `#${
                modalAttrs.value?.modalId
                    ? modalAttrs.value?.modalId
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
                                "Shift code was deleted successfully.",
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
    <Head title="Shift Code" />
    <HeaderLayout :systemSetup="systemSetup"/>
    <SidebarLayout />
    <main id="main" class="main">
        <div class="pagetitle">
        <h1>Shift Code</h1>
        <nav>
            <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <Link :href="route('employee-attendance')">Attendance</Link>
            </li>
            <li class="breadcrumb-item active">Shift Codes</li>
            </ol>
        </nav>
        </div>
        <section class="section">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title">Shift Code List</h5>
                <div class="row">
                    <div class="col-12 p-2 text-end">
                        <button type="button" class="btn btn-primary btn-sm" @click="btnActionFunc('', 'ADD')">Add Shift Code</button>
                    </div>
                    <div class="col-12 p-2">
                        <div class="table-responsive">
                            <table
                                class="table table-bordered table-sm display"
                                id="shift-code-table"
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
    </main>
    <FooterLayout :systemSetup="systemSetup"/>
</template>
