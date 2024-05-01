<script setup>
import HeaderLayout from '@/Layouts/HeaderLayout.vue';
import SidebarLayout from '@/Layouts/SidebarLayout.vue';
import FooterLayout from '@/Layouts/FooterLayout.vue';
import PageModal from '@/Pages/Employee/Modal/IndexModal.vue';
import notify from "@/common/notification.js";
import { Link, Head, usePage, router } from '@inertiajs/vue3';
import { onMounted, ref} from "vue";

const page = usePage()
const table = ref({});
const modalAttrs = ref({
    modalId: "employee-modal",
    title: "EMPLOYEE",
    action: "",
});
const reloadDatatableAjax = () => {
    modalAttrs.value.action = "";
    table.value.ajax.reload();
};
const reloadDatatable = () => {
    table.value = $("#employee-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: `${page.url}/show_table_data`,
        columns: [
            { data: "employee_name", title: "Name" },
            { data: "employee_no", title: "ID Number" },
            { data: "contact_no", title: "Contact Number" },
            { data: "employee_type", title: "Type" },
            { 
                data: "terminate", 
                title: "Status",
                render(h) {
                    return h == 'Y' ? 'Terminated': 'Active';
                }, 
            },
            {
                data: "id",
                title: "Action",
                orderable: false,
                searchable: false,
                render(h, type, row) {
                    var terminate = `<li class="terminate p-1" title="Remove Terminate"> <a href="#" class="terminate-btn-option grey"><i class="bi bi-shield-plus" data-id="${h}" data-status="${row.terminate}"></i></a></li>`;
                    if (row?.terminate == 'N') {
                        var terminate = `<li class="terminate p-1" title="Terminate"> <a href="#" class="terminate-btn-option grey"><i class="bi bi-shield-slash" data-id="${h}" data-status="${row.terminate}"></i></a></li>`;
                    }
                    var view = `<li class="view p-1" title="View"> <a href="#" class="view-btn-option blue"><i class="bi bi-eye" data-id="${h}"></i></a></li>`;
                    var del = `<li class="delete p-1" title="Delete"><a href="#" class="delete-btn-option red"><i class="bi bi-trash" data-id="${h}"></i></a></li>`;
                    var edit = `<li class="edit p-1" title="Edit"> <a href="#" class="edit-btn-option green"><i class="bi bi-pencil" data-id="${h}"></i></a></li>`;

                    return `<ul class="action text-center">
                                ${terminate}
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
    $("#employee-table").on("click", ".terminate-btn-option", (e) => {
        e.stopImmediatePropagation();
        var id = $(e.target).data("id");
        var status = $(e.target).data("status");
        btnActionTerminateFunc(id, status, "TERMINATE");
    });
    $("#employee-table").on("click", ".view-btn-option", (e) => {
        e.stopImmediatePropagation();
        var id = $(e.target).data("id");
        btnActionFunc(id, "VIEW");
    });
    $("#employee-table").on("click", ".delete-btn-option", (e) => {
        e.stopImmediatePropagation();
        var id = $(e.target).data("id");
        btnActionFunc(id, "DELETE");
    });
    $("#employee-table").on("click", ".edit-btn-option", (e) => {
        e.stopImmediatePropagation();
        var id = $(e.target).data("id");
        btnActionFunc(id, "EDIT");
    });
};
const btnActionTerminateFunc = (id, status, action) => {
    let input = (status == 'N' ? prompt('Type your reason here') : "");
    let confirmation = confirm(`Are you sure you want to ${status == 'N' ? 'terminate employee' : 'remove termination of employee'}?`);
    if (confirmation) {
        router.visit(`${page?.url}/terminate/${id}/${status == 'N' ? 'Y' : 'N'}`, {
                        method: 'post',
                        data: {
                            reason: input
                        },
                        onSuccess: () => {
                            swal(
                                "Success!",
                                `Employee was ${status == 'N' ? 'terminated' : 'removed termination'} successfully.`,
                                "success"
                            ),
                            reloadDatatableAjax();
                        },
                        onError: () => {
                            notify(
                                        "Error",
                                        "Oops, something went wrong.",
                                        "danger"
                                    );
                        },
                    });
    }
    // swal(`Are you sure you want to ${status == 'N' ? 'terminate employee' : 'remove termination of employee'}?`, {
    //         buttons: {
    //             cancel: "Cancel",
    //             catch: {
    //                 text: "Yes",
    //                 value: "catch",
    //             },
    //             // defeat: true,
    //         },
    //     }).then((value) => {
    //         switch (value) {
    //             case "catch":
                    // router.visit(`${page?.url}/terminate/${id}/${status == 'N' ? 'Y' : 'N'}`, {
                    //     method: 'post',
                    //     onSuccess: () => {
                    //         swal(
                    //             "Success!",
                    //             `Employee was ${status == 'N' ? 'terminated' : 'removed termination'} successfully.`,
                    //             "success"
                    //         ),
                    //         reloadDatatableAjax();
                    //     },
                    //     onError: () => {
                    //         notify(
                    //                     "Error",
                    //                     "Oops, something went wrong.",
                    //                     "danger"
                    //                 );
                    //     },
                    // });
    //                 break;
    //             default:
    //                 swal.close();
    //         }
    //     });
}

const btnActionFunc = (id, action) => {
    modalAttrs.value.dataId = id;
    modalAttrs.value.title = `${action} EMPLOYEE`;
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
                                "Employee was deleted successfully.",
                                "success"
                            ),
                            reloadDatatableAjax();
                        },
                        onError: () => {
                            notify(
                                        "Error",
                                        "Oops, something went wrong.",
                                        "danger"
                                    );
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
    <Head title="Employees" />
    <HeaderLayout :systemSetup="systemSetup"/>
    <SidebarLayout />
    <main id="main" class="main">
        <div class="pagetitle">
        <h1>Employees</h1>
        <nav>
            <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <Link :href="route('dashboard')">Home</Link>
            </li>
            <li class="breadcrumb-item active">Employees</li>
            </ol>
        </nav>
        </div>
        <section class="section">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title">Employee List</h5>
                <div class="row">
                    <div class="col-12 p-2 text-end">
                        <button type="button" class="btn btn-primary btn-sm" @click="btnActionFunc('', 'ADD')">Add Employee</button>
                    </div>
                    <div class="col-12 p-2">
                        <div class="table-responsive">
                            <table
                                class="table table-bordered table-sm display"
                                id="employee-table"
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
