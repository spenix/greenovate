<script setup>
import ErrorMessage from '@/Components/ErrorMessage.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    username: user.username,
    email: user.email,
});
</script>

<template>
   <section class="section profile-information">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                        <h5 class="card-title pb-1">Profile Information</h5>
                        <span>Update your account's profile information and email address.</span>
                        <div v-if="form.recentlySuccessful" class="alert alert-success alert-dismissible fade show" role="alert">
                            Saved .
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <!-- Vertical Form -->
                        <form class="row mt-1 g-3"  @submit.prevent="form.patch(route('profile.update'))"  novalidate>
                            <div class="col-12">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" :class="form?.errors?.name ? 'error-field' : ''" name="name" id="name" v-model="form.name" autocomplete="name">
                                <ErrorMessage :message="form?.errors?.name"/>
                            </div>
                            <div class="col-12">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" :class="form?.errors?.username ? 'error-field' : ''" name="username" id="username" v-model="form.username" autocomplete="username">
                                <ErrorMessage :message="form?.errors?.username"/>
                            </div>
                            <div class="col-12">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" :class="form?.errors?.email ? 'error-field' : ''" name="email" id="email" v-model="form.email" autocomplete="email">
                                <ErrorMessage :message="form?.errors?.email"/>
                            </div>
                            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                                <p class="text-sm mt-2 text-gray-800">
                                    Your email address is unverified.
                                    <Link
                                        :href="route('verification.send')"
                                        method="post"
                                        as="button"
                                        class="underline"
                                    >
                                        Click here to re-send the verification email.
                                    </Link>
                                </p>

                                <span
                                    
                                    class="mt-1 badge border-success border-1 text-success"
                                >
                                    A new verification link has been sent to your email address.
                                </span>
                            </div>
                            <div class="text-left">
                                <button type="submit" class="btn btn-primary" :disabled="form.processing">Save</button>
                            </div>
                        </form><!-- Vertical Form -->
                        </div>
                    </div>
                </div>
            </div>
        </section>
</template>
