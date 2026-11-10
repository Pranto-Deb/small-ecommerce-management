<template>
  <Head title="Sign in"/>
  <div class="login-wrap">
    <div class="login-card"><h1>Small Commerce</h1>
      <p class="muted">Sign in to manage shops and products.</p>
      <form @submit.prevent="form.post('/login')"><label>Email<input v-model="form.email" type="email" required
                                                                     autofocus></label><label>Password<input
          v-model="form.password" type="password" required></label>
        <p v-if="form.errors.email" class="field-error">{{ form.errors.email }}</p>
        <button class="btn primary" :disabled="form.processing">Sign in</button>
      </form>
      <p>
        <Link href="/">← Back to shops</Link>
      </p>
    </div>
  </div>
</template>

<script setup>
import {useForm, Head, Link} from '@inertiajs/vue3';

const form = useForm({email: '', password: ''});
</script>
