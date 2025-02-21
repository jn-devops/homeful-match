<script setup>
import { useForm } from '@inertiajs/vue3';
import Input from '@/Input/Input.vue';
import DefaultButton from '@/Buttons/DefaultButton.vue';
import { ref } from 'vue';

const form = useForm({
    tcp: 0,
    gmi: 0,
    percent_dp: 0,
    dp_term: 0,
    percent_mf: 0,
    gmi_percent: 0,
    bp_term: 0,
    processing_fee: 0,
    consultation_fee: 0,
    mri: 0,
    fi: 0,
})

const computedData = ref({
    downpayment: 0,
    downpayment_amortization: 0,
    cash_out: 0,
    loan_amortization: 0,
    partial_miscellaneous_fees: 0,
    income_requirement: 0,
    maximum_downpayment_from_monthly_income: 0,
    loan_difference: 0,
})

const computedDiv = ref()

const submit = () => {

    // Focusing on the Computed Div. This can be use after form onSuccess.
    computedDiv.value.scrollIntoView({ behavior: 'smooth', block: 'center' });
    setTimeout(() => {
      computedDiv.value.focus();
    }, 300); 
}

const formatNumber = (value) => {
    return Number(parseFloat(value).toFixed(2)).toLocaleString("en-US", {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  });
}

</script>
<template>
    <div class="w-full px-5 md:px-20 py-10 bg-gray-100">
        <h6>Let's Calculate</h6>
        <h1 class="font-bold text-2xl">Home Match Calculator</h1>
        <form @submit.prevent="submit">
            
            <div class="flex flex-col-reverse lg:flex-row  gap-4 mt-10">
                <div class="basis-1/2 w-full p-5 bg-white rounded-xl shadow-xl">
                    <h2 class="text-base/7 font-semibold text-gray-900 leading-none">Input</h2>
                    <p class="mt-1 text-sm/6 text-gray-600 leading-none mb-5">Provide the required information below.</p>
                    <Input 
                        v-model="form.tcp"
                        label="TCP"
                        placeholder="Enter TCP"
                        :error-message="form.errors.tcp"
                        type="number"
                    />
                    <Input 
                        v-model="form.gmi"
                        label="Gross Monthly Income"
                        placeholder="Enter Gross Monthly Income"
                        :error-message="form.errors.gmi"
                        type="number"
                    />
                    <Input 
                        v-model="form.percent_dp"
                        label="Gross Monthly Income"
                        placeholder="Enter Gross Monthly Income"
                        :error-message="form.errors.percent_dp"
                        type="number"
                    />
                    <Input 
                        v-model="form.dp_term"
                        label="Downpayment Term (months)"
                        placeholder="Enter Downpayment Term"
                        :error-message="form.errors.dp_term"
                        type="number"
                    />
                    <Input 
                        v-model="form.percent_mf"
                        label="Percent Miscellaneous Fee"
                        placeholder="Enter Percent Miscellaneous Fee"
                        :error-message="form.errors.percent_mf"
                        type="number"
                    />
                    <Input 
                        v-model="form.gmi_percent"
                        label="GMI Percent"
                        placeholder="Enter GMI Percent"
                        :error-message="form.errors.gmi_percent"
                        type="number"
                    />
                    <Input 
                        v-model="form.bp_term"
                        label="Balance Payment Term (years)"
                        placeholder="Enter Balance Payment Term"
                        :error-message="form.errors.bp_term"
                        type="number"
                    />
                    <Input 
                        v-model="form.processing_fee"
                        label="Processing Fee"
                        placeholder="Enter Processing Fee"
                        :error-message="form.errors.processing_fee"
                        type="number"
                    />
                    <Input 
                        v-model="form.consultation_fee"
                        label="Consultation Fee"
                        placeholder="Enter Consultation Fee"
                        :error-message="form.errors.consultation_fee"
                        type="number"
                    />
                    <Input 
                        v-model="form.mri"
                        label="MRI"
                        placeholder="Enter MRI"
                        :error-message="form.errors.mri"
                        type="number"
                    />
                    <Input 
                        v-model="form.fi"
                        label="Fire Insurence"
                        placeholder="Enter Fire Insurence"
                        :error-message="form.errors.fi"
                        type="number"
                    />
                    <DefaultButton>Calculate</DefaultButton>
                </div>
                <div ref="computedDiv" tabindex="-1" class="basis-1/2 w-full h-fit p-5 bg-white rounded-xl shadow-xl">
                    <h2 class="text-base/7 font-semibold text-gray-900 leading-none">Computed</h2>
                    <p class="mt-1 text-sm/6 text-gray-600 leading-none">This result has been generated using the HomeMatch Calculator for accurate assessment.</p>
                    <div class="grid grid-cols-2 gap-5 mt-5">
                        <div class="mb-5">
                            <h3 class="font-semibold text-sm">Downpayment</h3>
                            <div class="">₱ {{ formatNumber(computedData.downpayment) }}</div>
                        </div>
                        <div class="mb-5">
                            <h3 class="font-semibold text-sm">Downpayment Amortization</h3>
                            <div class="">{{ formatNumber(computedData.downpayment_amortization) }}</div>
                        </div>
                        <div class="mb-5">
                            <h3 class="font-semibold text-sm">Cash Out</h3>
                            <div class="">{{ formatNumber(computedData.cash_out) }}</div>
                        </div>
                        <div class="mb-5">
                            <h3 class="font-semibold text-sm">Loan Amortization</h3>
                            <div class="">{{ formatNumber(computedData.loan_amortization) }}</div>
                        </div>
                        <div class="mb-5">
                            <h3 class="font-semibold text-sm">Partial Miscellaneous Fees</h3>
                            <div class="">{{ formatNumber(computedData.partial_miscellaneous_fees) }}</div>
                        </div>
                        <div class="mb-5">
                            <h3 class="font-semibold text-sm">Income Requirement</h3>
                            <div class="">{{ formatNumber(computedData.income_requirement) }}</div>
                        </div>
                        <div class="mb-5">
                            <h3 class="font-semibold text-sm">Maximum Downpayment From Monthly Income</h3>
                            <div class="">{{ formatNumber(computedData.maximum_downpayment_from_monthly_income) }}</div>
                        </div>
                        <div class="mb-5">
                            <h3 class="font-semibold text-sm">Loan Difference</h3>
                            <div class="">{{ formatNumber(computedData.loan_difference) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>