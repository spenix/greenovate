<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
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
    canRegister: {
        type: Boolean,
    },
});

const form = useForm({
    login: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
   <Head title="Log in" />
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
                    <h5 class="card-title text-center pb-0 fs-4">Login to Your Account</h5>
                    <p class="text-center small">Enter your username & password to login</p>
                  </div>

                  <form class="row g-3 needs-validation" :class="form?.hasErrors ? 'was-validated' : ''" @submit.prevent="submit" novalidate>

                    <div class="col-12">
                      <label for="login" class="form-label">Email/Username</label>
                      <div class="input-group has-validation">
                        <input type="text" name="login" class="form-control" ref="login" id="login" v-model="form.login" required>
                        <div class="invalid-feedback">{{ form?.errors?.login }}</div>
                      </div>
                    </div>

                    <div class="col-12">
                      <label for="password" class="form-label">Password</label>
                      <input type="password" name="password" class="form-control" id="password" v-model="form.password" required>
                      <div class="invalid-feedback">{{ form?.errors?.password ?? form?.errors?.email }}</div>
                    </div>

                    <div class="col-12 d-flex justify-content-between">
                      <div class="form-check">
                        <input 
                          class="form-check-input" 
                          type="checkbox"  
                          name="remember" 
                          value="true" 
                          id="remember"
                          :checked="form.remember"
                          v-model="form.remember"
                        >
                        <label class="form-check-label small" for="remember">Remember me</label>
                      </div>
                      <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="small"
                    >
                        Forgot your password?
                    </Link>
                    </div>
                    <div class="col-12">
                      <button class="btn btn-primary w-100" type="submit">Login</button>
                    </div>
                    <div class="col-12">
                      
                      <p class="small mb-0">Don't have account? 
                        <Link
                          v-if="canRegister"
                          :href="route('register')"
                        >
                        Create an account
                        </Link>
                      </p>
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
