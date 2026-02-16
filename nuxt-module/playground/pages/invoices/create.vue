<script setup>
import { useForm } from 'vee-validate'
import { toTypedSchema } from '@vee-validate/zod'
import * as zod from 'zod'

const router = useRouter()

const schema = toTypedSchema(
  zod.object({
    number: zod.string().min(1, 'Required'),
    supplier_name: zod.string().min(1, 'Required'),
    supplier_tax_id: zod.string().min(1, 'Tax ID is required'),
    net_amount: zod.number({ invalid_type_error: 'Must be a number' }).gt(0, 'Must be > 0'),
    vat_amount: zod.number({ invalid_type_error: 'Must be a number' }).min(0, 'Must be >= 0'),
    due_date: zod.string().min(1, 'Required'),
    issue_date: zod.string(),
    currency: zod.string(),
    status: zod.string(),
  }).refine(data => new Date(data.due_date) >= new Date(data.issue_date), {
    message: 'Due date must be >= Issue date',
    path: ['due_date'],
  }),
)

const { errors, defineField, handleSubmit, values } = useForm({
  validationSchema: schema,
  initialValues: {
    number: '',
    supplier_name: '',
    supplier_tax_id: '',
    net_amount: 0,
    vat_amount: 0,
    currency: 'UAH',
    status: 'pending',
    issue_date: new Date().toISOString().split('T')[0],
    due_date: '',
  },
})

const [number] = defineField('number')
const [supplier_name] = defineField('supplier_name')
const [net_amount] = defineField('net_amount')
const [vat_amount] = defineField('vat_amount')
const [status] = defineField('status')
const [issue_date] = defineField('issue_date')
const [due_date] = defineField('due_date')
const [supplier_tax_id] = defineField('supplier_tax_id')

const gross_amount = computed(() => (Number(values.net_amount) || 0) + (Number(values.vat_amount) || 0))

const onSubmit = handleSubmit(async (formValues) => {
  try {
    await $fetch('http://localhost:8000/api/invoices', {
      method: 'POST',
      body: { ...formValues, gross_amount: gross_amount.value },
      headers: { Accept: 'application/json' },
    })
    await router.push('/invoices')
  }
  catch (e) {
    console.log('Validation error', e)
    alert(e.response?._data?.message || 'Validation error')
  }
})
</script>

<template>
  <div
    v-if="Object.keys(errors).length"
    class="text-red-500 mb-4"
  >
    <span>Check these fields: {{ Object.keys(errors).join(', ') }}</span>
  </div>

  <div class="p-8 max-w-2xl mx-auto text-left">
    <button
      class="mb-6 text-slate-500 hover:text-slate-800 transition"
      @click="router.back()"
    >
      ← Back to List
    </button>

    <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
      <h1 class="text-2xl font-bold mb-6">
        Create New Invoice
      </h1>

      <form
        class="space-y-4"
        @submit="onSubmit"
      >
        <div class="grid grid-cols-2 gap-4">
          <div class="flex flex-col">
            <label class="text-xs font-bold text-slate-400 uppercase">Number</label>
            <input
              v-model="number"
              type="text"
              class="input-style"
              :class="{ 'border-red-500': errors.number }"
            >
            <span class="text-red-500 text-[10px]">{{ errors.number }}</span>
          </div>

          <div class="flex flex-col">
            <label class="text-xs font-bold text-slate-400 uppercase">Status</label>
            <select
              v-model="status"
              class="input-style"
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
          </div>
        </div>

        <div class="flex flex-col">
          <label class="text-xs font-bold text-slate-400 uppercase">Supplier Name</label>
          <input
            v-model="supplier_name"
            type="text"
            class="input-style"
          >
          <span class="text-red-500 text-[10px]">{{ errors.supplier_name }}</span>
        </div>

        <div class="flex flex-col">
          <label class="text-xs font-bold text-slate-400 uppercase">Supplier Tax ID</label>
          <input
            v-model="supplier_tax_id"
            type="text"
            placeholder="e.g. 12345678"
            class="input-style"
            :class="{ 'border-red-500': errors.supplier_tax_id }"
          >
          <span
            v-if="errors.supplier_tax_id"
            class="text-red-500 text-[10px] mt-1"
          >
            {{ errors.supplier_tax_id }}
          </span>
        </div>

        <div class="grid grid-cols-3 gap-4 font-mono">
          <div class="flex flex-col">
            <label class="text-xs font-bold text-slate-400 uppercase font-sans">Net</label>
            <input
              v-model.number="net_amount"
              type="number"
              class="input-style"
            >
            <span class="text-red-500 text-[10px]">{{ errors.net_amount }}</span>
          </div>
          <div class="flex flex-col">
            <label class="text-xs font-bold text-slate-400 uppercase font-sans">VAT</label>
            <input
              v-model.number="vat_amount"
              type="number"
              class="input-style"
            >
            <span class="text-red-500 text-[10px]">{{ errors.vat_amount }}</span>
          </div>
          <div class="flex flex-col bg-slate-50 p-2 rounded-lg border border-slate-200">
            <label class="text-xs font-bold text-slate-400 uppercase font-sans">Gross</label>
            <div class="text-lg font-bold">
              {{ gross_amount }} {{ values.currency }}
            </div>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div class="flex flex-col">
            <label class="text-xs font-bold text-slate-400 uppercase">Issue Date</label>
            <input
              v-model="issue_date"
              type="date"
              class="input-style"
            >
            <span class="text-red-500 text-[10px]">{{ errors.issue_date }}</span>
          </div>
          <div class="flex flex-col">
            <label class="text-xs font-bold text-slate-400 uppercase">Due Date</label>
            <input
              v-model="due_date"
              type="date"
              class="input-style"
              :class="{ 'border-red-500': errors.due_date }"
            >
            <span class="text-red-500 text-[10px]">{{ errors.due_date }}</span>
          </div>
        </div>

        <button
          type="submit"
          class="w-full bg-indigo-600 text-white py-3 rounded-xl font-bold hover:bg-indigo-700 transition mt-4"
        >
          Save Invoice
        </button>
      </form>
    </div>
  </div>
</template>

<style scoped>
  .input-style { @apply w-full p-2.5 border border-slate-200 rounded-lg bg-white outline-none focus:ring-2 focus:ring-indigo-500 transition; }
</style>
