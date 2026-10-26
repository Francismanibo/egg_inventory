EGG PRODUCT IMAGE FEATURE - INSTALLATION

This package adds product photo uploads for egg products in your existing Laravel app.

FEATURES
- Admin can upload an optional photo while creating an egg product.
- Admin can replace a photo or remove the current photo from the Edit Product page.
- Photos appear in the admin product catalogue, admin inventory stock table, customer homepage, and customer order cards.
- Accepts JPG, JPEG, PNG, and WEBP images up to 4 MB.
- Existing inventory transactions and order handling are retained.
- The inventory Blade page is restored as a complete page and its @foreach syntax is corrected.

INSTALL STEPS (Windows / VS Code)
1. Back up your project folder and database.
2. Extract this ZIP into the ROOT of your Laravel project (the folder that contains artisan). Allow it to merge/overwrite the listed files.
3. In VS Code Terminal at the project root, run:

   php patch_customer_views.php
   php artisan migrate
   php artisan storage:link
   php artisan optimize:clear
   npm run build

   If public/storage already exists as a correct Laravel storage link, you can skip `php artisan storage:link`.
   If `npm run build` reports missing Node modules, run `npm install` first.

4. Start or refresh the Laravel app and sign in as Admin.
5. Open /products, add a product or click Edit on an existing product, then upload a photo and save.
6. Check /inventory, the customer homepage, and /order to see the photo.

WHERE UPLOADS ARE SAVED
Images are stored under storage/app/public/products. The public/storage link lets the browser display them. The image path is saved in egg_products.image_path.

NOTES
- The migration adds a nullable image_path column, so existing products continue to work without a photo.
- Do not run `php artisan migrate:fresh`; it would erase your application data.
- The upload is restricted by server-side validation, not just the browser's file picker.
- The ZIP contains replacement application files and is intended to be applied to your local project. It has not been pushed to GitHub automatically.
