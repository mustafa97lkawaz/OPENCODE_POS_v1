<!-- Products Panel -->
<div class="products-panel">
    <div class="category-tabs" id="categoryTabs">
        <div class="category-tab active" data-category="all">الكل</div>
        @foreach($categories as $category)
            <div class="category-tab" data-category="{{ $category->id }}">{{ $category->Category_name }}</div>
        @endforeach
    </div>
    <div class="product-grid" id="productGrid">
        <div class="loading-spinner">
            <i class="las la-spinner"></i>
            <p>جاري التحميل...</p>
        </div>
    </div>
</div>
