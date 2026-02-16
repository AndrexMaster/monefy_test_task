<script setup>
const router = useRouter()

const { data: invoices } = await useFetch('/api/invoices', {
  baseURL: import.meta.server ? 'http://laravel-api:8000' : 'http://localhost:8000',
})

const getStatusClasses = (status) => {
  const base = 'px-2 py-1 rounded-full text-xs font-bold uppercase'
  switch (status?.toLowerCase()) {
    case 'approved':
      return `${base} bg-green-100 text-green-700`
    case 'rejected':
      return `${base} bg-red-100 text-red-700`
    case 'pending':
      return `${base} bg-yellow-100 text-yellow-700`
    default:
      return `${base} bg-gray-100 text-gray-700`
  }
}

const goToInvoice = (id) => {
  router.push(`/invoices/${id}`)
}
</script>

<template>
  <div class="p-8 max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-3xl font-bold text-slate-800">
        Invoices
      </h1>
      <button
        class="bg-indigo-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-indigo-700 transition"
        @click="$router.push('/invoices/create')"
      >
        + New Invoice
      </button>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-xs uppercase tracking-wider">
            <th class="px-6 py-4 font-semibold">
              Number
            </th>
            <th class="px-6 py-4 font-semibold">
              Supplier
            </th>
            <th class="px-6 py-4 font-semibold">
              Amount
            </th>
            <th class="px-6 py-4 font-semibold">
              Status
            </th>
            <th class="p-6 py-4 font-semibold">
              Due Date
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr
            v-for="invoice in invoices"
            :key="invoice.id"
            class="group cursor-pointer hover:bg-slate-50 transition-colors"
            @click="goToInvoice(invoice.id)"
          >
            <td class="px-6 py-4 text-sm font-medium text-slate-900">
              #{{ invoice.number }}
            </td>
            <td class="px-6 py-4 text-sm text-slate-600">
              {{ invoice.supplier_name }}
            </td>
            <td class="px-6 py-4 text-sm font-mono text-slate-600">
              {{ invoice.gross_amount }} {{ invoice.currency }}
            </td>
            <td class="px-6 py-4">
              <span :class="getStatusClasses(invoice.status)">
                {{ invoice.status }}
              </span>
            </td>
            <td class="px-6 py-4 text-sm text-slate-500">
              {{ invoice.due_date }}
            </td>
          </tr>
        </tbody>
      </table>

      <div
        v-if="!invoices?.length"
        class="p-12 text-center text-slate-400"
      >
        No invoices found.
      </div>
    </div>
  </div>
</template>
