<script setup>
import {Head, useForm, usePage} from '@inertiajs/vue3';
import Input from '@/Input/Input.vue';
import DefaultButton from '@/Buttons/DefaultButton.vue';
import {ref, watch} from 'vue';
import ToggleInput from '@/Input/ToggleInput.vue';

const form = useForm({
    age: 30,
    tcp: 3200000,
    gmi: 80000,
    percent_dp: 3,
    dp_term: 6,
    percent_mf: 7,
    gmi_percent: null,
    bp_term: null,
    processing_fee: 10000,
    interest_rate: null,
    mri_fi: 0
})

const computedData = ref({
    downpayment: 0,
    downpayment_amortization: 0,
    downpayment_term: null,
    cash_out: 0,
    loan_amortization: 0,
    miscellaneous_fees: 0,
    income_requirement: 0,
    balance_payment: 0,
    loan_value: 0,
    bp_term: null,
    bp_interest_rate: null,
    gmi_percent: null,
    loan_difference: null,
    joint_disposable_monthly_income: null,
    present_value_from_monthly_disposable_income: null
})

const computedDiv = ref(null)

const calculated_data = ref({});

watch(
    () => usePage().props.flash.event,
    (event) => {
        if (event?.name === 'calculated') {
            console.log('event1:', event?.data);
            calculated_data.value = event?.data;

            // Update computedData via .value
            computedData.value.downpayment = calculated_data.value['down_payment'];
            computedData.value.downpayment_amortization = calculated_data.value['dp_amortization'];
            computedData.value.downpayment_term = calculated_data.value['dp_term'];
            computedData.value.cash_out = calculated_data.value['cash_out'];
            computedData.value.loan_amortization = calculated_data.value['loan_amortization'];
            computedData.value.miscellaneous_fees = calculated_data.value['miscellaneous_fees'];
            computedData.value.income_requirement = calculated_data.value['income_requirement'];
            computedData.value.balance_payment = calculated_data.value['balance_payment'];
            computedData.value.loan_value = calculated_data.value['loan_amount'];
            computedData.value.bp_term = calculated_data.value['bp_term'];
            computedData.value.bp_interest_rate = calculated_data.value['bp_interest_rate'] * 100;
            computedData.value.gmi_percent = calculated_data.value['income_requirement_multiplier'] * 100;
            computedData.value.loan_difference = calculated_data.value['loan_difference'];
            computedData.value.joint_disposable_monthly_income = calculated_data.value['joint_disposable_monthly_income'];
            computedData.value.present_value_from_monthly_disposable_income = calculated_data.value['present_value_from_monthly_disposable_income'];

        }
    },
    { immediate: true }
);

const submit = () => {
    form.post(route('match.calculate'), {
        onFinish: () => form.reset(),
        preserveScroll: true,
    });
    // Focusing on the Computed Div. This can be use after form onSuccess.
    // computedDiv.value.scrollIntoView({ behavior: 'smooth', block: 'center' });
    // setTimeout(() => {
    //   computedDiv.value.focus();
    // }, 300);
}

const formatNumber = (value) => {
    return Number(parseFloat(value).toFixed(2)).toLocaleString("en-US", {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  });
}

