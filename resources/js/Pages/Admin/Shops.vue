<template>
  <Head title="Shops"/>
  <Shell title="Shops">
    <div class="heading">
      <div><h1>Shops</h1>
        <p class="muted">Each shop can be assigned to one owner.</p></div>
      <button class="btn primary" @click="add">+ New shop</button>
    </div>
    <div class="panel table-scroll">
      <table>
        <thead>
        <tr>
          <th>Shop</th>
          <th>Slug</th>
          <th>Owner</th>
          <th>Listings</th>
          <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="shop in shops" :key="shop.id">
          <td>
            <Link :href="'/shops/'+shop.slug">{{ shop.name }}</Link>
          </td>
          <td>{{ shop.slug }}</td>
          <td>{{ shop.owner?.name || 'Unassigned' }}</td>
          <td>{{ shop.listings_count }}</td>
          <td>
            <button class="btn small" @click="edit(shop)">Edit</button>
            <button class="btn small danger" @click="remove(shop)">Delete</button>
          </td>
        </tr>
        <tr v-if="!shops.length">
          <td colspan="5" class="muted">No shops created yet.</td>
        </tr>
        </tbody>
      </table>
    </div>
    <Modal v-if="open" :title="editing?'Edit shop':'New shop'" @close="open=false">
      <form @submit.prevent="save">
        <Errors :errors="form.errors"/>
        <label>Shop name<input v-model="form.name" required maxlength="255"></label><label>URL slug (letters, numbers,
        dashes)<input v-model="form.slug" required placeholder="my-shop"></label><label>Description<textarea
          v-model="form.description" rows="3"></textarea></label><label>Shop owner<select v-model="form.owner_id">
        <option value="">Unassigned</option>
        <option v-for="owner in owners" :key="owner.id" :value="owner.id">{{ owner.name }} — {{ owner.email }}</option>
      </select></label>
        <div class="actions end">
          <button type="button" class="btn" @click="open=false">Cancel</button>
          <button class="btn primary" :disabled="form.processing">Save shop</button>
        </div>
      </form>
    </Modal>
  </Shell>
</template>

<script setup>
import {ref} from 'vue';
import {useForm, Head, router, Link} from '@inertiajs/vue3';
import Shell from '../../Components/Shell.vue';
import Modal from '../../Components/Modal.vue';
import Errors from '../../Components/Errors.vue';

defineProps({shops: Array, owners: Array});
const open = ref(false), editing = ref(null);
const form = useForm({name: '', slug: '', description: '', owner_id: ''});

function add() {
  editing.value = null;
  form.reset();
  form.clearErrors();
  open.value = true;
}

function edit(shop) {
  editing.value = shop;
  form.name = shop.name;
  form.slug = shop.slug;
  form.description = shop.description || '';
  form.owner_id = shop.owner_id || '';
  form.clearErrors();
  open.value = true;
}

function save() {
  const data = {...form.data(), owner_id: form.owner_id || null};
  form.transform(() => data);
  const options = {onSuccess: () => open.value = false, onFinish: () => form.transform(d => d)};
  if (editing.value) form.put('/admin/shops/' + editing.value.id, options); else form.post('/admin/shops', options);
}

function remove(shop) {
  if (confirm('Delete shop and all its listings?')) router.delete('/admin/shops/' + shop.id);
}
</script>

