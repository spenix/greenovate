<script setup>
import ErrorMessage from '@/Components/ErrorMessage.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
defineProps({
    appName: {
        type: String,
    },
    appLogo: {
        type: String,
    },
    appLogo1: {
        type: String,
    },
    app_icon: {
        type: String,
    },
});
const form = useForm({
    name: '',
    email: '',
    username: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Register" />
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

                    <div class="pt-4 pb-2">
                        <h5 class="card-title text-center pb-0 fs-4">Create an Account</h5>
                        <p class="text-center small">Enter your personal details to create account</p>
                    </div>

                    <form class="row g-3" @submit.prevent="submit" novalidate>
                        <div class="col-12">
                            <label for="name" class="form-label">Name</label>
                            <input 
                                type="text" 
                                name="name" 
                                id="name" 
                                class="form-control" 
                                :class="form?.errors?.name ? 'error-field' : ''" 
                                v-model="form.name"
                                required
                                autofocus
                                autocomplete="name"
                            >
                            <ErrorMessage :message="form?.errors?.name"/>
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
                            <label for="username" class="form-label">Username</label>
                            <input 
                                type="text" 
                                name="username" 
                                id="username" 
                                class="form-control" 
                                :class="form?.errors?.username ? 'error-field' : ''" 
                                v-model="form.username"
                                required
                                autocomplete="username"
                            >
                            <ErrorMessage :message="form?.errors?.username"/>
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
                            Create Account
                        </button>
                        </div>
                        <div class="col-12">
                        <p class="small mb-0">Already have an account?    
                        <Link
                          :href="route('login')"
                        >
                        Log in
                        </Link></p>
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
