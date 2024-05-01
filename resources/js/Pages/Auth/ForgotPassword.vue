<script setup>
import ErrorMessage from '@/Components/ErrorMessage.vue';
import { Head,Link, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <Head title="Forgot Password" />
    <main>
        <div class="container">
            <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-6 col-md-8 d-flex flex-column align-items-center justify-content-center">
                            <div class="card mb-3 p-2">
                                <div class="card-body">
                                        <p class="mb-4 text-secondary">Forgot your password? No problem. Just let us know your email address and we will email you a password reset
            link that will allow you to choose a new one.</p>
                                        <div v-if="status" class="mb-4 text-success">
                                            {{ status }}
                                        </div>

                                        <form class="row g-3" @submit.prevent="submit">
                                            <div class="col-12">
                                                <label for="email" class="form-label">Email</label>
                                                <input 
                                                    type="email" 
                                                    name="email" 
                                                    id="email" 
                                                    class="form-control" 
                                                    :class="form?.errors?.email ? 'error-field' : ''" 
                                                    v-model="form.email"
                                                    autocomplete="email"
                                                >
                                                <ErrorMessage :message="form?.errors?.email"/>
                                            </div>
                                            <div class="d-flex justify-content-end">
                                                <button 
                                                    class="btn btn-secondary w-100" 
                                                    :class="{ 'opacity-25': form.processing }" 
                                                    :disabled="form.processing" 
                                                    type="submit"
                                                >
                                                Email Password Reset Link
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
    </main>

</template>
