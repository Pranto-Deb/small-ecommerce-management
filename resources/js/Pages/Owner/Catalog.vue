<template>
  <Head title="Shared Catalog"/>
  <Shell title="Shared Catalog">
    <div class="heading">
      <div><h1>Shared Product Catalog</h1>
        <p class="muted">Browse products created by administrators and other shop owners.</p></div>
      <button v-if="shop" class="btn primary" @click="addNew">+ Create product</button>
    </div>
    <div v-if="!shop" class="notice">Ask an administrator to assign you a shop before adding products.</div>
    <div class="panel table-scroll">
      <table>
        <thead>
        <tr>
          <th>Product</th>
          <th>SKU</th>
          <th>Description</th>
          <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="product in products" :key="product.id">
          <td>
            <div class="product-name"><img v-if="product.thumbnail_url" class="thumb" :src="product.thumbnail_url"
                                           alt="">{{ product.name }}
            </div>
          </td>
          <td>{{ product.sku || '—' }}</td>
          <td class="max-cell">{{ product.description || '—' }}</td>
          <td><span v-if="listedIds.includes(product.id)" class="tag">Listed</span>
            <button v-else class="btn small primary" :disabled="!shop" @click="select(product)">Add to shop</button>
          </td>
        </tr>
        <tr v-if="!products.length">
          <td colspan="4" class="muted">Catalog is empty. Create the first product.</td>
        </tr>
        </tbody>
      </table>
    </div>
    <Modal v-if="open" :title="create?'Create shared product':'Add product to shop'" @close="open=false">
      <form @submit.prevent="submit">
        <Errors :errors="create?newProduct.errors:listing.errors"/>
        <template v-if="create"><label>Product name<input v-model="newProduct.name" required></label><label>SKU
          (optional)<input v-model="newProduct.sku"></label><label>Catalog description<textarea
            v-model="newProduct.description" rows="2"></textarea></label><label>Product thumbnail<input type="file"
                                                                                                        accept="image/*"
                                                                                                        @change="newProduct.thumbnail=$event.target.files[0]||null"></label>
        </template>
        <p v-else><strong>{{ selected?.name }}</strong></p><label>Price (BDT)<input v-if="create"
                                                                                    v-model="newProduct.price"
                                                                                    type="number" min="0" step="0.01"
                                                                                    required><input v-else
                                                                                                    v-model="listing.price"
                                                                                                    type="number"
                                                                                                    min="0" step="0.01"
                                                                                                    required></label><label>Shop-specific
        details<textarea v-if="create" v-model="newProduct.details" rows="3"></textarea><textarea v-else
                                                                                                  v-model="listing.details"
                                                                                                  rows="3"></textarea></label><label
          v-if="!create">Shop thumbnail override<input type="file" accept="image/*"
                                                       @change="listing.thumbnail=$event.target.files[0]||null"></label>
        <div class="actions end">
          <button type="button" class="btn" @click="open=false">Cancel</button>
          <button class="btn primary" :disabled="create?newProduct.processing:listing.processing">
            {{ create ? 'Create & list' : 'Add to shop' }}
          </button>
        </div>
      </form>
    </Modal>
  </Shell>
</template>

<script setup>
import {ref} from 'vue';
import {useForm, Head} from '@inertiajs/vue3';
import Shell from '../../Components/Shell.vue';
import Modal from '../../Components/Modal.vue';
import Errors from '../../Components/Errors.vue';

const props = defineProps({shop: Object, products: Array, listedIds: Array});
const open = ref(false), create = ref(false), selected = ref(null);
const listing = useForm({price: '', details: '', thumbnail: null});
const newProduct = useForm({name: '', sku: '', description: '', thumbnail: null, price: '', details: ''});

function select(product) {
  selected.value = product;
  listing.reset();
  listing.clearErrors();
  create.value = false;
  open.value = true;
}

function addNew() {
  newProduct.reset();
  newProduct.clearErrors();
  create.value = true;
  open.value = true;
}

function submit() {
  if (create.value) newProduct.post('/owner/catalog', {
    forceFormData: true,
    onSuccess: () => open.value = false
  }); else listing.post('/owner/catalog/' + selected.value.id + '/list', {
    forceFormData: true,
    onSuccess: () => open.value = false
  });
}
</script>
