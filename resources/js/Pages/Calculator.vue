<script setup>
import {Head, useForm, usePage} from '@inertiajs/vue3';
import Input from '@/Input/Input.vue';
import DefaultButton from '@/Buttons/DefaultButton.vue';
import Tab from '@/Components/Tab.vue';
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

            // Other Details
            computedData.value.borrower = {}
            computedData.value.borrower.gross_monthly_income = calculated_data.value['borrower']['gross_monthly_income'];
            computedData.value.borrower.regional = calculated_data.value['borrower']['regional'];
            computedData.value.borrower.birthdate = calculated_data.value['borrower']['birthdate'];
            computedData.value.borrower.age = calculated_data.value['borrower']['age'];
            computedData.value.borrower.as_of_date = calculated_data.value['borrower']['as_of_date'];
            computedData.value.borrower.work_area = calculated_data.value['borrower']['work_area'];
            computedData.value.borrower.employment_type = calculated_data.value['borrower']['employment_type'];
            computedData.value.borrower.formatted_age = calculated_data.value['borrower']['formatted_age'];
            computedData.value.borrower.payment_mode = calculated_data.value['borrower']['payment_mode'];
            computedData.value.borrower.maturity_date = calculated_data.value['borrower']['maturity_date'];
            computedData.value.borrower.age_at_maturity_date = calculated_data.value['borrower']['age_at_maturity_date'];
            computedData.value.borrower.lending_institution_alias = calculated_data.value['borrower']['lending_institution_alias'];
            computedData.value.borrower.lending_institution_name = calculated_data.value['borrower']['lending_institution_name'];
            computedData.value.borrower.maximum_term_allowed = calculated_data.value['borrower']['maximum_term_allowed'];
            computedData.value.borrower.repricing_frequency = calculated_data.value['borrower']['repricing_frequency'];
            computedData.value.borrower.interest_rate = calculated_data.value['borrower']['interest_rate'];
            
            computedData.value.property = {}
            computedData.value.property.sku = calculated_data.value['property']['sku'];
            computedData.value.property.market_segment = calculated_data.value['property']['market_segment'];
            computedData.value.property.total_contract_price = calculated_data.value['property']['total_contract_price'];
            computedData.value.property.appraised_value = calculated_data.value['property']['appraised_value'];
            computedData.value.property.default_loanable_value_multiplier = calculated_data.value['property']['default_loanable_value_multiplier'];
            computedData.value.property.loanable_value_multiplier = calculated_data.value['property']['loanable_value_multiplier'];
            computedData.value.property.loanable_value = calculated_data.value['property']['loanable_value'];
            computedData.value.property.disposable_income_requirement_multiplier = calculated_data.value['property']['disposable_income_requirement_multiplier'];
            computedData.value.property.default_disposable_income_requirement_multiplier = calculated_data.value['property']['default_disposable_income_requirement_multiplier'];
            computedData.value.property.work_area = calculated_data.value['property']['work_area'];
            computedData.value.property.development_type = calculated_data.value['property']['development_type'];
            computedData.value.property.housing_type = calculated_data.value['property']['housing_type'];
            computedData.value.property.storeys = calculated_data.value['property']['storeys'];
            computedData.value.property.floor_area = calculated_data.value['property']['floor_area'];
            computedData.value.property.price_ceiling = calculated_data.value['property']['price_ceiling'];
            computedData.value.property.fees = calculated_data.value['property']['fees'];
            computedData.value.property.fee_structure = calculated_data.value['property']['fee_structure'];
            computedData.value.property.selling_price = calculated_data.value['property']['selling_price'];
            computedData.value.property.name = calculated_data.value['property']['name'];
            computedData.value.property.brand = calculated_data.value['property']['brand'];
            computedData.value.property.category = calculated_data.value['property']['category'];
        }
    },
    { immediate: true }
);

