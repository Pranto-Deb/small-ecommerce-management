<template>
  <Head title="My Products"/>
  <Shell title="My Products">
    <div class="heading">
      <div><h1>My Shop Products</h1>
        <p class="muted">Set your own prices, details, and thumbnails.</p></div>
      <Link v-if="shop" class="btn primary" href="/owner/catalog">+ Add products</Link>
    </div>
    <div v-if="!shop" class="notice">No shop assigned to your account.</div>
    <div class="panel table-scroll">
      <table>
        <thead>
        <tr>
          <th>Product</th>
          <th>Price (BDT)</th>
          <th>Shop details</th>
          <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="listing in listings" :key="listing.id">
          <td>
            <div class="product-name"><img v-if="listing.thumbnail_url" :src="listing.thumbnail_url" class="thumb"
                                           alt="">{{ listing.product?.name }}
            </div>
          </td>
          <td>{{ Number(listing.price).toFixed(2) }}</td>
          <td class="max-cell">{{ listing.details || listing.product?.description || '—' }}</td>
          <td>
            <button class="btn small" @click="edit(listing)">Edit</button>
            <button class="btn small danger" @click="remove(listing)">Remove</button>
          </td>
        </tr>
        <tr v-if="!listings.length">
          <td colspan="4" class="muted">No products assigned to your shop yet.</td>
        </tr>
        </tbody>
      </table>
    </div>
    <Modal v-if="open" title="Edit shop listing" @close="open=false">
      <form @submit.prevent="save"><p><strong>{{ selected?.product?.name }}</strong></p>
        <Errors :errors="form.errors"/>
        <label>Price (BDT)<input v-model="form.price" type="number" step="0.01" min="0" required></label><label>Shop-specific
          details<textarea v-model="form.details" rows="4"></textarea></label><label>Override thumbnail<input
            type="file" accept="image/*" @change="form.thumbnail=$event.target.files[0]||null"></label>
        <div class="actions end">
          <button type="button" class="btn" @click="open=false">Cancel</button>
          <button class="btn primary" :disabled="form.processing">Save changes</button>
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

defineProps({shop: Object, listings: Array});
const open = ref(false), selected = ref(null);
const form = useForm({price: '', details: '', thumbnail: null});

function edit(listing) {
  selected.value = listing;
  form.price = listing.price;
  form.details = listing.details || '';
  form.thumbnail = null;
  form.clearErrors();
  open.value = true;
}

function save() {
  form.post('/owner/listings/' + selected.value.id, {forceFormData: true, onSuccess: () => open.value = false});
}

function remove(listing) {
  if (confirm('Remove this product from your shop? The shared catalog will be unaffected.')) router.delete('/owner/listings/' + listing.id);
}
</script>

