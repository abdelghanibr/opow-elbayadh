{{-- Partiel : une seule carte (badge CR80). Attend la variable $card. --}}
<div class="card-piece">
    <div class="badge">
        {{-- Bandeau --}}
        <div class="badge-top">
            @if($card['logo'])
                <img class="logo" src="{{ $card['logo'] }}" alt="الديوان">
            @else
                <span style="width:6mm"></span>
            @endif
            <div class="titles">
                <span class="rep">الجمهورية الجزائرية الديمقراطية الشعبية</span>
                <span class="org">وزارة الشباب والرياضة — {{ $card['complex'] ?: 'ديوان ميلة' }}</span>
            </div>
            <div class="season-chip">{{ $card['seasonLabel'] }}</div>
        </div>

        {{-- Corps --}}
        <div class="badge-body">
            <div class="photo-wrap">
                @if($card['photoAvailable'])
                    <img src="{{ $card['photo'] }}" alt="صورة اللاعب">
                @else
                    <div class="no-photo">لا توجد صورة</div>
                @endif
            </div>

            <div class="info">
                <div class="name">{{ $card['lastname'] }} {{ $card['firstname'] }}</div>
                <div class="row">
                    <span class="k">الفئة</span><span class="v">{{ $card['category'] ?: '—' }}</span>
                    <span class="k" style="min-width:9mm">النادي</span><span class="v">{{ $card['club'] ?: '—' }}</span>
                </div>
                <div class="row">
                    <span class="k">النشاط</span><span class="v">{{ $card['activity'] ?: '—' }}</span>
                </div>
                <div class="row">
                    <span class="k">العنوان</span><span class="v addr">{{ $card['address'] ?: '—' }}</span>
                </div>
                <div class="row dayshours">
                    <span class="k">الايام والوقت</span>
                    <span class="v">{{ $card['days'] ?: '—' }} {{ $card['hours'] ? '| ' . $card['hours'] : '' }}</span>
                </div>
                <div class="row">
                    <span class="k">معرف</span><span class="v">{{ $card['licenseNumber'] ?: $card['cardNumber'] }}</span>
                    <span class="k" style="min-width:9mm">فصيلة الدم</span><span class="v">{{ $card['bloodType'] ?: '—' }}</span>
                </div>
            </div>

            <div class="qr-col">
                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(110)->margin(0)->generate($card['verifyUrl']) !!}
                <span class="n">{{ $card['cardNumber'] }}</span>
            </div>
        </div>

        {{-- Pied --}}
        <div class="badge-foot">
            <span>صدر في: <b>{{ $card['issuedAt'] }}</b></span>
            <span>ينتهي: <b>{{ $card['expiresAt'] }}</b></span>
            <span class="st {{ $card['canIssue'] ? 'sariya' : ($card['status'] === 'موقوفة' ? 'mawqofa' : 'muntahia') }}">
                {{ $card['status'] === 'سارية' ? '✔ سارية' : ($card['status'] === 'موقوفة' ? '⛔ موقوفة' : '⏳ منتهية') }}
            </span>
        </div>
    </div>
</div>