/* BusinessSphere ERP — Main Application JavaScript
   No React. No build tools. Plain JavaScript that works everywhere.
*/

// ── Supabase Client ────────────────────────────────────────────────────────
const { createClient } = supabase;
const db = createClient(window.BS.url, window.BS.key);

// ── Utilities ──────────────────────────────────────────────────────────────
const money = n => window.BS.currency + ' ' + new Intl.NumberFormat('en-US').format(Math.round(n || 0));
const fmt   = n => new Intl.NumberFormat('en-US').format(Math.round(n || 0));
const today = () => new Date().toISOString().slice(0, 10);
const uid   = () => Math.floor(1000 + Math.random() * 9000);

// ── Toast Notifications ────────────────────────────────────────────────────
function toast(msg, type = 'success') {
  const el = document.createElement('div');
  el.className = `toast ${type}`;
  el.innerHTML = `<span>${msg}</span>`;
  document.getElementById('toasts').appendChild(el);
  setTimeout(() => el.remove(), 3500);
}

// ── Sidebar Toggle ─────────────────────────────────────────────────────────
let sidebarOpen = true;
function toggleSidebar() {
  sidebarOpen = !sidebarOpen;
  document.getElementById('sidebar').classList.toggle('collapsed', !sidebarOpen);
  document.querySelectorAll('[data-lucide="panel-left-close"]').forEach(el => {
    el.setAttribute('data-lucide', sidebarOpen ? 'panel-left-close' : 'panel-left-open');
  });
  lucide.createIcons();
}

// ── Dark Mode ──────────────────────────────────────────────────────────────
function toggleDark() {
  document.body.classList.toggle('dark');
  localStorage.setItem('bs_dark', document.body.classList.contains('dark') ? '1' : '0');
}
if (localStorage.getItem('bs_dark') === '1') document.body.classList.add('dark');

// ── Generic Supabase helpers ────────────────────────────────────────────────
async function fetchRows(table, filters = {}, limit = 500) {
  try {
    let q = db.from(table).select('*').limit(limit);
    for (const [col, val] of Object.entries(filters)) q = q.eq(col, val);
    const { data, error } = await q;
    if (error) throw error;
    return data || [];
  } catch (e) {
    console.warn('fetchRows', table, e.message);
    return [];
  }
}

async function insertRow(table, row) {
  const { data, error } = await db.from(table).insert(row).select().single();
  if (error) throw error;
  return data;
}

async function updateRow(table, id, patch) {
  const { data, error } = await db.from(table).update(patch).eq('id', id).select().single();
  if (error) throw error;
  return data;
}

async function deleteRow(table, id) {
  const { error } = await db.from(table).delete().eq('id', id);
  if (error) throw error;
}

// ── KPI Card Builder ────────────────────────────────────────────────────────
function buildKpi(label, value, delta, deltaUp, color = '#16A34A') {
  return `
    <div class="kpi">
      <div class="flex items-center justify-between mb-2">
        <span class="text-[11.5px] font-medium text-slate-500">${label}</span>
        <div class="w-7 h-7 rounded-xl flex items-center justify-center" style="background:${color}18">
          <div class="w-3.5 h-3.5 rounded-sm" style="background:${color}"></div>
        </div>
      </div>
      <div class="flex items-end justify-between">
        <span class="text-[17px] font-bold text-[#111827]">${value}</span>
        ${delta ? `<span class="text-[11px] font-semibold px-1.5 py-0.5 rounded-md ${deltaUp ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600'}">${delta}</span>` : ''}
      </div>
    </div>`;
}

// ── Pill Badge ─────────────────────────────────────────────────────────────
function buildPill(label, color = '#16A34A') {
  return `<span class="pill" style="background:${color}18;color:${color}">
    <span style="width:5px;height:5px;border-radius:50%;background:${color};display:inline-block"></span>
    ${label}
  </span>`;
}

