# Installation: Product Image Upload

1. Back up your current project and database.
2. Extract this ZIP into the existing Laravel project folder and merge/replace the listed files. This is an overlay, not a complete Laravel project.
3. In a terminal opened at the project root, run:

   ```powershell
   php artisan migrate
   php artisan storage:link
   php artisan optimize:clear
   ```

4. Run the app as usual, sign in as Admin, and open `/products`.
5. Upload a clear egg-product photo while adding a product, or open Edit on an existing product and upload/replace its photo. The photo preview appears before you save.
6. Open the customer homepage or `/order` to see the photos.

The migration only adds a nullable `image_path` column to `egg_products`. Existing products and stock records are preserved. Do not run `php artisan migrate:fresh`.

## If an image does not appear

- Confirm `php artisan storage:link` completed successfully.
- Confirm the app is using the expected project folder and database.
- Upload a JPG, PNG, or WEBP file no larger than 4 MB.
- Check that `storage/app/public/products` exists after uploading. Laravel creates the subfolder automatically on first upload.
