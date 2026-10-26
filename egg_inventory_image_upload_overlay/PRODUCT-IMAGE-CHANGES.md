# Product Image Upload Changes

- Added nullable `image_path` to `egg_products` using a guarded migration.
- Added image-path mass assignment to `EggProduct`.
- Updated `ProductController` to validate image files, store them on Laravel's `public` disk, replace old images when updated, and allow Admin to remove an image.
- Added upload fields and client-side previews to Add Product and Edit Product.
- Added product thumbnail to the Admin product list.
- Added a shared `partials.product-image` Blade view, then used it in customer product cards and the Order Eggs page.
- The landing page hero also displays the first product's uploaded image when available.
- Supported extensions: JPG/JPEG, PNG, WEBP. Maximum: 4096 KB.