// ── Table Builder ───────────────────────────────────────────────────────────
function buildTable(cols, rows, renderRow) {
  return `
    <div class="card overflow-hidden p-0">
      <div class="overflow-x-auto">
        <table class="bs-table">
          <thead><tr>${cols.map(c => `<th>${c}</th>`).join('')}</tr></thead>
          <tbody>${rows.map(renderRow).join('')}</tbody>
        </table>
        ${rows.length === 0 ? '<div class="py-12 text-center text-slate-400 text-sm">No records found</div>' : ''}
      </div>
    </div>`;
}

// ── Seed data (used when Supabase is not connected) ─────────────────────────
window.SEED = {
  leads: [
    {id:1,company:'Dar Tech Solutions',contact:'James Omondi',value:8500000,stage:'Proposal',email:'james@dartech.co.tz'},
    {id:2,company:'Kilosa Hardware Ltd',contact:'Amina Rashid',value:3200000,stage:'Negotiation',email:'amina@kilosa.co.tz'},
    {id:3,company:'Moshi Fresh Foods',contact:'Peter Mwamba',value:1800000,stage:'Won',email:'peter@moshi.co.tz'},
    {id:4,company:'Arusha Electronics',contact:'Grace Kimani',value:12000000,stage:'Prospecting',email:'grace@arusha.co.tz'},
    {id:5,company:'Dodoma Building Sup.',contact:'Hassan Ally',value:4500000,stage:'Qualified',email:'hassan@dodoma.co.tz'},
  ],
  invoices: [
    {id:'INV-2847',customer:'Mwangi Supplies',amount:2400000,status:'Unpaid',date:'2026-07-20'},
    {id:'INV-2846',customer:'Karibu Supermarket',amount:5800000,status:'Partial',date:'2026-07-18'},
    {id:'INV-2845',customer:'Salama Retailers',amount:1200000,status:'Paid',date:'2026-07-15'},
    {id:'INV-2844',customer:'Dar Tech Solutions',amount:8500000,status:'Unpaid',date:'2026-07-12'},
    {id:'INV-2843',customer:'Moshi Fresh Foods',amount:3200000,status:'Paid',date:'2026-07-10'},
  ],
  inventory: [
    {id:1,sku:'ELEC-001',name:'Samsung 55" TV',category:'Electronics',qty:12,reorder:5,cost:890000,price:1200000},
    {id:2,sku:'ELEC-002',name:'LG Fridge 400L',category:'Electronics',qty:8,reorder:3,cost:1200000,price:1650000},
    {id:3,sku:'FMCG-001',name:'Unga Dona 2kg',category:'FMCG',qty:250,reorder:50,cost:3500,price:5000},
    {id:4,sku:'FMCG-002',name:'Cooking Oil 5L',category:'FMCG',qty:180,reorder:40,cost:12000,price:16500},
    {id:5,sku:'HARD-001',name:'Cement 50kg',category:'Hardware',qty:3,reorder:20,cost:15000,price:19000},
  ],
  employees: [
    {id:1,name:'Amina Said',role:'Sales Manager',dept:'Sales',salary:850000,status:'Active',joined:'2022-03'},
    {id:2,name:'John Mwangi',role:'Accountant',dept:'Finance',salary:700000,status:'Active',joined:'2021-07'},
    {id:3,name:'Grace Kimani',role:'HR Officer',dept:'HR',salary:650000,status:'Active',joined:'2023-01'},
    {id:4,name:'Peter Ally',role:'Warehouse Staff',dept:'Operations',salary:450000,status:'Active',joined:'2022-11'},
    {id:5,name:'Fatima Omar',role:'IT Support',dept:'Technology',salary:580000,status:'On Leave',joined:'2023-06'},
  ],
};

// Stage colors
window.STAGE_COLOR = {
  Prospecting:'#94A3B8',Qualified:'#3B82F6',Proposal:'#8B5CF6',
  Negotiation:'#F59E0B',Won:'#16A34A',Lost:'#EF4444'
};
window.STATUS_COLOR = {
  Paid:'#16A34A',Unpaid:'#EF4444',Partial:'#F59E0B',Draft:'#94A3B8',
  Active:'#16A34A','On Leave':'#F59E0B',Inactive:'#EF4444'
};
