<div class="page-head"><div><h1 class="h1">Activity log</h1><p>Every change made in the back office: who, what and when. Entries can't be edited or deleted.</p></div></div>
<div class="a-card" data-table>
  <form class="toolbar" method="get" action="<?= e(route('admin.activity')) ?>" data-table-filters>
    <?php partial('admin/partials/search', ['value' => $filters['q'], 'placeholder' => 'Search descriptions']); ?>
    <label class="visually-hidden" for="f-entity">Record type</label>
    <select class="select" id="f-entity" name="entity">
      <option value="">All records</option>
      <?php foreach ($entities as $en): ?><option value="<?= e($en) ?>"<?= $filters['entity'] === $en ? ' selected' : '' ?>><?= e(ucfirst($en)) ?></option><?php endforeach; ?>
    </select>
    <label class="visually-hidden" for="f-user">Person</label>
    <select class="select" id="f-user" name="user">
      <option value="">Everyone</option>
      <?php foreach ($actors as $a): ?><option value="<?= (int) $a['id'] ?>"<?= $filters['user'] === (string) $a['id'] ? ' selected' : '' ?>><?= e($a['name']) ?></option><?php endforeach; ?>
    </select>
    <input type="hidden" name="sort" value="<?= e($filters['sort']) ?>"><input type="hidden" name="dir" value="<?= e($filters['dir']) ?>">
  </form>
  <div data-table-body><?php partial('admin/activity/_table', get_defined_vars()); ?></div>
</div>
