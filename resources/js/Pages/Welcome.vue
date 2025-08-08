<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    <Head title="Bienvenido a Amplifica Dashboard" />

    <div class="bg-gray-50 text-gray-800">
        <!-- Header -->
        <header class="absolute inset-x-0 top-0 z-50">
            <nav class="flex items-center justify-between p-6 lg:px-8" aria-label="Global">
                <div class="flex lg:flex-1">
                    <a href="#" class="-m-1.5 p-1.5 flex items-center">
                        <svg class="h-8 w-auto text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>

                    </a>
                </div>
                <div class="lg:flex lg:flex-1 lg:justify-end">
                    <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="text-sm font-semibold leading-6 text-gray-900">
                        Dashboard <span aria-hidden="true">&rarr;</span>
                    </Link>
                    <Link v-else :href="route('login')" class="text-sm font-semibold leading-6 text-gray-900">
                        Iniciar Sesión <span aria-hidden="true">&rarr;</span>
                    </Link>
                </div>
            </nav>
        </header>

        <!-- Hero Section -->
        <main class="relative isolate px-6 pt-8 lg:px-8">
            <div class="mx-auto max-w-7xl py-32 sm:py-32 lg:py-32">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div class="text-center lg:text-left">
                        <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-6xl">
                            Tu centro de control para E-commerce
                        </h1>
                        <p class="mt-6 text-lg leading-8 text-gray-600">
                            Conecta tus tiendas de Shopify y WooCommerce. Visualiza productos, pedidos y métricas clave en un dashboard unificado y fácil de usar.
                        </p>
                        <div class="mt-10 flex items-center justify-center lg:justify-start gap-x-6">
                            <Link :href="route('register')" class="rounded-md bg-blue-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                Comenzar ahora
                            </Link>
                            <a href="#features" class="text-sm font-semibold leading-6 text-gray-900">Ver características <span aria-hidden="true">→</span></a>
                        </div>
                    </div>
                    <div class="hidden lg:block">
                        <!-- Imagen SVG Ilustrativa -->
                        <svg viewBox="0 0 500 300" xmlns="http://www.w3.org/2000/svg">
                            <rect x="10" y="10" width="480" height="280" rx="20" fill="#FFFFFF" stroke="#E5E7EB" stroke-width="2"/>
                            <rect x="30" y="30" width="120" height="240" rx="10" fill="#F3F4F6"/>
                            <rect x="160" y="30" width="310" height="80" rx="10" fill="#F3F4F6"/>
                            <rect x="160" y="120" width="150" height="150" rx="10" fill="#F3F4F6"/>
                            <rect x="320" y="120" width="150" height="150" rx="10" fill="#F3F4F6"/>
                            <!-- Elementos de UI simulados -->
                            <circle cx="50" cy="50" r="10" fill="#3B82F6"/>
                            <rect x="70" y="45" width="60" height="10" rx="5" fill="#D1D5DB"/>
                            <rect x="40" y="80" width="100" height="8" rx="4" fill="#E5E7EB"/>
                            <rect x="40" y="100" width="80" height="8" rx="4" fill="#E5E7EB"/>
                            <rect x="40" y="120" width="100" height="8" rx="4" fill="#E5E7EB"/>
                            <rect x="180" y="50" width="40" height="40" rx="5" fill="#A5B4FC"/>
                            <rect x="230" y="55" width="120" height="10" rx="5" fill="#D1D5DB"/>
                            <rect x="230" y="75" width="80" height="10" rx="5" fill="#E5E7EB"/>
                            <!-- Gráfico de barras simulado -->
                            <rect x="180" y="220" width="20" height="30" fill="#60A5FA"/>
                            <rect x="210" y="190" width="20" height="60" fill="#3B82F6"/>
                            <rect x="240" y="210" width="20" height="40" fill="#60A5FA"/>
                            <rect x="270" y="170" width="20" height="80" fill="#3B82F6"/>
                        </svg>
                    </div>
                </div>
            </div>
        </main>

        <!-- Features Section -->
        <section id="features" class="py-24 sm:py-32 bg-white">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-2xl lg:text-center">
                    <h2 class="text-base font-semibold leading-7 text-blue-600">Todo en un solo lugar</h2>
                    <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                        La herramienta que necesitas para escalar tu operación
                    </p>
                </div>
                <div class="mx-auto mt-16 max-w-2xl sm:mt-20 lg:mt-24 lg:max-w-4xl">
                    <dl class="grid max-w-xl grid-cols-1 gap-x-8 gap-y-10 lg:max-w-none lg:grid-cols-2 lg:gap-y-16">
                        <!-- Feature 1 -->
                        <div class="relative pl-16">
                            <dt class="text-base font-semibold leading-7 text-gray-900">
                                <div class="absolute left-0 top-0 flex h-10 w-10 items-center justify-center rounded-lg bg-blue-600">
                                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 16.875h3.375m0 0h3.375m-3.375 0V13.5m0 3.375v3.375M6 10.5h2.25a2.25 2.25 0 002.25-2.25V6a2.25 2.25 0 00-2.25-2.25H6A2.25 2.25 0 003.75 6v2.25A2.25 2.25 0 006 10.5zm0 9.75h2.25A2.25 2.25 0 0010.5 18v-2.25a2.25 2.25 0 00-2.25-2.25H6a2.25 2.25 0 00-2.25 2.25V18A2.25 2.25 0 006 20.25z" /></svg>
                                </div>
                                Multi-Plataforma
                            </dt>
                            <dd class="mt-2 text-base leading-7 text-gray-600">Conecta y gestiona tus tiendas de WooCommerce y Shopify desde una única interfaz unificada.</dd>
                        </div>
                        <!-- Feature 2 -->
                        <div class="relative pl-16">
                            <dt class="text-base font-semibold leading-7 text-gray-900">
                                <div class="absolute left-0 top-0 flex h-10 w-10 items-center justify-center rounded-lg bg-blue-600">
                                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1.5-1.5m1.5 1.5l1.5-1.5m0 0l1.5 1.5m-1.5-1.5l-1.5 1.5m7.5-9l-1.5-1.5m1.5 1.5l1.5-1.5m0 0l1.5 1.5m-1.5-1.5l-1.5 1.5" /></svg>
                                </div>
                                Visualización de Datos
                            </dt>
                            <dd class="mt-2 text-base leading-7 text-gray-600">Accede a listados claros y concisos de tus productos y pedidos recientes para una gestión más eficiente.</dd>
                        </div>
                        <!-- Feature 3 -->
                        <div class="relative pl-16">
                            <dt class="text-base font-semibold leading-7 text-gray-900">
                                <div class="absolute left-0 top-0 flex h-10 w-10 items-center justify-center rounded-lg bg-blue-600">
                                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" /></svg>
                                </div>
                                Métricas Clave
                            </dt>
                            <dd class="mt-2 text-base leading-7 text-gray-600">Obtén una vista rápida del rendimiento de tu negocio con métricas agregadas como ventas totales y valor promedio de pedido.</dd>
                        </div>
                        <!-- Feature 4 -->
                        <div class="relative pl-16">
                            <dt class="text-base font-semibold leading-7 text-gray-900">
                                <div class="absolute left-0 top-0 flex h-10 w-10 items-center justify-center rounded-lg bg-blue-600">
                                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                                </div>
                                Exportación Fácil
                            </dt>
                            <dd class="mt-2 text-base leading-7 text-gray-600">Exporta tus datos de productos y pedidos a formato Excel o CSV con un solo clic para análisis externos.</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </section>
    </div>
</template>
