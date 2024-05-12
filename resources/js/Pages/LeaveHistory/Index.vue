<script setup>
import HeaderLayout from '@/Layouts/HeaderLayout.vue';
import SidebarLayout from '@/Layouts/SidebarLayout.vue';
import FooterLayout from '@/Layouts/FooterLayout.vue';
import PageModal from '@/Pages/LeaveHistory/Modal/IndexModal.vue';
import { Link, Head, usePage } from '@inertiajs/vue3';
import { onMounted, ref} from "vue";

const page = usePage()
const table = ref({});
const modalAttrs = ref({
    modalId: "leave-history-modal",
    title: "EMPLOYEE LEAVE HISTORY",
    action: "",
});
const props = defineProps({
    systemSetup: {
        type: Object,
    },
});


const reloadDatatable = () => {
    table.value?.ajax?.reload();
    table.value = $("#leave-history-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: `${page.url}/show_table_data`,
        columns: [
            { data: "employee_name", title: "Employee Name" },
            { data: "department", title: "Department" },
            { data: "designation", title: "Position" },
            { data: "running_balance", title: "Remaining Balance" },
            {
                data: "id",
                title: "Action",
                orderable: false,
                searchable: false,
                render(h, type, row) {
                    var manage = `<li class="manage p-1" title="View History"> <a href="#" class="manage-btn-option grey"><i class="bi bi-card-list" data-id="${h}"></i></a></li>`;
                    return `<ul class="action text-center">
                                ${manage}
                            </ul>`;
                },
            },
        ],
    });
    setTableBtnAction();
};
const setTableBtnAction = () => {
    $("#leave-history-table").on("click", ".manage-btn-option", (e) => {
        e.stopImmediatePropagation();
        var id = $(e.target).data("id");
        btnActionFunc(id, "MANAGE");
    });
};

const btnActionFunc = (id, action) => {
    modalAttrs.value.dataId = id;
    modalAttrs.value.action = action;
    if (["MANAGE"].includes(action)) {
        $(
            `#${
                modalAttrs.value?.modalId
                    ? modalAttrs.value?.modalId
                    : "page-modal"
            }`
        ).modal("show");
    } 
};

onMounted(() => {
    reloadDatatable();
})
</script>

<template>
    <Head title="Leave History" />
    <HeaderLayout :systemSetup="systemSetup"/>
    <SidebarLayout />
    <main id="main" class="main">
        <div class="pagetitle">
        <h1>Leave History</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                <Link :href="route('employee-leaves')">Leave</Link>
            </li>
            <li class="breadcrumb-item active">Leave History</li>
            </ol>
        </nav>
        </div>
        <section class="section">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title">Employee List</h5>
                <div class="row">
                    <div class="col-12 p-2 text-end">
                        <button type="button" class="btn btn-primary btn-sm" @click="btnActionFunc('', 'ADD')" hidden>Add Employee Leave</button>
                    </div>
                    <div class="col-12 p-2">
                        <div class="table-responsive">
                            <table
                                class="table table-bordered table-sm display"
                                id="leave-history-table"
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
        />
    </main>
    <FooterLayout :systemSetup="systemSetup"/>
</template>
