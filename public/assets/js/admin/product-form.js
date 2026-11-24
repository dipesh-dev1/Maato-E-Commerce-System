/** Product editor: variant rows, image ordering (drag or buttons), removal toggles and upload previews. */
export function initProductForm(form) {
  // ---- Variants ----
  const rows = form.querySelector('[data-variant-rows]');
  const template = form.querySelector('[data-variant-template]');
  let next = rows.children.length;
  form.querySelector('[data-add-variant]').addEventListener('click', () => {
    const html = template.innerHTML.replaceAll('__INDEX__', String(next++));
    rows.insertAdjacentHTML('beforeend', html);
    rows.lastElementChild.querySelector('input:not([type="hidden"])').focus();
  });
  rows.addEventListener('click', (e) => {
    const remove = e.target.closest('[data-remove-variant]');
    if (!remove) return;
    if (rows.children.length === 1) {
      remove.closest('tr').querySelectorAll('input:not([type="hidden"]):not([type="color"])').forEach((i) => { i.value = i.name.endsWith('[stock]') ? '0' : ''; });
      return;
    }
    remove.closest('tr').remove();
  });

  // ---- Existing images ----
  const list = form.querySelector('[data-image-list]');
  const refreshBadges = () => {
    const tiles = [...list.querySelectorAll('[data-image-tile]')];
    const firstKept = tiles.find((t) => !t.classList.contains('is-removed'));
    tiles.forEach((t) => { t.querySelector('[data-main-badge]').hidden = t !== firstKept; });
  };
  list.addEventListener('click', (e) => {
    const move = e.target.closest('[data-move]');
    if (!move) return;
    const tile = move.closest('[data-image-tile]');
    if (move.dataset.move === '-1' && tile.previousElementSibling) tile.previousElementSibling.before(tile);
    if (move.dataset.move === '1' && tile.nextElementSibling) tile.nextElementSibling.after(tile);
    move.focus();
    refreshBadges();
  });
  list.addEventListener('change', (e) => {
    if (!e.target.matches('[data-remove-image]')) return;
    e.target.closest('[data-image-tile]').classList.toggle('is-removed', e.target.checked);
    refreshBadges();
  });
  let dragged = null;
  list.addEventListener('dragstart', (e) => {
    dragged = e.target.closest('[data-image-tile]');
    dragged?.classList.add('is-dragging');
    e.dataTransfer.effectAllowed = 'move';
  });
  list.addEventListener('dragover', (e) => {
    e.preventDefault();
    const over = e.target.closest('[data-image-tile]');
    if (!dragged || !over || over === dragged) return;
    const rect = over.getBoundingClientRect();
    (e.clientX < rect.left + rect.width / 2 ? over.before.bind(over) : over.after.bind(over))(dragged);
  });
  list.addEventListener('dragend', () => { dragged?.classList.remove('is-dragging'); dragged = null; refreshBadges(); });

  // ---- New uploads ----
  const input = form.querySelector('[data-image-input]');
  const previews = form.querySelector('[data-image-previews]');
  const zone = form.querySelector('[data-dropzone]');
  const renderPreviews = () => {
    previews.querySelectorAll('img').forEach((img) => URL.revokeObjectURL(img.src));
    previews.innerHTML = '';
    [...input.files].forEach((file) => {
      const li = document.createElement('li');
      li.className = 'image-tile';
      const img = document.createElement('img');
      img.src = URL.createObjectURL(file);
      img.alt = `New: ${file.name}`;
      const bar = document.createElement('div');
      bar.className = 'image-tile__bar small muted';
      bar.textContent = `New · ${(file.size / 1048576).toFixed(1)} MB`;
      li.append(img, bar);
      previews.append(li);
    });
  };
  input.addEventListener('change', renderPreviews);
  ['dragenter', 'dragover'].forEach((type) => zone.addEventListener(type, (e) => { e.preventDefault(); zone.classList.add('is-over'); }));
  ['dragleave', 'drop'].forEach((type) => zone.addEventListener(type, () => zone.classList.remove('is-over')));
  zone.addEventListener('drop', (e) => {
    e.preventDefault();
    if (!e.dataTransfer.files.length) return;
    input.files = e.dataTransfer.files;
    renderPreviews();
  });
}
