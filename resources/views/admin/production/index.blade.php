
@extends('layouts.admin')

@section('title', 'Production')
@section('page-title', 'Production')
@section('page-subtitle', 'Record daily or periodic egg production')

@section('content')

    {{-- PRODUCTION ENTRY --}}
    <section class="card p-6 sm:p-7">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-[#163765]">
                Production Entry
            </h2>

            <p class="mt-2 text-sm leading-6 text-slate-500">
                Record the total eggs produced and classify them as saleable,
                damaged, or rejected. The classified total must not exceed
                the total quantity produced.
            </p>
        </div>

        @if ($products->isEmpty())

            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                No active egg products are available. Add or activate an egg
                product before recording production.
            </div>

        @else

            <form
                id="production-form"
                method="POST"
                action="{{ route('admin.production.store') }}"
                class="grid gap-5 md:grid-cols-2"
            >
                @csrf

                {{-- PRODUCTION DATE --}}
                <div>
                    <label
                        for="production_date"
                        class="mb-1 block text-sm font-semibold text-[#163765]"
                    >
                        Production Date
                    </label>

                    <input
                        id="production_date"
                        class="input"
                        type="date"
                        name="production_date"
                        value="{{ old('production_date', now()->toDateString()) }}"
                        required
                    >

                    @error('production_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- EGG PRODUCT --}}
                <div>
                    <label
                        for="product_id"
                        class="mb-1 block text-sm font-semibold text-[#163765]"
                    >
                        Egg Product
                    </label>

                    <select
                        id="product_id"
                        class="input"
                        name="product_id"
                        required
                    >
                        @foreach ($products as $product)
                            <option
                                value="{{ $product->id }}"
                                @selected(
                                    (string) old('product_id', $products->first()->id)
                                    === (string) $product->id
                                )
                            >
                                {{ $product->product_name }}
                            </option>
                        @endforeach
                    </select>

                    @error('product_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- TOTAL QUANTITY PRODUCED --}}
                <div>
                    <label
                        for="quantity_produced"
                        class="mb-1 block text-sm font-semibold text-[#163765]"
                    >
                        Quantity Produced
                    </label>

                    <input
                        id="quantity_produced"
                        class="input"
                        type="number"
                        name="quantity_produced"
                        min="0"
                        step="1"
                        value="{{ old('quantity_produced') }}"
                        placeholder="Enter total eggs produced"
                        required
                    >

                    <p class="mt-1 text-xs text-slate-500">
                        Total eggs collected during this production period.
                    </p>

                    @error('quantity_produced')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- SALEABLE QUANTITY --}}
                <div>
                    <label
                        for="saleable_quantity"
                        class="mb-1 block text-sm font-semibold text-[#163765]"
                    >
                        Saleable Quantity
                    </label>

                    <input
                        id="saleable_quantity"
                        class="input classification-input"
                        type="number"
                        name="saleable_quantity"
                        min="0"
                        step="1"
                        value="{{ old('saleable_quantity') }}"
                        placeholder="Enter saleable eggs"
                        required
                    >

                    <p class="mt-1 text-xs text-slate-500">
                        Eggs suitable for sale and inventory.
                    </p>

                    @error('saleable_quantity')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- DAMAGED QUANTITY --}}
                <div>
                    <label
                        for="damaged_quantity"
                        class="mb-1 block text-sm font-semibold text-[#163765]"
                    >
                        Damaged Quantity
                    </label>

                    <input
                        id="damaged_quantity"
                        class="input classification-input"
                        type="number"
                        name="damaged_quantity"
                        min="0"
                        step="1"
                        value="{{ old('damaged_quantity') }}"
                        placeholder="Enter damaged eggs"
                        required
                    >

                    <p class="mt-1 text-xs text-slate-500">
                        Eggs cracked or damaged during collection.
                    </p>

                    @error('damaged_quantity')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- REJECTED QUANTITY --}}
                <div>
                    <label
                        for="rejected_quantity"
                        class="mb-1 block text-sm font-semibold text-[#163765]"
                    >
                        Rejected Quantity
                    </label>

                    <input
                        id="rejected_quantity"
                        class="input classification-input"
                        type="number"
                        name="rejected_quantity"
                        min="0"
                        step="1"
                        value="{{ old('rejected_quantity') }}"
                        placeholder="Enter rejected eggs"
                        required
                    >

                    <p class="mt-1 text-xs text-slate-500">
                        Eggs classified as unsuitable for sale.
                    </p>

                    @error('rejected_quantity')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- LIVE CLASSIFICATION SUMMARY --}}
                <div class="md:col-span-2">
                    <div
                        id="classification-summary"
                        class="rounded-xl border border-[#d8e5f3] bg-[#f8fbff] p-4"
                        aria-live="polite"
                    >
                        <p class="text-sm font-semibold text-[#163765]">
                            Production Classification Summary
                        </p>

                        <div class="mt-3 grid gap-3 sm:grid-cols-3">
                            <div>
                                <p class="text-xs text-slate-500">Total Produced</p>
                                <p id="summary-produced" class="mt-1 text-lg font-bold text-[#163765]">0</p>
                            </div>

                            <div>
                                <p class="text-xs text-slate-500">Classified Total</p>
                                <p id="summary-classified" class="mt-1 text-lg font-bold text-[#163765]">0</p>
                            </div>

                            <div>
                                <p class="text-xs text-slate-500">Unclassified Difference</p>
                                <p id="summary-difference" class="mt-1 text-lg font-bold text-[#163765]">0</p>
                            </div>
                        </div>
                    </div>

                    <div
                        id="classification-warning"
                        class="mt-3 hidden rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700"
                        role="alert"
                    >
                        The saleable, damaged, and rejected quantities combined
                        cannot exceed the total quantity produced. Please correct
                        the quantities before saving.
                    </div>
                </div>

                {{-- NOTES --}}
                <div class="md:col-span-2">
                    <label
                        for="notes"
                        class="mb-1 block text-sm font-semibold text-[#163765]"
                    >
                        Notes (Optional)
                    </label>

                    <textarea
                        id="notes"
                        class="input"
                        name="notes"
                        rows="3"
                        placeholder="Additional production details..."
                    >{{ old('notes') }}</textarea>

                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- ACTIONS --}}
                <div class="flex flex-wrap items-center gap-3 md:col-span-2">
                    <button
                        id="save-production"
                        type="submit"
                        class="btn-primary justify-center"
                    >
                        Save Production Record
                    </button>

                    <button
                        type="reset"
                        class="btn-secondary"
                    >
                        Clear Form
                    </button>
                </div>
            </form>

        @endif
    </section>


    {{-- PRODUCTION RECORDS --}}
    <section class="card mt-6 overflow-hidden">

        <div class="border-b border-[#d8e5f3] px-6 py-5">
            <h2 class="text-xl font-bold text-[#163765]">
                Production Records
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Review recorded production and egg classifications.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">

                <thead class="bg-[#f8fbff] text-slate-500">
                    <tr>
                        <th class="px-5 py-4 font-semibold">Date</th>
                        <th class="px-5 py-4 font-semibold">Product</th>
                        <th class="px-5 py-4 font-semibold">Produced</th>
                        <th class="px-5 py-4 font-semibold">Saleable</th>
                        <th class="px-5 py-4 font-semibold">Damaged</th>
                        <th class="px-5 py-4 font-semibold">Rejected</th>
                        <th class="px-5 py-4 font-semibold">Recorded By</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($records as $record)
                        <tr class="border-t border-[#eef3f8]">

                            <td class="px-5 py-4 whitespace-nowrap">
                                {{ $record->production_date->format('M d, Y') }}
                            </td>

                            <td class="px-5 py-4 font-semibold text-[#163765]">
                                {{ $record->product->product_name ?? 'Unknown Product' }}
                            </td>

                            <td class="px-5 py-4 font-medium">
                                {{ number_format($record->quantity_produced) }}
                            </td>

                            <td class="px-5 py-4 text-emerald-700">
                                {{ number_format($record->saleable_quantity) }}
                            </td>

                            <td class="px-5 py-4 text-amber-700">
                                {{ number_format($record->damaged_quantity) }}
                            </td>

                            <td class="px-5 py-4 text-red-600">
                                {{ number_format($record->rejected_quantity) }}
                            </td>

                            <td class="px-5 py-4">
                                {{ $record->recorder->name ?? 'Unknown User' }}
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td
                                colspan="7"
                                class="px-5 py-10 text-center text-slate-500"
                            >
                                No production records have been recorded yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

        <div class="border-t border-[#eef3f8] p-5">
            {{ $records->links() }}
        </div>

    </section>


    {{-- LIVE VALIDATION --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('production-form');

            if (!form) {
                return;
            }

            const producedInput = document.getElementById('quantity_produced');
            const saleableInput = document.getElementById('saleable_quantity');
            const damagedInput = document.getElementById('damaged_quantity');
            const rejectedInput = document.getElementById('rejected_quantity');

            const warning = document.getElementById('classification-warning');
            const saveButton = document.getElementById('save-production');

            const summaryProduced = document.getElementById('summary-produced');
            const summaryClassified = document.getElementById('summary-classified');
            const summaryDifference = document.getElementById('summary-difference');

            const classificationInputs = [
                saleableInput,
                damagedInput,
                rejectedInput
            ];

            function readQuantity(input) {
                if (input.value.trim() === '') {
                    return 0;
                }

                const value = Number(input.value);

                return Number.isFinite(value) && value >= 0 ? value : 0;
            }

            function updateClassification() {
                const produced = readQuantity(producedInput);

                const saleable = readQuantity(saleableInput);
                const damaged = readQuantity(damagedInput);
                const rejected = readQuantity(rejectedInput);

                const classified = saleable + damaged + rejected;
                const difference = produced - classified;

                const exceedsProduction =
                    producedInput.value.trim() !== ''
                    && classified > produced;

                summaryProduced.textContent = produced.toLocaleString();
                summaryClassified.textContent = classified.toLocaleString();
                summaryDifference.textContent = difference.toLocaleString();

                warning.classList.toggle('hidden', !exceedsProduction);

                summaryDifference.classList.toggle(
                    'text-red-600',
                    exceedsProduction
                );

                summaryDifference.classList.toggle(
                    'text-[#163765]',
                    !exceedsProduction
                );

                saveButton.disabled = exceedsProduction;

                saveButton.classList.toggle(
                    'cursor-not-allowed',
                    exceedsProduction
                );

                saveButton.classList.toggle(
                    'opacity-50',
                    exceedsProduction
                );
            }

            [producedInput, ...classificationInputs].forEach(function (input) {
                input.addEventListener('input', updateClassification);
            });

            form.addEventListener('reset', function () {
                // Run after the browser has reset the input values.
                setTimeout(updateClassification, 0);
            });

            form.addEventListener('submit', function (event) {
                const produced = readQuantity(producedInput);

                const classified = classificationInputs.reduce(
                    (total, input) => total + readQuantity(input),
                    0
                );

                if (
                    producedInput.value.trim() === ''
                    || classified > produced
                ) {
                    event.preventDefault();
                    updateClassification();

                    if (classified > produced) {
                        warning.classList.remove('hidden');
                    }
                }
            });

            updateClassification();
        });
    </script>

@endsection
