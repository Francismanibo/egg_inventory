@extends('layouts.customer')

@section('title', 'Order Submitted')

@section('content')
    <div class="mx-auto flex min-h-[65vh] max-w-7xl items-center justify-center px-5 py-12">
        <section class="card w-full max-w-3xl p-7 text-center sm:p-10">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-[#e8f1ff] text-4xl font-bold text-[#2878d4]">✓</div>
            <h1 class="mt-6 text-3xl font-bold">Order Request Submitted</h1>
            <p class="mt-3 leading-7 text-slate-600">Your request has been received. It will remain Pending until the administrator reviews and accepts it.</p>

            <div class="mt-8 rounded-2xl border border-[#d8e5f3] bg-[#f8fbff] p-5 text-left sm:p-6">
                <p class="text-sm text-slate-500">Your order reference number</p>
                <p class="mt-1 break-all text-2xl font-bold text-[#163765]">{{ $order->order_number }}</p>
                <div class="mt-5 space-y-3 border-t border-[#d8e5f3] pt-4 text-sm">
                    <div class="flex justify-between gap-4"><span class="text-slate-500">Submitted</span><strong>{{ $order->order_date->format('M d, Y h:i A') }}</strong></div>
                    <div class="flex justify-between gap-4"><span class="text-slate-500">Current status</span><span class="badge-pending">Pending</span></div>
                    <div class="flex justify-between gap-4"><span class="text-slate-500">Estimated total</span><strong>₱{{ number_format((float) $order->estimated_total, 2) }}</strong></div>
                </div>
            </div>

            <p class="mt-5 text-sm leading-6 text-slate-600">Save your order number. To check progress later, sign in to this account and use the reference number below. Only orders belonging to this account can be viewed.</p>
            <div class="mt-7 flex flex-wrap justify-center gap-3">
                <form method="POST" action="{{ route('customer.track') }}">
                    @csrf
                    <input type="hidden" name="order_number" value="{{ $order->order_number }}">
                    <button type="submit" class="btn-primary">Track This Order</button>
                </form>
                <a href="{{ route('customer.order') }}" class="btn-secondary inline-flex">Place Another Order</a>
            </div>
        </section>
    </div>
@endsection
