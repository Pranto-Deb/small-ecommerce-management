<template>
  <Head title="Shop Owners"/>
  <Shell title="Shop Owners">
    <div class="heading">
      <div><h1>Shop Owners</h1>
        <p class="muted">Create and manage login accounts for shop owners.</p></div>
      <button class="btn primary" @click="add">+ New owner</button>
    </div>
    <div class="panel table-scroll">
      <table>
        <thead>
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Assigned shop</th>
          <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="owner in owners" :key="owner.id">
          <td>{{ owner.name }}</td>
          <td>{{ owner.email }}</td>
          <td>{{ owner.shop?.name || 'Not assigned' }}</td>
          <td>
            <button class="btn small" @click="edit(owner)">Edit</button>
            <button class="btn small danger" @click="remove(owner)">Delete</button>
          </td>
        </tr>
        <tr v-if="!owners.length">
          <td colspan="4" class="muted">No shop owners yet.</td>
        </tr>
        </tbody>
      </table>
    </div>
    <Modal v-if="open" :title="editing?'Edit shop owner':'New shop owner'" @close="open=false">
      <form @submit.prevent="save">
        <Errors :errors="form.errors"/>
        <label>Name<input v-model="form.name" required maxlength="255"></label><label>Email<input v-model="form.email"
                                                                                                  type="email" required></label><label>Password
        <small>{{ editing ? 'Leave blank to keep password' : 'Minimum 8 characters' }}</small><input
            v-model="form.password" type="password" :required="!editing" minlength="8"></label>
        <div class="actions end">
          <button type="button" class="btn" @click="open=false">Cancel</button>
          <button class="btn primary" :disabled="form.processing">Save owner</button>
        </div>
      </form>
    </Modal>
  </Shell>
</template>

<script setup>
import {ref} from 'vue';
import {useForm, Head, router} from '@inertiajs/vue3';
import Shell from '../../Components/Shell.vue';
import Modal from '../../Components/Modal.vue';
import Errors from '../../Components/Errors.vue';

defineProps({owners: Array});
const open = ref(false), editing = ref(null);
const form = useForm({name: '', email: '', password: ''});

function add() {
  editing.value = null;
  form.reset();
  form.clearErrors();
  open.value = true;
}

function edit(owner) {
  editing.value = owner;
  form.name = owner.name;
  form.email = owner.email;
  form.password = '';
  form.clearErrors();
  open.value = true;
}

function save() {
  const opt = {onSuccess: () => open.value = false};
  if (editing.value) form.put('/admin/owners/' + editing.value.id, opt); else form.post('/admin/owners', opt);
}

function remove(owner) {
  if (confirm('Delete this shop owner? Their shop will become unassigned.')) router.delete('/admin/owners/' + owner.id);
}
</script>

