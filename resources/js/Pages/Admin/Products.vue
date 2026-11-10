<template>
  <Head title="Product Catalog"/>
  <Shell title="Product Catalog">
    <div class="heading">
      <div><h1>Shared Product Catalog</h1>
        <p class="muted">Products are reusable across all shops.</p></div>
      <button class="btn primary" @click="add">+ New product</button>
    </div>
    <div class="panel table-scroll">
      <table>
        <thead>
        <tr>
          <th>Product</th>
          <th>SKU</th>
          <th>Created by</th>
          <th>Shops</th>
          <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="product in products" :key="product.id">
          <td>
            <div class="product-name"><img v-if="product.thumbnail_url" :src="product.thumbnail_url" alt=""
                                           class="thumb"><span>{{ product.name }}</span></div>
          </td>
          <td>{{ product.sku || '—' }}</td>
          <td>{{ product.creator?.name || 'Unknown' }}</td>
          <td>{{ product.listings_count }}</td>
          <td>
            <button class="btn small" @click="edit(product)">Edit</button>
            <button class="btn small danger" @click="remove(product)">Delete</button>
          </td>
        </tr>
        <tr v-if="!products.length">
          <td colspan="5" class="muted">No products in catalog.</td>
        </tr>
        </tbody>
      </table>
    </div>
    <Modal v-if="open" :title="editing?'Edit product':'New product'" @close="open=false">
      <form @submit.prevent="save">
        <Errors :errors="form.errors"/>
        <label>Name<input v-model="form.name" required></label><label>SKU (optional)<input
          v-model="form.sku"></label><label>Description<textarea v-model="form.description" rows="3"></textarea></label><label>Thumbnail
        <small>JPG, PNG or WebP up to 2MB</small><input type="file" accept="image/*"
                                                        @change="form.thumbnail=$event.target.files[0]||null"></label>
        <div class="actions end">
          <button type="button" class="btn" @click="open=false">Cancel</button>
          <button class="btn primary" :disabled="form.processing">Save product</button>
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

defineProps({products: Array});
const open = ref(false), editing = ref(null);
const form = useForm({name: '', sku: '', description: '', thumbnail: null});

function add() {
  editing.value = null;
  form.reset();
  form.clearErrors();
  open.value = true;
}

function edit(product) {
  editing.value = product;
  form.name = product.name;
  form.sku = product.sku || '';
  form.description = product.description || '';
  form.thumbnail = null;
  form.clearErrors();
  open.value = true;
}

function save() {
  const options = {forceFormData: true, onSuccess: () => open.value = false};
  if (editing.value) form.post('/admin/products/' + editing.value.id, options); else form.post('/admin/products', options);
}

function remove(product) {
  if (confirm('Delete catalog product from ALL shops?')) router.delete('/admin/products/' + product.id);
}
</script>

