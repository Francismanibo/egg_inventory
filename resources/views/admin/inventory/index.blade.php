@extends('layouts.admin') 
@section('title','Inventory') 
@section('page-title','Inventory') 
@section('page-subtitle','Add, deduct, adjust, and view available stock') 
@section('content')

<div class="card p-7">
    <h2 class="text-xl font-bold">Inventory Transaction</h2>
    <form method="POST" action="{{route('admin.inventory.store')}}" class="mt-5 grid gap-5 md:grid-cols-2">
        @csrf
        <div>
            <label class="text-sm text-slate-500">Product</label>
            <select class="input mt-1" name="product_id">
                @foreach($products as$p)
                    <option value="{{$p->id}}">{{$p->product_name}}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-sm text-slate-500">Transaction Type</label>
            <select class="input mt-1" name="transaction_type">
                <option value="addition">Addition</option>
                <option value="deduction">Deduction</option>
                <option value="adjustment">Adjustment</option>
            </select>
        </div>
        <div>
            <label class="text-sm text-slate-500">Quantity</label>
            <input class="input mt-1" type="number" name="quantity" min="1" required>
        </div>
        <div>
            <label class="text-sm text-slate-500">Adjustment Sign</label>
            <select class="input mt-1" name="adjustment_sign">
                <option value="1">Increase (+)</option>
                <option value="-1">Decrease (-)</option>
            </select>
        </div>
        <div>
            <label class="text-sm text-slate-500">Reason</