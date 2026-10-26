<?php

/**
 * Run from the Laravel project root after extracting this feature package:
 * php patch_customer_views.php
 */

$homePath = __DIR__ . '/resources/views/customer/home.blade.php';
$orderPath = __DIR__ . '/resources/views/customer/order.blade.php';

if (! is_file($homePath) || ! is_file($orderPath)) {
    fwrite(STDERR, "Could not find the customer Blade views. Extract this package into your Laravel project root.\n");
    exit(1);
}

$home = file_get_contents($homePath);
$updatedHome = str_replace(
    ['!empty($product->image)', 'asset(\'storage/\' . $product->image)'],
    ['!empty($product->image_path)', 'asset(\'storage/\' . $product->image_path)'],
    $home,
);

if ($updatedHome !== $home) {
    file_put_contents($homePath, $updatedHome);
    echo "Updated customer homepage to use product image_path.\n";
} elseif (str_contains($home, '$product->image_path')) {
    echo "Customer homepage already uses image_path; no change needed.\n";
} else {
    fwrite(STDERR, "Could not find the expected product image snippet in customer/home.blade.php. Please check the customer homepage manually.\n");
}

$order = file_get_contents($orderPath);
$oldOrderImage = '<div class="flex h-28 items-center justify-center rounded-2xl bg-[#f8fbff] text-5xl">🥚</div>';
$newOrderImage = <<<'BLADE'
<div class="h-40 overflow-hidden rounded-2xl border border-[#e8f1ff] bg-[#f8fbff]">
                                    <img
                                        src="{{ $product->image_path ? asset('storage/' . $product->image_path) : asset('images/egg-hero.jpg') }}"
                                        alt="{{ $product->product_name }}"
                                        class="h-full w-full object-cover"
                                        loading="lazy"
                                    >
                                </div>
BLADE;

if (str_contains($order, $oldOrderImage)) {
    $order = str_replace($oldOrderImage, $newOrderImage, $order);
    file_put_contents($orderPath, $order);
    echo "Updated customer order cards to show each product photo.\n";
} elseif (str_contains($order, '$product->image_path')) {
    echo "Customer order page already uses product images; no change needed.\n";
} else {
    fwrite(STDERR, "Could not find the expected egg placeholder in customer/order.blade.php. Please update the product card image manually.\n");
}
