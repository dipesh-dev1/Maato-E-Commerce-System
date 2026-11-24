// JSON over fetch with the CSRF header. Always resolves to { ok, status, data, error, errors }.
const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';

export const baseUrl = (path = '') => (document.querySelector('meta[name="base-url"]')?.content ?? '') + path;

export async function request(url, { method = 'GET', body = null } = {}) {
  const headers = { Accept: 'application/json', 'X-Requested-With': 'fetch', 'X-CSRF-Token': csrfToken() };
  let payload = body;
  if (body && !(body instanceof FormData)) {
    headers['Content-Type'] = 'application/json';
    payload = JSON.stringify(body);
  }
  let response;
  try {
    response = await fetch(url, { method, headers, body: payload, credentials: 'same-origin' });
  } catch {
    return { ok: false, status: 0, data: null, error: 'You seem to be offline. Check your connection and try again.' };
  }
  let json = null;
  try { json = await response.json(); } catch { /* non-JSON error page */ }
  return {
    ok: response.ok && json?.ok !== false,
    status: response.status,
    data: json?.data ?? null,
    error: json?.error ?? (response.ok ? null : 'Something went wrong. Please try again.'),
    errors: json?.errors ?? null,
  };
}

/** Submits a <form> via fetch as POST (method spoofing travels in the _method field). */
export function submitForm(form, submitter = null) {
  const action = submitter?.getAttribute('formaction') || form.action;
  return request(action, { method: 'POST', body: new FormData(form) });
}

export function debounce(fn, wait = 250) {
  let timer;
  return (...args) => { clearTimeout(timer); timer = setTimeout(() => fn(...args), wait); };
}
