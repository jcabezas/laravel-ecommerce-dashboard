<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatsCard from '@/Components/StatsCard.vue'; // Importa el nuevo componente
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    metrics: Object,
    storeConnected: Boolean,
});

// Formateamos los valores para mostrarlos
const formattedRevenue = new Intl.NumberFormat('es-CL', { style: 'currency', currency: 'CLP' }).format(props.metrics.totalRevenue);
const formattedAverage = new Intl.NumberFormat('es-CL', { style: 'currency', currency: 'CLP' }).format(props.metrics.averageOrderValue);

</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div v-if="!storeConnected" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <h3 class="text-lg font-medium text-gray-900">¡Bienvenido!</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Para empezar, conecta una tienda y podrás ver tus métricas aquí.
                        </p>
                        <Link :href="route('store.create')" class="mt-4 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            Conectar Tienda Ahora
                        </Link>
                    </div>
                </div>

                <!-- Grid de Métricas -->
                <div v-else class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    <StatsCard title="Ventas (Últimos 30 días)" :value="formattedRevenue" />
                    <StatsCard title="Pedidos (Últimos 30 días)" :value="metrics.orderCount" />
                    <StatsCard title="Valor Promedio por Pedido" :value="formattedAverage" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
