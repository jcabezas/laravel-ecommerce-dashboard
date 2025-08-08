<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

// Pestaña activa
const activeTab = ref('woocommerce');

// Formulario para WooCommerce
const wooCommerceForm = useForm({
    platform: 'woocommerce',
    store_url: '',
    api_key: '',
    api_secret: '',
});

const submitWooCommerce = () => {
    wooCommerceForm.post(route('store.store'), {
        onFinish: () => wooCommerceForm.reset('api_key', 'api_secret'),
    });
};

// Lógica para Shopify
const shopifyUrl = ref('');
const connectShopify = () => {
    // Redirigimos a nuestra propia ruta de backend, que a su vez redirigirá a Shopify
    window.location.href = route('shopify.redirect', { store_url: shopifyUrl.value });
};
</script>

<template>
    <Head title="Conectar Tienda" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Conectar una Tienda</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <section class="p-6">
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">Información de la Tienda</h2>
                            <p class="mt-1 text-sm text-gray-600">
                                Elige tu plataforma e ingresa los datos para empezar a sincronizar.
                            </p>
                        </header>

                        <!-- Selector de Pestañas -->
                        <div class="mt-6 border-b border-gray-200">
                            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                                <button @click="activeTab = 'woocommerce'" :class="['whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm', activeTab === 'woocommerce' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300']">
                                    WooCommerce
                                </button>
                                <button @click="activeTab = 'shopify'" :class="['whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm', activeTab === 'shopify' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300']">
                                    Shopify
                                </button>
                            </nav>
                        </div>

                        <!-- Formulario de WooCommerce -->
                        <form v-if="activeTab === 'woocommerce'" @submit.prevent="submitWooCommerce" class="mt-6 space-y-6">
                            <div>
                                <InputLabel for="store_url" value="Dominio de la Tienda" />
                                <TextInput id="store_url" type="text" class="mt-1 block w-full" v-model="wooCommerceForm.store_url" required placeholder="mitienda.com" />
                                <p class="mt-1 text-xs text-gray-500">Ingresa solo el dominio, sin https://.</p>
                                <InputError class="mt-2" :message="wooCommerceForm.errors.store_url" />
                            </div>
                            <div>
                                <InputLabel for="api_key" value="API Key (Clave de Cliente)" />
                                <TextInput id="api_key" type="password" class="mt-1 block w-full" v-model="wooCommerceForm.api_key" required />
                                <InputError class="mt-2" :message="wooCommerceForm.errors.api_key" />
                            </div>
                            <div>
                                <InputLabel for="api_secret" value="API Secret (Clave Secreta)" />
                                <TextInput id="api_secret" type="password" class="mt-1 block w-full" v-model="wooCommerceForm.api_secret" required />
                                <InputError class="mt-2" :message="wooCommerceForm.errors.api_secret" />
                            </div>
                            <div class="flex items-center gap-4">
                                <PrimaryButton :disabled="wooCommerceForm.processing">Conectar WooCommerce</PrimaryButton>
                            </div >
                        </form>

                        <!-- Formulario de Shopify -->
                        <form v-if="activeTab === 'shopify'" @submit.prevent="connectShopify" class="mt-6 space-y-6">
                            <div>
                                <InputLabel for="shopify_url" value="Dominio de tu tienda Shopify" />
                                <TextInput id="shopify_url" type="text" class="mt-1 block w-full" v-model="shopifyUrl" required placeholder="nombre-tienda.myshopify.com" />
                                <p class="mt-1 text-xs text-gray-500">Ingresa solo el dominio, sin https://.</p>
                            </div>
                            <div class="flex items-center gap-4">
                                <PrimaryButton :disabled="!shopifyUrl">Conectar con Shopify</PrimaryButton>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
