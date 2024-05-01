<script setup>
import ErrorMessage from '@/Components/ErrorMessage.vue';
import HeaderLayout from '@/Layouts/HeaderLayout.vue';
import SidebarLayout from '@/Layouts/SidebarLayout.vue';
import FooterLayout from '@/Layouts/FooterLayout.vue';
import { Link, Head, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import { commonFuntions } from "@/common/common-function.js";
const { isNumber } = commonFuntions();
const pageData = usePage().props.data;
const leaveCredit = ref(null);

defineProps({
    systemSetup: {
        type: Object,
    }
});

const form = useForm({
    id: pageData?.id,
    leave_credit: pageData?.leave_credit,
});

const submitData = () => {
    if (pageData) {
        form.put(route('leave-credits.update', [form.id]), {
            preserveScroll: true,
            onError: () => {
                if (form.errors.leave_credit) {
                    form.reset('leave_credit');
                    leaveCredit.value.focus();
                }
            },
        });
    } else {
        form.post(route('leave-credits.store'));
    }
    
};
</script>

<template>
    <Head title="Leave Credit" />
    <HeaderLayout :systemSetup="systemSetup"/>
    <SidebarLayout />
    <main id="main" class="main">
        <div class="pagetitle">
        <h1>Leave Credit</h1>
        <nav>
            <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <Link :href="route('employee-leaves')">Leave</Link>
            </li>
            <li class="breadcrumb-item active">Leave Credit</li>
            </ol>
        </nav>
        </div>
        <section class="section">
            <div class="row">
                <div class="col-6 p-2">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Leave Credit</h5>
                        
                            <div class="row">
                                <div class="col-12 p-2">
                                    <div v-if="form.recentlySuccessful" class="alert alert-success alert-dismissible fade show" role="alert">
                                        Leave credit was successfully saved.
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                    <form class="row g-3" @submit.prevent="submitData" novalidate>
                                        <div class="col-12">
                                            <label for="leave_credit" class="form-label">Total Days</label>
                                            <input 
                                                type="text" 
                                                class="form-control" 
                                                name="leave_credit" 
                                                id="leave_credit"
                                                ref="leaveCredit"
                                                :class="form.errors.leave_credit ? 'error-field' : ''"
                                                v-model="form.leave_credit"
                                                @keypress="isNumber($event)"
                                            >
                                            <ErrorMessage :message="form.errors.leave_credit"/>
                                        </div>
                                        <div class="text-end">
                                            <button type="submit" class="btn btn-primary" :disabled="form.processing">Save</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        </div>
                </div>
                <div class="col-6 p-2"></div>
            </div>
            
        </section>
    </main>
    <FooterLayout :systemSetup="systemSetup"/>
</template>
