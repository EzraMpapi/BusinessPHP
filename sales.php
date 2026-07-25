<div class="space-y-4">
  <div class="section-header">
    <h2>Sales &amp; Invoices</h2>
    <button class="btn btn-primary" onclick="openInvoiceForm()">
      <i data-lucide="plus" class="w-3.5 h-3.5"></i> New Invoice
    </button>
  </div>

  <div id="sales-kpis" class="grid-3" style="gap:12px">
    <div class="kpi"><div class="spinner"></div></div>
    <div class="kpi"><div class="spinner"></div></div>
    <div class="kpi"><div class="spinner"></div></div>
  </div>

  <div id="invoices-table"></div>

  <!-- New Invoice Modal -->
  <div id="inv-modal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="card w-full max-w-md">
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-bold text-[15px] text-[#111827]">New Invoice</h3>
        <button onclick="document.getElementById('inv-modal').classList.add('hidden')" class="text-slate-400 hover:text-[#111827]">
          <i data-lucide="x" class="w-4 h-4"></i>
        </button>
      </div>
      <div class="space-y-3">
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Customer *</label>
          <input id="i-customer" class="bs-input" placeholder="Customer name" /></div>
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Amount (<?= CURRENCY ?>) *</label>
          <input id="i-amount" type="number" class="bs-input" placeholder="e.g. 1500000" /></div>
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Status</label>
          <select id="i-status" class="bs-input">
            <option>Unpaid</option><option>Partial</option><option>Paid</option><option>Draft</option>
          </select></div>
        <div class="flex gap-2 pt-2">
          <button class="btn btn-primary flex-1" onclick="saveInvoice()">Create Invoice</button>
          <button class="btn btn-secondary" onclick="document.getElementById('inv-modal').classList.add('hidden')">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
let invoices = [];

(async function() {
  const rows = await fetchRows('sales_invoices');
  invoices = rows.length ? rows : window.SEED.invoices;
  renderSales();
})();

function renderSales() {
  const paid    = invoices.filter(i=>i.status==='Paid').reduce((s,i)=>s+(i.amount||i.total_amount||0),0);
  const unpaid  = invoices.filter(i=>i.status==='Unpaid').reduce((s,i)=>s+(i.amount||i.total_amount||0),0);
  const total   = invoices.reduce((s,i)=>s+(i.amount||i.total_amount||0),0);

  document.getElementById('sales-kpis').innerHTML =
    buildKpi('Total Revenue', money(total), '+12%', true, '#16A34A') +
    buildKpi('Outstanding AR', money(unpaid), '', null, '#EF4444') +
    buildKpi('Invoices Issued', String(invoices.length), '+5', true, '#2563EB');

  document.getElementById('invoices-table').innerHTML = buildTable(
    ['Invoice #','Customer','Amount','Status','Date',''],
    invoices,
    inv => `<tr>
      <td class="font-mono font-semibold text-[#111827]">${inv.id||inv.doc_number||'INV-'+uid()}</td>
      <td class="font-medium">${inv.customer||inv.customer_name||''}</td>
      <td class="font-bold">${money(inv.amount||inv.total_amount)}</td>
      <td>${buildPill(inv.status, window.STATUS_COLOR[inv.status]||'#94A3B8')}</td>
      <td class="text-slate-500">${inv.date||inv.issue_date||''}</td>
      <td><button onclick="markPaid(${JSON.stringify(inv.id||inv.doc_number).replace(/"/g,"'")})" class="text-[11px] text-green-600 font-semibold hover:underline">Mark Paid</button></td>
    </tr>`
  );
  lucide.createIcons();
}

function openInvoiceForm() { document.getElementById('inv-modal').classList.remove('hidden'); }

async function saveInvoice() {
  const customer = document.getElementById('i-customer').value.trim();
  const amount   = Number(document.getElementById('i-amount').value);
  if (!customer || !amount) { toast('Customer and amount are required', 'error'); return; }

  const inv = { id:'INV-'+uid(), customer, amount, status: document.getElementById('i-status').value, date: today() };
  invoices.unshift(inv);
  try { await insertRow('sales_invoices', { customer_name:customer, total_amount:amount, status:inv.status, issue_date:inv.date }); toast('Invoice saved!'); }
  catch(e) { toast('Added locally (no DB connection)'); }
  document.getElementById('inv-modal').classList.add('hidden');
  renderSales();
}

async function markPaid(id) {
  invoices = invoices.map(i => (i.id==id||i.doc_number==id) ? {...i, status:'Paid'} : i);
  try { await updateRow('sales_invoices', id, { status:'Paid' }); } catch(e) {}
  renderSales(); toast('Marked as Paid');
}
</script>
