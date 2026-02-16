<template>
  <div class="p-8 max-w-4xl mx-auto">
    <header class="flex justify-between items-center mb-8">
      <h1 class="text-3xl font-bold">Мои счета</h1>
    </header>

    <div class="grid gap-4">
      <NuxtLink to="/invoices" class="p-4 border rounded hover:bg-gray-50 block">
        Open invoices
      </NuxtLink>
    </div>
  </div>
</template>
