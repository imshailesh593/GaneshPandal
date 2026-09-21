<div class="center">
    <div class="mandal">शकुंतल नगर गणेशोत्सव तरुण मंडळ</div>
    <div class="festival">गणेशोत्सव {{ $year }}</div>
    <div class="title" style="margin-top: 4mm;">प्रमाणपत्र</div>
    <div class="title-en">CERTIFICATE OF ACHIEVEMENT</div>
    <div class="certify" style="margin-top: 8mm;">हे प्रमाणित करण्यात येते की</div>
    <div class="name">{{ $winner->displayName() }}</div>
    <div style="margin: 0 auto; width: 190mm; border-top: 0.4mm dotted #b8892e;"></div>
    <div class="body" style="margin-top: 6mm;">
        यांनी गणेशोत्सव {{ $year }} निमित्त आयोजित &ldquo;<b>{{ $winner->game->name }}</b>&rdquo; मध्ये
        <b>{{ $winner->categoryLabel() }}</b> विभागात
    </div>
    <div class="rank" style="margin-top: 3mm;">{{ $winner->position->formal() }} क्रमांक</div>
    <div class="honour" style="margin-top: 3mm;">प्राप्त केल्याबद्दल हे प्रमाणपत्र देऊन त्यांचा सन्मान करण्यात येत आहे.</div>
</div>