const submit = () => {
    form.post(route('match.calculate'), {
        // onFinish: () => form.reset(),
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

const tabs = ref([
    { name: 'Computed', href: '#', current: true },
    { name: 'Other Details', href: '#', current: false },
])

const switchTab = (name) => {
    const updatedTabs = tabs.value.map(tab => 
        tab.name === name ? { ...tab, current: true } : { ...tab, current: false }
    );
    tabs.value = updatedTabs
}

</script>
<template>
    <Head title="Homeful Match Calculator" />
    <div class="w-full min-h-screen px-5 md:px-20 py-10 bg-gray-100">
        <h6>Let's Calculate</h6>
        <h1 class="font-bold text-2xl">Home Match Calculator</h1>
        <form @submit.prevent="submit">

            <div class="flex flex-col-reverse lg:flex-row  gap-4 mt-10">
                <div class="basis-1/2 w-full p-5 bg-white h-fit rounded-xl shadow-xl">
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
                    <Tab 
                        :tabs="tabs"
                        @switch-tab="switchTab"
                    />
                    <template v-if="tabs.find(tab => tab.current).name == 'Computed'">
                        <h2 class="text-base/7 font-semibold text-gray-900 leading-none">{{ tabs.find(tab => tab.current).name  }}</h2>
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
                    </template>
                    <template v-else>
                        <h2 class="text-base/7 font-semibold text-gray-900 leading-none">{{ tabs.find(tab => tab.current).name  }}</h2>
                        <p class="mt-1 text-sm/6 text-gray-600 leading-none">This result has been generated using the HomeMatch Calculator for accurate assessment.</p>
                        <p class="mt-5 font-bold mb-2">Barrower</p>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                            <div class="mb-5">
                                <h3 class="font-semibold text-sm">Gross Monthly Income</h3>
                                <div class="">₱ {{ formatNumber(computedData.borrower?.gross_monthly_income) }}</div>
                            </div>
                            <div class="mb-5">
                                <h3 class="font-semibold text-sm">Regional</h3>
                                <div class=""> {{ computedData.borrower?.regional }}</div>
                            </div>
                            <div class="mb-5">
                                <h3 class="font-semibold text-sm">Birth Date</h3>
                                <div class=""> {{ computedData.borrower?.birthdate }}</div>
                            </div>
                            <div class="mb-5">
                                <h3 class="font-semibold text-sm">Age</h3>
                                <div class=""> {{ computedData.borrower?.age }}</div>
                            </div>
                            <div class="mb-5">
                                <h3 class="font-semibold text-sm">As of</h3>
                                <div class=""> {{ computedData.borrower?.as_of_date }}</div>
                            </div>
                            <div class="mb-5">
                                <h3 class="font-semibold text-sm">Work Area</h3>
                                <div class=""> {{ computedData.borrower?.work_area }}</div>
                            </div>
                            <div class="mb-5">
                                <h3 class="font-semibold text-sm">Employment Type</h3>
                                <div class=""> {{ computedData.borrower?.employment_type }}</div>
                            </div>
                            <div class="mb-5">
                                <h3 class="font-semibold text-sm">Formatted Age</h3>
                                <div class=""> {{ computedData.borrower?.formatted_age }}</div>
                            </div>
                            <div class="mb-5">
                                <h3 class="font-semibold text-sm">Payment Mode</h3>
                                <div class=""> {{ computedData.borrower?.payment_mode }}</div>
                            </div>
                            <div class="mb-5">
                                <h3 class="font-semibold text-sm">Maturity Date</h3>
                                <div class=""> {{ computedData.borrower?.maturity_date }}</div>
                            </div>
                            <div class="mb-5">
                                <h3 class="font-semibold text-sm">Age At Maturity Date</h3>
                                <div class=""> {{ computedData.borrower?.age_at_maturity_date }}</div>
                            </div>
                            <div class="mb-5">
                                <h3 class="font-semibold text-sm">Lending Institution Alias</h3>
                                <div class=""> {{ computedData.borrower?.lending_institution_alias }}</div>
                            </div>
                            <div class="mb-5">
                                <h3 class="font-semibold text-sm">Lending Institution Name</h3>
                                <div class=""> {{ computedData.borrower?.lending_institution_name }}</div>
                            </div>
                            <div class="mb-5">
                                <h3 class="font-semibold text-sm">Maximum Term Allowed</h3>
                                <div class=""> {{ computedData.borrower?.maximum_term_allowed }}</div>
                            </div>
                            <div class="mb-5">
                                <h3 class="font-semibold text-sm">Repricing Frequency</h3>
                                <div class=""> {{ computedData.borrower?.repricing_frequency }}</div>
                            </div>
                            <div class="mb-5">
                                <h3 class="font-semibold text-sm">Interest_Rate</h3>
                                <div class=""> {{ computedData.borrower?.interest_rate }}</div>
                            </div>
                        </div>

                        <p class="mt-5 font-bold mb-2">Property</p>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                            <div class="mb-5">
                                <h3 class="font-semibold text-sm">SKU</h3>
                                <div class=""> {{ computedData.property?.sku }}</div>
                            </div>
                            <div class="mb-5">
                                <h3 class="font-semibold text-sm">Market Segment</h3>
                                <div class=""> {{ computedData.property?.market_segment }}</div>
                            </div>
                            <div class="mb-5">
                                <h3 class="font-semibold text-sm">Total Contract Price</h3>
                                <div class="">₱ {{ formatNumber(computedData.property?.total_contract_price) }}</div>
                            </div>
                            <div class="mb-5">
                                <h3 class="font-semibold text-sm">Appraised Value</h3>
                                <div class="">₱ {{ formatNumber(computedData.property?.appraised_value) }}</div>
                            </div>
                            <div class="mb-5">
                                <h3 class="font-semibold text-sm">Default Loanable Value Multiplier</h3>
                                <div class=""> {{ computedData.property?.default_loanable_value_multiplier }}</div>
                            </div>
                            <div class="mb-5">
                                <h3 class="font-semibold text-sm">Loanable Value Multiplier</h3>
                                <div class=""> {{ computedData.property?.loanable_value_multiplier }}</div>
                            </div>
                            <div class="mb-5">
                                <h3 class="font-semibold text-sm">Loanable Value</h3>
                                <div class=""> {{ computedData.property?.loanable_value }}</div>
                            </div>
                            <div class="mb-5">
                                <h3 class="font-semibold text-sm">Disposable Income Requirement Multiplier</h3>
                                <div class=""> {{ computedData.property?.disposable_income_requirement_multiplier }}</div>
                            </div>
                            <div class="mb-5">
                                <h3 class="font-semibold text-sm">Default Disposable Income Requirement Multiplier</h3>
                                <div class=""> {{ computedData.property?.default_disposable_income_requirement_multiplier }}</div>
                            </div>
                            <div class="mb-5">
                                <h3 class="font-semibold text-sm">Work Area</h3>
                                <div class=""> {{ computedData.property?.work_area }}</div>
                            </div>
                            <div class="mb-5">
                                <h3 class="font-semibold text-sm">Development Type</h3>
                                <div class=""> {{ computedData.property?.development_type }}</div>
                            </div>
                            <div class="mb-5">
                                <h3 class="font-semibold text-sm">Housing Type</h3>
                                <div class=""> {{ computedData.property?.housing_type }}</div>
                            </div>
                            <div class="mb-5">
                                <h3 class="font-semibold text-sm">Storeys</h3>
                                <div class=""> {{ computedData.property?.storeys }}</div>
                            </div>
                            <div class="mb-5">
                                <h3 class="font-semibold text-sm">Floor Area</h3>
                                <div class=""> {{ computedData.property?.floor_area }}</div>
                            </div>
                            <div class="mb-5">
                                <h3 class="font-semibold text-sm">Price Ceiling</h3>
                                <div class="">₱ {{ formatNumber(computedData.property?.price_ceiling) }}</div>
                            </div>
                            <div class="mb-5">
                                <h3 class="font-semibold text-sm">Fees</h3>
                                <div class="">₱ {{ formatNumber(computedData.propertyrfees) }}</div>
                            </div>
                            <div class="mb-5">
                                <h3 class="font-semibold text-sm">Fee Structure</h3>
                                <div class=""> {{ computedData.property?.fee_structure }}</div>
                            </div>
                            <div class="mb-5">
                                <h3 class="font-semibold text-sm">Selling Price</h3>
                                <div class="">₱ {{ formatNumber(computedData.property?.selling_price) }}</div>
                            </div>
                            <div class="mb-5">
                                <h3 class="font-semibold text-sm">Name</h3>
                                <div class=""> {{ computedData.propertyrname }}</div>
                            </div>
                            <div class="mb-5">
                                <h3 class="font-semibold text-sm">Brand</h3>
                                <div class=""> {{ computedData.property?.brand }}</div>
                            </div>
                            <div class="mb-5">
                                <h3 class="font-semibold text-sm">Category</h3>
                                <div class=""> {{ computedData.property?.category }}</div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </form>
    </div>
</template>
