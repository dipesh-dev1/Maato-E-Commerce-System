<?php use App\Models\OrderStatus; ?>
<span class="badge badge--<?= e(OrderStatus::TONES[$status] ?? 'neutral') ?>"><?= e(OrderStatus::label($status)) ?></span>
