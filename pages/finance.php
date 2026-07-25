<div class="space-y-4">
  <div class="section-header">
    <h2>Finance Overview</h2>
    <button class="btn btn-primary" onclick="openExpenseForm()">
      <i data-lucide="plus" class="w-3.5 h-3.5"></i> Add Expense
    </button>
  </div>

  <div class="grid-4" style="gap:12px">
    <div id="fin-income" class="kpi"><div class="spinner"></div></div>
    <div id="fin-expense" class="kpi"><div class="spinner"></div></div>
    <div id="fin-profit" class="kpi"><div class="spinner"></div></div>
    <div id="fin-ar" class="kpi"><div class="spinner"></div></div>
  </div>

  <div class="grid-2" style="gap:16px">
    <div class="card">
      <h3 class="text-[14px] font-bold text-[#111827] mb-3">Monthly P&amp;L</h3>
      <canvas id="pl-chart" height="200"></canvas>
    </div>
    <div class="card">
      <h3 class="text-[14px] font-bold text-[#111827] mb-3">Recent Expenses</h3>
      <div id="expense-list" class="space-y-0"></div>
    </div>
  </div>

  <!-- Expense Modal -->
  <div id="exp-modal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="card w-full max-w-sm">
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-bold text-[15px] text-[#111827]">Add Expense</h3>
        <button onclick="document.getElementById('exp-modal').classList.add('hidden')" class="text-slate-400">
          <i data-lucide="x" class="w-4 h-4"></i></button>
      </div>
      <div class="space-y-3">
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Description *</label>
          <input id="ex-desc" class="bs-input" placeholder="e.g. Office rent"/></div>
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Amount (<?= CURRENCY ?>)</label>
          <input id="ex-amount" type="number" class="bs-input" placeholder="0"/></div>
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Category</label>
          <select id="ex-cat" class="bs-input">
            <option>Rent &amp; Utilities</option><option>Salaries</option><option>Marketing</option>
            <option>Equipment</option><option>Travel</option><option>Other</option>
          </select></div>
        <div class="flex gap-2 pt-2">
          <button class="btn btn-primary flex-1" onclick="saveExpense()">Save Expense</button>
          <button class="btn btn-secondary" onclick="document.getElementById('exp-modal').classList.add('hidden')">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
let expenses = [];

(async function() {
  const invoices = await fetchRows('sales_invoices');
  const expRows  = await fetchRows('finance_expenses');
  expenses = expRows.length ? expRows : [
    {id:1,description:'Office Rent',amount:2500000,category:'Rent & Utilities',date:'2026-07-01'},
    {id:2,description:'Staff Salaries',amount:8500000,category:'Salaries',date:'2026-07-05'},
    {id:3,description:'Internet & Power',amount:350000,category:'Rent & Utilities',date:'2026-07-10'},
    {id:4,description:'Marketing Campaign',amount:1200000,category:'Marketing',date:'2026-07-15'},
  ];

  const income  = invoices.filter(i=>i.status==='Paid').reduce((s,i)=>s+(i.amount||i.total_amount||0),0) || 34800000;
  const expense = expenses.reduce((s,e)=>s+(e.amount||0),0);
  const profit  = income - expense;
  const ar      = invoices.filter(i=>i.status==='Unpaid').reduce((s,i)=>s+(i.amount||i.total_amount||0),0) || 12100000;

  document.getElementById('fin-income').innerHTML  = buildKpi('Total Income',  money(income),  '+12%', true,  '#16A34A');
  document.getElementById('fin-expense').innerHTML = buildKpi('Total Expenses', money(expense), '+8%',  false, '#EF4444');
  document.getElementById('fin-profit').innerHTML  = buildKpi('Net Profit',     money(profit),  '+18%', true,  '#2563EB');
  document.getElementById('fin-ar').innerHTML      = buildKpi('Outstanding AR', money(ar),      '-3%',  false, '#D97706');

  // P&L Chart
  const months = ['Mar','Apr','May','Jun','Jul'];
  const incData = [19800000,25600000,28900000,31200000,34800000];
  const expData = [12100000,14800000,16200000,17500000,18900000];
  new Chart(document.getElementById('pl-chart'), {
    type:'bar',
    data:{ labels:months, datasets:[
      {label:'Income', data:incData, backgroundColor:'#16A34A', borderRadius:4},
      {label:'Expenses', data:expData, backgroundColor:'#EF4444', borderRadius:4}
    ]},
    options:{ responsive:true, plugins:{legend:{labels:{font:{size:11}}}},
      scales:{y:{ticks:{callback:v=>(v/1000000).toFixed(0)+'M',font:{size:10}}}}}
  });

  renderExpenses();
})();

function renderExpenses() {
  document.getElementById('expense-list').innerHTML = expenses.slice(0,6).map(e=>`
    <div class="stat">
      <div>
        <p class="text-[12.5px] font-medium text-[#111827]">${e.description}</p>
        <p class="text-[11px] text-slate-400">${e.category||''} &middot; ${e.date||''}</p>
      </div>
      <span class="text-[13px] font-bold text-red-600">${money(e.amount)}</span>
    </div>`).join('');
}

function openExpenseForm() { document.getElementById('exp-modal').classList.remove('hidden'); }

async function saveExpense() {
  const desc = document.getElementById('ex-desc').value.trim();
  if (!desc) { toast('Description required','error'); return; }
  const row = {
    description:desc,
    amount: Number(document.getElementById('ex-amount').value)||0,
    category: document.getElementById('ex-cat').value,
    date: today(), status:'Approved'
  };
  expenses.unshift({id:uid(),...row});
  try { await insertRow('finance_expenses',row); toast('Expense saved!'); }
  catch(e) { toast('Added locally'); }
  document.getElementById('exp-modal').classList.add('hidden');
  renderExpenses();
}
</script>
