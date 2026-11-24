<?php /** Size tables, shared by the drawer and the size guide page. $type: bedding | rug | furniture | all */ ?>
<?php if ($type === 'bedding' || $type === 'all'): ?>
  <section class="stack">
    <h3 class="h4">Bedding</h3>
    <p class="small muted">Sized for the mattresses most common in Nepal. Fitted sheets have 35 cm pockets.</p>
    <table class="size-table">
      <thead><tr><th scope="col">Size</th><th scope="col">Mattress</th><th scope="col">Duvet cover</th><th scope="col">Flat sheet</th></tr></thead>
      <tbody>
        <tr><th scope="row">Single</th><td>90 × 190 cm</td><td>140 × 200 cm</td><td>180 × 260 cm</td></tr>
        <tr><th scope="row">Double</th><td>150 × 190 cm</td><td>200 × 200 cm</td><td>240 × 260 cm</td></tr>
        <tr><th scope="row">King</th><td>180 × 200 cm</td><td>230 × 220 cm</td><td>275 × 280 cm</td></tr>
      </tbody>
    </table>
    <table class="size-table">
      <thead><tr><th scope="col">Pillow</th><th scope="col">Size</th></tr></thead>
      <tbody><tr><th scope="row">Standard</th><td>50 × 75 cm</td></tr><tr><th scope="row">King</th><td>50 × 90 cm</td></tr></tbody>
    </table>
  </section>
<?php endif; ?>
<?php if ($type === 'rug' || $type === 'all'): ?>
  <section class="stack">
    <h3 class="h4">Rugs</h3>
    <p class="small muted">A rug should reach under at least the front legs of your sofa and chairs. Mark the size out with masking tape before you order.</p>
    <table class="size-table">
      <thead><tr><th scope="col">Size</th><th scope="col">Works for</th></tr></thead>
      <tbody>
        <tr><th scope="row">120 × 180 cm</th><td>Beside a bed, reading corner, entrance</td></tr>
        <tr><th scope="row">160 × 230 cm</th><td>Three-seater sofa with a coffee table</td></tr>
        <tr><th scope="row">200 × 300 cm</th><td>Full seating area, or under a six-seat dining table</td></tr>
      </tbody>
    </table>
  </section>
<?php endif; ?>
<?php if ($type === 'furniture' || $type === 'all'): ?>
  <section class="stack">
    <h3 class="h4">Furniture</h3>
    <p class="small muted">All measurements are in centimetres, taken at the widest point. Allow 60 cm of walkway around tables and 45 cm in front of a sofa.</p>
    <table class="size-table">
      <thead><tr><th scope="col">Stool height</th><th scope="col">Counter or bar height</th></tr></thead>
      <tbody>
        <tr><th scope="row">65 cm</th><td>Kitchen counters, 88–92 cm</td></tr>
        <tr><th scope="row">75 cm</th><td>Bar tables, 100–106 cm</td></tr>
      </tbody>
    </table>
    <p class="small">Check doorways and stairs for sofas: the Dhaka sofa needs an 84 cm clear opening. Our delivery team can remove legs on site.</p>
  </section>
<?php endif; ?>
