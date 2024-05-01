<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <Head title="Email Verification" />
    <main>
        <div class="container">
            <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-6 col-md-8 d-flex flex-column align-items-center justify-content-center">
                            <div class="card mb-3 p-2">
                                <div class="card-body">
                                        <p class="mb-4 text-secondary">
                                            Thanks for signing up! Before getting started, could you verify your email address by clicking on the link
            we just emailed to you? If you didn't receive the email, we will gladly send you another.
                                        </p>
                                        <div v-if="verificationLinkSent" class="mb-4 text-success">
                                            A new verification link has been sent to the email address you provided during registration.
                                        </div>

                                        <form @submit.prevent="submit">
                                            <div class="d-flex justify-content-between">
                                                <button 
                                                    class="btn btn-secondary" 
                                                    :class="{ 'opacity-25': form.processing }" 
                                                    :disabled="form.processing" 
                                                    type="submit"
                                                >
                                                    Resend Verification Email
                                                </button>
                                                
                                                <Link
                                                    :href="route('logout')"
                                                    method="post"
                                                    class="text-secondary"
                                                >Log Out</Link>
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
