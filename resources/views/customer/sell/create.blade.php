@extends('layouts.storefront')
@section('title', '판매하기 · MOONS')

@section('content')
<div class="wrap" style="max-width:820px">
    <div class="listing__head" style="margin-top:28px"><h1>💰 내 명품 판매하기</h1></div>
    <p style="color:var(--muted);margin:-8px 0 24px">본사(MOONS) 또는 가까운 지점에 직접 판매하세요. 정품 감정 후 견적을 받아보실 수 있습니다.</p>

    @if($errors->any())<div class="alert alert--err">{{ $errors->first() }}</div>@endif

    <form action="{{ route('sell.store') }}" method="POST" enctype="multipart/form-data"
          style="background:#fff;border:1px solid var(--line);border-radius:18px;padding:30px">
        @csrf

        {{-- 판매 방식 --}}
        <div class="field">
            <label>판매 방식</label>
            <div style="display:flex;gap:12px">
                <label class="pick"><input type="radio" name="method" value="quote" checked onchange="toggleMethod()"> 일반 견적 <small>(본사/지점 지정)</small></label>
                <label class="pick"><input type="radio" name="method" value="auction" onchange="toggleMethod()"> 경매 견적 <small>(여러 지점 입찰)</small></label>
            </div>
        </div>

        {{-- 판매 대상 (일반 견적일 때만) --}}
        <div class="field" id="targetBox">
            <label>판매 대상</label>
            <div style="display:flex;gap:12px;margin-bottom:10px">
                <label class="pick"><input type="radio" name="target_type" value="head_office" checked onchange="toggleTarget()"> 🏢 MOONS 본사</label>
                <label class="pick"><input type="radio" name="target_type" value="store" onchange="toggleTarget()"> 🏬 지점 선택</label>
            </div>
            <select name="target_store_id" id="storeSelect" style="display:none">
                <option value="">지점을 선택하세요</option>
                @foreach($stores as $st)
                    <option value="{{ $st->id }}">{{ $st->company_name }} @if($st->brand)· {{ $st->brand }}@endif</option>
                @endforeach
            </select>
        </div>

        <div class="form-2col">
            <div class="field"><label>브랜드 *</label><input type="text" name="brand" value="{{ old('brand') }}" placeholder="Louis Vuitton" required></div>
            <div class="field"><label>카테고리</label>
                <select name="category_id">
                    <option value="">선택</option>
                    @foreach($categories as $c)<option value="{{ $c->id }}" @selected(old('category_id')==$c->id)>{{ $c->name }}</option>@endforeach
                </select>
            </div>
        </div>

        <div class="field"><label>상품명 *</label><input type="text" name="title" value="{{ old('title') }}" placeholder="네버풀 MM 모노그램 토트백" required></div>
        <div class="field"><label>상세 설명 / 상태</label><textarea name="description" rows="3" placeholder="구매 시기, 사용감, 구성품(더스트백/보증서 등)을 적어주세요">{{ old('description') }}</textarea></div>

        <div class="form-2col">
            <div class="field"><label>희망 판매가 (원)</label><input type="number" name="desired_price" value="{{ old('desired_price') }}" min="0" placeholder="예: 1,500,000"></div>
            <div class="field"><label>수령 방법</label>
                <div style="display:flex;gap:12px;padding-top:6px">
                    <label class="pick"><input type="radio" name="delivery_method" value="parcel" checked onchange="toggleVisit()"> 📦 택배 접수</label>
                    <label class="pick"><input type="radio" name="delivery_method" value="visit" onchange="toggleVisit()"> 🏬 방문 예약</label>
                </div>
            </div>
        </div>
        <div class="field" id="visitBox" style="display:none"><label>방문 예약 일시</label><input type="datetime-local" name="visit_at" value="{{ old('visit_at') }}"></div>

        {{-- 사진 여러 장 --}}
        <div class="field">
            <label>상품 사진 (여러 장, 최대 10장) *</label>
            <input type="file" name="photos[]" accept="image/*" multiple id="photoInput" onchange="previewPhotos()">
            <div id="photoPreview" class="photo-preview"></div>
            <small style="color:var(--muted)">브랜드 로고, 시리얼 넘버, 사용감 부위를 선명하게 촬영해 주세요.</small>
        </div>

        <button type="submit" class="btn btn--primary btn--block" style="margin-top:8px">판매 접수하기</button>
    </form>
</div>

<style>
    .pick { display:flex;align-items:center;gap:6px;padding:11px 16px;border:1px solid var(--line);border-radius:11px;cursor:pointer;font-size:14px;font-weight:600;flex:1 }
    .pick small { color:var(--muted);font-weight:500 }
    .pick:has(input:checked) { border-color:var(--ink);background:var(--bg-alt) }
    .form-2col { display:grid;grid-template-columns:1fr 1fr;gap:0 18px }
    .photo-preview { display:flex;gap:8px;flex-wrap:wrap;margin-top:10px }
    .photo-preview img { width:76px;height:76px;object-fit:cover;border-radius:9px;border:1px solid var(--line) }
    @media(max-width:640px){ .form-2col{grid-template-columns:1fr} }
</style>
<script>
    function toggleMethod(){
        var auction = document.querySelector('input[name=method]:checked').value === 'auction';
        document.getElementById('targetBox').style.display = auction ? 'none' : '';
    }
    function toggleTarget(){
        var store = document.querySelector('input[name=target_type]:checked').value === 'store';
        document.getElementById('storeSelect').style.display = store ? '' : 'none';
    }
    function toggleVisit(){
        var visit = document.querySelector('input[name=delivery_method]:checked').value === 'visit';
        document.getElementById('visitBox').style.display = visit ? '' : 'none';
    }
    function previewPhotos(){
        var box = document.getElementById('photoPreview'); box.innerHTML = '';
        Array.prototype.slice.call(document.getElementById('photoInput').files).slice(0,10).forEach(function(f){
            var img = document.createElement('img'); img.src = URL.createObjectURL(f); box.appendChild(img);
        });
    }
</script>
@endsection
