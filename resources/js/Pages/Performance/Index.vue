<script setup>
import HeaderLayout from '@/Layouts/HeaderLayout.vue';
import SidebarLayout from '@/Layouts/SidebarLayout.vue';
import FooterLayout from '@/Layouts/FooterLayout.vue';
import PageModal from '@/Pages/Performance/Modal/IndexModal.vue';
import notify from "@/common/notification.js";
import moment from "moment";
import { Link, Head, usePage, router } from '@inertiajs/vue3';
import { onMounted, ref} from "vue";

const page = usePage()
const table = ref({});
const modalAttrs = ref({
    modalId: "performance-modal",
    title: "PERFORMANCE",
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
    table.value = $("#performance-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: `${page.url}/show_table_data`,
        columns: [
            { data: "employee_no", title: "ID No." },
            { data: "employee_name", title: "Employee" },
            { data: "violation_type", title: "Violation" },
            { 
                data: "date_committed", 
                title: "Date Committed",
                render(h) {
                    return moment(h).format('L');
                },  
            },
            { data: "attemps", title: "Attempt", className: "text-center" },
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
    $("#performance-table").on("click", ".view-btn-option", (e) => {
        e.stopImmediatePropagation();
        var id = $(e.target).data("id");
        btnActionFunc(id, "VIEW");
    });
    $("#performance-table").on("click", ".delete-btn-option", (e) => {
        e.stopImmediatePropagation();
        var id = $(e.target).data("id");
        btnActionFunc(id, "DELETE");
    });
    $("#performance-table").on("click", ".edit-btn-option", (e) => {
        e.stopImmediatePropagation();
        var id = $(e.target).data("id");
        btnActionFunc(id, "EDIT");
    });
};

const btnActionFunc = (id, action) => {
    modalAttrs.value.dataId = id;
    modalAttrs.value.title = `${action} PERFORMANCE`;
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
                                "Performance was deleted successfully.",
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
    <Head title="Performance" />
    <HeaderLayout :systemSetup="systemSetup"/>
    <SidebarLayout />
    <main id="main" class="main">
        <div class="pagetitle">
        <h1>Performance</h1>
        <nav>
            <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <Link :href="route('performance')">Performance Monitoring</Link>
            </li>
            <li class="breadcrumb-item active">Performance</li>
            </ol>
        </nav>
        </div>
        <section class="section">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title">Performance List</h5>
                <div class="row">
                    <div class="col-12 p-2 text-end">
                        <button type="button" class="btn btn-primary btn-sm" @click="btnActionFunc('', 'ADD')">Add Performance</button>
                    </div>
                    <div class="col-12 p-2">
                        <div class="table-responsive">
                            <table
                                class="table table-bordered table-sm display"
                                id="performance-table"
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
