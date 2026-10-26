@extends('layouts.customer')

@section('title', 'My Orders')

@section('content')
    <div class="mx-auto max-w-5xl px-5 py-10 sm:px-8 sm:py-14">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-sm font-bold uppercase tracking-wider text-[#2878d4]">Customer account</p>
                <h1 class="mt-2 text-4xl font-bold">My Orders</h1>
                <p class="mt-3 max-w-2xl leading-7 text-slate-600">
                    View your order history and current delivery status. Refresh this page to see the latest update after the administrator processes an order.
                </p>
            </div>
            <a href="{{ route('customer.order') }}" class="btn-primary inline-flex">Order Eggs</a>
        </div>

        <div class="mt-7 grid gap-3 sm:grid-cols-3">
            @foreach ([
                ['Pending', $counts['pending'], 'bg-blue-50 text-blue-700'],
                ['Out for Delivery', $counts['outgoing'], 'bg-amber-50 text-amber-800'],
                ['Completed', $counts['done'], 'bg-emerald-50 text-emerald-700'],
            ] as [$label, $count, $classes])
                <div class="card p-5">
                    <p class="text-sm text-slate-500">{{ $label }} orders</p>
                    <p class="mt-2 text-3xl font-bold">{{ $count }}</p>
                    <span class="mt-2 inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $classes }}">{{ $label }}</span>
                </div>
            @endforeach
        </div>

        <div class="mt-7 space-y-5">
            @forelse ($orders as $order)
                @php
                    $statusIndex = array_search($order->status, ['pending', 'outgoing', 'done'], true);
                    $statusLabel = match ($order->status) {
                        'pending' => 'Pending Review',
                        'outgoing' => 'Out for Delivery',
                        'done' => 'Delivered / Completed',
                        'cancelled' => 'Cancelled',
                        default => ucfirst($order->status),
                    };
                @endphp
                <article class="card overflow-hidden">
                    <div class="flex flex-wrap items-start justify-between gap-4 border-b border-[#d8e5f3] p-5 sm:p-6">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Order reference</p>
                            <h2 class="mt-1 break-all text-lg font-bold">{{ $order->order_number }}</h2>
                            <p class="mt-1 text-sm text-slate-500">Placed {{ $order->order_date->format('M d, Y · h:i A') }}</p>
                        </div>
                        <div class="text-right">
                            @if ($order->status === 'pending')
                                <span class="badge-pending">{{ $statusLabel }}</span>
                            @elseif ($order->status === 'outgoing')
                                <span class="badge-outgoing">{{ $statusLabel }}</span>
                            @elseif ($order->status === 'done')
                                <span class="badge-done">{{ $statusLabel }}</span>
                            @else
                                <span class="inline-flex rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700">{{ $statusLabel }}</span>
                            @endif
                            <p class="mt-2 text-xl font-bold">₱{{ number_format((float) $order->estimated_total, 2) }}</p>
                        </div>
                    </div>

                    <div class="p-5 sm:p-6">
                        <div class="space-y-2">
                            @foreach ($order->items as $item)
                                <div class="flex flex-wrap items-center justify-between gap-2 text-sm">
                                    <span class="text-slate-600">{{ $item->product?->product_name ?? 'Product no longer available' }} × {{ $item->quantity }}</span>
                                    <span class="font-semibold">₱{{ number_format((int) $item->quantity * (float) $item->unit_price, 2) }}</span>
                                </div>
                            @endforeach
                        </div>

                        @if ($order->status !== 'cancelled')
                            <div class="mt-6 grid grid-cols-3 gap-2" aria-label="Order progress">
                                @foreach ([['pending', 'Order Received'], ['outgoing', 'Out for Delivery'], ['done', 'Completed']] as $index => $step)
                                    @php $isComplete = $statusIndex !== false && $statusIndex >= $index; @endphp
                                    <div>
                                        <div class="h-1.5 rounded-full {{ $isComplete ? 'bg-[#2878d4]' : 'bg-slate-200' }}"></div>
                                        <p class="mt-2 text-xs font-semibold {{ $isComplete ? 'text-[#163765]' : 'text-slate-400' }}">{{ $step[1] }}</p>
                                    </div>
                                @endforeach
                            </div>
                            <p class="mt-4 rounded-xl {{ $order->status === 'outgoing' ? 'border border-amber-200 bg-amber-50 text-amber-900' : ($order->status === 'done' ? 'border border-emerald-200 bg-emerald-50 text-emerald-900' : 'border border-blue-200 bg-blue-50 text-blue-900') }} p-3 text-sm leading-6">
                                @if ($order->status === 'pending')
                                    Your order has been received and is waiting for admin approval.
                                @elseif ($order->status === 'outgoing')
                                    Your order was accepted by the administrator and is now <strong>Out for Delivery</strong>.
                                @else
                                    Your order has been marked as <strong>Delivered / Completed</strong>.
                                @endif
                            </p>
                        @else
                            <p class="mt-4 rounded-xl border border-red-200 bg-red-50 p-3 text-sm leading-6 text-red-800">This order was cancelled. Its reserved stock has been released and is not being delivered.</p>
                        @endif

                        <div class="mt-5 flex flex-wrap justify-end gap-3">
                            @if ($order->status !== 'cancelled')
                                <form method="POST" action="{{ route('customer.track') }}">
                                    @csrf
                                    <input type="hidden" name="order_number" value="{{ $order->order_number }}">
                                    <button type="submit" class="btn-secondary">View Tracking Details</button>
                                </form>
                            @endif
                            <a href="{{ route('customer.order') }}" class="btn-secondary inline-flex">Order Again</a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="card p-10 text-center sm:p-14">
                    <div class="text-5xl">📦</div>
                    <h2 class="mt-4 text-xl font-bold">No orders yet</h2>
                    <p class="mt-2 text-slate-500">Orders you place using this customer account will appear here, including status updates from the administrator.</p>
                    <a href="{{ route('customer.order') }}" class="btn-primary mt-6 inline-flex">Browse Eggs</a>
                </div>
            @endforelse
        </div>

        <div class="mt-6">{{ $orders->links() }}</div>
    </div>
@endsection
