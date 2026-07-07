@extends('layouts.partner')
@section('title', $product->exists ? '상품 수정' : '상품 등록')
@section('subtitle', $product->exists ? $product->brand.' · '.$product->name : '새 상품을 등록합니다')

@section('content')
<form action="{{ $product->exists ? route('partner.products.update', $product) : route('partner.products.store') }}" method="POST">
    @csrf
    @if($product->exists) @method('PUT') @endif
    <div class="panel">
        <div class="panel__body">
            <div class="form-grid">
                <div class="pfield">
                    <label>카테고리 *</label>
                    <select name="category_id" required>
                        <option value="">선택</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}" @selected(old('category_id', $product->category_id)==$c->id)>
                                {{ $c->parent?->name }} · {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="pfield">
                    <label>브랜드 *</label>
                    <input type="text" name="brand" value="{{ old('brand', $product->brand) }}" placeholder="GUCCI" required>
                </div>
                <div class="pfield">
                    <label>상품명 *</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" placeholder="레더 토트백" required>
                </div>
                <div class="pfield">
                    <label>대표색 그라디언트</label>
                    <input type="text" name="color" value="{{ old('color', $product->color) }}" placeholder="#232526,#414345">
                </div>
                <div class="pfield">
                    <label>정가 (원) *</label>
                    <input type="number" name="price" value="{{ old('price', $product->price) }}" min="0" required>
                </div>
                <div class="pfield">
                    <label>판매가/할인가 (원)</label>
                    <input type="number" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" min="0" placeholder="할인 없으면 비워두세요">
                </div>
                <div class="pfield">
                    <label>재고 *</label>
                    <input type="number" name="stock" value="{{ old('stock', $product->stock ?? 10) }}" min="0" required>
                </div>
                <div class="pfield full">
                    <label>상품 설명</label>
                    <textarea name="description" placeholder="상품 상세 설명">{{ old('description', $product->description) }}</textarea>
                </div>
                <div class="pfield full">
                    <label>옵션</label>
                    <div class="checks">
                        <label><input type="checkbox" name="is_new" value="1" @checked(old('is_new', $product->is_new))> 신상품(NEW)</label>
                        <label><input type="checkbox" name="is_best" value="1" @checked(old('is_best', $product->is_best))> 베스트(BEST)</label>
                        <label><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->exists ? $product->is_active : true))> 판매중</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel__head" style="border-top:1px solid var(--p-line);border-bottom:0;justify-content:flex-end;gap:8px">
            <a class="pbtn" href="{{ route('partner.products.index') }}">취소</a>
            <button class="pbtn pbtn--primary" type="submit">{{ $product->exists ? '수정 저장' : '상품 등록' }}</button>
        </div>
    </div>
</form>
@endsection
