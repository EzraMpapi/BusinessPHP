<div class="space-y-4">
  <div class="section-header">
    <h2>Inventory</h2>
    <button class="btn btn-primary" onclick="openItemForm()">
      <i data-lucide="plus" class="w-3.5 h-3.5"></i> Add Item
    </button>
  </div>

  <div id="inv-kpis" class="grid-3" style="gap:12px">
    <div class="kpi"><div class="spinner"></div></div>
    <div class="kpi"><div class="spinner"></div></div>
    <div class="kpi"><div class="spinner"></div></div>
  </div>

  <!-- Low stock alert -->
  <div id="low-stock-alert"></div>

  <div id="inv-table"></div>

  <!-- Add Item Modal -->
  <div id="item-modal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="card w-full max-w-md">
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-bold text-[15px] text-[#111827]">Add Inventory Item</h3>
        <button onclick="document.getElementById('item-modal').classList.add('hidden')" class="text-slate-400">
          <i data-lucide="x" class="w-4 h-4"></i></button>
      </div>
      <div class="space-y-3">
        <div class="grid-2" style="gap:8px">
          <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">SKU</label>
            <input id="it-sku" class="bs-input" placeholder="ELEC-001"/></div>
          <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Category</label>
            <input id="it-cat" class="bs-input" placeholder="Electronics"/></div>
        </div>
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Product Name *</label>
          <input id="it-name" class="bs-input" placeholder="Product name"/></div>
        <div class="grid-3" style="gap:8px">
          <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Qty</label>
            <input id="it-qty" type="number" class="bs-input" placeholder="0"/></div>
          <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Reorder</label>
            <input id="it-reorder" type="number" class="bs-input" placeholder="5"/></div>
          <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Cost (<?= CURRENCY ?>)</label>
            <input id="it-cost" type="number" class="bs-input" placeholder="0"/></div>
        </div>
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Selling Price (<?= CURRENCY ?>)</label>
          <input id="it-price" type="number" class="bs-input" placeholder="0"/></div>
        <div class="flex gap-2 pt-2">
          <button class="btn btn-primary flex-1" onclick="saveItem()">Add Item</button>
          <button class="btn btn-secondary" onclick="document.getElementById('item-modal').classList.add('hidden')">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
let items = [];

(async function() {
  const rows = await fetchRows('inventory_items');
  items = rows.length ? rows : window.SEED.inventory;
  renderInventory();
})();

function renderInventory() {
  const totalValue = items.reduce((s,i)=>s+(i.qty||i.quantity||0)*(i.cost||i.unit_cost||0),0);
  const lowStock   = items.filter(i=>(i.qty||i.quantity||0)<=(i.reorder||i.reorder_level||5));

  document.getElementById('inv-kpis').innerHTML =
    buildKpi('Total SKUs', String(items.length), '', null, '#7C3AED') +
    buildKpi('Inventory Value', money(totalValue||89400000), '+5.7%', true, '#16A34A') +
    buildKpi('Low Stock Items', String(lowStock.length), '!', false, '#EF4444');

  if (lowStock.length) {
    document.getElementById('low-stock-alert').innerHTML = `
      <div class="card" style="border-left:3px solid #EF4444;background:#FEF2F2">
        <p class="text-[13px] font-semibold text-red-700">
          <i data-lucide="alert-circle" class="w-4 h-4 inline mr-1"></i>
          ${lowStock.length} item(s) below reorder level:
          <strong>${lowStock.map(i=>i.name||i.item_name).join(', ')}</strong>
        </p>
      </div>`;
  }

  document.getElementById('inv-table').innerHTML = buildTable(
    ['SKU','Product','Category','Stock','Reorder','Unit Price','Value',''],
    items,
    item => {
      const qty = item.qty || item.quantity || 0;
      const reorder = item.reorder || item.reorder_level || 5;
      const low = qty <= reorder;
      return `<tr class="${low ? 'bg-red-50' : ''}">
        <td class="font-mono text-[11px] text-slate-500">${item.sku||item.item_sku||''}</td>
        <td class="font-semibold text-[#111827]">${item.name||item.item_name||''}</td>
        <td><span class="pill" style="background:#F1F5F9;color:#374151">${item.category||''}</span></td>
        <td class="font-bold ${low?'text-red-500':'text-[#111827]'}">${qty}${low?' ⚠':''}</td>
        <td class="text-slate-500">${reorder}</td>
        <td>${money(item.price||item.selling_price)}</td>
        <td class="font-bold">${money(qty*(item.cost||item.unit_cost||0))}</td>
        <td><button onclick="adjustStock(${item.id})" class="text-[11px] text-blue-600 font-semibold hover:underline">Adjust</button></td>
      </tr>`;
    }
  );
  lucide.createIcons();
}

function openItemForm() { document.getElementById('item-modal').classList.remove('hidden'); }

async function saveItem() {
  const name = document.getElementById('it-name').value.trim();
  if (!name) { toast('Product name is required','error'); return; }
  const row = {
    item_sku: document.getElementById('it-sku').value,
    item_name: name,
    category: document.getElementById('it-cat').value,
    quantity: Number(document.getElementById('it-qty').value)||0,
    reorder_level: Number(document.getElementById('it-reorder').value)||5,
    unit_cost: Number(document.getElementById('it-cost').value)||0,
    selling_price: Number(document.getElementById('it-price').value)||0,
  };
  items.unshift({id:uid(), sku:row.item_sku, name:row.item_name, category:row.category,
    qty:row.quantity, reorder:row.reorder_level, cost:row.unit_cost, price:row.selling_price});
  try { await insertRow('inventory_items', row); toast('Item added to Supabase!'); }
  catch(e) { toast('Added locally'); }
  document.getElementById('item-modal').classList.add('hidden');
  renderInventory();
}

function adjustStock(id) {
  const qty = prompt('Enter new stock quantity:');
  if (qty === null || isNaN(qty)) return;
  items = items.map(i => i.id==id ? {...i, qty:Number(qty)} : i);
  updateRow('inventory_items', id, {quantity:Number(qty)}).catch(()=>{});
  renderInventory(); toast('Stock updated');
}
</script>
