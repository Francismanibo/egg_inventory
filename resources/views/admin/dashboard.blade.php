@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Overview of egg production and inventory')

@section('content')
    <section class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
        <article class="card p-6">
            <p class="text-sm font-medium text-slate-500">Today's Production</p>
            <p class="mt-2 text-4xl font-bold text-[#163765]">{{ number_format($todayProduction) }}</p>
            <p class="mt-2 text-sm text-slate-400">Eggs recorded for today</p>
        </article>

        <article class="card p-6">
            <p class="text-sm font-medium text-slate-500">Current Inventory</p>
            <p class="mt-2 text-4xl font-bold text-[#163765]">{{ number_format($currentInventory) }}</p>
            <p class="mt-2 text-sm text-slate-400">Available stock from transactions</p>
        </article>

        <article class="card p-6">
            <p class="text-sm font-medium text-slate-500">Low-Stock Products</p>
            <p class="mt-2 text-4xl font-bold {{ $lowStockCount > 0 ? 'text-amber-600' : 'text-[#163765]' }}">{{ number_format($lowStockCount) }}</p>
            <p class="mt-2 text-sm text-slate-400">Products at or below reorder level</p>
        </article>
    </section>

    <section class="card mt-6 overflow-hidden">
        <div class="border-b border-[#d8e5f3] px-6 py-5">
            <h2 class="text-xl font-bold">Recent Production Records</h2>
            <p class="mt-1 text-sm text-slate-500">A quick view of the most recent recorded production.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-[#f8fbff] text-slate-500">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Date</th>
                        <th class="px-6 py-4 font-semibold">Product</th>
                        <th class="px-6 py-4 font-semibold">Produced</th>
                        <th class="px-6 py-4 font-semibold">Saleable</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($productionSummary as $record)
                        <tr class="border-t border-[#eef3f8]">
                            <td class="px-6 py-4">{{ $record->production_date->format('M d, Y') }}</td>
                            <td class="px-6 py-4 font-semibold">{{ $record->product->product_name }}</td>
                            <td class="px-6 py-4">{{ number_format($record->quantity_produced) }}</td>
                            <td class="px-6 py-4">{{ number_format($record->saleable_quantity) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-slate-500">No production records yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
