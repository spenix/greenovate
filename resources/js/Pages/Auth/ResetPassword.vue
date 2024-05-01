<script setup>
import ErrorMessage from '@/Components/ErrorMessage.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Reset Password" />
    <main>
        <div class="container">

        <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
            <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

                <div class="d-flex justify-content-center py-4">
                    <a href="#" class="logo d-flex align-items-center w-auto">
                    <img :src="appLogo1" :alt="appName">
                    <span class="d-none d-lg-block">{{ appName }}</span>
                    </a>
                </div><!-- End Logo -->

                <div class="card mb-3">
                    
                    <div class="card-body">
                    <form class="row g-3" @submit.prevent="submit" novalidate>
                        
                        <div class="pt-4 pb-2">
                            <h5 class="card-title text-center pb-0 fs-4">Reset Password</h5>
                        </div>
                        <div class="col-12">
                            <label for="email" class="form-label">Email</label>
                            <input 
                                type="email" 
                                name="email" 
                                id="email" 
                                class="form-control" 
                                :class="form?.errors?.email ? 'error-field' : ''" 
                                v-model="form.email"
                                required
                                autocomplete="email"
                            >
                            <ErrorMessage :message="form?.errors?.email"/>
                        </div>

                        
                        <div class="col-12">
                            <label for="password" class="form-label">password</label>
                            <input 
                                type="password" 
                                name="password" 
                                id="password" 
                                class="form-control" 
                                :class="form?.errors?.password ? 'error-field' : ''" 
                                v-model="form.password"
                                required
                                autocomplete="new-password"
                            >
                            <ErrorMessage :message="form?.errors?.password"/>
                        </div>

                        <div class="col-12">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input 
                                type="password" 
                                name="password_confirmation" 
                                id="password_confirmation" 
                                class="form-control" 
                                :class="form?.errors?.password_confirmation ? 'error-field' : ''" 
                                v-model="form.password_confirmation"
                                required
                                autocomplete="new-password"
                            >
                            <ErrorMessage :message="form?.errors?.password_confirmation"/>
                        </div>
                    
                        <div class="col-12">
                        <button 
                            class="btn btn-primary w-100" 
                            :class="{ 'opacity-25': form.processing }" 
                            :disabled="form.processing" 
                            type="submit"
                        >
                            Reset Password
                        </button>
                        </div>
                    </form>

                    </div>
                </div>
                </div>
            </div>
            </div>

        </section>

        </div>
    </main><!-- End #main -->
</template>
