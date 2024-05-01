<script setup>
import ErrorMessage from '@/Components/ErrorMessage.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <section class="section update-password">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                        <h5 class="card-title">Update Password</h5>
                        <span>Ensure your account is using a long, random password to stay secure.</span>
                        <div v-if="form.recentlySuccessful" class="alert alert-success alert-dismissible fade show" role="alert">
                            Saved .
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <!-- Vertical Form -->
                        <form  @submit.prevent="updatePassword"  class="row mt-1 g-3">
                            <div class="col-12">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input 
                                    type="password" 
                                    class="form-control" 
                                    :class="form?.errors?.current_password ? 'error-field' : ''"
                                    name="current_password" 
                                    id="current_password"
                                    ref="currentPasswordInput"
                                    v-model="form.current_password"
                                    autocomplete="current-password"
                                >
                                <ErrorMessage :message="form?.errors?.current_password"/>
                            </div>
                            <div class="col-12">
                                <label for="password" class="form-label">New Password</label>
                                <input 
                                    type="password" 
                                    class="form-control" 
                                    :class="form?.errors?.password ? 'error-field' : ''"
                                    name="password" 
                                    id="password"
                                    ref="passwordInput"
                                    v-model="form.password"
                                    autocomplete="new-password"
                                >
                                <ErrorMessage :message="form?.errors?.password"/>
                            </div>
                            <div class="col-12">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input 
                                    type="password" 
                                    class="form-control" 
                                    :class="form?.errors?.password_confirmation ? 'error-field' : ''"
                                    name="password_confirmation" 
                                    id="password_confirmation"
                                    v-model="form.password_confirmation"
                                    autocomplete="new-password"
                                >
                                <ErrorMessage :message="form?.errors?.password_confirmation"/>
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
