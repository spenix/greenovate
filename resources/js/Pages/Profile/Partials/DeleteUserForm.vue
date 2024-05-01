<script setup>
import ErrorMessage from '@/Components/ErrorMessage.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    $("#disablebackdrop").modal("show");
    confirmingUserDeletion.value = true;
    nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    form.reset();
};
</script>

<template>
   <section class="section delete-account">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                        <h5 class="card-title">Delete Account</h5>
                        <span> Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting
                            your account, please download any data or information that you wish to retain.</span>
                        <!-- Vertical Form -->
                            <div class="mt-4 text-left">
                                 <button type="button" class="btn btn-danger" @click="confirmUserDeletion">Delete Account</button>
                            </div>
                            <div class="modal fade" id="disablebackdrop" tabindex="-1" data-bs-backdrop="false" style="display: none;" aria-hidden="true">
                                <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-body">
                                        <p class="h5">Are you sure you want to delete your account?</p>
                                        <p class="mt-1 small">Once your account is deleted, all of its resources and data will be permanently deleted. Please
                    enter your password to confirm you would like to permanently delete your account.</p>

                                        <div class="col-12">
                                            <label for="password" class="form-label">password</label>
                                            <input 
                                                type="password" 
                                                name="password" 
                                                ref="passwordInput"
                                                id="password" 
                                                class="form-control" 
                                                :class="form?.errors?.password ? 'error-field' : ''" 
                                                v-model="form.password"
                                                @keyup.enter="deleteUser"
                                                placeholder="Password"
                                            >
                                            <ErrorMessage :message="form?.errors?.password"/>
                                        </div>
                                        <div class="mt-4 d-flex justify-content-end">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="button" class="btn btn-danger ms-3" :class="{ 'opacity-25': form.processing }"
                                             :disabled="form.processing"  @click="deleteUser">Delete Account</button>
                                        </div>
                                        
                                    </div>
                                    
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
</template>
