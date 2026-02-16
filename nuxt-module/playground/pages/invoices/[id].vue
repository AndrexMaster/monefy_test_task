<script setup>
const route = useRoute()
const router = useRouter()
const invoiceId = route.params.id

const apiBase = process.server ? 'http://laravel-api:8000' : 'http://localhost:8000'
const { data: invoice, pending } = await useFetch(`${apiBase}/api/invoices/${invoiceId}`)

const computedGross = computed(() => {
  if (!invoice.value) return 0
  return (Number(invoice.value.net_amount) || 0) + (Number(invoice.value.vat_amount) || 0)
})

const isSaving = ref(false)
const updateInvoice = async () => {
  isSaving.value = true
  try {
    invoice.value.gross_amount = computedGross.value

    await $fetch(`http://localhost:8000/api/invoices/${invoiceId}`, {
      method: 'PUT',
      body: invoice.value,
    })
    router.push('/invoices')
  }
  catch (e) {
    alert('Error: Check your data (due date must be >= issue date)')
  } finally {
    isSaving.value = false
  }
}

const labels = {
  number: 'Invoice Number',
  supplier_name: 'Supplier Name',
  supplier_tax_id: 'Supplier Tax ID',
  net_amount: 'Net Amount',
  vat_amount: 'VAT Amount',
  gross_amount: 'Gross Amount (Auto)',
  currency: 'Currency',
  status: 'Status',
  issue_date: 'Issue Date',
  due_date: 'Due Date',
}
</script>

<template>
  <div class="p-8 max-w-2xl mx-auto">
    <button
      class="text-slate-400 hover:text-indigo-600 mb-6 flex items-center transition font-medium"
      @click="router.push('/invoices')"
    >
      ← Back to list
    </button>

    <div
      v-if="pending"
      class="py-20 text-center text-slate-400"
    >
      Loading...
    </div>

    <div
      v-else
      class="bg-white p-8 rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100"
    >
      <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-bold text-slate-800">
          Invoice #{{ invoice.number }}
        </h1>
        <div
          v-if="invoice.status !== 'pending'"
          class="text-[10px] bg-slate-100 text-slate-500 px-3 py-1 rounded-lg uppercase font-bold"
        >
          Read Only
        </div>
      </div>

      <div class="grid grid-cols-1 gap-5">
        <div
          v-for="(label, key) in labels"
          :key="key"
          class="flex flex-col gap-1.5 text-left"
        >
          <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">{{ label }}</label>

          <select
            v-if="key === 'status'"
            v-model="invoice[key]"
            class="input-style"
            :disabled="invoice.status !== 'pending'"
          >
            <option value="pending">
              Pending
            </option>
            <option value="approved">
              Approved
            </option>
            <option value="rejected">
              Rejected
            </option>
          </select>

          <div
            v-else-if="key === 'gross_amount'"
            class="input-style bg-slate-50 text-indigo-600 font-bold border-dashed"
          >
            {{ computedGross }} {{ invoice.currency }}
          </div>

          <input
            v-else
            v-model="invoice[key]"
            :type="key.includes('amount') ? 'number' : key.includes('date') ? 'date' : 'text'"
            class="input-style"
            :disabled="invoice.status !== 'pending' || key === 'number'"
          >
        </div>

        <button
          v-if="invoice.status === 'pending'"
          :disabled="isSaving"
          class="w-full bg-indigo-600 text-white py-4 rounded-2xl font-bold hover:bg-indigo-700 transition mt-4 shadow-lg shadow-indigo-100"
          @click="updateInvoice"
        >
          {{ isSaving ? 'Saving...' : 'Save Changes' }}
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
  .input-style {
    @apply w-full p-3.5 border border-slate-200 rounded-2xl bg-slate-50/50 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all text-sm text-slate-700 disabled:opacity-60 disabled:cursor-not-allowed;
  }
</style>
