<div class="space-y-5">

  <!-- Page header -->
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-[20px] font-bold text-[#111827]">Good morning, Admin</h1>
      <p class="text-[13px] text-slate-500 mt-0.5"><?= COMPANY_NAME ?> &middot; <?= date('l, j F Y') ?></p>
    </div>
    <button class="btn btn-primary" onclick="toast('Feature coming soon!','success')">
      <i data-lucide="plus" class="w-3.5 h-3.5"></i> Quick Action
    </button>
  </div>

  <!-- KPI Grid -->
  <div id="kpi-grid" class="grid-3" style="gap:12px">
    <div class="kpi"><div class="spinner"></div></div>
    <div class="kpi"><div class="spinner"></div></div>
    <div class="kpi"><div class="spinner"></div></div>
    <div class="kpi"><div class="spinner"></div></div>
    <div class="kpi"><div class="spinner"></div></div>
    <div class="kpi"><div class="spinner"></div></div>
  </div>

  <!-- Charts row -->
  <div class="grid-3" style="gap:16px">
    <div class="card" style="grid-column:span 2">
      <div class="flex items-center justify-between mb-3">
        <h3 class="text-[14px] font-bold text-[#111827]">Revenue vs Expenses</h3>
        <span class="text-[11px] text-slate-400">Jan &ndash; Jul 2026</span>
      </div>
      <canvas id="revenue-chart" height="160"></canvas>
    </div>
    <div class="card">
      <h3 class="text-[14px] font-bold text-[#111827] mb-3">Revenue by Category</h3>
      <canvas id="pie-chart" height="160"></canvas>
      <div id="pie-legend" class="mt-3 space-y-1.5"></div>
    </div>
  </div>

  <!-- Recent activity -->
  <div class="card p-0">
    <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100">
      <h3 class="text-[14px] font-bold text-[#111827]">Recent Activity</h3>
      <a href="?page=sales" class="text-[12px] text-[#16A34A] font-semibold">View all</a>
    </div>
    <div id="activity-list"></div>
  </div>

</div>

<script>
(async function() {

  // ── KPIs ──────────────────────────────────────────────────────────────────
  const invRows = await fetchRows('sales_invoices');
  const invoices = invRows.length ? invRows : window.SEED.invoices;
  const leads    = await fetchRows('crm_leads');
  const inv_items = await fetchRows('inventory_items');
  const employees = await fetchRows('hr_employees');

  const revenue   = invoices.filter(i=>i.status==='Paid').reduce((s,i)=>s+(i.amount||0),0);
  const customers = leads.filter(l=>l.stage==='Won').length || 47;
  const openInv   = invoices.filter(i=>i.status==='Unpaid').reduce((s,i)=>s+(i.amount||0),0);
  const invValue  = inv_items.reduce((s,i)=>s+(i.qty||0)*(i.cost||0),0) || 89400000;
  const pending   = invoices.filter(i=>i.status==='Unpaid').length;
  const staff     = employees.length || 28;

  document.getElementById('kpi-grid').innerHTML =
    buildKpi('Monthly Revenue', money(revenue||34800000), '+12.4%', true, '#16A34A') +
    buildKpi('Active Customers', fmt(customers||1247), '+8.2%', true, '#2563EB') +
    buildKpi('Open Invoices', money(openInv||12100000), '-3.1%', false, '#D97706') +
    buildKpi('Inventory Value', money(invValue), '+5.7%', true, '#7C3AED') +
    buildKpi('Pending Orders', String(pending||43), '+2', false, '#DC2626') +
    buildKpi('Team Members', String(staff), '+1', true, '#0891B2');

  // ── Revenue Chart ──────────────────────────────────────────────────────────
  new Chart(document.getElementById('revenue-chart'), {
    type: 'line',
    data: {
      labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul'],
      datasets: [
        { label:'Revenue', data:[18400000,22100000,19800000,25600000,28900000,31200000,34800000],
          borderColor:'#16A34A', backgroundColor:'rgba(22,163,74,.1)', fill:true, tension:.4, borderWidth:2 },
        { label:'Expenses', data:[11200000,13400000,12100000,14800000,16200000,17500000,18900000],
          borderColor:'#EF4444', backgroundColor:'rgba(239,68,68,.08)', fill:true, tension:.4, borderWidth:2 }
      ]
    },
    options: { responsive:true, plugins:{ legend:{ labels:{ font:{ size:11 } } } },
      scales:{ y:{ ticks:{ callback:v=>(v/1000000).toFixed(0)+'M', font:{ size:10 } } } } }
  });

  // ── Pie Chart ──────────────────────────────────────────────────────────────
  const pieData = [{l:'Electronics',v:38,c:'#16A34A'},{l:'FMCG',v:27,c:'#2563EB'},
                   {l:'Hardware',v:19,c:'#D97706'},{l:'Apparel',v:16,c:'#7C3AED'}];
  new Chart(document.getElementById('pie-chart'), {
    type: 'doughnut',
    data: { labels: pieData.map(d=>d.l), datasets:[{ data: pieData.map(d=>d.v), backgroundColor: pieData.map(d=>d.c) }] },
    options: { responsive:true, plugins:{ legend:{ display:false } } }
  });
  document.getElementById('pie-legend').innerHTML = pieData.map(d=>
    `<div class="flex items-center justify-between text-[11px]">
       <div class="flex items-center gap-1.5"><span style="width:8px;height:8px;border-radius:50%;background:${d.c};display:inline-block"></span><span class="text-slate-500">${d.l}</span></div>
       <span class="font-bold text-[#111827]">${d.v}%</span>
     </div>`
  ).join('');

  // ── Recent Activity ─────────────────────────────────────────────────────────
  const recent = invoices.slice(0, 5);
  document.getElementById('activity-list').innerHTML = recent.map(inv => `
    <div class="flex items-center gap-3 px-4 py-3 border-b border-slate-50 last:border-0">
      <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0" style="background:#2563EB18">
        <i data-lucide="file-text" class="w-3.5 h-3.5" style="color:#2563EB"></i>
      </div>
      <div class="flex-1 min-w-0">
        <p class="text-[12.5px] font-medium text-[#111827] truncate">Invoice ${inv.id || inv.doc_number} issued to ${inv.customer || inv.customer_name}</p>
        <p class="text-[11px] text-slate-400">${inv.date || inv.issue_date || today()}</p>
      </div>
      <span class="text-[12px] font-bold text-[#111827]">${money(inv.amount || inv.total_amount)}</span>
      ${buildPill(inv.status, window.STATUS_COLOR[inv.status]||'#94A3B8')}
    </div>`
  ).join('');
  lucide.createIcons();

})();
</script>
