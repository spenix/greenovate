<script setup>
import ErrorMessage from '@/Components/ErrorMessage.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Confirm Password" />
    <main>
        <div class="container">
            <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-6 col-md-8 d-flex flex-column align-items-center justify-content-center">
                            <div class="card mb-3 p-2">
                                <div class="card-body">
                                        <p class="mb-4 text-secondary"> This is a secure area of the application. Please confirm your password before continuing.</p>

                                        <form class="row g-3" @submit.prevent="submit">
                                            <div class="col-12">
                                                <label for="password" class="form-label">Password</label>
                                                <input 
                                                    type="password" 
                                                    name="password" 
                                                    id="password" 
                                                    class="form-control" 
                                                    :class="form?.errors?.password ? 'error-field' : ''" 
                                                    v-model="form.password"
                                                    autocomplete="password"
                                                >
                                                <ErrorMessage :message="form?.errors?.password"/>
                                            </div>
                                            <div class="d-flex justify-content-end">
                                                <button 
                                                    class="btn btn-primary" 
                                                    :class="{ 'opacity-25': form.processing }" 
                                                    :disabled="form.processing" 
                                                    type="submit"
                                                >
                                                    Confirm
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
