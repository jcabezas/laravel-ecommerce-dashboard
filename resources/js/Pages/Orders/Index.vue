<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import throttle from 'lodash/throttle'

const props = defineProps({
    orders: Object,
    storeConnected: Boolean,
    filters: Object,
});

// Creamos refs para los valores de los filtros, inicializados con los props
const search = ref(props.filters.search);
const status = ref(props.filters.status);

// Usamos 'watch' para observar cambios en los filtros.
// Con 'throttle' evitamos hacer demasiadas peticiones al escribir rápido.
watch([search, status], throttle(function ([searchVal, statusVal]) {
    router.get(route('orders.index'), {
        search: searchVal,
        status: statusVal,
    }, {
        preserveState: true,
        replace: true,
    });
}, 300));

const getStatusClass = (status) => {
    const baseClass = 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full';
    switch (status.toLowerCase()) {
        case 'completed':
            return `${baseClass} bg-green-100 text-green-800`;
        case 'processing':
            return `${baseClass} bg-yellow-100 text-yellow-800`;
        case 'cancelled':
        case 'failed':
            return `${baseClass} bg-red-100 text-red-800`;
        case 'on-hold':
            return `${baseClass} bg-blue-100 text-blue-800`;
        default:
            return `${baseClass} bg-gray-100 text-gray-800`;
    }
};
</script>

<template>
    <Head title="Pedidos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pedidos Recientes (Últimos 30 días)</h2>

                <a v-if="storeConnected" :href="route('orders.export', {search, status})" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500">
                        Exportar a Excel
                </a>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-6 bg-white shadow-sm sm:rounded-lg p-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700">Buscar por cliente</label>
                            <input v-model="search" type="text" id="search" class="px-3 py-2 mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Ej: Juan Pérez...">
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Estado del pedido</label>
                            <select v-model="status" id="status" class="px-3 py-2 mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option :value="null">Todos</option>
                                <option value="processing">Procesando</option>
                                <option value="completed">Completado</option>
                                <option value="on-hold">En espera</option>
                                <option value="cancelled">Cancelado</option>
                                <option value="failed">Fallido</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div v-if="!storeConnected" class="p-6 text-center">
                        <h3 class="text-lg font-medium text-gray-900">No hay ninguna tienda conectada.</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Por favor, conecta una tienda para poder ver tus pedidos.
                        </p>
                        <Link :href="route('store.create')" class="mt-4 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            Conectar Tienda Ahora
                        </Link>
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="w-full whitespace-nowrap">
                            <thead class="bg-gray-50">
                                <tr class="text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    <th class="px-6 py-3">ID Pedido</th>
                                    <th class="px-6 py-3">Cliente</th>
                                    <th class="px-6 py-3">Fecha</th>
                                    <th class="px-6 py-3">Estado</th>
                                    <th class="px-6 py-3">Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-if="orders.data.length === 0">
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">No se encontraron pedidos en los últimos 30 días.</td>
                                </tr>
                                <tr v-for="order in orders.data" :key="order.id" class="text-gray-700">
                                    <td class="px-6 py-4 font-medium">#{{ order.id }}</td>
                                    <td class="px-6 py-4">{{ order.customer }}</td>
                                    <td class="px-6 py-4">{{ order.date }}</td>
                                    <td class="px-6 py-4">
                                        <span :class="getStatusClass(order.status)">{{ order.status }}</span>
                                    </td>
                                    <td class="px-6 py-4 font-semibold">${{ new Intl.NumberFormat('es-CL').format(order.total) }}</td>
                                </tr>
                            </tbody>
                        </table>

                        <Pagination class="p-6" :links="orders.links" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
