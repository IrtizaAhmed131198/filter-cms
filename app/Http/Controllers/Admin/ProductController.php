<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Attribute;
use App\Models\ProductAttribute;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreProductRequest;
use App\Traits\FileUploadTrait;

class ProductController extends Controller
{
    /**
     * PRODUCT LIST PAGE
     */
    public function index()
    {
        return view('admin.product.index');
    }

    /**
     * DATATABLE LISTING
     */
    public function getData(Request $request)
    {
        $products = Product::with('category', 'subCategory')
            ->orderByDesc('id')
            ->get();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('created_at', [
                $request->from_date . ' 00:00:00',
                $request->to_date . ' 23:59:59'
            ]);
        }

        return datatables()->of($products)
            ->addColumn('category', fn ($row) => $row->category->name ?? '-')
            ->addColumn('sub_category', fn ($row) => $row->subCategory->name ?? '-')
            ->addColumn('image', function ($row) {
                return '<img src="'.asset($row->image).'" width="120">';
            })
            ->addColumn('status', function ($row) {
                $checked = $row->status ? 'checked' : '';
                return '
                    <label class="switch">
                        <input type="checkbox" class="toggleBannerStatus" data-id="' . $row->id . '" ' . $checked . '>
                        <span class="slider round" title="Click to toggle status"></span>
                    </label>
                ';
            })
            ->addColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->format('d M, Y h:i A') : '-';
            })
            ->addColumn('action', function ($row) {
                $actions = '';
                if (auth()->user()->hasPermission('edit_product')) {
                    $actions .= '<a href="' . route('admin.product.edit', $row->id) . '"
                                    class="btn btn-sm btn-info" title="Edit User">
                                    <i class="la la-pencil"></i>
                                </a> ';
                }
                if (auth()->user()->hasPermission('delete_product')) {
                    $actions .= '<button class="btn btn-sm btn-danger deleteProduct"
                                    data-id="' . $row->id . '" title="Delete User">
                                    <i class="la la-trash"></i>
                                </button>';
                }
                return $actions ?: '<span class="text-muted">No actions</span>';
            })
            ->rawColumns(['image', 'status', 'action'])
            ->make(true);
    }

    /**
     * CREATE PAGE
     */
    public function create()
    {
        return view('admin.product.create', [
            'categories' => Category::where('status', 1)->get(),
            'attributes' => Attribute::with('values')->where('status', 1)->get(),
        ]);
    }

    /**
     * STORE PRODUCT
     */
    public function store(StoreProductRequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();

            // Generate slug
            $data['slug'] = Str::slug($data['name']);
            $data['created_by'] = auth()->id();

            // -------------------------
            // CREATE PRODUCT
            $product = Product::create($data);

            // -------------------------
            // SAVE PRIMARY IMAGE
            // -------------------------
            if ($request->hasFile('image')) {
                $primaryImagePath = $this->uploadFile(
                    $request->file('image'),
                    'uploads/products/',
                    'product'
                );

                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $primaryImagePath,
                    'is_primary' => 1,
                ]);
            }

            // -------------------------
            // SAVE GALLERY IMAGES
            // -------------------------
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $galleryFile) {
                    $galleryPath = $this->uploadFile(
                        $galleryFile,
                        'uploads/products/',
                        'gallery'
                    );

                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $galleryPath,
                        'is_primary' => 0,
                    ]);
                }
            }

            // -------------------------
            // FALLBACK: MAKE FIRST IMAGE PRIMARY
            // -------------------------
            if (!$product->images()->where('is_primary', 1)->exists()) {
                $firstImage = $product->images()->first();
                if ($firstImage) {
                    $firstImage->update(['is_primary' => 1]);
                }
            }

            // -------------------------
            // SAVE SIMPLE PRODUCT ATTRIBUTES
            // -------------------------
            if ($request->has('product_attributes')) {
                foreach ($request->product_attributes as $attr) {
                    ProductAttribute::create([
                        'product_id'   => $product->id,
                        'attribute_id' => $attr['attribute_id'],
                        'value'        => $attr['value'],
                        'price'        => $attr['price'] ?? 0,
                        'qty'          => $attr['qty'] ?? 0,
                    ]);
                }
            }

            // -------------------------
            // SAVE PRODUCT VARIANTS
            // -------------------------
            if ($request->has('variants')) {
                foreach ($request->variants as $variant) {
                    $product->variants()->create([
                        'attributes' => json_encode($variant['attributes']),
                        'sku'        => $variant['sku'] ?? null,
                        'price'      => $variant['price'] ?? 0,
                        'stock'      => $variant['stock'] ?? 0,
                        'status'     => 1,
                    ]);
                }
            }

            // -------------------------
            // LOG ACTIVITY
            // -------------------------
            log_activity(
                'create',
                Product::class,
                $product->id,
                'Created new product: ' . $product->name,
                ['product' => $product->toArray()]
            );

            DB::commit();

            return redirect()
                ->route('admin.product.index')
                ->with('message', 'Product added successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * EDIT PAGE
     */
    public function edit(Product $product)
    {
        return view('admin.product.edit', [
            'product'     => $product,
            'images'      => $product->images,
            'categories'  => Category::where('status', 1)->get(),
            'subCats'     => SubCategory::where('category_id', $product->category_id)->get(),
            'attributes'  => Attribute::with('values')->where('status', 1)->get(),
            'variants'    => $product->variants ?? [],
        ]);
    }

    /**
     * UPDATE PRODUCT
     */
    public function update(Request $request, Product $product)
    {
        DB::beginTransaction();
        try {
            $product->update([
                'category_id'       => $request->category_id,
                'sub_category_id'   => $request->sub_category_id,
                'name'              => $request->name,
                'slug'              => Str::slug($request->name),
                'short_description' => $request->short_description,
                'description'       => $request->description,
                'base_price'        => $request->base_price,
                'discount_price'    => $request->discount_price,
                'sku'               => $request->sku,
                'stock'             => $request->stock,
                'is_charge_tax'     => $request->is_charge_tax ?? 0,
                'is_featured'       => $request->is_featured ?? 0,
                'status'            => $request->status ?? 1,
            ]);

            /**
             * NEW IMAGES
             */
            if ($request->hasFile('images')) {
                foreach ($request->images as $img) {
                    $path = $img->store('uploads/products', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                        'is_primary' => 0
                    ]);
                }
            }

            /**
             * ATTRIBUTES (DELETE OLD + ADD NEW)
             */
            ProductAttribute::where('product_id', $product->id)->delete();

            if ($request->has('product_attributes')) {
                foreach ($request->product_attributes as $attr) {
                    ProductAttribute::create([
                        'product_id'   => $product->id,
                        'attribute_id' => $attr['attribute_id'],
                        'value'        => $attr['value'],
                        'price'        => $attr['price'] ?? 0,
                        'qty'          => $attr['qty'] ?? 0,
                    ]);
                }
            }

            /**
             * VARIANTS (DELETE OLD + ADD NEW)
             */
            $product->variants()->delete();

            if ($request->has('variants')) {
                foreach ($request->variants as $variant) {
                    $product->variants()->create([
                        'attributes' => json_encode($variant['attributes']),
                        'sku'        => $variant['sku'] ?? null,
                        'price'      => $variant['price'] ?? 0,
                        'stock'      => $variant['stock'] ?? 0,
                        'status'     => 1
                    ]);
                }
            }

            log_activity('update', Product::class, $product->id, 'Updated product: '.$product->name);

            DB::commit();

            return redirect()->route('admin.product.index')->with('message', 'Product updated successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * DELETE PRODUCT (Soft Delete)
     */
    public function destroy(Product $product)
    {
        $product->delete();

        log_activity('delete', Product::class, $product->id, "Deleted product {$product->name}");

        return response()->json(['success' => 'Product deleted successfully.']);
    }

    /**
     * TOGGLE STATUS
     */
    public function toggleStatus(Product $product)
    {
        $old = $product->status;
        $product->status = !$old;
        $product->save();

        return response()->json(['status' => $product->status]);
    }

    /**
     * TRASH PAGE
     */
    public function trash()
    {
        return view('admin.product.trash');
    }

    /**
     * TRASH AJAX DATA
     */
    public function getTrashedData()
    {
        $products = Product::onlyTrashed()->get();

        return datatables()->of($products)
            ->addColumn('action', function ($row) {
                return '
                    <button class="btn btn-sm btn-success restoreProduct" data-id="'.$row->id.'">Restore</button>
                    <button class="btn btn-sm btn-danger forceDeleteProduct" data-id="'.$row->id.'">Delete Permanently</button>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function restore($id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->restore();

        return response()->json(['success' => 'Product restored successfully.']);
    }

    public function forceDelete($id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);

        $product->images()->delete();
        $product->forceDelete();

        return response()->json(['success' => 'Product permanently deleted.']);
    }

    public function getSubcategories($id)
    {
        $subcategories = SubCategory::where('category_id', $id)->get();

        return response()->json($subcategories);
    }
}
