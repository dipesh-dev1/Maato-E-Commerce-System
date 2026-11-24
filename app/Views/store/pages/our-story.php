<?php partial('partials/breadcrumb', ['crumbs' => [['Our story', null]]]); ?>
<div class="container">
  <header class="page-hero">
    <h1 class="display">We started with a mug.</h1>
    <p>In 2021 we couldn't find a cup we liked in Kathmandu that wasn't imported. So we walked to Thimi, sat with a family of potters for a week, and came back with twelve. Maato grew from there, one workshop at a time.</p>
  </header>
  <div class="page-body">
    <img src="<?= e(asset('img/story-studio.jpg')) ?>" alt="The sample shelf in the Maato studio" width="1000" height="1250" loading="lazy">
    <div class="prose">
      <h2>Seven workshops, all of them visited</h2>
      <p>Every piece we sell is made by one of seven small workshops: a pottery in Thimi, metalsmiths in Patan, cane weavers in Chitwan, a joinery by Phewa Lake, handloom weavers in Gorkha and an apothecary in Jumla. Our own studio in Jhamsikhel designs alongside them.</p>
      <p>We pay what each workshop asks, agree batch sizes they can manage without overtime, and pay half up front so they never carry the cost of our stock.</p>
      <h2>Fewer things, made properly</h2>
      <p>We keep about thirty pieces in the range. Each one has been used in our own homes for at least a month before it goes on sale. If something chips too easily or a lamp runs hot, it doesn't make it.</p>
      <h2>Why cash on delivery</h2>
      <p>Most people we know still prefer to see something before paying for it. So that's how we work: you pay when it's in your hands, or when you collect it from the studio.</p>
      <p><a class="btn" href="<?= e(route('shop')) ?>">Shop the range</a></p>
    </div>
  </div>
</div>