</script>
<template>
    <Head title="Homeful Match Calculator" />
    <div class="w-full min-h-screen px-5 md:px-20 py-10 bg-gray-100">
        <h6>Let's Calculate</h6>
        <h1 class="font-bold text-2xl">Home Match Calculator</h1>
        <form @submit.prevent="submit">

            <div class="flex flex-col-reverse lg:flex-row  gap-4 mt-10">
                <div class="basis-1/2 w-full p-5 bg-white rounded-xl shadow-xl">
                    <h2 class="text-base/7 font-semibold text-gray-900 leading-none">Input</h2>
                    <p class="mt-1 text-sm/6 text-gray-600 leading-none mb-5">Provide the required information below.</p>
                    <div class="lg:grid lg:grid-cols-2 lg:gap-3 mb-5">
                      <Input
                          v-model="form.age"
                          label="Age"
                          placeholder="Enter TCP"
                          :error-message="form.errors.age"
                          type="number"
                      />
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
                            label="Percent Down Payment"
                            placeholder="Enter Gross Monthly Income"
                            :error-message="form.errors.percent_dp"
                            type="number"
                            step="0.01"
                        />
                        <Input
                            v-model="form.dp_term"
                            label="Down Payment Term (months)"
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
                            step="0.01"
                        />
                        <Input
                            v-model="form.gmi_percent"
                            label="GMI Percent"
                            placeholder="Enter GMI Percent"
                            :error-message="form.errors.gmi_percent"
                            type="number"
                            step="0.01"
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
                            v-model="form.interest_rate"
                            label="Interest Rate"
                            placeholder="Enter Interest Rate"
                            :error-message="form.errors.interest_rate"
                            type="number"
                            step="0.01"
                        />
                        <ToggleInput 
                             v-model="form.mri_fi"
                             label="With MRI/FI"
                             :error-message="form.errors.mri_fi"
                        />
                    </div>
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
                            <div class="">₱ {{ formatNumber(computedData.downpayment_amortization) }}</div>
                        </div>
                        <div class="mb-5">
                          <h3 class="font-semibold text-sm">Downpayment Term</h3>
                          <div class=""> {{ computedData.downpayment_term }}</div>
                        </div>
                        <div class="mb-5">
                            <h3 class="font-semibold text-sm">Cash Out</h3>
                            <div class="">₱ {{ formatNumber(computedData.cash_out) }}</div>
                        </div>
                        <div class="mb-5">
                            <h3 class="font-semibold text-sm">Loan Amortization</h3>
                            <div class="">₱ {{ formatNumber(computedData.loan_amortization) }}</div>
                        </div>
                        <div class="mb-5">
                            <h3 class="font-semibold text-sm">Miscellaneous Fees</h3>
                            <div class="">₱ {{ formatNumber(computedData.miscellaneous_fees) }}</div>
                        </div>
                        <div class="mb-5">
                            <h3 class="font-semibold text-sm">Income Requirement</h3>
                            <div class="">₱ {{ formatNumber(computedData.income_requirement) }}</div>
                        </div>
                        <div class="mb-5">
                            <h3 class="font-semibold text-sm">Balance Payment</h3>
                            <div class="">₱ {{ formatNumber(computedData.balance_payment) }}</div>
                        </div>
                        <div class="mb-5">
                            <h3 class="font-semibold text-sm">Loan Value</h3>
                            <div class="">₱ {{ formatNumber(computedData.loan_value) }}</div>
                        </div>
                        <div class="mb-5">
                          <h3 class="font-semibold text-sm">BP Term</h3>
                          <div class="">{{ computedData.bp_term }}</div>
                        </div>
                        <div class="mb-5">
                          <h3 class="font-semibold text-sm">BP Interest Rate</h3>
                          <div class="">{{ computedData.bp_interest_rate }}</div>
                        </div>
                        <div class="mb-5">
                          <h3 class="font-semibold text-sm">GMI Percent</h3>
                          <div class="">{{ computedData.gmi_percent }}</div>
                        </div>
                        <div class="mb-5">
                          <h3 class="font-semibold text-sm">Loan Difference</h3>
                          <div class="">₱ {{ formatNumber(computedData.loan_difference) }}</div>
                        </div>
                        <div class="mb-5">
                          <h3 class="font-semibold text-sm">Disposable Income</h3>
                          <div class="">₱ {{ formatNumber(computedData.joint_disposable_monthly_income) }}</div>
                        </div>
                        <div class="mb-5">
                          <h3 class="font-semibold text-sm">PV from Disposable Income</h3>
                          <div class="">₱ {{ formatNumber(computedData.present_value_from_monthly_disposable_income) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>
