<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    products: Object,
    storeConnected: Boolean,
});
</script>

<template>
    <Head title="Productos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Productos</h2>

                <a v-if="storeConnected" :href="route('products.export')" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500">
                    Exportar a Excel
                </a>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div v-if="!storeConnected" class="p-6 text-center">
                        <h3 class="text-lg font-medium text-gray-900">No hay ninguna tienda conectada.</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Por favor, conecta una tienda para poder ver tus productos.
                        </p>
                        <Link :href="route('store.create')" class="mt-4 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            Conectar Tienda Ahora
                        </Link>
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="w-full whitespace-nowrap">
                            <thead class="bg-gray-50">
                                <tr class="text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    <th class="px-6 py-3">Imagen</th>
                                    <th class="px-6 py-3">Nombre</th>
                                    <th class="px-6 py-3">SKU</th>
                                    <th class="px-6 py-3">Precio</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-if="products.data.length === 0">
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">No se encontraron productos en tu tienda.</td>
                                </tr>
                                <tr v-for="product in products.data" :key="product.id" class="text-gray-700">
                                    <td class="px-6 py-4">
                                        <img :src="product.image" alt="Imagen del producto" class="h-12 w-12 rounded-md object-cover shadow-sm">
                                    </td>
                                    <td class="px-6 py-4 font-medium">{{ product.name }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 font-semibold leading-tight text-gray-700 bg-gray-100 rounded-full text-xs">{{ product.sku }}</span>
                                    </td>
                                    <td class="px-6 py-4 font-semibold">${{ new Intl.NumberFormat('es-CL').format(product.price) }}</td>
                                </tr>
                            </tbody>
                        </table>

                        <Pagination class="p-6" :links="products.links" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
