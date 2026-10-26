@extends('layouts.admin') 
@section('title','Production') 
@section('page-title','Production') 
@section('page-subtitle','Record daily or periodic egg production') 
@section('content')

<div class="card p-7">
    <h2 class="text-xl font-bold">Production Entry</h2>
    <form method="POST" action="{{route('admin.production.store')}}" class="mt-5 grid gap-5 md:grid-cols-2">
        @csrf
        <div>
            <label class="text-sm text-slate-500">Production Date</label>
            <input class="input mt-1" type="date" name="production_date" value="{{old('production_date',now()->toDateString())}}" required>
        </div>
        <div>
            <label class="text-sm text-slate-500">Egg Product</label>
            <select class="input mt-1" name="product_id" required>
                @foreach($products as $p)
                    <option value="{{$p->id}}">{{$p->product_name}}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-sm text-slate-500">Quantity Produced</label>
            <input class="input mt-1" type="number" name="quantity_produced" min="0" required>
        </div>
        <div>
            <label class="text-sm text-slate-500">Saleable Quantity</label>
            <input class="input mt-1" type="number" name="saleable_quantity" min="0" required>
        </div>
        <div>
            <label class="text-sm text-slate-500">Damaged Quantity</label>
            <input class="input mt-1" type="number" name="damaged_quantity" min="0" required>
        </div>
        <div>
            <label class="text-sm text-slate-500">Rejected Quantity</label>
            <input class="input mt-1" type="number" name="rejected_quantity" min="0" required>
        </div>
        <div class="md:col-span-2">
            <label class="text-sm text-slate-500">Notes</label>
            <textarea class="input mt-1" name="notes" rows="3"></textarea>
        </div>
        <div class="md:col-span-2">
            <button class="btn-primary">Save Record</button>
        </div>
    </form>
</div>

<div class="card mt-6 overflow-hidden">
    <div class="border-b border-[#d8e5f3] px-6 py-5">
        <h2 class="text-xl font-bold">Production Records</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-[#f8fbff] text-left text-slate-500">
                <tr>
                    <th class="px-6 py-4">Date</th>
                    <th class="px-6 py-4">Product</th>
                    <th class="px-6 py-4">Produced</th>
                    <th class="px-6 py-4">Saleable</th>
                    <th class="px-6 py-4">Recorded By</th>
                </tr>
            </thead>
            <tbody>
                @foreach($records as $r)
                    <tr class="border-t border-[#eef3f8]">
                        <td class="px-6 py-4">{{$r->production_date->format('m/d/Y')}}</td>
                        <td class="px-6 py-4">{{$r->product->product_name}}</td>
                        <td class="px-6 py-4">{{$r->quantity_produced}}</td>
                        <td class="px-6 py-4">{{$r->saleable_quantity}}</td>
                        <td class="px-6 py-4">{{$r->recorder->name}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-5">
        {{$records->links()}}
    </div>
</div>

@endsection